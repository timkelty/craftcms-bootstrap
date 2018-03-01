# Craft CMS Bootstrap

Provides you with methods to streamline your Craft CMS bootstrapping process.

## Installation

Within your Craft 3 project:
```
composer require fusionary/craftcms-bootstrap
```

## Usage Examples

### Console app at `bin/craft`:

```php
<?php
use fusionary\craftcms\bootstrap\Bootstrap;
require_once dirname(__DIR__) . '/vendor/composer/autoload.php';
exit(Bootstrap::getInstance()->getApp('console')->run());
```

### Web app at `public/index.php`:

```php
<?php
use fusionary\craftcms\bootstrap\Bootstrap;
require_once dirname(__DIR__) . '/vendor/composer/autoload.php';
Bootstrap::getInstance()->getApp()->run();
```

### Multi-site web app at `public/site-handle/index.php`:

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

### `config/general.php`

```php
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
