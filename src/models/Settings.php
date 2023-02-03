<?php
/**
 * MarketoForm plugin for Craft CMS 3.x
 *
 * Retrieves Marketo form and makes it available on the front end
 *
 * @link      https://www.imarc.com
 * @copyright Copyright (c) 2022 Linnea Hartsuyker
 */


namespace imarc\marketoform\models;

use craft\base\Model;

/**
 * MarketoForm Settings Model
 *
 * @author    Linnea Hartsuyker
 * @package   MarketoForm
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    public $clientId = '';
    public $clientSecret = '';
    public $marketoUrl = '';
    public $munchkinId = '';
    public $baseUrl = '';
    public $cacheTimeout = null;

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            [['clientId', 'clientSecret', 'marketoUrl', 'munchkinId', 'baseUrl'], 'string'],
            ['cacheTimeout', 'integer']
        ];
    }
}
