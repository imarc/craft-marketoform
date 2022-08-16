<?php
/**
 * MarketoForm plugin for Craft CMS 3.x
 *
 * Retrieves Marketo form and makes it available on the front end
 *
 * @link      https://www.imarc.com
 * @copyright Copyright (c) 2022 Linnea Hartsuyker
 */

namespace imarc\marketoform\variables;

use imarc\marketoform\services\MarketoFormService;

/**
 * MarketoForm Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.marketoForm }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Linnea Hartsuyker
 * @package   MarketoForm
 * @since     1.0.0
 */
class MarketoFormVariable
{
    // Public Methods
    // =========================================================================

    // returns form as an array of form fields
    public function marketoFormJson($id) {
        $marketoSvc = new MarketoFormService();

        $result = $marketoSvc->getFormFieldsById($id);

        return $result;
    }

    // returns form as an object of form fields with arrays of hidden and htmlText fields
    public function marketoForm($id) {
        $marketoSvc = new MarketoFormService();

        $result = $marketoSvc->getFormObjectById($id);

        return $result;
    }

    public function munchkinId() {

        $marketoSvc = new MarketoFormService();

        return $marketoSvc->munchkinId;
    }

    public function baseUrl() {

        $marketoSvc = new MarketoFormService();

        return $marketoSvc->baseUrl;
    }

    //public function marketo
}
