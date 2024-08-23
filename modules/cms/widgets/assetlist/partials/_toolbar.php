<div class="layout-row min-size">
    <div class="control-toolbar toolbar-padded">

        <!-- Control Panel -->
        <div class="toolbar-item" data-calculate-width>
            <div class="btn-group">
                <div class="dropdown last">
                    <button type="button" class="btn btn-default wn-icon-plus"
                        data-control="create-asset"
                        data-toggle="dropdown"
                        ><?= e(trans('cms::lang.sidebar.add')) ?></button>

                    <ul class="dropdown-menu offset-left" role="menu" data-dropdown-title="<?= e(trans('cms::lang.asset.drop_down_add_title')) ?>">
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:;" data-control="create-template" class="wn-icon-file-text-o"><?= e(trans('cms::lang.asset.create_file')) ?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:;" data-control="upload-assets" class="wn-icon-upload"><?= e(trans('cms::lang.asset.upload_files')) ?></a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a
                            role="menuitem"
                            tabindex="-1"
                            href="javascript:;"
                            class="wn-icon-folder-o"
                            data-control="popup"
                            data-handler="<?= $this->getEventHandler('onLoadNewDirPopup') ?>"
                        ><?= e(trans('cms::lang.asset.create_directory')) ?></a></li>
                    </ul>
                </div>

                <div class="dropdown hide"
                    id="<?= $this->getId('tools-button') ?>"
                    data-trigger-action="show"
                    data-trigger="<?= '#' . $this->getId('asset-list') ?> input[type=checkbox]"
                    data-trigger-condition="checked">
                    <button type="button" class="btn btn-default empty wn-icon-wrench last"
                        data-toggle="dropdown"
                        data-control="asset-tools"
                        ></button>

                    <ul class="dropdown-menu" role="menu" data-dropdown-title="<?= e(trans('cms::lang.asset.drop_down_operation_title')) ?>">
                        <li role="presentation"><a
                            role="menuitem"
                            tabindex="-1"
                            href="javascript:;"
                            data-control="delete-asset"
                            data-confirmation="<?= e(trans($this->deleteConfirmation)) ?>"
                            class="wn-icon-trash-o"
                        ><?= e(trans('cms::lang.asset.delete')) ?></a></li>

                        <li role="presentation"><a
                            role="menuitem"
                            tabindex="-1"
                            href="javascript:;"
                            class="wn-icon-angle-double-right"
                            data-control="popup"
                            data-handler="<?= $this->getEventHandler('onLoadMovePopup') ?>"
                        ><?= e(trans('cms::lang.asset.move')) ?></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Asset Search -->
        <div class="relative toolbar-item loading-indicator-container size-input-text">
            <input
                type="text"
                name="search"
                value="<?= e($this->getSearchTerm()) ?>"
                class="form-control icon search" autocomplete="off"
                placeholder="<?= e(trans('cms::lang.sidebar.search')) ?>"
                data-track-input
                data-load-indicator
                data-load-indicator-opaque
                data-request-success="$('<?= '#' . $this->getId('tools-button') ?>').trigger('oc.triggerOn.update')"
                data-request="<?= $this->getEventHandler('onSearch') ?>"
            />
        </div>

    </div>
</div>