# Craft CMS Bootstrap

![Boot by Ben Davis from the Noun Project](resources/boot-logo.svg)

## What it does

Reduces boilerplate for bootstrapping and configuration by abstracting common tasks to a simple api. It consists of 2 parts:

### Bootstrap

> e.g `@webroot/index.php`

- Reduces your app bootstrap boilerplate code to a single chainable statement.
  - This is especially helpful for achieving consistency when dealing with multiple access points (e.g. [multi-site](https://craftcms.com/news/craft-3-multi-site), [console app](https://craftcms.com/classreference/etc/console/ConsoleApp))
- Sets [PHP constants](https://github.com/craftcms/docs/blob/v3/en/configuration.md#php-constants), with sensible fallbacks.
- Gracefully loads .env file environment variables.

### Configuration files

> e.g. `config/general.php` or any [configuration files](https://docs.craftcms.com/api/v3/craft-config-generalconfig.html#properties)

- Retrieves environment variables with fallbacks and [content-aware type conversion](https://github.com/jpcercal/environment#examples). For example:
  - `export MY_BOOL=true` → `bool`
  - `export MY_INT=3` → `int`
- Provides access to HTTP request headers (via `yii\web\Request`), should your configuration rely on it.
- Provides method to map your entire config to any matching/prefixed environment variables.
  - For example, `$config['allowAutoUpdates']` will match `CRAFT_ALLOW_AUTO_UPDATES` from environment

## Prerequisites

```
"php": ">=7.1.0",
"craftcms/cms": "^3.0.0-RC1",
```

## Installation

```
composer require fusionary/craftcms-bootstrap
```

## API Documentation
[Class Reference / API Documentation](http://htmlpreview.github.io/?https://github.com/timkelty/craftcms-bootstrap/blob/master/docs/api/fusionary-craftcms-bootstrap-bootstrap.html)

## Examples

### Web app

> e.g. `@root/public/index.php`

```php
<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
fusionary\craftcms\bootstrap\Bootstrap::run();
```

### Multi-site web app

> e.g. `@root/public/site-handle/index.php`

```php
<?php
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
fusionary\craftcms\bootstrap\Bootstrap
    ->setDepth(2) // Set the depth of this script from your project root (`CRAFT_BASE_PATH`) to determine paths
    ->setSite('site-handle') // If the containing folder matches the site handle, you could dynamically set this with `basename(__DIR__)`
    ->run();
```

### Console app

> e.g. `@root/craft`

```php
<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
exit(Bootstrap::run('console')->setDepth(0)->run()); // Override the default depth of 1, since this script is in `@root`.
```

### Environment variable mapping

Passing your config through `Config::mapMultiEnvConfig` or `Config::mapConfig`
will map all settings to corresponding environment variables (if they exist).

Settings are converted from their Craft/PHP versions (camel-case) to their environment variable versions (all-caps, snake-case, prefixed — e.g. **CRAFT_**, **DB_**).

#### General config

> e.g. @root/config/general.php

```php
<?php
// Example environment:
// export CRAFT_ALLOW_AUTO_UPDATES=true;

use fusionary\craftcms\bootstrap\helpers\Config;

return Config::mapMultiEnvConfig([
    '*' => [
        'allowAutoUpdates' => true,
        'someOtherSetting' => 'foo',

        // Example: get HTTP header from request
        'devServerProxy' => Config::getHeader('x-dev-server-proxy') ?? false,
    ],
    'production' => [
        'allowAutoUpdates' => false,
    ]
]);

// Result:
// return [
//  '*' => [
//    'allowAutoUpdates' => true,
//    'someOtherSetting' => 'foo'
//  ],
//  'production' => [
//    'allowAutoUpdates' => true
//  ]
// ];
```

#### Database config

> e.g. @root/config/db.php

```php
<?php
// Example environment:
// export DB_DRIVER=mysql
// export DB_SERVER=mysql
// export DB_USER=my_app_user
// export DB_PASSWORD=secret
// export DB_DATABASE=my_app_production
// export DB_SCHEMA=public

use fusionary\craftcms\bootstrap\helpers\Config;

// Pass prefix as 2nd argument, defaults to 'CRAFT_'
return Config::mapConfig([
  'driver' => null,
  'server' => null,
  'user' => null,
  'password' => null,
  'database' => null,
  'schema' => null,
], 'DB_');

// Result:
// return [
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

## Acknowledgements

"[Boot](https://thenounproject.com/term/boot/1466612/)" icon by Ben Davis from [The Noun Project](https://thenounproject.com/)
