<div id="<?= $this->getId('mailPreviewContainer') ?>"></div>

<script>
    $(function(){
        // A mail partial is free-form HTML and may contain a complete script element, so the
        // message is passed as a JSON string literal rather than in a block it could terminate.
        createPreviewContainer(
            $('#<?= $this->getId('mailPreviewContainer') ?>').get(0),
            <?= json_encode($this->renderSampleMessage(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE) ?>
        )
    })
</script>
