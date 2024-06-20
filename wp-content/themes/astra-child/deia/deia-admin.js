// JavaScript for media uploader
jQuery(document).ready(function ($) {
  var mediaUploader;

  $("#upload_musician_image_button").click(function (e) {
    e.preventDefault();

    // If the media frame already exists, reopen it.
    if (mediaUploader) {
      mediaUploader.open();
      return;
    }

    // Create the media frame.
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: "Choose Image",
      button: {
        text: "Choose Image",
      },
      multiple: false, // Allow only single image upload
    });

    // When an image is selected, run a callback.
    mediaUploader.on("select", function () {
      var attachment = mediaUploader.state().get("selection").first().toJSON();
      $("#musician_image").val(attachment.id);
      $("img").attr("src", attachment.url);
    });

    // Open the media uploader.
    mediaUploader.open();
  });
});
