jQuery(document).ready(function ($) {
    // Media uploader
    var mediaUploader;

    // Image upload button click event
    $('#backgrounder_upload_image').on('click', function (e) {
        e.preventDefault();

        // If the media uploader instance exists, open the media library
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create the media uploader
        mediaUploader = wp.media({
            title: 'Select Background Image',
            button: {
                text: 'Insert Image'
            },
            multiple: false
        });

        // Media selection event
        mediaUploader.on('select', function () {
            var attachment = mediaUploader.state().get('selection').first().toJSON();

            // Set the selected image URL in the text field
            $('#background_image').val(attachment.url);
        });

        // Open the media library
        mediaUploader.open();
    });
});
