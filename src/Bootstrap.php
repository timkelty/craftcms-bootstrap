<?php
namespace fusionary\craftcms\bootstrap;

use Cekurte\Environment\Environment;
use Dotenv\Dotenv;

class Bootstrap
{

    public $app;
    protected $appType;

    public function __construct($appType)
    {
        $this->appType = $appType;

        $this
            ->defineConstant('CRAFT_VENDOR_PATH', dirname(__DIR__, 3))
            ->defineConstant('CRAFT_BASE_PATH', dirname(CRAFT_VENDOR_PATH, 2))
            ->defineConstant('CRAFT_TEMPLATES_PATH', CRAFT_BASE_PATH . '/src/views')
            ->dotEnv();

        $this->app = require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/' . $this->appType . '.php';

        return $this;
    }

    public static function create($appType = 'web')
    {
        return new static($appType);
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

        return $this->defineConstant('CRAFT_ENVIRONMENT', Environment::get('CRAFT_ENVIRONMENT', 'production'));
    }

    public function run()
    {
        return $this->app->run();
    }

    public function runAndExit()
    {
        exit($this->run());
    }
}
