<?php
namespace fusionary\craftcms\bootstrap;

use Cekurte\Environment\Environment;
use Stringy\Stringy;
use Illuminate\Support\Collection;
use yii\web\Request;

class ConfigHelper
{
    const ENV_PREFIX = 'CRAFT_';

    /** @var Request */
    private static $request;

    public static function getEnv(string $name, string $default = null, string $envPrefix = self::ENV_PREFIX): string
    {
        $envVar = Stringy::create($name)->underscored()->toUpperCase()->prepend($envPrefix);

        return Environment::get($envVar, $default);
    }

    public static function mapConfig(array $config, string $envPrefix = self::ENV_PREFIX): array
    {
        return Collection::make($config)->map(function ($value, $name) use ($envPrefix) {
            return static::getEnv($name, $value, $envPrefix);
        })->all();
    }

    public static function mapMultiEnvConfig(array $config, string $envPrefix = self::ENV_PREFIX): array
    {
        return Collection::make($config)->map(function ($config, $envName) use ($envPrefix) {
            return static::mapConfig($config, $envPrefix);
        })->all();
    }

    public static function getHeader(string $name): string
    {
        return static::getRequest()->getHeaders()->get($name);
    }

    private function getRequest(): Request
    {
        return static::$request = static::$request ?? new Request;
    }
}
