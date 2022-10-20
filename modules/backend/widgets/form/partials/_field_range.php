<!-- Range -->
<?php if ($this->previewMode): ?>
    <span class="form-control"><?= isset($field->value) ? e($field->value) : '&nbsp;' ?></span>
<?php else: ?>
    <?php
    $min = $field->config['min'] ?? 0;
    $max = $field->config['max'] ?? 100;
    $step = $field->config['step'] ?? 1;
    $value = $field->value;
    if ($min > $max) {
        $min = $max - $step;
    }
    if (is_null($value)) {
        $value = ($min + $max) / 2;
    }
    ?>

    <input
        type="range"
        step="<?= $step ?>"
        name="<?= $field->getName() ?>"
        id="<?= $field->getId() ?>"
        value="<?= e($value) ?>"
        min="<?= $min ?>"
        max="<?= $max ?>"
        <?= $field->getAttributes() ?>
    />
    <span style="position: absolute; transform: translateX(-50%)"></span>
    <script>
        (() => {
            const input = document.getElementById("<?= $field->getId() ?>");
            input.addEventListener("input", function () {
                this.nextElementSibling.innerHTML = this.value;
                var pos = ((this.value - <?= $min ?>) / (<?= $max ?> - <?= $min ?>) * 100);
                this.nextElementSibling.style.left = `calc(${pos}% + ${8 - pos * 0.15}px)`;
            });
            input.dispatchEvent(new Event('input'));
        })();
    </script>
<?php endif; ?>
