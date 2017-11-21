(function($) {
    $('.mailchimp-form').submit(function(event){

        var name = $('.mailchimp-form #form-name').val();
        var email = $('.mailchimp-form #form-email').val();

        var form = $('.insights-articles__main-form-done');

        event.preventDefault();
        $.ajax({
            url: ajaxmailchimp.ajaxurl,
            dataType: 'script',
            type: 'post',
            data: {
                action: 'ajax_mailchimp',
                name: name,
                email: email
            },
            success: function( result ) {
                if ( result == 'true') {
                    form.show().css('display', 'flex');
                } else {
                    form.show().css('display', 'flex');
                }
            }
            
        })

    });
})(jQuery);