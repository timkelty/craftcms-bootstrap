<?php
namespace fusionary\craftcms\bootstrap;

use Cekurte\Environment\Environment;
use Stringy\Stringy;
use Illuminate\Support\Collection;

class ConfigHelper
{
    const ENV_PREFIX = 'CRAFT_';

    public static function getSetting($name, $default = null, $envPrefix = self::ENV_PREFIX)
    {
        $envVar = Stringy::create($name)->underscored()->toUpperCase()->prepend($envPrefix);

        return Environment::get($envVar, $default);
    }

    public static function mapSettings($config, $envPrefix = self::ENV_PREFIX)
    {
        return Collection::make($config)->map(function ($value, $name) use ($envPrefix) {
            return static::getSetting($name, $value, $envPrefix);
        })->all();
    }
}
