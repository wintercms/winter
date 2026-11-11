<?php

namespace System\Tests\Classes;

use System\Classes\SettingsManager;
use System\Tests\Bootstrap\TestCase;
use System\Tests\Concerns\AssertsNoDeprecations;

class SettingsManagerTest extends TestCase
{
    use AssertsNoDeprecations;

    public function testNoContextFindsNothingWithoutDeprecations()
    {
        // Every backend page outside a settings controller renders the breadcrumb with no context.
        $manager = SettingsManager::instance();
        $manager->addSettingItem('Winter.Tester', 'test_item', ['label' => 'Test item']);

        [$context, $item] = $this->assertNoDeprecations(function () use ($manager) {
            $context = $manager->getContext();

            return [$context, $manager->findSettingItem($context->owner, $context->itemCode)];
        });

        $this->assertNull($context->owner);
        $this->assertNull($context->itemCode);
        $this->assertFalse($item);
    }

    public function testFindsItemForContext()
    {
        $manager = SettingsManager::instance();
        $manager->addSettingItem('Winter.Tester', 'test_item', ['label' => 'Test item']);
        SettingsManager::setContext('Winter.Tester', 'test_item');

        $item = $this->assertNoDeprecations(function () use ($manager) {
            $context = $manager->getContext();

            return $manager->findSettingItem($context->owner, $context->itemCode);
        });

        $this->assertSame('Test item', $item->label);
    }
}
