<?php

namespace Backend\Tests\FormWidgets;

use Backend\Classes\BackendController;
use Backend\Classes\Controller;
use Backend\Models\User as BackendUser;
use Backend\Tests\Fixtures\Models\RelationBehaviorFixture;
use Backend\Widgets\Form;
use Symfony\Component\HttpKernel\Exception\HttpException;
use System\Models\File;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Database\Model;

/**
 * `previewMode` is the flag that says a form field is not editable. The parent form propagates it
 * to every field widget it builds (`Backend\Widgets\Form::makeFormFieldWidget()`), a `disabled`
 * field raises it on its own widget, and a form rendered in a preview context carries it.
 *
 * Widgets honoured it while rendering, but no widget handler consulted it. A widget's AJAX
 * handler is dispatched by alias straight to the widget, so it does not pass through whatever
 * authorized the page.
 *
 * The invariant: a widget handler that writes refuses when the widget is in preview mode; a
 * handler that only reads still works, because a preview form is still meant to be looked at.
 * Proven here away from the relation behavior, since the rule is not relation-specific.
 */
class FormWidgetPreviewModeTest extends PluginTestCase
{
    protected RelationBehaviorFixture $record;
    protected File $file;

    public function setUp(): void
    {
        parent::setUp();

        BackendController::$action = null;
        BackendController::$params = [];

        Model::unguard();

        $this->actingAs(BackendUser::create([
            'first_name' => 'Preview',
            'last_name' => 'Tester',
            'login' => 'previewtester',
            'email' => 'preview@test.com',
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1',
            'is_activated' => true,
            'is_superuser' => true,
        ]), 'backend');

        RelationBehaviorFixture::migrateUp();

        $this->record = RelationBehaviorFixture::create(['name' => 'Record']);

        $this->file = new File;
        $this->file->data = base_path('modules/backend/tests/fixtures/reference/file1.txt');
        $this->file->title = 'original title';
        $this->file->save();
        $this->record->thumb()->add($this->file);
    }

    public function tearDown(): void
    {
        RelationBehaviorFixture::migrateDown();
        Model::reguard();

        parent::tearDown();
    }

    protected function postData(array $data): void
    {
        request()->setMethod('POST');
        request()->request->replace([
            'file_id' => $this->file->id,
            'RelationBehaviorFixture' => ['thumb' => ['title' => 'renamed title']],
        ] + $data);
    }

    /**
     * Builds a controller carrying a form over the fixture, bound for AJAX dispatch.
     *
     * @param bool $disabledField Raise previewMode the way a `disabled` field does.
     * @param bool $previewForm Raise previewMode on the whole form, as a preview action does.
     */
    protected function makeController(bool $disabledField = false, bool $previewForm = false): Controller
    {
        $controller = new Controller;

        $widget = new Form($controller, [
            'model' => $this->record,
            'arrayName' => 'RelationBehaviorFixture',
            'alias' => 'testForm',
            'fields' => [
                'name' => ['label' => 'Name'],
                'thumb' => [
                    'label' => 'Thumb',
                    'type' => \Backend\FormWidgets\FileUpload::class,
                    'disabled' => $disabledField,
                ],
                'points' => [
                    'label' => 'Points',
                    'type' => \Backend\FormWidgets\Repeater::class,
                    'form' => ['fields' => ['point' => ['label' => 'Point']]],
                ],
            ],
        ]);
        $widget->previewMode = $previewForm;
        $widget->bindToController();

        return $controller;
    }

    protected function handlerStatus(Controller $controller, string $handler): int
    {
        try {
            static::callProtectedMethod($controller, 'runAjaxHandler', [$handler]);
        } catch (HttpException $ex) {
            return $ex->getStatusCode();
        }

        return 200;
    }

    public function writeHandlerProvider(): array
    {
        return [
            'upload'        => ['testFormThumb::onUpload'],
            'remove'        => ['testFormThumb::onRemoveAttachment'],
            'sort'          => ['testFormThumb::onSortAttachments'],
            'save config'   => ['testFormThumb::onSaveAttachmentConfig'],
            'repeater add'  => ['testFormPoints::onAddItem'],
            'repeater remove' => ['testFormPoints::onRemoveItem'],
        ];
    }

    /**
     * A form rendered in preview mode -- a FormController preview action, say -- refuses every
     * write handler of every field widget it carries.
     *
     * @dataProvider writeHandlerProvider
     */
    public function testPreviewFormRefusesWriteHandlers(string $handler): void
    {
        $this->postData([]);

        $this->assertEquals(403, $this->handlerStatus($this->makeController(false, true), $handler));
        $this->assertEquals('original title', File::find($this->file->id)->title);
        $this->assertNotNull(File::find($this->file->id));
    }

    /**
     * And so does a single `disabled` field on an otherwise editable form.
     */
    /**
     * A field-level `disabled` is a rendering concern: the client decides whether it comes back,
     * so it has never been a server side control and is not made into one here. Several widgets do
     * set preview mode from it to draw themselves as not editable, which is why this is pinned -
     * the write guard follows the form or relation that built the widget, not how it renders.
     */
    public function testDisabledFieldIsNotAServerSideControl(): void
    {
        $this->postData([]);

        $controller = $this->makeController(true);

        $this->assertEquals(200, $this->handlerStatus($controller, 'testFormThumb::onSaveAttachmentConfig'));
        $this->assertEquals(200, $this->handlerStatus($controller, 'testFormThumb::onRemoveAttachment'));

        // The sibling field is not disabled either way.
        $this->assertEquals(200, $this->handlerStatus($controller, 'testFormPoints::onAddItem'));
    }

    /**
     * Read handlers are not refused: a preview form is still meant to be looked at.
     */
    public function testPreviewFormStillAllowsReadHandlers(): void
    {
        $this->postData(['_repeater_index' => 0]);

        $controller = $this->makeController(false, true);

        $this->assertEquals(200, $this->handlerStatus($controller, 'testFormThumb::onLoadAttachmentConfig'));
        $this->assertEquals(200, $this->handlerStatus($controller, 'testFormPoints::onRefresh'));
    }

    /**
     * Invalidation: an ordinary editable form is untouched -- the write goes through.
     */
    public function testEditableFormStillWrites(): void
    {
        $this->postData([]);

        $controller = $this->makeController();

        $this->assertEquals(200, $this->handlerStatus($controller, 'testFormThumb::onSaveAttachmentConfig'));
        $this->assertEquals('renamed title', File::find($this->file->id)->title);

        $this->assertEquals(200, $this->handlerStatus($controller, 'testFormPoints::onAddItem'));
        $this->assertEquals(200, $this->handlerStatus($controller, 'testFormPoints::onRemoveItem'));
    }
}
