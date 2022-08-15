<?php
/**
 * MarketoForm plugin for Craft CMS 3.x
 *
 * Retrieves Marketo form and makes it available on the front end
 *
 * @link      https://www.imarc.com
 * @copyright Copyright (c) 2022 Linnea Hartsuyker
 */

namespace imarc\marketoform\assetbundles\marketoform;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * MarketoFormAsset AssetBundle
 * 
 * Currently all assets are empty. Defines the asset bundle for the screen where a CMS admin sets up a new Marketoform field 
 * templates/components/fields/MarketoFormField_settings.twig. Consider deleting if it won't be used.
 * 
 * @author    Linnea Hartsuyker
 * @package   MarketoForm
 * @since     1.0.0
 */
class MarketoFormAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        $this->sourcePath = "@imarc/marketoform/assetbundles/marketoform/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/MarketoForm.js',
        ];

        $this->css = [
            'css/MarketoForm.css',
        ];

        parent::init();
    }
}
