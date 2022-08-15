<?php
/**
 * MarketoForm plugin for Craft CMS 3.x
 *
 * Retrieves Marketo form and makes it available on the front end
 *
 * @link      https://www.imarc.com
 * @copyright Copyright (c) 2022 Linnea Hartsuyker
 */

namespace imarc\marketoform\services;

use imarc\marketoform\MarketoForm;

use Exception;

use Craft;
use GuzzleHttp\Client;
use craft\base\Component;

/**
 * MarketoForm Service
 *
 * @author    Linnea Hartsuyker
 * @package   MarketoForm
 * @since     1.0.0
 */
class MarketoFormService extends Component
{
    private $clientId     = null;
    private $clientSecret = null;
    private $marketoUrl   = null;
    private $cacheKey     = 'marketoApiAccessToken';
    private $cacheTimeout = null;

    public $munchkinId = null;
    public $baseUrl = null;
    
    // Public Methods
    // =========================================================================

    public function init() 
    {
        $this->clientId     = MarketoForm::getInstance()->settings->clientId;
        $this->clientSecret = MarketoForm::getInstance()->settings->clientSecret;
        $this->marketoUrl   = MarketoForm::getInstance()->settings->marketoUrl;
        $this->munchkinId   = MarketoForm::getInstance()->settings->munchkinId;
        $this->baseUrl      = MarketoForm::getInstance()->settings->baseUrl;
        $this->cacheTimeout = MarketoForm::getInstance()->settings->cacheTimeout ?: 86400;

        parent::init();

    }

    /**
     * Retrieves a form from Marketo and returns the JSON result, an array of form fields
     * 
     * @param integer $id
     * 
     * @return JSON
     */

    public function getFormFieldsById($id) {

        // Get form JSON from cache if it exists, otherwise pull from Marketo
        $formJson = Craft::$app->getCache()->get("marketoForm" . $id);
        if ($formJson === false) {
            $formJson = $this->sendMarketoRequest('GET', '/rest/asset/v1/form/' . $id . '/fields.json');
            Craft::$app->getCache()->set("marketoForm" . $id, $formJson, $this->cacheTimeout);

        } 
        
        // Make sure Marketo Form ID is in list of all cached forms and add it if not
        $allMarketoForms = Craft::$app->getCache()->get("allMarketoForms");
        if ($allMarketoForms && is_countable($allMarketoForms) && !in_array($id, $allMarketoForms)) {
            $allMarketoForms[] = $id;
        } else {
            $allMarketoForms = [$id];
        }

        Craft::$app->getCache()->set("allMarketoForms", $allMarketoForms, $this->cacheTimeout);

        // Return form json
        if ($formJson["result"]) {
            return $formJson["result"];
        }
        return $formJson;
    }

    /**
     * Retrieves a form from Marketo and returns a JSON object with the format {"fieldId" : {"attribute1" : "value1", "attribute2" : "value2" etc }}
     * 
     * @param integer id
     * 
     * @return JSON
     * 
     */

    public function getFormObjectById($id) {
        $formJson = $this->getFormFieldsById($id);
        $formObj = [];
        $htmlText = [];
        $hiddenFields = [];
        foreach ($formJson as $field) {
            if ($field["dataType"] == "htmltext") {
                $htmlText[] = $field;
            } else if ($field["dataType"] == "hidden") {
                $hiddenFields[] = $field;
            } else {
                $formObj[$field["id"]] = $field;
            }
            
        }
        $formObj["htmlText"] = $htmlText;
        $formObj["hidden"] = $hiddenFields;

        return $formObj;

    }


   // Protected Methods
    // =========================================================================

    /**
     * Send a request to Marketo API.
     *
     * @param string $method
     * @param string $requestPath
     * @param array $options
     * @return Response
     */
    protected function sendMarketoRequest($method, $requestPath, $data = [])
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'Application/json'
            ],
            'body' => json_encode($data)
        ];

        $client = new Client([
            'base_uri' => $this->marketoUrl
        ]);


        $result = $client->request($method, $requestPath, $options);

        $body = $this->handleResult($result);

        if (isset($body['error'])) {
            return false;
        }
        
        if (isset($body['success']) && $body['success'] == false) {
            //Try getting a new token and resend the request
            sleep(15); //Wait a little before retrying
            $this->renewAccessToken();
            $result = $client->request($method, $requestPath, $options);
            $body = $this->handleResult($result);
        }

        

        return $body;

    }

     /**
     * Accquire a new access token from Marketo.
     *
     * @return string|bool
     */
    protected function renewAccessToken()
    {
        $client = new Client([
            'base_uri' => $this->marketoUrl
        ]);

        $result = $client->request('GET', '/identity/oauth/token', [
            'query' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret
            ]
        ]);
        $data = $this->handleResult($result);

        if (!isset($data['access_token'])) {
            throw new Exception('Failed to retrieve a new marketo token.');
        }
        $data['expires_in'] -= 60; //Reduce expire time for reliability.

        Craft::$app->getCache()->set($this->cacheKey, $data['access_token'], $data['expires_in']);

        return $data['access_token'];
    }

    /**
     * Get the access token for MarketoApi, if expired get a new one from marketo.
     *
     * @return string|bool
     */
    protected function getAccessToken() {
        
        $token = Craft::$app->getCache()->get($this->cacheKey);

        if ($token === false) {
            $token  = $this->renewAccessToken();
        }
        
        return $token;
    }



    // Private Methods
    // =========================================================================

    /**
     * Takes a GuzzleHttp response and handles the http status code checks, and data formatting. (Mini-middleware)
     *
     * @param \GuzzleHttp\Psr7\Response $result
     * @return mixed 
     */
    private function handleResult(\GuzzleHttp\Psr7\Response $result): array
    {
        if ($result->getStatusCode() != 422  && $result->getStatusCode() != 200) {
            throw new Exception(
                sprintf(
                    "API returned an unexpected status (%s).\n Result:\n %s \n", 
                    $result->getStatusCode(),
                    json_encode(json_decode($result->getBody(), TRUE), JSON_PRETTY_PRINT)
                )
            );
        }

        return json_decode($result->getBody(), TRUE);
    }

}
