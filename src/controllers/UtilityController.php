<?php

/**
 * MarketoForm plugin for Craft CMS 3.x
 *
 * Retrieves Marketo form and makes it available on the front end
 *
 * @link      https://www.imarc.com
 * @copyright Copyright (c) 2022 Linnea Hartsuyker
 */

 namespace imarc\marketoform\controllers;

 use Craft;
 use craft\web\Controller;
 use craft\web\View;
 use yii\web\Response;

class UtilityController extends Controller 
{

    /**
     * Handle a request going to actionClearCache URL
     * i.e.: marketoform/utility/clear-cache
     */
    public function actionClearCache()
    {
        $request = Craft::$app->getRequest();

        $craftCache = Craft::$app->getCache();
        $marketoFormIds = $craftCache->get("allMarketoForms");
        $message = "No forms cleared";
        if ($request->getIsPost() && $request->getParam('formIds')) {
            
            $formIds = $request->getParam('formIds');
            if (is_countable($formIds)) {
                foreach ($formIds as $formId) {
                    $craftCache->delete("marketoForm" . $formId);
                }
                $marketoFormIds = array_diff($marketoFormIds, $formIds);
                $craftCache->set("allMarketoForms", $marketoFormIds, 86400);
                $message = 'Cleared forms ' . implode(", ", $formIds);
            }
        } 
        return $this->_getResponse($message);
    
    }

    /**
     * Handle a request going to actionClearAllCache URL
     * i.e.: marketoform/utility/clear-all-cache
     */

    public function actionClearAllCache() {
        $request = Craft::$app->getRequest();

        $craftCache = Craft::$app->getCache();
        $marketoFormIds = $craftCache->get("allMarketoForms");
        $message = "No forms cleared";
        if ($request->getIsPost() && $marketoFormIds && is_countable(($marketoFormIds))) {
            foreach ($marketoFormIds as $formId) {
                $craftCache->delete("marketoForm" . $formId);
            }
            $craftCache->delete("allMarketoForms");
            $message = 'Cleared all forms';
        } 

        return $this->_getResponse($message);
    }


    // Private Methods
    // =========================================================================
    /**
     * Returns a response.
     *
     *
     */
    private function _getResponse(string $message, bool $success = true): Response
    {
        $request = Craft::$app->getRequest();

        // If front-end or JSON request
        if (Craft::$app->getView()->templateMode == View::TEMPLATE_MODE_SITE || $request->getAcceptsJson()) {
            return $this->asJson([
                'success' => $success,
                'message' => Craft::t('marketoform', $message),
            ]);
        }

        if ($success) {
            Craft::$app->getSession()->setNotice(Craft::t('marketoform', $message));
        }
        else {
            Craft::$app->getSession()->setError(Craft::t('marketoform', $message));
        }

        return $this->redirectToPostedUrl();
    }

    
}
