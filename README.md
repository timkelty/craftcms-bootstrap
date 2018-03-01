# Craft CMS Bootstrap

[Class Reference / API Documentation](http://htmlpreview.github.io/?https://github.com/timkelty/craftcms-bootstrap/blob/master/docs/api/fusionary-craftcms-bootstrap-bootstrap.html)

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
require_once dirname(__DIR__) . '/vendor/autoload.php';
Bootstrap::getInstance()->getApp()->run();
```

### Multi-site web app

`public/site-handle/index.php`
```php
<?php
 use fusionary\craftcms\bootstrap\Bootstrap;
 require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
 Bootstrap::getInstance()
     ->setDepth(2)
     ->setSite('site-handle') // or use basename(__DIR__) if the containing folder matches the site handle
     ->getApp()
     ->run();
```

### Console app

`bin/craft`
```php
<?php
use fusionary\craftcms\bootstrap\Bootstrap;
require_once dirname(__DIR__) . '/vendor/autoload.php';
exit(Bootstrap::getInstance()->getApp('console')->run());
```

### Dynamically loading config from environment variables

Passing your config through `Config::mapMultiEnvConfig` or `Config::mapConfig`
will map all settings to corresponding environment variables (if they exist).

Settings assumed to be camel-case, while environment variables are snake-cake
and all-caps, with prefix (e.g. **CRAFT_**, **DB_**).

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
]);

// [
//  '*' => [
//    'allowAutoUpdates' => true,
//    'someOtherSetting' => 'foo'
//  ],
//  'production' => [
//    'allowAutoUpdates' => true
//  ]
// ]
```

`config/db.php`
```php
<?php
// export DB_DRIVER=mysql
// export DB_SERVER=mysql
// export DB_USER=my_app_user
// export DB_PASSWORD=secret
// export DB_DATABASE=my_app_production
// export DB_SCHEMA=public

use fusionary\craftcms\bootstrap\helpers\Config;

return Config::mapConfig([
  'driver' => null,
  'server' => null,
  'user' => null,
  'password' => null,
  'database' => null,
  'schema' => null,
], 'DB_');

// [
//   'driver' => 'mysql',
//   'server' => 'mysql',
//   'user' => 'my_app_user',
//   'password' => 'secret',
//   'database' => 'my_app_production',
//   'schema' => 'public',
// ]
```


## Generate documentation

```
composer run-script build-docs
```
