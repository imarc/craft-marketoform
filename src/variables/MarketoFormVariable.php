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

//use imarc\marketoform\MarketoForm;

use imarc\marketoform\services\MarketoFormService;

use Craft;

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

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.marketoForm.exampleVariable }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.marketoForm.exampleVariable(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function exampleVariable($optional = null)
    {
        $result = "And away we go to the Twig template...";
        if ($optional) {
            $result = "I'm feeling optional today...";
        }
        return $result;
    }

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
