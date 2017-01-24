$(document).ready(function(){
    $('#pay_system').change(function () {
        $('#not_have_payment_detail').hide();
        var pay_id = $(this).val();
        if(!pay_id) {
            $('#payment_detail').val('');
            return false;
        }

        $.ajax({
            url: "/pay/get-payment",
            type: "post",
            data: {
                pay_id: pay_id
            },
            success: function(data){
                if(data) {
                    $('#payment_detail').val(data);
                } else {
                    $('#payment_detail').val('');
                    $('#not_have_payment_detail').show();
                }
            },
            error: function () {
                alert('ajax error');
            }
        });
    });
});
