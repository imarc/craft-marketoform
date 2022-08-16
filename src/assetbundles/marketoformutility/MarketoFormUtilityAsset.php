<?php
/**
 * MarketoForm plugin for Craft CMS 3.x
 *
 * Retrieves Marketo form and makes it available on the front end
 *
 * @link      https://www.imarc.com
 * @copyright Copyright (c) 2022 Linnea Hartsuyker
 */

 namespace imarc\marketoform\assetbundles\marketoformutility;

 use craft\web\AssetBundle;
 use craft\web\assets\cp\CpAsset;

/**
 * MarketoFormUtilityAsset AssetBundle
 * 
 * Defines the asset bundle for the screen where a CMS admin sets up a new Marketoform field 
 * templates/components/utilities/MarketoFormUtility_content.twig. Currently only the image is used
 * 
 * @author    Linnea Hartsuyker
 * @package   MarketoForm
 * @since     1.0.0
 */
class MarketoFormUtilityAsset extends AssetBundle 
 {

    // Public Methods

    /**
     * Initializes the bundle
     */

     public function init() {
        $this->sourcePath = "@imarc/marketoform/assetbundles/marketoformutility/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/MarketoFormUtility.js',
        ];

        $this->css = [
            'css/MarketoFormUtility.css',
        ];

        parent::init();

     }

 }