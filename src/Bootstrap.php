<?php
namespace fusionary\craftcms\bootstrap;

use Cekurte\Environment\Environment;
use Dotenv\Dotenv;
use yii\base\Application;

/**
 * Craft CMS bootstrap
 *
 * Use this class in your entry script (`index.php`) to bootstrap your app.
 */

class Bootstrap
{
    const APP_TYPE_WEB = 'web';
    const APP_TYPE_CONSOLE = 'console';

    /**
     * @var array Valid application type.
     * @see http://www.yiiframework.com/doc-2.0/guide-structure-applications.html#applications
     */
    protected static $appTypes = [self::APP_TYPE_WEB, self::APP_TYPE_CONSOLE];

    /**
     * @var int Depth of running script from project root.
     */
    protected static $depth = 1;

    /**
     * @var Bootstrap Singleton instance.
     */
    protected static $instance;

    /**
     * Get instance statically.
     * @return Bootstrap
     */
    protected static function getInstance(): Bootstrap
    {
        return static::$instance = static::$instance ?? new static;
    }

    /**
     * Get the bootstrapped app.
     *
     * @param  string      $type One of [[$appTypes]], deaults to 'web'.
     * @return Application
     */
    public static function getApp(string $type = self::APP_TYPE_WEB): Application
    {
        if (!in_array($type, static::$appTypes)) {
            throw new \Exception(sprintf('"%s" is not a valid type.', $type));
        }

        static::getInstance()
          ->define('CRAFT_VENDOR_PATH', dirname(__DIR__, 3))
          ->define('CRAFT_BASE_PATH', dirname(realpath($_SERVER['SCRIPT_FILENAME']), static::$depth + 1))
          ->define('CRAFT_TEMPLATES_PATH', CRAFT_BASE_PATH . '/src/templates')
          ->loadDotEnv(CRAFT_BASE_PATH);

        return require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/' . $type . '.php';
    }

    /**
     * Gracefully define a PHP constant.
     * @param  string    $name
     * @param  mixed     $value
     * @return Bootstrap
     */
    public static function define(string $name, $value): Bootstrap
    {
        if (!defined($name)) {
            define($name, $value);
        }

        return static::getInstance();
    }

    /**
     * Set depth from project root
     * @param  int       $depth
     * @return Bootstrap
     */
    public static function setDepth(int $depth): Bootstrap
    {
        static::$depth = $depth;

        return static::getInstance();
    }

    /**
     * Define the `CRAFT_SITE` constant
     * @param  string    $handle site handle
     * @return Bootstrap
     */
    public static function setSite(string $handle): Bootstrap
    {
        return static::getInstance()->define('CRAFT_SITE', $handle);
    }

    /**
     * Apply environment variables from a .env file and defines CRAFT_ENVIRONMENT constant.
     *
     * Fails silently if
     * - env file is not found (e.g. in production).
     * - `CRAFT_ENVIRONMENT` is already defined.
     *
     * @param  string    $path          Path to directory containing env file
     * @param  string    $fileName          Filename of env file
     * @param  bool      $logExceptions Log exceptions with error_log
     * @return Bootstrap
     */
    public static function loadDotEnv(string $path, string $fileName = '.env', $logExceptions = false): Bootstrap
    {
        $dotenv = new Dotenv($path, $fileName);

        try {
            $dotenv->load();
        } catch (\Dotenv\Exception\InvalidPathException $e) {
            if ($logException) {
                error_log($e->getMessage());
            }
        }

        return static::getInstance()
            ->define('CRAFT_ENVIRONMENT', Environment::get('CRAFT_ENVIRONMENT', 'production'));
    }
}
