/* globals window, jQuery */

/**
 * Winter CMS HTML sanitizer module.
 *
 * Based off https://gist.github.com/ufologist/5a0da51b2b9ef1b861c30254172ac3c9. Requires jQuery
 *
 * @copyright 2021 Winter CMS
 * @author Ben Thomson <git@alfreido.com>
 * @link https://wintercms.com
 */

if (!window.winter) {
    throw new Error('The Winter CMS JS framework base must be loaded before the HTML Sanitizer module can be registered.')
}

(function (winter, $) {
    'use strict';

    var Sanitizer = function () {
    }

    Sanitizer.prototype.singleton = true

    Sanitizer.prototype.trimAttributes = function (node) {
        for (i = 0; i < node.attributes.length; i++) {
            var attrName = node.attributes.getItem(i).name
            var attrValue = node.attributes.getItem(i).value

            /*
             * remove attributes where the names start with "on" (for example: onload, onerror...)
             * remove attributes where the value starts with the "javascript:" pseudo protocol (for example href="javascript:alert(1)")
             */
            if (attrName.indexOf('on') == 0 || attrValue.indexOf('javascript:') == 0) {
                node.removeAttribute(attrName)
            }
        }
    }

    Sanitizer.prototype.sanitize = function (html) {
        var parser = new DOMParser()
        var dom = parser.parseFromString(html, 'text/html')

        // Strip script tags
        console.log(dom.getRootNode());
        // for (var i in dom.getElementsByTagName('script'))

        // output.find('*').each(function() {
        //     self.trimAttributes(this)
        // })
        // return output.html()
    }

    // Extend the Winter CMS JS framework
    winter.extend('Sanitizer', Sanitizer)

    // Add to global function for backwards compatibility
    window.wnSanitize = function (html) {
        return winter.sanitizer.sanitize(html)
    }
}(window.winter, jQuery));
