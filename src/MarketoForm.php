<?php
/**
 * MarketoForm plugin for Craft CMS 3.x
 *
 * Retrieves Marketo form and makes it available on the front end
 *
 * @link      https://www.imarc.com
 * @copyright Copyright (c) 2022 Linnea Hartsuyker
 */

namespace imarc\marketoform;

use imarc\marketoform\services\MarketoFormService;
use imarc\marketoform\variables\MarketoFormVariable;
/*use imarc\baseplugin\twigextensions\BasePluginTwigExtension;*/
use imarc\marketoform\models\Settings;
use imarc\marketoform\fields\MarketoFormField;
use imarc\marketoform\utilities\MarketoFormUtility;
/*use imarc\baseplugin\widgets\BasePluginWidget as BasePluginWidgetWidget;*/

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\services\Elements;
use craft\services\Fields;
use craft\services\Utilities;
use craft\web\twig\variables\CraftVariable;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 *
 * @author    Linnea Hartsuyker
 * @package   MarketoForm
 * @since     1.0.0
 *
 * @property  MarketoFormService $marketoFormService
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class MarketoForm extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * MarketoForm::$plugin
     *
     * @var MarketoForm
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    //public $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    //public $hasCpSection = true;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'imarc\marketoform\console\controllers';
        }

        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'marketo-form/marketo-form';
            }
        );

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'marketo-form/marketo-form/do-something';
            }
        );

        // Register our fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = MarketoFormField::class;
            }
        );

        // Register our utilities
        Event::on(
            Utilities::class,
            Utilities::EVENT_REGISTER_UTILITY_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = MarketoFormUtility::class;
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('marketoForm', MarketoFormVariable::class);
            }
        );

        Craft::info(
            Craft::t(
                'marketoform',
                'MarketoForm plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

}
