showLoading = function () {
    $('body').css({'overflow': 'hidden'});
    $('#page-loading').show();
};

hideLoading = function () {
    $('#page-loading').hide();
    $('body').css({'overflow': 'auto'});
};

$(document).on('ready pjax:end', function () {
    hideLoading();
});

$(document).on('pjax:end', function () {
    $("[data-toggle='tooltip']").tooltip();
});


$(function () {
    $("[data-toggle='tooltip']").tooltip();
    $("[data-toggle='popover']").popover();

});

$(window).on('load', function () {
    hideLoading();
});
