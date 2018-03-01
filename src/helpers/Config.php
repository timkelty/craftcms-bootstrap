<?php
namespace fusionary\craftcms\bootstrap\helpers;

use Cekurte\Environment\Environment;
use Stringy\Stringy;
use Illuminate\Support\Collection;
use yii\web\Request;

/**
 * Bootstrap Config Helper
 *
 * Use this class in your config files to dynamically
 * map environment variables to Craft settings.
 */
class Config
{
    /**
     * @var string The default prefix for environment variables.
     */
    const ENV_PREFIX = 'CRAFT_';

    /**
     * @var Request Instance of yii\web\Request
     */
    protected static $request;

    /**
     * Get the value of an environment variable based on a config setting.
     * Values are converted to converted to their respective data types
     * using Cekurte\Environment.
     *
     * ```php
     * use fusionary\craftcms\bootstrap\helpers\Config;
     *
     * // export CRAFT_DEV_MODE=true
     * Config::getEnv('devMode', false); // → (bool) true
     *
     * // export CRAFT_MY_SETTING=someSetting
     * Config::getEnv('mySetting'); // → (string) 'someSetting'
     *
     * // export PLUGIN_MY_SETTING=100
     * Config::getEnv('mySetting', null, 'PLUGIN_'); // → (int) 100
     * ```
     *
     * @see https://github.com/jpcercal/environment#examples
     * @param  string      $name      Config setting name (camel-cased)
     * @param  string|null $default   Default value
     * @param  string      $envPrefix Environment variable prefix (e.g. CRAFT_)
     * @return mixed                  Converted value, or the $default if not found
     */
    public static function getEnv(string $name, string $default = null, string $envPrefix = self::ENV_PREFIX)
    {
        $envVar = Stringy::create($name)->underscored()->toUpperCase()->prepend($envPrefix);

        return Environment::get($envVar, $default);
    }

    /**
     * Map config array to matching environment variables
     *
     * ```php
     * // export CRAFT_TEST_TO_EMAIL_ADDRESS=user@domain.com
     * use fusionary\craftcms\bootstrap\helpers\Config;
     *
     * $config = Config::mapConfig([
     *     'testToEmailAddress' => null,
     *     'securityKey' => 'dev-key',
     * ]); // → ['testToEmailAddress' => 'user@domain.com', 'securityKey' => 'dev-key']
     * ```
     *
     * @param  array  $config
     * @param  string $envPrefix Environment variable prefix (e.g. CRAFT_)
     * @return array             $config, with existing env vars mapped.
     */
    public static function mapConfig(array $config, string $envPrefix = self::ENV_PREFIX): array
    {
        return Collection::make($config)->map(function ($value, $name) use ($envPrefix) {
            return static::getEnv($name, $value, $envPrefix);
        })->all();
    }

    /**
     * Map a multi-environment config to matching environment variables
     *
     * ```php
     * // export CRAFT_ALLOW_AUTO_UPDATES=true
     * ConfigHelper::mapMultiEnvConfig([
     *     '*' => [
     *         'allowAutoUpdates' => true,
     *     ],
     *     'production' => [
     *         'allowAutoUpdates' => false,
     *     ]
     * ]); // → ['*' => ['allowAutoUpdates' => true], 'production' => ['allowAutoUpdates' => true]]
     * ```
     *
     * @see https://craftcms.com/docs/multi-environment-configs
     * @param  array  $config    Multi-environment config
     * @param  string $envPrefix Environment variable prefix (e.g. 'CRAFT_')
     * @return array             $config, with existing env vars mapped
     */
    public static function mapMultiEnvConfig(array $config, string $envPrefix = self::ENV_PREFIX): array
    {
        return Collection::make($config)->map(function ($config, $envName) use ($envPrefix) {
            return static::mapConfig($config, $envPrefix);
        })->all();
    }

    /**
     * Get request headers
     *
     * @param  string $name header name
     * @return string       header value
     */
    public static function getHeader(string $name): string
    {
        return static::getRequest()->getHeaders()->get($name);
    }

    /**
     * Get request instance
     *
     * Note: this retrieves an instance of yii\web\Request in an un-bootstraped
     * state, so be cautious with its use. In this case, we only use it to read
     * HTTP request headers whilc bootstrapping our app.
     *
     * @return Request
     */
    protected function getRequest(): Request
    {
        return static::$request = static::$request ?? new Request;
    }
}
