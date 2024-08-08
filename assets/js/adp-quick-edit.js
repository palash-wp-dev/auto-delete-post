(function($) {
    "use strict";
    $(document).ready(function() {
        // Bind the event for opening the Quick Edit option
        $('#the-list').on('click', 'button.editinline', function() {
            // Get the post ID from the row being edited
            var post_id = $(this).closest('tr').attr('id').replace('post-', '');

            // Get the deletion time from the post meta
            var $delete_time = $('#post-' + post_id).find('.adp_post_deletion_time_column').text().trim();

            // Convert the time to the format required by datetime-local input
            var formatted_delete_time = formatDateTimeLocal($delete_time);

            // Set the deletion time in the Quick Edit field
            $('input[name="adp-time"]').val(formatted_delete_time);
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
                // Update the display for each post ID
                post_ids.forEach(function(id) {
                    $('#post-' + id).find('.adp_post_deletion_time_column').text(auto_delete_time.replace('T', ' '));
                });
            });
        });

        function formatDateTimeLocal(dateTimeStr) {
            // Convert datetime string "2024-08-15 01:03 AM" to "2024-08-15T01:03"
            var dateParts = dateTimeStr.split(' ');
            var date = dateParts[0];
            var time = dateParts[1] + ' ' + dateParts[2];
            var dateTime = new Date(date + ' ' + time);

            // Format the date and time parts
            var year = dateTime.getFullYear();
            var month = ('0' + (dateTime.getMonth() + 1)).slice(-2);
            var day = ('0' + dateTime.getDate()).slice(-2);
            var hours = ('0' + dateTime.getHours()).slice(-2);
            var minutes = ('0' + dateTime.getMinutes()).slice(-2);

            return year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
        }
    });
})(jQuery);