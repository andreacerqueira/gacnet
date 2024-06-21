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


// Enqueue custom admin scripts
function deia_enqueue_custom_admin_scripts() {
    if (current_user_can('musician')) {
        // Enqueue jQuery Validate from CDN
        wp_enqueue_script('jquery-validate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js', array('jquery'), '1.19.3', true);
        
        // Enqueue WordPress media scripts
        wp_enqueue_media();
        
        // Enqueue custom admin JavaScript
        wp_enqueue_script('deia-custom-admin-js', get_stylesheet_directory_uri() . '/deia/deia-admin.js', array('jquery', 'jquery-validate', 'wp-mediaelement'), null, true);
        
        // Localize script to pass data from PHP to JavaScript
        // Optionally, you can use `wp_localize_script` to pass PHP data to your JavaScript file. In this example, `deiaAdminParams` is the object name in JavaScript, and ajaxUrl is an example of a parameter that could be passed to your script.
        // wp_localize_script('deia-custom-admin-js', 'deiaAdminParams', array(
        //     'ajaxUrl' => admin_url('admin-ajax.php'),
        //     // Add more parameters as needed
        // ));

        // Enqueue custom admin styles
        wp_enqueue_style('deia-custom-admin-styles', get_stylesheet_directory_uri() . '/deia/deia-admin.css');
    }
}
add_action('admin_enqueue_scripts', 'deia_enqueue_custom_admin_scripts');


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


// Filter to show only musician's own posts
function deia_show_only_musician_own_posts($query) {
    if (is_admin() && $query->is_main_query() && current_user_can('musician')) {
        $query->set('author', get_current_user_id());
    }
}
add_action('pre_get_posts', 'deia_show_only_musician_own_posts');


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


// Function to save musician details meta box
function deia_save_musician_details($post_id) {
    // Ensure nonce verification is successful
    if (!isset($_POST['musician_details_nonce']) || !wp_verify_nonce($_POST['musician_details_nonce'], 'deia_save_musician_details')) {
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

    // Define the fields to sanitize
    $fields = array(
        'musician_email',
        'musician_website',
        'musician_spotify',
        'musician_youtube',
        'musician_vimeo',
        'musician_twitter',
        'musician_facebook',
        'musician_tiktok',
        'musician_bio',
        'musician_header_image',
        'musician_image',
    );

    // Sanitize and update musician details
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            switch ($field) {
                case 'musician_email':
                    $musician_email = sanitize_email($_POST[$field]);
                    if (!empty($musician_email) && !is_email($musician_email)) {
                        return; // Exit early if email is invalid
                    }
                    update_post_meta($post_id, $field, $musician_email);
                    break;
                case 'musician_website':
                case 'musician_spotify':
                case 'musician_youtube':
                case 'musician_vimeo':
                case 'musician_twitter':
                case 'musician_facebook':
                case 'musician_tiktok':
                    $url = esc_url_raw($_POST[$field]);
                    if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
                        return; // Exit early if URL is invalid
                    }
                    update_post_meta($post_id, $field, $url);
                    break;
                case 'musician_bio':
                    $musician_bio = wp_kses_post($_POST[$field]); // Allow basic HTML tags
                    update_post_meta($post_id, $field, $musician_bio);
                    break;
                case 'musician_header_image':
                    $image_header_id = intval($_POST[$field]); // Ensure it's a valid URL
                    update_post_meta($post_id, $field, $image_header_id);
                    break;
                case 'musician_image':
                    $image_id = intval($_POST[$field]); // Ensure it's a valid URL
                    update_post_meta($post_id, $field, $image_id);
                    break;
                default:
                    $value = sanitize_text_field($_POST[$field]);
                    update_post_meta($post_id, $field, $value);
                    break;
            }
        }
    }
}
add_action('save_post', 'deia_save_musician_details');
