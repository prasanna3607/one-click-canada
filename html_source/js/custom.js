//  ==========
//  = Custom JS and jQuery =
//  ==========

$(document).ready(function() {'use strict';
    
    var isTouch = function() {
        return $(window).width() < 982 ? true : false;
    };

    //  ==========
    //  = Tooltip =
    //  ==========
    $(".add-tooltip").tooltip({
        animation : false
    });

    //  ==========
    //  = Revolution Slider =
    //  ==========
    (function() {
        var $mainSlider;
        
        $mainSlider = $(".banner").revolution({
            delay : 7250,
            startheight : 450,
            startwidth : 964,

            navigationType : "none",
            navigationArrows : "none",
            touchenabled : "on",
            onHoverStop : "off",

            navOffsetHorizontal : 0,
            navOffsetVertical : 20,

            hideCaptionAtLimit : 0,
            hideAllCaptionAtLilmit : 0,
            hideSliderAtLimit : 0,

            stopAtSlide : -1,
            stopAfterLoops : -1,

            shadow : 0,
            fullWidth:"on"
        });

        // navigation over API
        $('.nav-icons a').click(function() {
            return false;
        });

        $(".nav-icons .slider-right").click(function() {
            $mainSlider.revnext();
        });
        $(".nav-icons .slider-left").click(function() {
            $mainSlider.revprev();
        });
        $(".nav-icons .toogle-pause-resume").click(function() {
            if ($(this).hasClass('slider-resume')) {
                $mainSlider.revresume();
                $(this).removeClass('slider-resume').addClass('slider-pause');

            } else {
                $mainSlider.revpause();
                $(this).removeClass('slider-pause').addClass('slider-resume');
            }
        });

        $mainSlider.bind("revolution.slide.onloaded", function() {
            var numOfPins = $('.revslider-initialised li').length;

            var pins = '';
            for (var i = 0; i < numOfPins; i++) {
                pins += '<a href="#" class="slider-pin" data-n="' + (i + 1) + '"></a>';
            }

            $('.slider-pins').html(pins);
            $('.slider-pins a:first-child').addClass('selected');

            $('.slider-pins > a').click(function() {
                $mainSlider.revshowslide($(this).data('n'));
            });
        });
        $mainSlider.bind("revolution.slide.onchange", function(ev, data) {
            $('.slider-pins a').removeClass('selected').filter(':nth-child(' + data.slideIndex + ')').addClass('selected');
        });
    })();
    
    

    //  ==========
    //  = Search in focus =
    //  ==========
    var searchInFocus = function() {
        $(document).on({
            'focus': function() {
                $(this).parents('.navbar-menu-line').addClass('search-mode');
                repositionLine();
            },
            'blur' : function() {
                $(this).parents('.navbar-menu-line').removeClass('search-mode');
                repositionLine();
            }
        }, '.click-enabled .navbar-form .search-input');
        
        if(isTouch()) {
            $('.navbar-menu-line').removeClass('search-mode');
        }
    };
    

    //  ==========
    //  = Scroll event function =
    //  ==========
    var isScrolledIntoView = function(elem) {
        var docViewTop = $(window).scrollTop(),
            docViewBottom = docViewTop + $(window).height(),
            elemTop = elem.offset().top,
            elemBottom = elemTop + elem.height();

        return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
    };
    
    //  ==========
    //  = Sticky header shrink and scroll to top button =
    //  ==========
    var parallaxNavbar = function() {
        var shrinkTheNavbar = function() {
            if (isTouch()) {
                $('#shrinkableHeader, .navbar .nav > li > a, .navbar-form .search-form, .navbar-menu-line .divider-vertical, .navbar-form .btn-search, .navbar-head .social, .brand > img').removeAttr('style');
                
            } else {
                $('#shrinkableHeader').css({
                    paddingTop : 7,
                    paddingBottom : 10
                });
                $('.navbar .nav > li > a').css({
                    paddingTop : 10,
                    paddingBottom : 10
                });
                $('.navbar-form .search-form').css({
                    paddingTop : 12,
                    paddingBottom : 12
                });
                $('.navbar-menu-line .divider-vertical').css({
                    marginTop : 10
                });
                $('.navbar-form .btn-search').css({
                    top : 15
                });
                $('.navbar-head .social').css({
                    marginTop : 6
                });
                $('.brand > img').css({
                    maxHeight : 47
                });
            }
        };
        
        if(isTouch()) {
            $(window).off('scroll.onlyOnDesktop');
            shrinkTheNavbar();
        } else {
            $(window).on('scroll.onlyOnDesktop', function() {
                var scrollX = $(window).scrollTop(),
                    topOffset = 90,
                    multiplyFactor = (topOffset - scrollX) / topOffset;
    
                if ( ! $('body').hasClass('no-sticky') ) {
                    if (scrollX < topOffset) {
                        $('#shrinkableHeader').css({
                            paddingTop : 7 + 17 * multiplyFactor,
                            paddingBottom : 10 + 31 * multiplyFactor
                        });
                        $('.navbar .nav > li > a').css({
                            paddingTop : 10 + 13 * multiplyFactor,
                            paddingBottom : 10 + 12 * multiplyFactor
                        });
                        $('.navbar-form .search-form').css({
                            paddingTop : 12 + 14 * multiplyFactor,
                            paddingBottom : 12 + 11 * multiplyFactor
                        });
                        $('.navbar-menu-line .divider-vertical').css({
                            marginTop : 8 + 13 * multiplyFactor
                        });
                        $('.navbar-form .btn-search').css({
                            top : 15 + 14 * multiplyFactor
                        });
                        $('.navbar-head .social').css({
                            marginTop : 21 - 15 * scrollX / topOffset
                        });
                        $('.brand > img').css({
                            maxHeight : 63 - 16 * scrollX / topOffset
                        });
    
                        $('.parallax-slider').css({
                            marginTop : 0
                        });
                        $('.fullwidthbanner-subpage-container').css({
                            backgroundAttachment: 'scroll',
                            backgroundPosition: 'center top'
                        });
    
                    } else {
                        shrinkTheNavbar();
                        
                        if( topOffset > 100 ) {
                            return;
                        }
    
                        //  = Slider parallax effect =
                        $('.parallax-slider').css({
                            marginTop : (scrollX - topOffset) / 2
                        });
                        /*$('.fullwidthbanner-subpage-container').css({
                            backgroundPosition: 'center ' + (scrollX - topOffset) + 'px' 
                        });*/
                        $('.fullwidthbanner-subpage-container').css({
                            backgroundAttachment: 'fixed',
                            backgroundPosition: 'center 118px'
                        });
                    }
                }
    
                //  = Scroll to the top icon =
                if (scrollX > 60) {
                    $('#tothetop').fadeIn();
                } else {
                    $('#tothetop').fadeOut();
                }
            });
        }
    };
        
    
    
    //  ==========
    //  = Progress bars =
    //  ==========
    $('.progress .bar').data('width', $(this).width()).css({
        width : 0
    });
    $(window).scroll(function() {
        $('.progress .bar').each(function() {
            if (isScrolledIntoView($(this))) {
                $(this).css({
                    width : $(this).attr('aria-valuenow') + '%'
                });
            }
        });
    });
        
    

    //  ==========
    //  = Smooth scroll to the top of the page =
    //  ==========
    $("#tothetop").click(function() {
        $("html, body").animate({
            'scrollTop' : 0
        }, 2000);
        return false;
    });

    //  ==========
    //  = Collapse PLUS/MINUS =
    //  ==========
    $(".accordion-body").on("show", function() {
        $('span', $(this).prev()).html('<i class="icon-minus"></i>');
    });
    $(".accordion-body").on("hide", function() {
        $('span', $(this).prev()).html('<i class="icon-plus"></i>');
    });
    
    
    var resizableFunctions = function() {
        
        //  ========== 
        //  = Embedded video iframes = 
        //  ========== 
        $('iframe[src*="vimeo.com"], iframe[src*="youtube.com"]').each(function() {
            var $this = $(this);
            $this.css('height', parseInt( $this.width() * $this.attr('height') / $this.attr('width'), 10 ) );
        });
            
        
        //  ==========
        //  = Magic Line =
        //  ==========
        // @see http://css-tricks.com/jquery-magicline-navigation/
        (function() {
            var $el,
                leftPos, 
                newWidth, 
                $mainNav = $("#mainNavigation"),
                $magicLine = $("#magic-line");
            
            if( $magicLine.length < 1 ) {
                $mainNav.prepend('<li id="magic-line"></li>');
                $magicLine = $("#magic-line");
            }
    
            $magicLine.width($("#mainNavigation > .active").width())
                .css("left", $("#mainNavigation > .active").position().left)
                .data("origLeft", $magicLine.position().left).data("origWidth", $magicLine.width());
    
            $(document).on({
                'mouseenter': function(ev) {
                    $el = $(this);
    
                    if ($el.hasClass('dropdown') && typeof ev.isTrigger === 'undefined') {
                        $magicLine.hide();
                    } else {
                        $magicLine.show();
                    }
    
                    leftPos = $el.position().left;
                    newWidth = $el.width();
                    $magicLine.stop().animate({
                        left : leftPos,
                        width : newWidth
                    }, 250);
                },
                'mouseleave': function() {
                    $el = $(this);
    
                    $magicLine.stop().show().animate({
                        left : $magicLine.data("origLeft"),
                        width : $magicLine.data("origWidth")
                    }, 250);
    
                }
            }, '.click-enabled #mainNavigation > li' );
        })();
        
        if (!isTouch()) {
    
            //  ==========
            //  = Navbar dropdowns animated on hover =
            //  ==========
            //$('.click-enabled .navbar .dropdown-menu').hide();
            $(document).on({
                'mouseenter': function(ev) {
                    if ( typeof ev.isTrigger === 'undefined') {
                        $(this).find('.dropdown-menu').first().stop(true, true).delay(50).slideDown(100);
                    }
                },
                'mouseleave':  function() {
                    $(this).find('.dropdown-menu').first().stop(true, true).delay(100).hide();
                }
            }, '.click-enabled .navbar .dropdown');
        }
    };
    resizableFunctions();
    
    // trigger reposition of the magic line
    var repositionLine = function() {
        setTimeout(function() {
            $('#mainNavigation > li.active').trigger('mouseenter');
        }, 250);
    };
    
    // remove the jump to the top when clicked on a blank link
    $('.navbar a[href="#"]').click(function(ev) {
        ev.preventDefault();
    });
    
        

    //  ==========
    //  = Isotope plugin for portolio =
    //  ==========
    var $isotypeContainer = $('.isotope-container');
    $isotypeContainer.imagesLoaded(function() {
        $isotypeContainer.isotope({
            itemSelector : '.isotope-tile',
            layoutMode : 'fitRows'
        });
    });

    $('#isotopeNavBtns > a').click(function() {
        var selector = $(this).attr('data-filter');
        $isotypeContainer.isotope({
            filter : selector
        });
        $(this).addClass('btn-green').siblings('.btn-green').removeClass('btn-green');

        return false;
    });

    //  ==========
    //  = Accordion group bg color change =
    //  ==========
    $('.accordion-group .accordion-toggle').click(function() {
        var $accordionGroup = $(this).parent().parent();
        if ($accordionGroup.hasClass('active')) {
            $accordionGroup.removeClass('active');
        } else {
            $accordionGroup.addClass('active').siblings().removeClass('active');
        }

    });

    //  ==========
    //  = Carousels =
    //  ==========
    $(window).load(function() {
        
        var configuration = {
            debug : false,
            auto : {
                play : false
            },
            width : '100%',
            height : "variable",
            items : {
                height : "variable"
            },
            prev : {},
            next : {},
            pagination : {},
            scroll : {
                duration : 1000
            },
            transition : true
        };

        $(".carouFredSel").each(function() {
            var $this = $(this);

            // prev and next buttons
            configuration.prev.button = $('#' + $this.data('nav') + 'Left');
            configuration.next.button = $('#' + $this.data('nav') + 'Right');

            // responsive param
            if ($this.data('responsive')) {
                configuration.responsive = true;
            } else {
                configuration.responsive = false;
            }

            // bullets if needed
            if ($this.data('pins')) {

                if ($this.data('pins') === 'siblings') {
                    configuration.pagination.container = $this.siblings('.pins');
                } else {
                    configuration.pagination.container = $($this.data('pins'));
                    // jQuery Selector
                }

                configuration.pagination.anchorBuilder = function(nr) {
                    return '<a href="#' + nr + '" class="slider-pin"></a> ';
                };
            }

            // onCreate the slides should not be wider than the container, no matter what
            configuration.onCreate = function() {
                $this.find('.slide').css({
                    width : $this.parent().width()
                });
            };
            // RUN THE CAROUSEL
            $this.carouFredSel(configuration);
        });

        // vertical carouFredSel
        $('.carouFredSel-vertical').each(function() {
            var $this = $(this);

            $this.carouFredSel({
                direction : 'vertical',
                items : 3,
                auto : {
                    play : false
                },
                prev : {
                    button : $('#' + $this.data('nav') + 'Left')
                },
                next : {
                    button : $('#' + $this.data('nav') + 'Right')
                },
                scroll : 1
            });
        });
    });
    

    var doit;
    function resizedw() {
        if( ! $('html').hasClass('lt-ie9')) {
            $(".carouFredSel").each(function() {
                var $this = $(this);
                $this.find('.slide').css({
                    //maxWidth : $this.parent().width()
                    width : $this.parent().width()
                });
    
                $this.trigger('configuration', ['debug', false, true]);
            });
            
            searchInFocus();
            resizableFunctions();
            parallaxNavbar();
        }
    }

    $(window).resize(function() {
        if(isTouch()) {
            $('body').addClass('touch-enabled').removeClass('click-enabled');
        } else {
            $('body').addClass('click-enabled').removeClass('touch-enabled');
        }
        
        clearTimeout(doit);
        doit = setTimeout(function() {
            resizedw();
        }, 250);
    });

    //  ==========
    //  = Contact form slider =
    //  ==========
    $('#contactFormSlider a').click(function() {
        if ('#first' === $(this).attr('href')) {
            $('.inner-slide-pane').animate({
                marginLeft : 0
            });
            $('#contactFormsContainer').css({
                height: $('#contactFormsContainer .slide-pane:nth-child(1)').height()
            });
        } else {
            $('.inner-slide-pane').animate({
                marginLeft : '-100%'
            });
            $('#contactFormsContainer').css({
                height: $('#contactFormsContainer .slide-pane:nth-child(2)').height()
            });
        }
    });
    $('#contactFormSlider li.active a').trigger('click');
    
    if( $('#contactFormSlider').length > 0 && isTouch() ) {
        $(window).resize(function() {
            var parent = $('#contactFormSlider').siblings('.tab-content');
            parent.find('.slide-pane').css({
                width: parent.width()
            });
        });
    }
    
        

    //  ==========
    //  = Media Element Player =
    //  @see https://github.com/johndyer/mediaelement/
    //  ==========
    $('audio').mediaelementplayer({
        alwaysShowControls : true,
        audioWidth: '100%'
    });

    //  ==========
    //  = Smoothly close the parent element =
    //  ==========
    $('.smooth-close-parent').click(function(ev) {
        ev.preventDefault();
        $(this).parent().fadeTo(500, 0, function() {
            $(this).delay(100).slideUp(500);
        });
    });

    //  ==========
    //  = Add prettyPhoto for images with class .add-prettyphoto =
    //  ==========
    $('.add-prettyphoto').prettyPhoto({
        default_width : 720,
        default_height : 405
    });

    //  ==========
    //  = Google Maps API with GoMap jQuery plugin =
    //  ==========
    if (jQuery.goMap) {
        $('#gmap').goMap({
            navigationControl: false,
            scaleControl: false,
            markers : [{
                address : 'Leatherhead, Surrey United Kingdom 01372 818123, United Kingdom', /* change your adress here */
                title : 'W Balls HTML Theme - ThemeForest', /* title information */
                icon : {
                    image : 'images/pin_red.png' /* your custom icon file */
                }
            }],
            scrollwheel : false,
            zoom : 13,
            maptype : 'ROADMAP'
        });
    }
    
    //  ========== 
    //  = Flickr Feed = 
    //  ========== 
    $('#flickrGallery').jflickrfeed({
        limit: 6,
        qstrings: {
            id: '52617155@N08'
        },
        itemTemplate: '<div class="picture">' +
                            '<a href="{{image}}" class="add-prettyphoto"> <img src="{{image_q}}" alt="{{title}}"> <span class="img-overlay"> <span class="plus"><i class="icon-plus"></i></span> </span> </a>' +
                      '</div>'
    }, function () {
        $(this).find('.add-prettyphoto').prettyPhoto();
    });
    
    
    //  ========== 
    //  = Custom select menu = 
    //  ========== 
    var is_touch_device = 'ontouchstart' in document.documentElement;
    if( ! is_touch_device ) {
        $('select').customSelectMenu();
    }
    
    
    
    //  ========== 
    //  = Triggers = 
    //  ========== 
    $(window).trigger('scroll').trigger('resize');
    
});

