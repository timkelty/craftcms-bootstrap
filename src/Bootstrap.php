<?php
namespace fusionary\craftcms\bootstrap;

use Cekurte\Environment\Environment;
use Dotenv\Dotenv;
use yii\base\BaseObject;

class Bootstrap extends BaseObject
{
    protected $instance;
    const TYPES = ['web', 'console'];

    public function __construct()
    {
        $this->instance = $this;

        return $this;
    }

    public static function getInstance()
    {
        return static::$instance ?? new static;
    }

    public function getApp($type = 'web')
    {
        if (!in_array($type, static::TYPES)) {
            throw new \Exception($type . ' is not a valid app type.');
        }

        $this
          ->defineConstant('CRAFT_VENDOR_PATH', dirname(__DIR__, 3))
          ->defineConstant('CRAFT_BASE_PATH', dirname(realpath($_SERVER['SCRIPT_FILENAME']), 2))
          ->defineConstant('CRAFT_TEMPLATES_PATH', CRAFT_BASE_PATH . '/src/templates')
          ->dotEnv();

        return require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/' . $type . '.php';
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
            error_log($e->getMessage());
        }

        return $this->defineConstant('CRAFT_ENVIRONMENT', Environment::get('CRAFT_ENVIRONMENT', 'production'));
    }
}
