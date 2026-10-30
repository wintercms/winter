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

    // Each token is load-bearing: allow-same-origin so adjustPreviewHeight() can read the
    // document, and the two popup tokens so the sample message's target="_blank" button can
    // open a tab that does not inherit this sandbox.
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
