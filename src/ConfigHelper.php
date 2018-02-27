<?php
namespace fusionary\craftcms\bootstrap;

use Cekurte\Environment\Environment;
use Stringy\Stringy;
use Illuminate\Support\Collection;
use yii\web\Request;

class ConfigHelper
{
    const ENV_PREFIX = 'CRAFT_';
    private static $request;

    public static function getEnv($name, $default = null, $envPrefix = self::ENV_PREFIX)
    {
        $envVar = Stringy::create($name)->underscored()->toUpperCase()->prepend($envPrefix);

        return Environment::get($envVar, $default);
    }

    public static function mapConfig($config, $envPrefix = self::ENV_PREFIX)
    {
        return Collection::make($config)->map(function ($value, $name) use ($envPrefix) {
            return static::getEnv($name, $value, $envPrefix);
        })->all();
    }

    public static function mapMultiEnvConfig($config, $envPrefix = self::ENV_PREFIX)
    {
        return Collection::make($config)->map(function ($config, $envName) use ($envPrefix) {
            return static::mapConfig($config, $envPrefix);
        })->all();
    }

    public static function getHeader($name)
    {
        return static::getRequest()->getHeaders()->get($name);
    }

    private function getRequest()
    {
        return static::$request = static::$request ?? new Request;
    }
}
