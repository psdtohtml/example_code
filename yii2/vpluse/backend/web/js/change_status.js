$(document).ready(function () {
    $('.change_user_request_status').click(function () {
        var status = $(this).attr('status');
        var keys = $('#w2').yiiGridView('getSelectedRows');
        if(!keys) {
            return false;
        }
        $('#loader').show();
        $.ajax({
            url: "/rebate/user-request/update-status",
            type: "post",
            data: {
                status: status,
                keys: keys
            },
            success: function(data){
                if(data == 'ok') {
                    location.reload();
                }
                $('#loader').hide();
            },
            error: function () {
                alert('ajax error');
                $('#loader').hide();
            }
        });

    });
});
