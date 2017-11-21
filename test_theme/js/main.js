document.addEventListener("DOMContentLoaded", function () {

    $("#nav_toggle_wrapper").click(function () {
        /*        $('.header-nav__box').toggleClass('active');*/
        $("#nav_toggle").toggleClass("active");
        $(".header-nav__box-wrapper").toggleClass("active");
        $("html,body").toggleClass("active");
        event.preventDefault();
    });

    /*
        // Modal window
        $('.form-popup').click(function () {
            $('.overlay, body').addClass('active');
            event.preventDefault();
        });

        $('.close').click(function() {
            $('.overlay, body').removeClass('active');
            event.preventDefault();
        });
    */


    /*

        $('.header-top__info > li, .homepage__metric-menu > li, .header-top__popup-title').click(function () {
                        if ($(window).width() > 991) {
                            if ($(this).hasClass('active')) {
                                $(this).toggleClass('active');
                            } else {
                                $('.header-top__info > li.active, .homepage__metric-menu > li.active, .header-top__popup-title.active').removeClass('active');
                                $(this).toggleClass('active');
                            }
                        }
                        event.preventDefault();
                    });

    */

/*
    $('.header-top__cross2').click(function () {
        if ($(this).hasClass('active')) {
            $(this).toggleClass('active');
            $(this).siblings('.header-top__popup-title').toggleClass('active');
            $(this).parent().siblings('ul').toggleClass('active');
        } else {
            $('.header-top__popup-nav').find('.active').removeClass('active');
            $(this).toggleClass('active');
            $(this).siblings('.header-top__popup-title').toggleClass('active');
            $(this).parent().siblings('ul').toggleClass('active');
        }
    });

*/


    $('.header-top__cross2').click(function () {
        if ($(this).hasClass('active')) {
            $(this).parent('li').toggleClass('active');
            $(this).toggleClass('active');
            $('.header-top__popup-nav.active').removeClass('active');
        } else {
            $('.header-top__popup-nav').find('.active').removeClass('active');
            $('.header-top__popup-nav.active').removeClass('active');
            $(this).toggleClass('active');
            $(this).parent('li').toggleClass('active');
            $(this).parent('li').find('.header-top__popup-nav').toggleClass('active');

/*            $('.header-top__link-popup ul li div > .sub-menu').parent('div').css("display", "none");
            $(this).siblings("div").css("display", "block");*/

        }
    });

    $('.header-top__cross').click(function () {
        if ($(this).hasClass('active')) {
            $(this).toggleClass('active');
            $(this).parent('li').toggleClass('active');
        } else {
            /* $('.header-top__cross.active').removeClass('active'); */
            $('.header-nav__box').find('.active').removeClass('active');
            $(this).toggleClass('active');
            $(this).parent('li').toggleClass('active');
        }

    });


    $('.events-menu > li').on('click', function () {
        event.preventDefault();
        if ($(this).hasClass('active')) {
        } else {
            $('.events-menu > li.active').removeClass('active');
            var linkAttr = $(this).attr('data-attr');
            $('.events__main-content').removeClass('only-events only-webinars').addClass(linkAttr);
            $(this).addClass('active');
        }

    });


    $('label').click(function () {
        if ($(this).find('input:checked').length) {
            $(this).addClass('active');
        } else {
            $(this).removeClass('active');
        }
    });


    $(window).on('load scroll', function () {
        var pageScroll = $(window).scrollTop();
        if (pageScroll > 180) {
            $('.header-top__fixed').addClass('active')
        } else if (pageScroll < 180) {
            $('.header-top__fixed').removeClass('active')
        }
    });


    /*    $('section').on('click', function() {
            $('.header-nav__box.active, #nav_toggle.active').removeClass('active');
        });*/


    $(window).on('load resize', function () {
        var h = $(this).height();
        if (h <= 400) {
            $('.menu-main-menu-container').css({maxHeight: '127px'});
        } else if (h <= 450) {
            $('.menu-main-menu-container').css({maxHeight: '160px'});
        } else if (h <= 500) {
            $('.menu-main-menu-container').css({maxHeight: '252px'});
        } else if (h <= 991) {
            $('.menu-main-menu-container').css({maxHeight: '310px'});
        }
    });


});
