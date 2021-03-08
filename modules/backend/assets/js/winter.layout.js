(function($){
    var WinterLayout = function() {
        this.$accountMenuOverlay = null
    }

    WinterLayout.prototype.setPageTitle = function(title) {
        var $title = $('title')

        if (this.pageTitleTemplate === undefined)
            this.pageTitleTemplate = $title.data('titleTemplate')

        $title.text(this.pageTitleTemplate.replace('%s', title))
    }

    WinterLayout.prototype.updateLayout = function(title) {
        var $children, $el, fixedWidth, margin

        $('[data-calculate-width]').each(function(){
            $children = $(this).children()

            if ($children.length > 0) {
                fixedWidth = 0

                $children.each(function() {
                    $el = $(this)
                    margin = $el.data('oc.layoutMargin')

                    if (margin === undefined) {
                        margin = parseInt($el.css('marginRight')) + parseInt($el.css('marginLeft'))
                        $el.data('oc.layoutMargin', margin)
                    }
                    fixedWidth += $el.get(0).offsetWidth + margin
                })

                $(this).width(fixedWidth)
                $(this).trigger('oc.widthFixed')
            }
        })
    }

    WinterLayout.prototype.toggleAccountMenu = function(el) {
        var self = this,
            $el = $(el),
            $parent = $(el).parent(),
            $menu = $el.next()

        $el.tooltip('hide')

        if ($menu.hasClass('active')) {
            self.$accountMenuOverlay.remove()
            $parent.removeClass('highlight')
            $menu.removeClass('active')
        }
        else {
            self.$accountMenuOverlay = $('<div />').addClass('popover-overlay')
            $(document.body).append(self.$accountMenuOverlay)
            $parent.addClass('highlight')
            $menu.addClass('active')

            self.$accountMenuOverlay.one('click', function(){
                self.$accountMenuOverlay.remove()
                $menu.removeClass('active')
                $parent.removeClass('highlight')
            })
        }
    }

     if ($.wn === undefined)
        $.wn = {}
    if ($.oc === undefined)
        $.oc = $.wn

    $.wn.layout = new WinterLayout()

    $(document).ready(function(){
        $.wn.layout.updateLayout()

        window.setTimeout($.wn.layout.updateLayout, 100)
    })
    $(window).on('resize', function() {
        $.wn.layout.updateLayout()
    })
    $(window).on('oc.updateUi', function() {
        $.wn.layout.updateLayout()
    })
})(jQuery);