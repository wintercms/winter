<?php Block::put('breadcrumb') ?>
    <ul>
        <li><a href="<?= Backend::url('system/mailtemplates') ?>"><?= e(trans('system::lang.mail_templates.menu_label')) ?></a></li>
        <li><?= e(trans($this->pageTitle)) ?></li>
    </ul>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>

    <?= Form::open(['class' => 'layout']) ?>

        <div class="layout-row">
            <?= $this->formRender() ?>
        </div>

        <div class="form-buttons p-t">
            <div class="loading-indicator-container">
                <button
                    type="submit"
                    data-request="onSave"
                    data-browser-validate
                    data-hotkey="ctrl+s, cmd+s"
                    data-load-indicator="<?= e(trans('system::lang.mail_templates.creating')) ?>"
                    class="btn btn-primary">
                    <?= e(trans('backend::lang.form.create')) ?>
                </button>
                <button
                    type="button"
                    data-request="onSave"
                    data-browser-validate
                    data-request-data="close:1"
                    data-hotkey="ctrl+enter, cmd+enter"
                    data-load-indicator="<?= e(trans('system::lang.mail_templates.creating')) ?>"
                    class="btn btn-default">
                    <?= e(trans('backend::lang.form.create_and_close')) ?>
                </button>
                <span class="btn-text">
                    <?= e(trans('backend::lang.form.or')) ?> <a href="<?= Backend::url('system/mailtemplates') ?>"><?= e(trans('backend::lang.form.close')) ?></a>
                </span>
            </div>
        </div>

    <?= Form::close() ?>

<?php else: ?>

    <p class="flash-message static error"><?= e(trans($this->fatalError)) ?></p>
    <p><a href="<?= Backend::url('system/mailtemplates') ?>" class="btn btn-default"><?= e(trans('system::lang.mail_templates.return')) ?></a></p>

<?php endif ?>
