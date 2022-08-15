<?php

/**
 * MarketoForm plugin for Craft CMS 3.x
 *
 * Retrieves Marketo form and makes it available on the front end
 *
 * @link      https://www.imarc.com
 * @copyright Copyright (c) 2022 Linnea Hartsuyker
 */

 namespace imarc\marketoform\utilities;

 use imarc\marketoform\MarketoForm;
 use imarc\marketoform\assetbundles\marketoformutility\MarketoFormUtilityAsset;
 use imarc\marketoform\variables\MarketoFormVariable;

 use Craft;
use craft\base\Utility;

class MarketoFormUtility extends Utility
{
    // Static
    // =========================================================================

    /**
     * Returns the display name of this utility.
     *
     * @return string The display name of this utility.
     */
    public static function displayName(): string
    {
        return Craft::t('marketoform', 'Marketo Form Cache');
    }

    /**
     * Returns the utility’s unique identifier in kebab-case
     *
     * @return string
     */
    public static function id(): string
    {
        return 'marketoformplugin-marketoform-plugin-utility';
    }

    /**
     * Returns the path to the utility's SVG icon.
     *
     * @return string|null The path to the utility SVG icon
     */
    public static function iconPath()
    {
        return Craft::getAlias("@imarc/marketoform/assetbundles/marketoformutility/dist/img/MarketoFormUtility-icon.svg");
    }

    /**
     * Returns the number that should be shown in the utility’s nav item badge.
     *
     * If `0` is returned, no badge will be shown
     *
     * @return int
     */
    public static function badgeCount(): int
    {
        return 0;
    }

    /**
     * Returns the utility's content HTML.
     *
     * @return string
     */
    public static function contentHtml(): string
    {
        Craft::$app->getView()->registerAssetBundle(MarketoFormUtilityAsset::class);

        $allMarketoForms = Craft::$app->getCache()->get("allMarketoForms");
        $marketoFormIds = [];
        if ($allMarketoForms) {
            foreach ($allMarketoForms as $formId) {
                $marketoFormIds[] = [
                    'label' => $formId,
                    'value' => $formId
                ];
            }
        }

        $marketoVars = new MarketoFormVariable();

        return Craft::$app->getView()->renderTemplate(
            'marketoform/_components/utilities/MarketoFormUtility_content',
            [
                'marketoFormIds' => $marketoFormIds,
                'munchkinId' => $marketoVars->munchkinId()
            ]
        );
    }
}
