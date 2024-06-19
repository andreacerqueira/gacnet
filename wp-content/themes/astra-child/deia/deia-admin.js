// jQuery(document).ready(function ($) {
//   if ($("body").hasClass("role-musician")) {
//     $("#postdivrich").hide(); // Hide the content editor
//     $("#minor-publishing-actions").hide(); // Hide the "Save Draft" and "Preview" buttons
//     $("#save-post").hide(); // Hide the "Save as Draft" button
//   }
// });

// jQuery(document).ready(function($) {
//     $('.delete-post').on('click', function(e) {
//         e.preventDefault();
//         var postId = $(this).data('post-id');
//         var nonce = '<?php echo wp_create_nonce('deia-delete-post-nonce'); ?>';

//         $.ajax({
//             type: 'POST',
//             url: ajaxurl,
//             data: {
//                 action: 'deia_delete_post',
//                 nonce: nonce,
//                 post_id: postId
//             },
//             success: function(response) {
//                 console.log(response);
//                 // Handle success, e.g., remove the deleted post from the UI
//             },
//             error: function(error) {
//                 console.error(error);
//                 // Handle error
//             }
//         });
//     });
// });
