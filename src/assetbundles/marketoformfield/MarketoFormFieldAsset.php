<?php
/**
 * MarketoForm plugin for Craft CMS 3.x
 *
 * Retrieves Marketo form and makes it available on the front end
 *
 * @link      https://www.imarc.com
 * @copyright Copyright (c) 2022 Linnea Hartsuyker
 */

namespace imarc\marketoform\assetbundles\marketoformfield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * MarketoFormFieldAsset AssetBundle
 * 
 * Defines the asset bundle for the screen where a CMS admin sets up a new Marketoform field 
 * templates/components/fields/MarketoFormField_input.twig. Currently only CSS is used.
 * 
 * @author    Linnea Hartsuyker
 * @package   MarketoForm
 * @since     1.0.0
 */
class MarketoFormFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@imarc/marketoform/assetbundles/marketoformfield/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/MarketoFormField.js',
        ];

        $this->css = [
            'css/MarketoFormField.css',
        ];

        parent::init();
    }
}
