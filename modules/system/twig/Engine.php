<?php namespace System\Twig;

use System\Twig\Loader as TwigLoader;
use Twig\Environment as TwigEnvironment;
use Illuminate\Contracts\View\Engine as EngineInterface;

/**
 * View engine used by the system, used for converting .htm files to twig.
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class Engine implements EngineInterface
{
    /**
     * @var TwigEnvironment
     */
    protected $environment;

    /**
     * Constructor
     */
    public function __construct(TwigEnvironment $environment)
    {
        $this->environment = $environment;
    }

    public function get($path, array $vars = [])
    {
        $previousAllow = TwigLoader::$allowInclude;

        TwigLoader::$allowInclude = true;

        try {
            $template = $this->environment->load($path);
        }
        finally {
            /*
             * Restore the previous value even when loading throws. Without this, a failed load
             * (missing template, syntax error) leaves arbitrary local file inclusion enabled for
             * the remainder of the process. Under PHP-FPM the process exits and the flag resets;
             * under a persistent worker it would stay enabled for every later request.
             */
            TwigLoader::$allowInclude = $previousAllow;
        }

        return $template->render($vars);
    }
}
