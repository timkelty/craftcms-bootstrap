<?php
namespace fusionary\craftcms\bootstrap;

use Cekurte\Environment\Environment;
use Dotenv\Dotenv;

class Bootstrap
{
    protected $depth = 1;
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
            throw new \Exception(sprintf('"%s" is not a valid type.', $type));
        }

        $this
          ->define('CRAFT_VENDOR_PATH', dirname(__DIR__, 3))
          ->define('CRAFT_BASE_PATH', dirname(realpath($_SERVER['SCRIPT_FILENAME']), $this->depth + 1))
          ->define('CRAFT_TEMPLATES_PATH', CRAFT_BASE_PATH . '/src/templates')
          ->dotEnv();

        return require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/' . $type . '.php';
    }

    public function define($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }

        return $this;
    }

    public function setDepth($depth)
    {
        $this->depth = $depth;

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

        return $this->define('CRAFT_ENVIRONMENT', Environment::get('CRAFT_ENVIRONMENT', 'production'));
    }
}
