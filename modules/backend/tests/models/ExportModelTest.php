<?php

namespace Backend\Tests\Models;

use System\Tests\Bootstrap\TestCase;
use Backend\Facades\BackendAuth;
use Backend\Models\ExportModel;
use Backend\Models\User;
use Illuminate\Http\Request;

if (!class_exists('Model')) {
    class_alias('Winter\Storm\Database\Model', 'Model');
}

class ExampleExportModel extends ExportModel
{
    public function exportData($columns, $sessionKey = null)
    {
        return [
            [
                'foo' => 'bar',
                'bar' => 'foo',
                'foobar' => 'Hello World!',
            ],
            [
                'foo' => 'bar2',
                'bar' => 'foo2',
                'foobar' => 'Hello World2!',
            ],
        ];
    }
}

class ExportModelTest extends TestCase
{

    //
    // Tests
    //

    public function testEncodeArrayValue()
    {
        $model = new ExampleExportModel;
        $data = ['foo', 'bar'];
        $result = self::callProtectedMethod($model, 'encodeArrayValue', [$data]);
        $this->assertEquals('foo|bar', $result);

        $data = ['dps | heals | tank', 'paladin', 'berserker', 'gunner'];
        $result = self::callProtectedMethod($model, 'encodeArrayValue', [$data]);
        $this->assertEquals('dps \| heals \| tank|paladin|berserker|gunner', $result);

        $data = ['art direction', 'roman empire', 'sci-fi'];
        $result = self::callProtectedMethod($model, 'encodeArrayValue', [$data, '-']);
        $this->assertEquals('art direction-roman empire-sci\-fi', $result);
    }

    public function testDownload()
    {
        // An export file belongs to the backend user that produced it, and is only served back to
        // that user; this test boots the framework alone, so the owner is set rather than created.
        $owner = new User;
        $owner->id = 1;
        BackendAuth::setUser($owner);

        $model = new ExampleExportModel;

        $csvName = $model->export(['foo' => 'title', 'bar' => 'title2'], []);
        $filePath = temp_path('exports/' . $owner->getKey() . '/' . $csvName);

        $this->assertFileExists($filePath, "Export-CSV was not written to its owner's directory.");

        $response = $model->download($csvName);

        $request = new Request();

        $response->prepare($request);

        $this->assertTrue($response->headers->has('Content-Type'), "Response is missing the Content-Type header!");

        $contentType = $response->headers->get('Content-Type');
        $this->assertTrue(
            str_contains($contentType, 'application/csv')
            || str_contains($contentType, 'text/plain')
            || str_contains($contentType, 'text/csv'),
            "Content-Type is not as expected, provided: " . $contentType
        );

        ob_start();
        $response->send();
        $output = ob_get_clean();

        $utf8BOM = chr(239) . chr(187) . chr(191);

        $this->assertEquals($utf8BOM . "title,title2\nbar,foo\nbar2,foo2\n", $output, "CSV is not right!");

        $fileGotDeleted = !is_file($filePath);

        $this->assertTrue($fileGotDeleted, "Export-CSV doesn't get deleted.");
        if (!$fileGotDeleted) {
            unlink($filePath);
        }
    }
}
