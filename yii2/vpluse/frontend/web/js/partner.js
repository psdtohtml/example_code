$(document).ready(function(){
    $('#partner_link_generate').click(function () {
        var home_page = $('#home_page').val();
        var ref_code = $('#ref_code').val();
        var page = $('#partner_page').val();
        if(!page || !ref_code || !home_page) {
            $('#partner_link_result').text(home_page);
            return false;
        }

        var start_symbol;
        if(page.indexOf('/?') + 1) {
            start_symbol = '&';
        } else{
            start_symbol = '/?';
        }
        $('#partner_link_result').text(page + start_symbol + 'ref=' + ref_code);

    });
});
