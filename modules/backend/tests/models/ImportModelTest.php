<?php

namespace Backend\Tests\Models;

use System\Tests\Bootstrap\PluginTestCase;
use Backend\Models\ImportModel;
use System\Models\File as FileModel;

if (!class_exists('Model')) {
    class_alias('Winter\Storm\Database\Model', 'Model');
}

class ExampleImportModel extends ImportModel
{
    public $rules = [];

    public function importData($results, $sessionKey = null)
    {
        return [];
    }
}

class ImportModelTest extends PluginTestCase
{

    //
    // Tests
    //

    public function testDecodeArrayValue()
    {
        $model = new ExampleImportModel;
        $data = 'foo|bar';
        $result = self::callProtectedMethod($model, 'decodeArrayValue', [$data]);
        $this->assertEquals(['foo', 'bar'], $result);

        $data = 'dps \| heals \| tank|paladin|berserker|gunner';
        $result = self::callProtectedMethod($model, 'decodeArrayValue', [$data]);
        $this->assertEquals(['dps | heals | tank', 'paladin', 'berserker', 'gunner'], $result);

        $data = 'art direction-roman empire-sci\-fi';
        $result = self::callProtectedMethod($model, 'decodeArrayValue', [$data, '-']);
        $this->assertEquals(['art direction', 'roman empire', 'sci-fi'], $result);
    }

    public function testGetImportFilePath()
    {
        $model = new ExampleImportModel;
        $sessionKey = uniqid('session_key', true);

        $file1 = FileModel::create([
            'data' => base_path().'/modules/backend/tests/fixtures/reference/file1.txt',
            'is_public' => false,
        ]);

        $file2 = FileModel::create([
            'data' => base_path().'/modules/backend/tests/fixtures/reference/file2.txt',
            'is_public' => false,
        ]);

        $model->import_file()->add($file1, $sessionKey);
        $model->import_file()->add($file2, $sessionKey);

        $this->assertEquals(
            $file2->getLocalPath(),
            $model->getImportFilePath($sessionKey),
            'ImportModel::getImportFilePath() should return the last uploaded file.'
        );
    }
}
