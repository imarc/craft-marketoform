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

```<?php

return [

    "clientId" => MARKETO CLIENT ID,
    "clientSecret" => MARKETO_CLIENT_SECRET,
    "marketoUrl" => MARKETO_URL,
    "munchkinId" => MUNCHKIN_ID,
    "baseUrl" => BLOG_BASE_URL,

    "cacheTimeout" => 86400

];```