jQuery(document).ready(function ($) {

  // Preventing dragging and dropping things around -----------------------------
  $("#poststuff").find(".meta-box-sortables").removeClass("meta-box-sortables");

  // Remove the 2 columns layout ------------------------------------------------
  $('.role-musician #post-body').removeClass('columns-2').addClass('columns-1');



  // JavaScript for media uploader ----------------------------------------------

  // Function to initialize media library with 'select' frame type
  function initializeMediaLibrary(frameTitle, imagePreviewSelector, hiddenInputSelector) {
    // Check if wp object and media library frames are defined
    if (typeof wp !== 'undefined' && wp.media && wp.media.frames) {
      // Clear previous file frame instance if exists
      wp.media.frames.file_frame = undefined;

      // Reinitialize media library with slight delay to clear previous instance
      setTimeout(function() {
        wp.media.frames.file_frame = wp.media({
          title: frameTitle,
          frame: 'select', // Force the Upload Files tab to be selected
          multiple: false
        });

        // When an image is selected, run a callback.
        wp.media.frames.file_frame.on('select', function() {
          var attachment = wp.media.frames.file_frame.state().get('selection').first().toJSON();
          $(hiddenInputSelector).val(attachment.id);
          $(imagePreviewSelector).attr('src', attachment.url);
        });

        // Open the media uploader.
        wp.media.frames.file_frame.open();
      }, 100); // Adjust delay as needed
    } else {
      console.error('WordPress media or frames not properly initialized.');
    }
  }

  // Header upload image click event
  $('#upload_musician_header_image_button').click(function(e) {
    e.preventDefault();
    initializeMediaLibrary(
      'Choose/Upload Header Image',
      '#image-preview-header',
      '#musician_header_image'
    );
  });

  // Band upload image click event
  $('#upload_musician_image_button').click(function(e) {
    e.preventDefault();
    initializeMediaLibrary(
      'Choose/Upload Musician Image',
      '#image-preview-musician',
      '#musician_image'
    );
  });



  // Validation -----------------------------------------------------------------

  // Custom validation method for player embed code
  $.validator.addMethod("validPlayerEmbed", function(value, element) {
    // console.log("Validating player embed code:", value);
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