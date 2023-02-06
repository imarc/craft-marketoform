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
 * MarketoForm Model
 *
 * @author    Linnea Hartsuyker
 * @package   BasePlugin
 * @since     1.0.0
 */

class MarketoForm extends Model
{
    // Public Properties
    // =========================================================================
    
    public $marketoFormId = '';
    public $redirectUrl = '';
    public $thankyouMessage = '';
    public $onSubmit = '';
    public $onSuccess = '';

    

    // Public Methods
    // =========================================================================


    public function __construct($config = [])
    {
        parent::__construct($config);
    }
   
    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            [['redirectUrl', 'marketoFormId', 'thankyouMessage', 'onSubmit', 'onSuccess'], 'string'],
        ];
    }
}
