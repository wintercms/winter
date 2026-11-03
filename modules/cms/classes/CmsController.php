<?php namespace Cms\Classes;

use App;
use Closure;
use Illuminate\Routing\Controller as ControllerBase;

/**
 * This is the master controller for all front-end pages.
 * All requests that have not been picked up already by the router will end up here,
 * then the URL is passed to the front-end controller for processing.
 *
 * @see Cms\Classes\Controller Front-end controller class
 * @package winter\wn-cms-module
 * @author Alexey Bobkov, Samuel Georges
 */
class CmsController extends ControllerBase
{
    use \Winter\Storm\Extension\ExtendableTrait;

    /**
     * @var array Behaviors implemented by this controller.
     */
    public $implement;

    /**
     * Instantiate a new CmsController instance.
     */
    public function __construct()
    {
        $this->extendableConstruct();
    }

    /**
     * Finds and serves the request using the primary controller.
     * @param string $url Specifies the requested page URL.
     * If the parameter is omitted, the current URL used.
     * @return BaseResponse Returns the response to the provided URL
     */
    public function run($url = '/')
    {
        return App::make(Controller::class)->run($url);
    }

    public function __call($name, $params)
    {
        if ($name === 'extend') {
            if (empty($params[0]) || !is_callable($params[0])) {
                throw new \InvalidArgumentException('The extend() method requires a callback parameter or closure.');
            }
            if ($params[0] instanceof \Closure) {
                return $params[0]->call($this, $params[1] ?? $this);
            }
            return \Closure::fromCallable($params[0])->call($this, $params[1] ?? $this);
        }

        return $this->extendableCall($name, $params);
    }

    public static function __callStatic($name, $params)
    {
        if ($name === 'extend') {
            if (empty($params[0])) {
                throw new \InvalidArgumentException('The extend() method requires a callback parameter or closure.');
            }
            self::extendableExtendCallback($params[0], $params[1] ?? false, $params[2] ?? null);
            return;
        }

        return self::extendableCallStatic($name, $params);
    }
}
