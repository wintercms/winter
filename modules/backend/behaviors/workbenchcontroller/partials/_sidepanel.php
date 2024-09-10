<div class="layout control-scrollpanel" id="cms-side-panel">
    <div class="layout-cell">
        <div class="layout-relative fix-button-container">
            <?php foreach ($this->getSectionListWidgets() as $section => $widget): ?>
                <form
                    role="form"
                    class="layout hide"
                    data-section-list="<?= $section ?>"
                >
                    <?= $widget->render() ?>
                </form>
            <?php endforeach ?>
        </div>
    </div>
</div>
