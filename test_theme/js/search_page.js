document.addEventListener("DOMContentLoaded", function () {

    $(window).on('load resize scroll', function () {
        var scrollTop = $(this).scrollTop(),
            filter = $('.search__main'),
            h = $('.filter_search').height();

        if (scrollTop > 150) {
            $('.sidebar').css({top: '150px'});
        }  else if (scrollTop < 150) {
            $('.sidebar').css({top: ''});
        }

        if (scrollTop > h - 540) {
            filter.css({alignSelf: 'flex-end'});
            $('.sidebar').css({position: 'static'});
        } else if (scrollTop < h - 540) {
            filter.css({alignSelf: 'inherit'});
            $('.sidebar').css({position: 'fixed'});
        }
    });

    $('.search__main #uwpqsf_id_btn').on('click', function() {
        $('body,html').animate({scrollTop: 0}, 0)
    });


});
