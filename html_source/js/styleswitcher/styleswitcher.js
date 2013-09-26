jQuery(document).ready(function($, undefined) {
    
    if($(window).width() > 1003) {
        /* setup */
        var $wrapper=$('#gw-switcher');
    
        /*if (!$.cookie('switcher')) { 
            $.cookie('switcher',1);
            $.cookie('sticky',1);
        }*/
    
        /* style switcher link event */
        $('.gw-switcher-tab').delegate('a', 'click', function(e){
            var $this=$(this);
            
            e.preventDefault();
            if ($this.hasClass('gw-switchter-open')) {
                $wrapper.animate({'left':'-143px'},350, function() { $this.removeClass('gw-switchter-open'); });
            } else {
                $wrapper.animate({'left':'-10px'},350, function() { $this.addClass('gw-switchter-open'); });
            };
        });
        
        /* switcher opt events */
        $('.gw-switcher-row').delegate('a', 'click', function(e){
            var $this=$(this);
            
            e.preventDefault();
    
            /* events */
            if ($this.hasClass('go-switcher-opt-boxed')) {
                if ($this.hasClass('go-switcher-opt-checked')) {
                    $('body').removeClass('boxed');
                    $.cookie('boxed',1, { expires: -1 });
                    
                } else {
                    $.cookie('boxed',1);
                    $('body').addClass('boxed');
                    
                };
                $(window).trigger('resize');
                
            } else if ($this.hasClass('go-switcher-opt-sticky')) { 
                if ($this.hasClass('go-switcher-opt-checked')) {
                    $.cookie('sticky',1);
                    $('body').addClass('no-sticky');
                    $('.navbar').removeClass('navbar-fixed-top').addClass('navbar-static-top');
                    $('#shrinkableHeader, .navbar .nav > li > a, .navbar-form .search-form, .navbar-menu-line .divider-vertical, .navbar-form .btn-search, .navbar-head .social, .brand > img, .parallax-slider').removeAttr('style');
                    
                } else {
                    $.cookie('sticky',1, { expires: -1 });
                    $('body').removeClass('no-sticky');
                    $('.navbar').removeClass('navbar-static-top').addClass('navbar-fixed-top');
                };
                $(window).trigger('scroll');
            };
    
            /* check & uncheck checkboxes */
            if ($this.hasClass('go-switcher-opt-checked')) {
                $this.removeClass('go-switcher-opt-checked');
            } else {
                $this.addClass('go-switcher-opt-checked');
            };
        });
        
        if ($.cookie('boxed')) { $('.go-switcher-opt-boxed').trigger('click'); };
        if ($.cookie('sticky')) { $('.go-switcher-opt-sticky').trigger('click'); };
    }

});