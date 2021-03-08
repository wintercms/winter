/*
 * Client side translations
 */

if ($.wn === undefined)
    $.wn = {}
if ($.oc === undefined)
    $.oc = $.wn

if ($.wn.langMessages === undefined)
    $.wn.langMessages = {}

$.wn.lang = (function(lang, messages) {

    lang.load = function(locale) {
        if (messages[locale] === undefined) {
            messages[locale] = {}
        }

        lang.loadedMessages = messages[locale]
    }

    lang.get = function(name, defaultValue) {
        if (!name) return

        var result = lang.loadedMessages

        if (!defaultValue) defaultValue = name

        $.each(name.split('.'), function(index, value) {
            if (result[value] === undefined) {
                result = defaultValue
                return false
            }

            result = result[value]
        })

        return result
    }

    if (lang.locale === undefined) {
        lang.locale = $('html').attr('lang') || 'en'
    }

    if (lang.loadedMessages === undefined) {
        lang.load(lang.locale)
    }

    return lang

})($.wn.lang || {}, $.wn.langMessages);