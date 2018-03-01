<?php
namespace fusionary\craftcms\bootstrap;

use Cekurte\Environment\Environment;
use Dotenv\Dotenv;
use \yii\base\Application;

/**
 * Craft CMS bootstrap
 *
 * Use this class in your entry script (index.php) to bootstrap your app.
 *
 * Example console app at `bin/craft`:
 *
 * ```php
 * <?php
 * use fusionary\craftcms\bootstrap\Bootstrap;
 * require_once dirname(__DIR__) . '/vendor/composer/autoload.php';
 * exit(Bootstrap::getInstance()->getApp('console')->run());
 * ```
 *
 * Example web app at `public/index.php`:
 *
 * ```php
 * <?php
 * use fusionary\craftcms\bootstrap\Bootstrap;
 * require_once dirname(__DIR__) . '/vendor/composer/autoload.php';
 * Bootstrap::getInstance()->getApp()->run();
 * ```
 *
 * Example multi-site web app at `public/site-handle/index.php`:
 *
 * ```php
 * <?php
 *  use fusionary\craftcms\bootstrap\Bootstrap;
 *  require_once dirname(__DIR__, 2) . '/vendor/composer/autoload.php';
 *  Bootstrap::getInstance()
 *      ->setDepth(2)
 *      ->define('CRAFT_SITE', basename(__DIR__))
 *      ->getApp()
 *      ->run();
 * ```
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
    protected $depth = 1;

    /**
     * @var Bootstrap Singleton instance of this class.
     */
    protected $instance;

    /**
     * Set instance.
     */
    public function __construct()
    {
        $this->instance = $this;
    }

    /**
     * @return Bootstrap
     */
    public static function getInstance(): Bootstrap
    {
        return static::$instance ?? new static;
    }

    /**
     * Get the bootstrapped app.
     * @param  string      $type One of [static::$appTypes], deaults to 'web'.
     * @return Application
     */
    public function getApp(string $type = TYPE_WEB): Application
    {
        if (!in_array($type, static::$appTypes)) {
            throw new \Exception(sprintf('"%s" is not a valid type.', $type));
        }

        $this
          ->define('CRAFT_VENDOR_PATH', dirname(__DIR__, 3))
          ->define('CRAFT_BASE_PATH', dirname(realpath($_SERVER['SCRIPT_FILENAME']), $this->depth + 1))
          ->define('CRAFT_TEMPLATES_PATH', CRAFT_BASE_PATH . '/src/templates')
          ->dotEnv();

        return require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/' . $type . '.php';
    }

    /**
     * Gracefully define a PHP constant.
     * @param  string    $name
     * @param  mixed     $value
     * @return Bootstrap
     */
    public function define(string $name, $value): Bootstrap
    {
        if (!defined($name)) {
            define($name, $value);
        }

        return $this;
    }

    /**
     * Set depth from project root
     * @param  int       $depth
     * @return Bootstrap
     */
    public function setDepth(int $depth): Bootstrap
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * Apply dotenv and defines CRAFT_ENVIRONMENT constant.
     *
     * Fails silently if
     * - env file is not found (e.g. in production).
     * - `CRAFT_ENVIRONMENT` is already defined.
     *
     * @param  string    $path          Path to directory containing env file
     * @param  string    $file          Filename of env file
     * @param  bool      $logExceptions Log exceptions with error_log
     * @return Bootstrap
     */
    public function dotEnv(string $path = CRAFT_BASE_PATH, string $file = '.env', $logExceptions = false): Bootstrap
    {
        $dotenv = new Dotenv($path, $file);

        try {
            $dotenv->load();
        } catch (\Dotenv\Exception\InvalidPathException $e) {
            if ($logException) {
                error_log($e->getMessage());
            }
        }

        return $this->define('CRAFT_ENVIRONMENT', Environment::get('CRAFT_ENVIRONMENT', 'production'));
    }
}
