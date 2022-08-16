# craft-marketoform

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Add the repo to the project. If using locally download the files and put them in your main web directory, then use the path repository, otherwise use the VCS repository. Add the appropriate code to your composer.json file:

        "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/imarc/craft-marketoform.git"
            },
            {
                "type": "path",
                "url": "../craft-marketoform"
            },
        ],


3. Then tell Composer to load the plugin:

        composer require imarc/craft-marketoform

4. In the Control Panel, go to Settings → Plugins and click the “Install” button for AdminCss.

5. In your config folder create a file called `marketoform.php` and put the following information in it, filling in your own values

```
<?php

return [

    "clientId" => MARKETO CLIENT ID,
    "clientSecret" => MARKETO_CLIENT_SECRET,
    "marketoUrl" => MARKETO_URL,
    "munchkinId" => MUNCHKIN_ID,
    "baseUrl" => BLOG_BASE_URL,

    "cacheTimeout" => 86400

];
```

## Usage

After you install the plugin, you can create Marketo Form fields using field settings in the control panel. All field settings can be found in the field manager. 

The plugin includes methods for retrieving Marketo Form JSON from Marketo with or without using the the Marketo Form field type. 
### Options

**Placeholder Text:** Text that appears to the CMS editor for the Marketo ID

**Allow thank you message:** Allows the CMS editor to enter a thank you message

**Allow redirect URL:** Allows the CMS editor to enter a redirect URL for where the user goes after a successful form submission

**Allow onSubmit code:** Allows the CMS editor to enter code that can be used in the onSubmit method of the Marketo javascript API

**Allow onSuccess code:** Allows the CMS editor to enter code that can be used in the onSuccess method of the Marketo javascript API

### Templating

#### Displaying the Marketo Form

The Marketo plugin provides two methods for retrieving Marketo Form JSON from Marketo.

    {% set marketoForm = craft.marketoForm.marketoFormJson(marketoId) %}

retrieves the raw JSON result from Marketo. This is in the form of an array of form fields. The simplest way to display this form will look something like:

    {% for field in marketoForm %}
        {% if field.dataType == 'checkboxes' %}
            {% for checkbox in field.fieldMetaData.values %}
                <label class="blogSubscription__checkbox" for="mktoCheckbox_{{ loop.index }}" id="Lbl{{ field.id }}">
                    <input name="{{ field.id }}" id="mktoCheckbox_{{ loop.index }}" type="checkbox" value="{{ checkbox.value }}" aria-required="true" aria-labelledby="Lbl{{ field.id }} LblmktoCheckbox_{{ loop.index }} Instruct{{ field.id }}">
                    {{ checkbox.label }}
                </label>
            {% endfor %}
        {% elseif field.dataType != 'checkboxes' and field.dataType != 'htmltext' and field.dataType != 'hidden' %}
            <input type="{{ field.dataType }}" class="email__input" placeholder="{{ field.hintText is defined ? field.hintText }}" id="{{ field.id }}" name="{{ field.id }}" aria-labelledby="Lbl{{ field.id }} Instruct{{ field.id }}" aria-required="{{ field.required }}" aria-invalid="true" aria-describedby="ValidMsg{{ field.id }}">
        {% elseif field.dataType == 'hidden' %}
            <input type="{{ field.dataType }}" name="{{ field.id }}">
        {% endif %}
    {% endfor %}

*Bear in mind that Marketo supports conditional form fields like state fields that appear when a specific country is selected. You may need to write javascript to accomodate this.*

This method retrieves a JSON object rather than an array:

    {% set marketoForm = craft.marketoForm.marketoForm(marketoId) %}

The field ids will be the keys, except for htmlText and hidden fields which are put into arrays. This is helpful when you know exactly what fields the form will have and you want to alter the order in which they show up. A field could be displayed like this:

    <label for="firstName" id="LblfirstName">{{ marketoForm.firstName.label }}</label>
    <input type="{{ marketoForm.firstName.dataType }}" class="email__input" placeholder="{{ marketoForm.firstName.hintText is defined ? marketoForm.firstName.hintText }}" id="firstName" name="firstName" aria-labelledby="LblfirstName InstructfirstName" aria-required="{{ marketoForm.firstName.required }}" aria-invalid="true" aria-describedby="ValidMsg{{ field.id }}">


#### Accessing Field Attributes

If you create a field called `blogSignupForm` you can access its attributes like so:

    {% set marketoFormId = entry.blogSignupForm.marketoFormId %}
    {% set thankyouMessage = entry.blogSignupForm.thankyouMessage %}
    {% set redirectUrl = entry.blogSignupForm.redirectUrl %}
    {% set onSubmit = entry.blogSignupForm.onSubmit %}
    {% set onSuccess = entry.blogSignupForm.onSuccess %}

### Submitting the Form

Marketo recommends submitting forms via its Javascript API. Here is some example code for gathering the field inputs and submitting: https://codepen.io/figureone/pen/QMBZqB/b8bc4bc6b321858c8a036dd60d36b89c

Imarc has created a companion vue library for validation and submitting to Marketo that can be found LINK TBD