<?php
// Add musician role with custom capabilities
function deia_add_musician_role() {
    add_role(
        'musician',
        'Musician',
        array(
            'read' => true,
            'edit_posts' => true,
            'delete_posts' => false,
            'publish_posts' => true,
            'upload_files' => true,
        )
    );
}
add_action('init', 'deia_add_musician_role');


// Ensure the musician role has the required capabilities
function deia_add_musician_capabilities() {
    $role = get_role('musician');
    if (!$role) {
        return; // Role not registered yet
    }
    $role->add_cap('read');
    $role->add_cap('edit_posts');
    $role->add_cap('edit_published_posts');
    $role->add_cap('delete_posts');
    $role->add_cap('delete_published_posts');
    $role->add_cap('publish_posts');
    $role->add_cap('upload_files');
}
add_action('admin_init', 'deia_add_musician_capabilities');


/**
* UI Hide Elements ------------------------------------------------------------------------
*/
require get_stylesheet_directory() . '/deia/musician-ui-hide.php';


/**
* Redirect musicians to their custom page upon login --------------------------------------
*/
// require get_stylesheet_directory() . '/deia/musician-redirect.php';


/**
* Publish / Save --------------------------------------------------------------------------
*/
require get_stylesheet_directory() . '/deia/musician-publish.php';


/**
* UI Admin Callbacks ----------------------------------------------------------------------
*/
require get_stylesheet_directory() . '/deia/musician-ui-callback.php';


// Enqueue custom js scripts
function deia_enqueue_custom_admin_scripts() {
    if (current_user_can('musician')) {
        wp_enqueue_script('deia-custom-admin-js', get_stylesheet_directory_uri() . '/deia/deia-admin.js', array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'deia_enqueue_custom_admin_scripts');


// Enqueue custom styles
function deia_enqueue_custom_admin_styles() {
    if (current_user_can('musician')) {
        wp_enqueue_style('deia-custom-admin-styles', get_stylesheet_directory_uri() . '/deia/deia-admin.css');
    }
}
add_action('admin_enqueue_scripts', 'deia_enqueue_custom_admin_styles');


// Disable autosave, revisions, and autosave notice for musicians
function deia_customize_admin_for_musicians() {
    if (current_user_can('musician')) {
        // Disable autosave
        wp_deregister_script('autosave');

        // Remove revisions meta box
        remove_meta_box('revisionsdiv', 'post', 'normal');

        // Remove autosave notice
        remove_action('admin_print_footer_scripts', 'wp_print_autosave_notice');
    }
}
add_action('admin_enqueue_scripts', 'deia_customize_admin_for_musicians');


// Modify capabilities for the musician role
function restrict_musician_media_library_access() {
    // Get the musician role object
    $role = get_role('musician');
    
    // Check if the role object is retrieved successfully
    if ($role !== null) {
        // Remove the capability to upload files
        $role->remove_cap('upload_files');
    }
}
add_action('init', 'restrict_musician_media_library_access');


// Restrict media library access to the user's own uploads
function restrict_media_library_access($query) {
    if (!current_user_can('administrator')) {
        global $pagenow;

        if (in_array($pagenow, array('upload.php', 'admin-ajax.php'))) {
            if (isset($query->query['post_type']) && $query->query['post_type'] === 'attachment') {
                $query->set('author', get_current_user_id());
            }
        }
    }
}
add_action('pre_get_posts', 'restrict_media_library_access');


// Ensure the new post is saved in the "musicians" category
function deia_pre_fill_musician_category($post_id, $post, $update) {
    // Check if this is a new post
    if ($update) {
        return;
    }

    // Get the "musicians" category ID
    $musicians_category = get_category_by_slug('musicians');
    $musicians_category_id = $musicians_category ? $musicians_category->term_id : 0;

    // Assign the "musicians" category to the new post
    if ($musicians_category_id) {
        wp_set_post_categories($post_id, array($musicians_category_id));
    }
}
add_action('wp_insert_post', 'deia_pre_fill_musician_category', 10, 3);


// Handle delete post action
function deia_handle_delete_post() {
    if (isset($_POST['action']) && $_POST['action'] === 'deia_delete_post') {
        if (isset($_POST['post_id']) && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'deia-delete-post-nonce')) {
            $post_id = intval($_POST['post_id']);
            if (current_user_can('delete_post', $post_id)) {
                wp_delete_post($post_id, true); // Set second parameter to true to force delete permanently
                wp_safe_redirect( $_SERVER['HTTP_REFERER'] ); // Redirect back to the previous page
                exit;
            }
        }
    }
}
add_action('admin_post_deia_delete_post', 'deia_handle_delete_post');


// Function to save musician details meta box
function deia_save_musician_details($post_id) {
    // error_log('Save post hook triggered for post ID: ' . $post_id);

    // Ensure nonce verification is successful
    if (!isset($_POST['musician_details_nonce']) || !wp_verify_nonce($_POST['musician_details_nonce'], 'deia_save_musician_details')) {
        // error_log('Nonce verification failed.');
        return;
    }

    // Prevent saving during autosave or ajax requests
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // var_dump($_POST);
    // exit;

    // Sanitize and update musician details
    $fields = array(
        'musician_email',
        'musician_website',
        'musician_spotify',
        'musician_youtube',
        'musician_vimeo',
        'musician_twitter',
        'musician_facebook',
        'musician_tiktok',
        'musician_events',
        'musician_bio',
        'musician_image',
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
    
    // Handle image upload separately to ensure security
    if (isset($_POST['musician_image'])) {
        update_post_meta($post_id, 'musician_image', sanitize_text_field($_POST['musician_image']));
    }
}
add_action('save_post', 'deia_save_musician_details');


// Filter to show only musician's own posts
function deia_show_only_musician_own_posts($query) {
    if (is_admin() && $query->is_main_query() && current_user_can('musician')) {
        $query->set('author', get_current_user_id());
    }
}
add_action('pre_get_posts', 'deia_show_only_musician_own_posts');


// Enqueue scripts for media uploader
function deia_enqueue_media_uploader_scripts($hook) {
    global $post;

    if ($hook == 'post-new.php' || $hook == 'post.php') {
        if ('post' === $post->post_type) {
            wp_enqueue_media();

            // Include custom script to handle media uploader
            wp_enqueue_script('deia-media-uploader', get_template_directory_uri() . '/js/media-uploader.js', array('jquery'), null, true);

            // Pass nonce and current post ID to the script
            wp_localize_script('deia-media-uploader', 'deiaMediaUploader', array(
                'nonce' => wp_create_nonce('deia-media-uploader-nonce'),
                'post_id' => $post->ID,
            ));
        }
    }
}
add_action('admin_enqueue_scripts', 'deia_enqueue_media_uploader_scripts');


// Filter to allow musicians to edit their own posts only
function deia_allow_musician_edit_own_posts($allcaps, $cap, $args) {
    if (isset($args[2]) && 'edit_post' == $args[0]) {
        $post_id = $args[2];
        $post = get_post($post_id);

        // Check if $post is null or not a valid WP_Post object
        if (!$post || !is_a($post, 'WP_Post')) {
            return $allcaps;
        }

        // Check if the current user can edit this post
        if ($post->post_author == get_current_user_id()) {
            $allcaps['edit_posts'] = true;
        }
    }
    return $allcaps;
}
add_filter('user_has_cap', 'deia_allow_musician_edit_own_posts', 10, 3);