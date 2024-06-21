// JavaScript for media uploader ------------------------------------------------
jQuery(document).ready(function ($) {
  // Preventing dragging and dropping things around -----------------------------
  $("#poststuff").find(".meta-box-sortables").removeClass("meta-box-sortables");

  // Band upload image ----------------------------------------------------------
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
      $("#image-preview").attr("src", attachment.url); // Update the image preview
    });

    // Open the media uploader.
    mediaUploader.open();
  });

  // Validation -----------------------------------------------------------------
  $("#post").validate({
    rules: {
      musician_email: {
        // required: true,
        email: true,
      },
      musician_website: {
        url: true,
      },
      musician_spotify: {
        url: true,
      },
      musician_youtube: {
        url: true,
      },
      musician_vimeo: {
        url: true,
      },
      musician_twitter: {
        url: true,
      },
      musician_facebook: {
        url: true,
      },
      musician_tiktok: {
        url: true,
      },
    },
    messages: {
      musician_email: {
        // required: "Please enter an email address.",
        email: "Please enter a valid email address.",
      },
      musician_website: {
        url: "Please enter a valid URL.",
      },
      musician_spotify: {
        url: "Please enter a valid URL.",
      },
      musician_youtube: {
        url: "Please enter a valid URL.",
      },
      musician_vimeo: {
        url: "Please enter a valid URL.",
      },
      musician_twitter: {
        url: "Please enter a valid URL.",
      },
      musician_facebook: {
        url: "Please enter a valid URL.",
      },
      musician_tiktok: {
        url: "Please enter a valid URL.",
      },
    },
    submitHandler: function (form) {
      form.submit();
    },
  });
});
