var previewIframe

$(document).on('change', '.field-colorpicker', function() {
    $('#brandSettingsForm').request('onUpdateSampleMessage').done(function(data) {
        updatePreviewContent(data.previewHtml)
    })
})

function updatePreviewContent(content) {
    'srcdoc' in previewIframe
        ? previewIframe.srcdoc = content
        : previewIframe.src = 'data:text/html;charset=UTF-8,' + content
}

function adjustPreviewHeight() {
    previewIframe.style.height = (previewIframe.contentWindow.document.getElementsByTagName('body')[0].scrollHeight) +'px'
}

function createPreviewContainer(el, content) {
    previewIframe = document.createElement('iframe')

    /*
     * The previewed document is assembled from the mail layout, the mail partials and the
     * compiled mail branding CSS, all of which are stored content. Emails never
     * legitimately carry scripts, so the preview does not need to run any.
     * allow-same-origin is kept because adjustPreviewHeight() reads the previewed
     * document to size the frame, and the two popup tokens are kept because the sample
     * message's action button is a target="_blank" link: without them the frame cannot
     * open a tab at all, and without the second one the tab it opens inherits the
     * sandbox and loads the linked page with scripting disabled.
     */
    previewIframe.setAttribute('sandbox', 'allow-same-origin allow-popups allow-popups-to-escape-sandbox')

    updatePreviewContent(content)

    previewIframe.style.width = '100%'
    previewIframe.setAttribute('frameborder', 0)
    previewIframe.setAttribute('id', el.id)
    previewIframe.onload = adjustPreviewHeight

    var parent = el.parentNode
    parent.replaceChild(previewIframe, el)

    /*
     * Auto adjust height
     */
    $(document).render(adjustPreviewHeight)
    $(window).resize(adjustPreviewHeight)
}
