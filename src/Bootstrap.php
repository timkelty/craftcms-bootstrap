<?php
namespace fusionary\craftcms\bootstrap;

use Cekurte\Environment\Environment;
use Dotenv\Dotenv;
use yii\base\BaseObject;

class Bootstrap extends BaseObject
{
    protected static $instance;

    public static function create()
    {
        return static::$instance ?? new static;
    }

    public function defineConstant($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }

        return $this;
    }

    public function dotEnv($path = CRAFT_BASE_PATH, $file = '.env')
    {
        $dotenv = new Dotenv($path, $file);

        try {
            $dotenv->load();
        } catch (\Dotenv\Exception\InvalidPathException $e) {
            error_log($e->getMessage);
        }

        return $this;
    }

    public function getApp($type = 'web')
    {
        $this
          ->defineConstant('CRAFT_TEMPLATES_PATH', CRAFT_BASE_PATH . '/src/views')
          ->defineConstant('CRAFT_ENVIRONMENT', Environment::get('CRAFT_ENVIRONMENT', 'production'));

        return require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/' . $type . '.php';
    }
}
