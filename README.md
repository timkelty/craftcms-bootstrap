# Craft CMS Bootstrap

[Class Reference / API Documentation](http://htmlpreview.github.io/?https://github.com/timkelty/craftcms-bootstrap/blob/master/docs/api/index.html)

- Provides chainable methods to streamline your Craft CMS bootstrapping process.
- Provides helpers to automatically retrieve Craft config settings from environment variables.

## Prerequisites

```
"php": ">=7.1.0",
"craftcms/cms": "^3.0.0-RC1",
```

## Installation

```
composer require fusionary/craftcms-bootstrap
```

## Examples

### Web app

`public/index.php`
```php
<?php
use fusionary\craftcms\bootstrap\Bootstrap;
require_once dirname(__DIR__) . '/vendor/composer/autoload.php';
Bootstrap::getInstance()->getApp()->run();
```

### Multi-site web app

`public/site-handle/index.php`
```php
<?php
 use fusionary\craftcms\bootstrap\Bootstrap;
 require_once dirname(__DIR__, 2) . '/vendor/composer/autoload.php';
 Bootstrap::getInstance()
     ->setDepth(2)
     ->setSite('site-handle') // or use basename(__DIR__)
     ->getApp()
     ->run();
```

### Console app

`bin/craft`
```php
<?php
use fusionary\craftcms\bootstrap\Bootstrap;
require_once dirname(__DIR__) . '/vendor/composer/autoload.php';
exit(Bootstrap::getInstance()->getApp('console')->run());
```

### Dynamically loading config from environment variables

`config/general.php`
```php
<?php
// export CRAFT_ALLOW_AUTO_UPDATES=true;
use fusionary\craftcms\bootstrap\helpers\Config;

return Config::mapMultiEnvConfig([
    '*' => [
        'allowAutoUpdates' => true,
        'someOtherSetting' => 'foo',
    ],
    'production' => [
        'allowAutoUpdates' => false,
    ]
]); // → ['*' => ['allowAutoUpdates' => true, 'someOtherSetting' => 'foo'], 'production' => ['allowAutoUpdates' => true]]
```
## Generate documentation

```
composer run-script build-docs
```
