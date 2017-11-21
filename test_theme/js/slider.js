document.addEventListener('DOMContentLoaded', function () {

    $('.homepage__services-item__slider').slick({
        dots: false,
        arrows: true,
        draggable: false,
        infinite: true,
        focusOnSelect: false,
/*      autoplaySpeed: 1800,   */
        autoplay: false,
        slidesToShow: 1

    });


    $('.clients-more__testimonials-slider-box').slick({
        dots: false,
        arrows: false,
        draggable: true,
        infinite: true,
        adaptiveHeight: true,
        focusOnSelect: true,
        autoplaySpeed: 3600,
        autoplay: true,
        slidesToShow: 1
    });


    $('.clients-more__clients-slider').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2500,
        focusOnSelect: true,
        arrows: true,
        infinite: true,
        responsive: [
            {
                breakpoint: 1700,
                settings: {
                    arrows: true,
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 1300,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 850,
                settings: {
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 1
                }
            }
        ]
    });


    $('.clients-finance__clients-slider, .clients-human__clients-slider, .clients-procurement__clients-slider, .clients-information__clients-slider, .clients-functional__clients-slider, .clients-sector__clients-slider').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2500,
        focusOnSelect: true,
        arrows: true,
        infinite: true,
        responsive: [
            {
                breakpoint: 1700,
                settings: {
                    arrows: true,
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 1300,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 850,
                settings: {
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 1
                }
            }
        ]
    });


/*
    $('.shared-services-more__clients-slider').slick({
        dots: true,
        arrows: true,
        draggable: true,
        infinite: true,
        autoplaySpeed: 3000,
        autoplay: true,
        /!*      speed: 300,*!/
        slidesToShow: 1
        /!* prevArrow: '<button class="slick-prev" type="button"><span class="lnr lnr-chevron-left"></span></button>',
         nextArrow: '<button class="slick-next" type="button"><span class="lnr lnr-chevron-right"></span></button>'*!/
    });
*/


});


