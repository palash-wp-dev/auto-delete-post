(function($) {
    "use strict";
    $(document).ready(function(){
        // Bind the event for opening the Quick Edit option
        $('#the-list').on('click', 'a.editinline', function() {
            // Get the post ID from the row being edited
            var post_id = $(this).closest('tr').attr('id').replace('post-', '');

            // Get the deletion time from the post meta
            var $delete_time = $('#adp-time-' + post_id).data('delete-time');

            // Set the deletion time in the Quick Edit field
            $('input[name="adp-time"]').val($delete_time);
        });

        // Bind the event for saving the Quick Edit option
        $('#bulk-edit').on('click', '.save', function() {
            // Collect the post IDs being edited
            var post_ids = [];
            $('#bulk-edit tbody tr').each(function() {
                post_ids.push($(this).attr('id').replace('post-', ''));
            });

            // Collect the auto delete time value
            var auto_delete_time = $('input[name="adp-time"]').val();

            // Prepare the data to be sent via AJAX
            var data = {
                action: 'save_quick_edit',
                post_ids: post_ids,
                auto_delete_time: auto_delete_time,
                security: inlineEditPost.nonce // Add security nonce
            };

            // Send the data via AJAX
            $.post(ajaxurl, data, function(response) {
                // Handle the response here if needed
            });
        });
    });
})(jQuery);