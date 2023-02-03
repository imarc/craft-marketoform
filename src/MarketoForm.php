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

use imarc\marketoform\variables\MarketoFormVariable;
use imarc\marketoform\models\Settings;
use imarc\marketoform\fields\MarketoFormField;
use imarc\marketoform\utilities\MarketoFormUtility;

use Craft;
use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use craft\services\Fields;
use craft\services\Utilities;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterComponentTypesEvent;

use yii\base\Event;

/**
 *
 * @author    Linnea Hartsuyker
 * @package   MarketoForm
 * @since     1.0.0
 */
class MarketoForm extends Plugin
{
    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'imarc\marketoform\console\controllers';
        }

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
