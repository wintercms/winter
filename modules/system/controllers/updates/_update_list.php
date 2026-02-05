<?php if ($this->fatalError): ?>
    <div class="modal-body">
        <p class="flash-message static error"><?= e(trans($this->fatalError)) ?></p>
    </div>
    <div class="modal-footer">
        <button
            type="button"
            class="btn btn-default"
            data-dismiss="popup">
            <?= e(trans('backend::lang.form.close')) ?>
        </button>
    </div>
<?php endif ?>

<?php if (!$hasUpdates): ?>
    <div class="modal-body">
        <p><?= e(trans('system::lang.updates.none.help')) ?></p>
    </div>
    <div class="modal-footer">
        <button
            type="button"
            class="btn btn-default"
            data-dismiss="popup">
            <?= e(trans('backend::lang.form.close')) ?>
        </button>
        <button
            type="button"
            class="btn btn-primary"
            data-dismiss="popup"
            data-control="popup"
            data-handler="onForceUpdate"
            data-keyboard="false">
            <?= e(trans('system::lang.updates.force_label')) ?>
        </button>
    </div>
<?php else: ?>
    <div class="modal-body updates-app">
        <p>
            <strong><?= e(trans('system::lang.updates.found.label')) ?></strong>
            <?= e(trans('system::lang.updates.found.help')) ?>
        </p>

        <div class="control-updatelist">
            <div class="control-scrollbar" style="height:400px" data-control="scrollbar">
                <?php if ($updates['modules']): ?>
                    <div class="update-item item-danger">
                        <div class="item-header">
                            <div class="important-update form-group form-group-sm">
                                <select
                                    name="core_action"
                                    class="form-control custom-select select-no-search"
                                    data-important-update-select>
                                    <option value="">-- <?= e(trans('system::lang.updates.important_action.empty')) ?> --</option>
                                    <option value="confirm"><?= e(trans('system::lang.updates.important_action.confirm')) ?></option>
                                </select>
                            </div>
                            <h5>
                                <i class="icon-cube"></i>
                                <?= e(trans('system::lang.system.name')) ?>
                            </h5>
                        </div>
                        <dl>
                            <?php foreach ($updates['modules'] as $module => $versions): ?>
                                <dt class="block w-full">
                                    <strong><?= e($module) ?></strong>
                                </dt>
                                <dd class="flex items-center font-mono gap-4">

                                    <span class="bg-gray-200 rounded-full px-3 pt-2 pb-1 text-2xl"><?= e(rtrim($versions['from'], '.0')) ?></span>
                                    <i class="icon icon-arrow-right"></i>
                                    <span class="bg-blue-200 rounded-full px-3 pt-2 pb-1 text-2xl">
                                        <?= e(rtrim($versions['to'], '.0')) ?>
                                        <small style="font-family: monospace;">(<?= e(substr($versions['ref'] ?? '', 0, 7)) ?>)</small>
                                    </span>
                                </dd>
                            <?php endforeach ?>
                        </dl>
                    </div>
                <?php endif ?>

                <?php foreach ($updates['themes'] as $code => $theme): ?>
                    <div class="update-item">
                        <div class="item-header">
                            <h5>
                                <i class="icon-picture-o"></i>
                                <?= e(array_get($theme, 'name', 'Unknown')) ?>
                            </h5>
                        </div>
                        <dl>
                            <dt><?= e(array_get($theme, 'from', 'v1.0.0')) ?></dt>
                            <dd><?= e(trans('system::lang.updates.theme_new_install')) ?></dd>
                        </dl>

                        <input type="hidden" name="themes[<?= e($this->encodeCode($code)) ?>]" value="<?= e($theme['to']) ?>" />
                    </div>
                <?php endforeach ?>

                <?php foreach ($updates['plugins'] as $plugin => $versions): ?>
                    <div class="update-item">
                        <div class="item-header">
                            <?php if (false): ?>
                                <div class="important-update form-group form-group-sm">
                                    <select
                                        name="plugin_actions[<?= e($this->encodeCode($code)) ?>]"
                                        class="form-control custom-select select-no-search"
                                        data-important-update-select>
                                        <option value="">-- <?= e(trans('system::lang.updates.important_action.empty')) ?> --</option>
                                        <option value="confirm"><?= e(trans('system::lang.updates.important_action.confirm')) ?></option>
                                        <option value="skip"><?= e(trans('system::lang.updates.important_action.skip')) ?></option>
                                        <option value="ignore"><?= e(trans('system::lang.updates.important_action.ignore')) ?></option>
                                    </select>
                                </div>
                            <?php endif ?>
                            <h5>
                                <i class="<?= e($versions['icon'] ?? 'icon-puzzle-piece') ?>"></i>
                                <?= e($plugin) ?>
                            </h5>
                        </div>
                        <dl class="flex justify-between items-center">
                            <dd class="flex items-center font-mono gap-4">
                                <span class="bg-gray-200 rounded-full px-3 pt-2 pb-1 text-2xl"><?= e(rtrim($versions['from'], '.0')) ?></span>

                                <i class="icon icon-arrow-right"></i>

                                <span class="bg-blue-200 rounded-full px-3 pt-2 pb-1 text-2xl">
                                    <?= e(rtrim($versions['to'], '.0')) ?>
                                    <small style="font-family: monospace;">(<?= e(substr($versions['ref'] ?? '', 0, 7)) ?>)</small>
                                </span>
                            </dd>
                            <dd>
                                <div>
                                    <div class="checkbox custom-checkbox nolabel">
                                        <input type="checkbox" name="checked[]" id="updates-checkbox-<?= $plugin ?>" value="2" autocomplete="off">
                                        <label for="updates-checkbox-<?= $plugin ?>">Check</label>
                                    </div>
                                </div>
                            </dd>
                        </dl>

                        <input type="hidden" name="plugins[<?= e($this->encodeCode($plugin)) ?>]" value="<?= e($versions['to']) ?>" />
                    </div>
                <?php endforeach ?>

            </div>
        </div>

    </div>

    <div class="modal-footer">
        <?php if ($hasImportantUpdates): ?>
            <p class="text-danger pull-left wn-icon-exclamation important-update-label" id="updateListImportantLabel">
                <?= e(trans('system::lang.updates.important_alert_text')) ?>
            </p>
        <?php endif ?>
        <button
            type="button"
            id="updateListUpdateButton"
            class="btn btn-primary"
            data-dismiss="popup"
            data-control="popup"
            data-handler="onApplyUpdates"
            data-keyboard="false">
            <?= e(trans('system::lang.updates.update_label')) ?>
        </button>
        <button
            type="button"
            class="btn btn-default"
            data-dismiss="popup">
            <?= e(trans('backend::lang.form.cancel')) ?>
        </button>
    </div>
    <?php /* @TODO: Move this */ ?>
<?php endif ?>


