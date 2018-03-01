# Craft CMS Bootstrap

[Class Reference / API Documentation](./docs/api)

- Provides methods to streamline your Craft CMS bootstrapping process.
- Provides helpers to automatically retrieve Craft config settings from environment variables.

## Installation

Within your Craft 3 project:
```
composer require fusionary/craftcms-bootstrap
```

## Usage

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
     ->define('CRAFT_SITE', basename(__DIR__))
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
    ],
    'production' => [
        'allowAutoUpdates' => false,
    ]
]); // → ['*' => ['allowAutoUpdates' => true], 'production' => ['allowAutoUpdates' => true]]
```

## Generate documentation

```
apidoc api src docs/api
```
