// JavaScript for media uploader ------------------------------------------------
jQuery(document).ready(function ($) {
  // Preventing dragging and dropping things around -----------------------------
  $("#poststuff").find(".meta-box-sortables").removeClass("meta-box-sortables");

  // Header upload image --------------------------------------------------------
  var mediaHeaderUploader;
  $("#upload_musician_header_image_button").click(function (e) {
    e.preventDefault();

    // If the media frame already exists, reopen it.
    if (mediaHeaderUploader) {
      mediaHeaderUploader.open();
      return;
    }

    // Create the media frame.
    mediaHeaderUploader = wp.media.frames.file_frame = wp.media({
      title: "Choose/Upload Header Image",
      multiple: false,
    });

    // When an image is selected, run a callback.
    mediaHeaderUploader.on("select", function () {
      var attachment = mediaHeaderUploader
        .state()
        .get("selection")
        .first()
        .toJSON();
      $("#musician_header_image").val(attachment.id);
      $("#image-preview-header").attr("src", attachment.url);
    });

    // Open the media uploader.
    mediaHeaderUploader.open();
  });

  // Remove the 2 columns layout ------------------------------------------------
  $('.role-musician #post-body').removeClass('columns-2').addClass('columns-1');

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
      title: "Choose/Upload Musician Image",
      multiple: false,
    });

    // When an image is selected, run a callback.
    mediaUploader.on("select", function () {
      var attachment = mediaUploader.state().get("selection").first().toJSON();
      $("#musician_image").val(attachment.id);
      $("#image-preview-musician").attr("src", attachment.url);
    });

    // Open the media uploader.
    mediaUploader.open();
  });

  // Validation -----------------------------------------------------------------

  // Custom validation method for player embed code
  $.validator.addMethod("validPlayerEmbed", function(value, element) {
    console.log("Validating player embed code:", value);
    // Simple validation to check if the value contains an iframe
    const isValid = this.optional(element) || /<iframe.*<\/iframe>/.test(value);
    // console.log("Validation result for player embed:", isValid);
    return isValid;
  }, "Please enter a valid player embed code.");

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
      musician_apple_music: {
        url: true,
      },
      musician_youtube_music: {
        url: true,
      },
      musician_youtube: {
        url: true,
      },
      musician_twitter: {
        url: true,
      },
      musician_facebook: {
        url: true,
      },
      musician_instagram: {
        url: true,
      },
      musician_tiktok: {
        url: true,
      },
      musician_player: {
        validPlayerEmbed: true,
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
      musician_apple_music: {
        url: "Please enter a valid URL.",
      },
      musician_youtube_music: {
        url: "Please enter a valid URL.",
      },
      musician_youtube: {
        url: "Please enter a valid URL.",
      },
      musician_twitter: {
        url: "Please enter a valid URL.",
      },
      musician_facebook: {
        url: "Please enter a valid URL.",
      },
      musician_instagram: {
        url: "Please enter a valid URL.",
      },
      musician_tiktok: {
        url: "Please enter a valid URL.",
      },
      musician_player: {
        validPlayerEmbed: "Please enter a valid player embed code.",
      },
    },
    errorPlacement: function(error, element) {
      console.error("Validation error:", error.text());
      error.appendTo(element.parent());
    },
    invalidHandler: function(event, validator) {
      console.error("Invalid form submission:", validator);
      event.preventDefault(); // Prevent form submission
    },
    submitHandler: function(form) {
      console.log("Form is valid and ready for submission.");
      form.submit();
    }
  });
});
