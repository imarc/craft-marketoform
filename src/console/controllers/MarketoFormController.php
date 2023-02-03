<?php
/**
 * MarketoForm plugin for Craft CMS 3.x
 *
 * Retrieves Marketo form and makes it available on the front end
 *
 * @link      https://www.imarc.com
 * @copyright Copyright (c) 2022 Linnea Hartsuyker
 */

namespace imarc\marketoform\console\controllers;

use imarc\marketoform\MarketoForm;

use imarc\marketoform\services\MarketoFormService;

use Craft;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * MarketoFormController
 * 
 * Console controller for MarketoForm. Currently used for ad-hoc testing. Consider deleting for release
 *
 * @author    Linnea Hartsuyker
 * @package   MarketoForm
 * @since     1.0.0
 */
class MarketoFormController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Handle marketoform/marketo-form console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'something';

        echo "Welcome to the console DefaultController actionIndex() method\n";

        echo MarketoForm::getInstance()->settings->clientId;
        echo "\n";
        echo MarketoForm::getInstance()->settings->clientSecret;
        echo "\n";
        echo MarketoForm::getInstance()->settings->marketoUrl;
        echo "\n";

        return $result;
    }

    /**
     * Handle marketoform/marketo-form/get-form-fields console commands
     *
     * @return mixed
     */
    public function actionGetFormFields() {
        $marketoSvc = new MarketoFormService();

        $result = $marketoSvc->getFormFieldsById(2585);

        echo json_encode($result, JSON_THROW_ON_ERROR);
    }

    /**
     * Handle marketoform/marketo-form/clear-cache console commands
     *
     * @return mixed
     */
    public function actionClearCache($id = null) {
        $craftCache = Craft::$app->getCache();
        $marketoFormIds = $craftCache->get("allMarketoForms");
        if (!$id) {
            foreach ($marketoFormIds as $formId) {
                $craftCache->delete("marketoForm" . $formId);
            }
            $craftCache->delete("allMarketoForms");
        } else {
            $craftCache->delete("marketoForm" . $id);
            $marketoFormIds = array_diff($marketoFormIds, [$id]);
            $craftCache->set("allMarketoForms", $marketoFormIds, 86400);    
        }
    }
}
