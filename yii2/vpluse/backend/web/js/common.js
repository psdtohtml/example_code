$(document).ready(function () {
    $('.modal-button').click(function () {
        $('#zzz_modal').modal('show')
            .find('#modal_content')
            .load($(this).attr('value'));
    });
});
