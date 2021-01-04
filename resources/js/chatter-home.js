import './bootstrap/tinymce';

/* Only if email notify is enabled */
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('notify_email').addEventListener('change', event => {
        const chatter_email_loader = $(event.target).find('.chatter_email_loader');
        
        chatter_email_loader.addClass('loading');

        $.post('/' + $('#current_path').val() + '/email', { '_token' : $('#csrf_token_field').val(), }, function(data){
            chatter_email_loader.removeClass('loading');

            if (data) {
                $('#email_notification').prop('checked', true);
            } else {
                $('#email_notification').prop('checked', false);
            }
        });
    });
});