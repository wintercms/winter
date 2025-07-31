<div class="updates-app" style="margin-top: -20px;">
    <div class="bg-blue-100 shadow-sm mb-4">
        <div class="flex flex-row gap-4 max-w-[1325px] bg-white mx-auto font-sans">
            <div class="w-full">
                <div class="flex flex-wrap gap-8 bg-blue-100 py-12 px-2">
                    <div class="title-value w-full-storm-fix sm:w-1/3 lg:w-2/8 bg-white p-6 rounded-3xl border border-blue-200 shadow-sm">
                        <h4><?= e(trans('system::lang.project.name')) ?></h4>
                        <?php if ($projectId): ?>
                            <p class="wn-icon-chain"><?= $projectName ?></p>
                            <p class="description">
                                <?= e(trans('system::lang.project.owner_label')) ?>: <?= $projectOwner ?>
                                (<a
                                    href="javascript:;"
                                    data-request-confirm="<?= e(trans('system::lang.project.detach_confirm')) ?>"
                                    data-request="onDetachProject"
                                    data-stripe-load-indicator><?= e(trans('system::lang.project.detach')) ?></a>)
                            </p>
                        <?php else: ?>
                            <p class="wn-icon-chain-broken"><?= e(trans('system::lang.project.none')) ?></p>
                            <p class="description">
                                <a
                                    href="javascript:;"
                                    data-control="popup"
                                    data-handler="onLoadProjectForm">
                                    <?= e(trans('system::lang.project.attach')) ?>
                                </a>
                            </p>
                        <?php endif ?>
                    </div>
                    <div class="title-value w-full-storm-fix sm:w-1/3 lg:w-2/8 bg-white p-6 rounded-3xl border border-blue-200 shadow-sm">
                        <h4><?= e(trans('system::lang.updates.plugins')) ?></h4>
                        <p><?= $pluginsCount ?></p>
                        <p class="description">
                            <?= e(trans('system::lang.updates.disabled')) ?>: <?= $pluginsCount - $pluginsActiveCount ?>
                        </p>
                    </div>
                    <?php if ($coreBuild): ?>
                        <div class="title-value w-full-storm-fix sm:w-1/3 lg:w-2/8 bg-white p-6 rounded-3xl border border-blue-200 shadow-sm">
                            <h4><?= e(trans('system::lang.updates.core_current_build')) ?></h4>
                            <?php if ($coreBuildModified): ?>
                                <p
                                    class="oc-icon-exclamation-circle"
                                    data-toggle="tooltip"
                                    data-placement="bottom"
                                    title="This build has been modified"
                                >
                                    <?= $coreBuild ?>
                                </p>
                            <?php else: ?>
                                <p><?= $coreBuild ?></p>
                            <?php endif; ?>

                            <p class="description">
                                <a
                                    href="javascript:;"
                                    data-control="popup"
                                    data-handler="onLoadChangelog">
                                    <?= e(trans('system::lang.updates.core_view_changelog')) ?>
                                </a>
                            </p>
                        </div>
                    <?php endif ?>
                </div>
            </div>
            <?php if (count($warnings)): ?>
                <div class="w-full">
                    <div class="scoreboard">
                        <div class="callout fade in callout-danger no-icon">
                            <div class="header">
                                <h3><?= e(trans('system::lang.updates.update_warnings_title')) ?></h3>
                                <ul>
                                    <?php foreach ($warnings as $warning): ?>
                                        <li><?= $warning ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
    <div class="w-full bg-white py-6 px-8 -my-4">
        <div data-control="toolbar" class="mx-6">
            <a
                href="javascript:;"
                class="btn btn-outline-primary wn-icon-refresh"
                data-control="popup"
                data-handler="onLoadUpdates">
                <?= e(trans('system::lang.updates.check_label')) ?>
            </a>
            <a
                href="<?= Backend::url('system/updates/install') ?>"
                class="btn btn-outline-success wn-icon-plus">
                <?= e(trans('system::lang.plugins.install')) ?>
            </a>
            <div class="btn-group dropdown dropdown-fixed">
                <button
                    data-primary-button
                    type="button"
                    class="btn btn-default wn-icon-caret-down dropdown-toggle"
                    data-toggle="dropdown"
                    data-trigger-action="enable"
                    data-trigger=".control-list .list-checkbox input[type=checkbox]"
                    data-trigger-condition="checked"
                    data-request-success="$(this).prop('disabled', true).next().prop('disabled', true)">
                    <?= e(trans('system::lang.plugins.select_label')) ?>
                </button>

                <ul class="dropdown-menu" data-dropdown-title="<?= e(trans('system::lang.plugins.bulk_actions_label')) ?>">
                    <li>
                        <a href="javascript:;" class="wn-icon-pause"
                           data-request="onBulkAction"
                           onclick="$(this).data('request-data', {
                                action: 'freeze',
                                checked: $('.control-list').listWidget('getChecked')
                            })"
                           data-request-update="list_manage_toolbar: '#plugin-toolbar'"
                           data-request-confirm="<?= e(trans('system::lang.plugins.action_confirm', ['action' => e(trans('system::lang.plugins.freeze'))])) ?>"
                           data-stripe-load-indicator>
                            <?= e(trans('system::lang.plugins.freeze_label')) ?>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" class="wn-icon-play"
                           data-request="onBulkAction"
                           onclick="$(this).data('request-data', {
                                action: 'unfreeze',
                                checked: $('.control-list').listWidget('getChecked')
                            })"
                           data-request-update="list_manage_toolbar: '#plugin-toolbar'"
                           data-request-confirm="<?= e(trans('system::lang.plugins.action_confirm', ['action' => e(trans('system::lang.plugins.unfreeze'))])) ?>"
                           data-stripe-load-indicator>
                            <?= e(trans('system::lang.plugins.unfreeze_label')) ?>
                        </a>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li>
                        <a href="javascript:;" class="wn-icon-ban"
                           data-request="onBulkAction"
                           onclick="$(this).data('request-data', {
                                action: 'disable',
                                checked: $('.control-list').listWidget('getChecked')
                            })"
                           data-request-update="list_manage_toolbar: '#plugin-toolbar'"
                           data-request-confirm="<?= e(trans('system::lang.plugins.action_confirm', ['action' => e(trans('system::lang.plugins.disable'))])) ?>"
                           data-stripe-load-indicator>
                            <?= e(trans('system::lang.plugins.disable_label')) ?>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" class="wn-icon-check"
                           data-request="onBulkAction"
                           onclick="$(this).data('request-data', {
                                action: 'enable',
                                checked: $('.control-list').listWidget('getChecked')
                            })"
                           data-request-update="list_manage_toolbar: '#plugin-toolbar'"
                           data-request-confirm="<?= e(trans('system::lang.plugins.action_confirm', ['action' => e(trans('system::lang.plugins.enable'))])) ?>"
                           data-stripe-load-indicator>
                            <?= e(trans('system::lang.plugins.enable_label')) ?>
                        </a>
                    </li>
                    <?php if (\Config::get('app.debug', false) && \BackendAuth::getUser()->is_superuser): ?>
                        <li role="separator" class="divider"></li>
                        <li>
                            <a href="javascript:;" class="wn-icon-bomb"
                               data-request="onBulkAction"
                               onclick="$(this).data('request-data', {
                                    action: 'refresh',
                                    checked: $('.control-list').listWidget('getChecked')
                                })"
                               data-request-update="list_manage_toolbar: '#plugin-toolbar'"
                               data-request-confirm="<?= e(trans('system::lang.plugins.refresh_confirm')) ?>"
                               data-stripe-load-indicator>
                                <?= e(trans('system::lang.plugins.refresh_label')) ?>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="btn-group">
                <button
                    class="btn btn-danger wn-icon-trash-o"
                    disabled="disabled"
                    data-request="onBulkAction"
                    onclick="$(this).data('request-data', {
                        action: 'remove',
                        checked: $('.control-list').listWidget('getChecked')
                    })"
                    data-request-update="list_manage_toolbar: '#plugin-toolbar'"
                    data-request-confirm="<?= e(trans('system::lang.plugins.remove_confirm')) ?>"
                    data-trigger-action="enable"
                    data-trigger=".control-list .list-checkbox input[type=checkbox]"
                    data-trigger-condition="checked"
                    data-request-success="$(this).closest('.btn-group').find('button').prop('disabled', true)"
                    data-stripe-load-indicator>
                    <?= e(trans('system::lang.plugins.remove')) ?>
                </button>
            </div>
        </div>
    </div>
    <div class="w-full mt-4">
        <?= $this->listRender(); ?>
    </div>
</div>
