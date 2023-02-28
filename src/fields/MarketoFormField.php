<?php
/**
 * MarketoForm plugin for Craft CMS 3.x
 *
 * Retrieves Marketo form and makes it available on the front end
 *
 * @link      https://www.imarc.com
 * @copyright Copyright (c) 2022 Linnea Hartsuyker
 */

namespace imarc\marketoform\fields;

use Exception;

use imarc\marketoform\models\MarketoForm as MarketoFormModel;
use imarc\marketoform\assetbundles\marketoformfield\MarketoFormFieldAsset;

use craft\redactor\Field as RedactorField;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use yii\db\Schema;
use craft\helpers\Json;

/**
 * MarketoFormField Field
 *
 * @author    Linnea Hartsuyker
 * @package   MarketoForm
 * @since     1.0.0
 */
class MarketoFormField extends Field
{
    // Public Properties
    // =========================================================================

    public $marketoFormId = '';
    public $placeholderText = '';
    public $redirectUrl = '';
    public $onSubmit = '';
    public $onSuccess = '';
    public $thankyouMessage = '';

    public $allowRedirectUrl = true;
    public $allowOnSubmit = false;
    public $allowOnSuccess = false;
    public $allowThankyouMessage = true;

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('marketoform', 'Marketo Form');
    }

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     **
     * @return array
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            [['placeholderText', 'redirectUrl', 'onSubmit', 'onSuccess', 'thankyouMessage', 'marketoFormId'], 'string'],
            [['allowRedirectUrl', 'allowOnSubmit', 'allowOnSuccess', 'allowThankyouMessage'], 'boolean'],
        ]);
        return $rules;
    }

    /**
     * Returns the column type that this field should get within the content table.
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * Normalizes the field’s value for use.
     *
     * @param mixed                 $value   The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return mixed The prepared field value
     */
    public function normalizeValue($value, ElementInterface $element = null): mixed
    {
        if ($value instanceof MarketoFormModel) {
            return $value;
        }

        $attr = [];
        if (is_string($value)) {
            // If value is a string we are loading the data from the database
            try {
                $decodedValue = Json::decode($value, true);
                if (is_array($decodedValue)) {
                  $attr += $decodedValue;
                }
              } catch (Exception) {}
        } else if (is_array($value) && isset($value['isCpFormData'])) {
            // If it is an array and the field `isCpFormData` is set, we are saving a cp form
            $attr += [
                'marketoFormId' => $this->marketoFormId && isset($value['marketoFormId']) ? $value['marketoFormId'] : null,
                'thankyouMessage' => $this->thankyouMessage && isset($value['thankyouMessage']) ? $value['thankyouMessage'] : null,
                'redirectUrl' => $this->redirectUrl && isset($value['redirectUrl']) ? $value['redirectUrl'] : null,
                'onSubmit' => $this->onSubmit && isset($value['onSubmit']) ? $value['onSubmit'] : null,
                'onSuccess' => $this->onSuccess & isset($value['onSuccess']) ? $value['onSuccess'] : null,
            ];
        } else if (is_array($value)) {
            // Finally, if it is an array it is a serialized value
            $attr += $value;
        }

        return new MarketoFormModel($attr);
    }

    /**
     * Prepares the field’s value to be stored somewhere, like the content table or JSON-encoded in an entry revision table.
     *
     * Data types that are JSON-encodable are safe (arrays, integers, strings, booleans, etc).
     * Whatever this returns should be something [[normalizeValue()]] can handle.
     *
     * @param mixed $value The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     * @return mixed The serialized field value
     */
    public function serializeValue($value, ElementInterface $element = null): mixed
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * Returns the field's settings HTML
     * 
     * @return string|null
     */
    public function getSettingsHtml(): ?string
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'marketoform/_components/fields/MarketoFormField_settings',
            [
                'field' => $this,
                'settings' => $this->getSettings()
            ]
        );
    }

    /**
     * Returns the field’s input HTML.
     *
     * @param mixed                 $value           The field’s value. This will either be the [[normalizeValue() normalized value]],
     *                                               raw POST data (i.e. if there was a validation error), or null
     * @param ElementInterface|null $element         The element the field is associated with, if there is one
     *
     * @return string The input HTML.
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(MarketoFormFieldAsset::class);

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
            ];
        $jsonVars = Json::encode($jsonVars);
        Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').MarketoFormMarketoFormField(" . $jsonVars . ");");

        // Add redactor field
        $config = ['handle' => $this->handle . '[thankyouMessage]'];
        $redactorField = new RedactorField($config);
        if (is_array($value) && array_key_exists('thankyouMessage', $value)) {
            $redactorFieldHtml = $redactorField->inputHtml($value['thankyouMessage']);
        } else {
            $redactorFieldHtml = '';
        }
        
        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'marketoform/_components/fields/MarketoFormField_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'redactorFieldHtml' => $redactorFieldHtml,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
                'settings' => $this->getSettings()
            ]
        );
    }
}
