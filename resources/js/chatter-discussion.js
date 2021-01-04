document.addEventListener("DOMContentLoaded", function() { 
    /* Initialize new response editor */
    new EasyMDE({element: document.getElementById('new-response-textarea')});

    /* Lookup for post edit editors */
    const editors = [];

    /* Get and process all posts on the page */
    for (const post of document.querySelectorAll('.post')) {
        /* Get the ID of the post */
        const postId = post.getAttribute('data-id');

        /* Get edit button and attach event listener */
        const btnEdit = post.querySelectorAll('.chatter_edit_btn');
        if (btnEdit.length) {
            const main = post.querySelectorAll('.main')[0];
            const body = post.querySelectorAll('.body')[0];

            btnEdit[0].addEventListener('click', e => {
                /* Add Editing class */
                post.classList.add('editing');

                /* Create a textarea */
                const textarea = document.createElement('textarea');
                textarea.setAttribute('id', 'post-edit-' + postId);
                
                /* Client side XSS fix */
                textarea.value = body.innerHTML;

                /* Add textarea to the post */
                main.insertBefore(textarea, main.firstChild);

                /* Create new editor from text area */
                editors[postId] = new EasyMDE({element: document.getElementById('post-edit-' + postId)});
            });
        }

        /* Get cancel edit button and attach event listener */
        const btnCancelEdit = post.querySelectorAll('.cancel_chatter_edit');
        if (btnCancelEdit.length) {
            btnCancelEdit[0].addEventListener('click', e => {
                /* Remove EasyMDE */
                editors[postId].toTextArea();
                editors[postId] = null;
                
                /* Remove the textarea */
                document.querySelector('#post-edit-' + postId).remove();

                /* Remove editing class */
                post.classList.remove('editing');
            });
        }

        /* Get cancel edit button and attach event listener */
        const btnSaveEdit = post.querySelectorAll('.update_chatter_edit');
        if (btnSaveEdit.length) {
            const form = post.querySelectorAll('.post-edit-form')[0];

            btnSaveEdit[0].addEventListener('click', e => {
                /* Get updated content */
                const body = editors[postId].value();

                /* Submit changes */
                form.querySelectorAll('input[name="body"]')[0].value = body;
                form.submit();
            });
        }

        /* Get delete post button and attach event listener */
        const btnDelete = post.querySelectorAll('.btn-delete-post');
        if (btnDelete.length) {
            const form = post.querySelectorAll('.post-delete-form')[0];

            btnDelete[0].addEventListener('click', e => {
                /* Submit changes */
                form.submit();
            });
        }
    }

    /* Add Listener to new response submit button */
    document.querySelector('#submit_response').addEventListener('click', event => {
        document.querySelector('#chatter_form_editor').submit();
    });
});