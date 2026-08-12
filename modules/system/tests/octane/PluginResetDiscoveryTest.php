<?php

namespace System\Tests\Octane;

use System\Classes\Octane\ResetsRequestState;
use System\Classes\PluginBase;
use System\Classes\PluginManager;
use System\Tests\Bootstrap\PersistentWorkerTestCase;
use Winter\Storm\Contracts\ResetsWorkerState;

/**
 * A plugin must be reset whether it names the contract or merely provides the method.
 *
 * The second form is the one that matters in practice. `implements` is resolved when the class is
 * loaded, so a plugin naming ResetsWorkerState cannot be loaded at all by a Winter whose Storm
 * predates the interface -- it fatals on every request, under PHP-FPM as much as under a worker,
 * because loading the plugin is what fails. A plugin distributed against several Winter versions
 * therefore cannot name it, and if the reset skipped those plugins they would keep exactly the
 * request-derived state this class exists to clear.
 */
class PluginResetDiscoveryTest extends PersistentWorkerTestCase
{
    /**
     * Both forms are discovered, and a plugin offering neither is left alone rather than fatalling.
     *
     * @return void
     */
    public function testAPluginIsResetByContractOrByMethod(): void
    {
        $app = $this->bootWorker();

        $byMethod = new PluginResetDiscoveryMethodPlugin($app);
        $neither  = new PluginResetDiscoveryInertPlugin($app);

        /*
         * Built here, and only where the contract exists, for the very reason this test covers. A
         * file-scope class naming ResetsWorkerState cannot be loaded by a Winter whose Storm predates
         * the interface, and PHPUnit loads every test file before running anything, so declaring one
         * would take the whole suite down rather than fail this test. An anonymous class is evaluated
         * when the line runs, which is after the check.
         */
        $byContract = null;
        if (interface_exists(ResetsWorkerState::class)) {
            $byContract = new class ($app) extends PluginBase implements ResetsWorkerState
            {
                /**
                 * @var int Number of times the reset was invoked.
                 */
                public $resets = 0;

                /**
                 * @return void
                 */
                public function resetWorkerState(): void
                {
                    $this->resets++;
                }
            };
        }

        /*
         * The plugin table is set directly rather than through registerPlugin(), which also wants a
         * path on disk for language and view namespaces. What is under test is which plugins the reset
         * selects, so the plugins only have to be present and enabled.
         */
        $manager    = PluginManager::instance();
        $plugins    = new \ReflectionProperty($manager, 'plugins');
        $normalized = new \ReflectionProperty($manager, 'normalizedMap');
        $original   = $plugins->getValue($manager);
        $originalNm = $normalized->getValue($manager);

        $fakes = [
            'System.Tests.ResetByMethod' => $byMethod,
            'System.Tests.ResetNeither'  => $neither,
        ];

        if ($byContract !== null) {
            $fakes['System.Tests.ResetByContract'] = $byContract;
        }

        $plugins->setValue($manager, $fakes);
        $normalized->setValue($manager, array_combine(array_keys($fakes), array_keys($fakes)));

        try {
            $reset  = new ResetsRequestState();
            $invoke = new \ReflectionMethod($reset, 'resetPlugins');
            $invoke->invoke($reset);
        }
        finally {
            $plugins->setValue($manager, $original);
            $normalized->setValue($manager, $originalNm);
        }

        if ($byContract !== null) {
            $this->assertSame(
                1,
                $byContract->resets,
                'A plugin implementing ResetsWorkerState was not reset.'
            );
        }

        $this->assertSame(
            1,
            $byMethod->resets,
            'A plugin declaring resetWorkerState() without implementing the contract was not reset. '
            . 'Such a plugin cannot name the interface without becoming unloadable on an older Storm.'
        );
        $this->assertFalse(
            $neither->touched,
            'A plugin providing neither the contract nor the method should be skipped.'
        );
    }
}

/**
 * Resettable by declaring the method alone, which is what a plugin spanning Storm versions must do.
 */
class PluginResetDiscoveryMethodPlugin extends PluginBase
{
    /**
     * @var int Number of times the reset was invoked.
     */
    public $resets = 0;

    /**
     * @return void
     */
    public function resetWorkerState(): void
    {
        $this->resets++;
    }
}

/**
 * Holds no request-derived state and offers neither form.
 */
class PluginResetDiscoveryInertPlugin extends PluginBase
{
    /**
     * @var bool Set only if something reached this plugin.
     */
    public $touched = false;
}
