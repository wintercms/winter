<div id="<?= $this->getId('mailPreviewContainer') ?>"></div>

<script>
    $(function(){
        /*
         * The sample message is assembled from the mail layout, the mail partials and the
         * compiled mail branding CSS. A mail partial is free-form HTML and may contain a
         * complete script element, so the message is passed here as a JSON string literal
         * with JSON_HEX_TAG rather than in a raw-text block that it could terminate.
         */
        createPreviewContainer(
            $('#<?= $this->getId('mailPreviewContainer') ?>').get(0),
            <?= json_encode($this->renderSampleMessage(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE) ?>
        )
    })
</script>
