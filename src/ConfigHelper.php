<?php
namespace fusionary\craftcms\bootstrap;

use Cekurte\Environment\Environment;
use Stringy\Stringy;
use Illuminate\Support\Collection;

class ConfigHelper
{
    const ENV_PREFIX = 'CRAFT_';

    public static function getEnvSetting($name, $default = null, $envPrefix = self::ENV_PREFIX)
    {
        $envVar = Stringy::create($name)->underscored()->toUpperCase()->prepend($envPrefix);

        return Environment::get($envVar, $default);
    }

    public static function mapEnvSettings($config, $envPrefix = self::ENV_PREFIX)
    {
        return Collection::make($config)->map(function ($value, $name) use ($envPrefix) {
            return static::getEnvSetting($name, $value, $envPrefix);
        })->all();
    }
}
