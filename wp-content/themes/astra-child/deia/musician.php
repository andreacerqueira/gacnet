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


// Remove post editor for musicians
function deia_remove_post_editor_for_musicians() {
    if (current_user_can('musician')) {
        remove_post_type_support('post', 'editor'); // Remove the post editor
    }
}
add_action('init', 'deia_remove_post_editor_for_musicians');


// Hide admin menu items for musicians and add custom pages
function deia_customize_musician_admin_menu() {
    if (current_user_can('musician')) {
        // Remove unwanted menu items
        remove_menu_page('index.php'); // Dashboard
        remove_menu_page('edit.php'); // Posts
        remove_menu_page('upload.php'); // Media
        remove_menu_page('edit-comments.php'); // Comments
        remove_menu_page('tools.php'); // Tools
        remove_menu_page('options-general.php'); // Settings
        remove_menu_page('themes.php'); // Appearance
        remove_menu_page('users.php'); // Users
        remove_menu_page('plugins.php'); // Plugins
        remove_menu_page('profile.php'); // Profile

        // Add custom pages for musicians
        add_menu_page(
            'Musicians/Bands', // Page title
            'Musicians/Bands', // Menu title
            'read', // Capability
            'musicians_bands', // Menu slug
            'deia_musician_posts_page', // Callback function
            'dashicons-format-audio', // Icon
            2 // Menu position
        );

        add_menu_page(
            'Profile', // Page title
            'Profile', // Menu title
            'read', // Capability
            'musician_profile', // Menu slug
            'deia_musician_profile_page', // Callback function
            'dashicons-admin-users', // Icon
            3 // Menu position
        );
    }
}
add_action('admin_menu', 'deia_customize_musician_admin_menu', 999); // High priority


// Hide admin notices for musicians
function deia_hide_admin_notices_for_musicians() {
    if (current_user_can('musician')) {
        global $wp_filter;
        
        // Remove actions from 'admin_notices' and 'all_admin_notices'
        if (isset($wp_filter['admin_notices'])) {
            unset($wp_filter['admin_notices']);
        }
        if (isset($wp_filter['all_admin_notices'])) {
            unset($wp_filter['all_admin_notices']);
        }
    }
}
add_action('admin_head', 'deia_hide_admin_notices_for_musicians');


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


// // SAVE DRAFT
// // Conditionally display the save button for musicians
// function deia_display_save_button() {
//     global $pagenow;

//     if (current_user_can('musician') && in_array($pagenow, array('post-new.php', 'post.php'))) {
//         echo '<div id="publishing-action">';
//         echo '<button type="submit" name="save" id="save-post" class="button button-primary button-large">Save</button>';
//         echo '</div>';
//     }
// }
// add_action('edit_form_top', 'deia_display_save_button');


// PUBLISH
// Disable auto save revisions for musicians
function deia_disable_auto_save_revisions() {
    if (current_user_can('musician')) {
        remove_action('post_updated', 'wp_save_post_revision');
    }
}
add_action('init', 'deia_disable_auto_save_revisions');


// Custom publish button for musicians
function deia_custom_publish_button() {
    global $post;

    if (current_user_can('musician') && $post->post_type === 'post') {
        echo '<div id="publishing-action">';
        echo '<button type="submit" name="publish" id="publish" class="button button-primary button-large">Publish</button>';
        echo '</div>';
    }
}
add_action('edit_form_top', 'deia_custom_publish_button');


// Change save behavior to publish immediately for musicians and fix slug issue
function deia_publish_immediately($data, $postarr) {
    if (current_user_can('musician') && $postarr['post_type'] === 'post') {
        // Ensure post status is publish
        $data['post_status'] = 'publish';

        // var_dump($data);
        // var_dump($postarr);
        // echo "slug: " . $data['post_name'] . " | title: " . $data['post_title'];
        // exit;

        // Generate unique slug
        $post_title = isset($data['post_title']) ? $data['post_title'] : '';

        // Generate unique slug based on title
        $data['post_name'] = wp_unique_post_slug(sanitize_title($post_title), $postarr['ID'], $postarr['post_status'], $postarr['post_type'], $postarr['post_parent']);
    }
    return $data;
}
add_filter('wp_insert_post_data', 'deia_publish_immediately', 10, 2);


// Modify the <h1> heading on "Add New Post" and "Edit Post" pages for musicians
function deia_custom_post_headings() {
    if (current_user_can('musician')) {
        echo '<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                if (document.body.classList.contains("post-new-php")) {
                    document.querySelector(".wrap h1.wp-heading-inline").innerText = "Add New Musician/Band";
                } else if (document.body.classList.contains("post-php")) {
                    document.querySelector(".wrap h1.wp-heading-inline").innerText = "Edit Musician/Band";
                }
            });
        </script>';
    }
}
add_action('admin_head', 'deia_custom_post_headings');


// Change placeholder text for title input field
function deia_change_title_placeholder($title_placeholder) {
    global $post_type;

    if (current_user_can('musician') && $post_type === 'post') {
        $title_placeholder = 'Enter Name';
    }

    return $title_placeholder;
}
add_filter('enter_title_here', 'deia_change_title_placeholder');


//// Redirect musicians to their custom page upon login
// function deia_redirect_musician_dashboard() {
//     if ( is_user_logged_in() && current_user_can('musician') ) {
//         wp_redirect( admin_url('admin.php?page=musicians_bands') );
//         exit;
//     }
// }
// add_action( 'admin_init', 'deia_redirect_musician_dashboard' );



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


// Add a body class to indicate the user role for more precise targeting in JavaScript
function deia_add_body_class_for_musicians($classes) {
    if (current_user_can('musician')) {
        $classes .= ' role-musician';
    }
    return $classes;
}
add_filter('admin_body_class', 'deia_add_body_class_for_musicians');


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


// Callback function for "My Posts" page
function deia_musician_posts_page() {
    // Get the ID of the "musicians" category
    $musicians_category = get_category_by_slug('musicians');
    $musicians_category_id = $musicians_category ? $musicians_category->term_id : 0;

    echo '<div class="wrap">';
    echo '<h1>Musicians/Bands</h1>';
    echo '<p>Published artists:</p>';

    // Add "Add New Band" button with category pre-filled //ghost css: page-title-action
    echo '<a href="' . admin_url('post-new.php?post_category=' . $musicians_category_id) . '" class="button button-primary button-large">Add New Band</a>';

    // Display musician's posts
    deia_display_musician_posts(); // Function to display posts

    echo '</div>';
}


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


// Diable items from Admin top bar
function deia_customize_admin_bar($wp_admin_bar) {
    if (current_user_can('musician')) {
        // Remove default items
        $wp_admin_bar->remove_node('wp-logo'); // WordPress logo
        // $wp_admin_bar->remove_node('site-name'); // Site name
        $wp_admin_bar->remove_node('updates'); // Updates
        $wp_admin_bar->remove_node('comments'); // Comments
        $wp_admin_bar->remove_node('new-content'); // New content
        $wp_admin_bar->remove_node('hostinger_admin_bar'); // Hostinger admin bar item

        // Leave only the logoff item
        // $wp_admin_bar->remove_node('edit-profile'); // Profile submenu

        // Add a custom Website Title
        // $wp_admin_bar->add_node(array(
        //     'id' => 'gacnet_home',
        //     'title' => '&#x2730; GACNET',
        //     'href' => home_url(),
        //     'meta' => array(
        //         'class' => 'gacnet-home-button', // Optional CSS class for styling
        //         'target' => '_blank', // Optional target attribute for the link
        //     ),
        // ));

        // Add a custom logoff item if needed
        // $wp_admin_bar->add_node(array(
        //     'id' => 'log-out',
        //     'title' => 'Log Out',
        //     'href' => wp_logout_url(),
        // ));
    }
}
add_action('admin_bar_menu', 'deia_customize_admin_bar', 999);


// Custom function to display musician's own posts
function deia_display_musician_posts() {
    $current_user_id = get_current_user_id();

    $args = array(
        'post_type' => 'post',
        'author' => $current_user_id,
        'posts_per_page' => -1,
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        echo '<div class="deia-list-block">';
        echo '<ul>';
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $post_title = get_the_title();

            if (current_user_can('edit_post', $post_id)) {
                $edit_post_url = get_edit_post_link($post_id);
                
                // Form to delete post
                echo '<li>';
                echo '<a href="' . esc_url($edit_post_url) . '">' . esc_html($post_title) . '</a>';
                echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
                echo '<input type="hidden" name="action" value="deia_delete_post">';
                echo '<input type="hidden" name="post_id" value="' . esc_attr($post_id) . '">';
                echo '<input type="hidden" name="nonce" value="' . wp_create_nonce('deia-delete-post-nonce') . '">';
                echo '<input type="submit" value="Delete" onclick="return confirm(\'Are you sure you want to delete this post?\')" class="button">';
                echo '</form>';
                echo '</li>';
            } else {
                echo '<li>' . esc_html($post_title) . '</li>';
            }
        }
        echo '</ul>';
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p>No posts found.</p>';
    }
}

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


// Callback function for musician profile page
function deia_musician_profile_page() {
    echo '<div class="wrap">';
    echo '<h1>Profile</h1>';

    // Display profile form
    deia_musician_profile_form();

    echo '</div>';
}


// Function to display and handle musician profile form
function deia_musician_profile_form() {
    $current_user = wp_get_current_user();

    if (isset($_POST['submit_user_profile'])) {
        $user_id = get_current_user_id();
        $user_email = sanitize_email($_POST['user_email']);
        $user_pass = $_POST['user_pass'];
        $user_pass_confirm = $_POST['user_pass_confirm'];

        // Validate and update user email
        if (!empty($user_email) && is_email($user_email)) {
            wp_update_user(array('ID' => $user_id, 'user_email' => $user_email));
            $current_user = get_userdata($user_id); // Refresh current user data
            echo '<div style="color: green;">Email updated successfully.</div>';
        }

        // Validate and update user password
        if (!empty($user_pass) && !empty($user_pass_confirm) && $user_pass === $user_pass_confirm) {
            wp_set_password($user_pass, $user_id);
            echo '<div style="color: green;">Password updated successfully.</div>';
        } elseif (!empty($user_pass) || !empty($user_pass_confirm)) {
            echo '<div style="color: red;">Passwords do not match.</div>';
        }
    }

    ?>
    <form method="post" action="">
        <label for="user_login">Username:</label><br>
        <input type="text" id="user_login" name="user_login" value="<?php echo esc_attr($current_user->user_login); ?>" readonly /><br>

        <label for="user_email">Email:</label><br>
        <input type="email" id="user_email" name="user_email" value="<?php echo esc_attr($current_user->user_email); ?>" size="40" /><br>

        <label for="user_pass">New Password:</label><br>
        <input type="password" id="user_pass" name="user_pass" /><br>

        <label for="user_pass_confirm">Confirm Password:</label><br>
        <input type="password" id="user_pass_confirm" name="user_pass_confirm" /><br>

        <?php wp_nonce_field('deia_save_user_profile', 'user_profile_nonce'); ?>

        <?php submit_button('Save Changes', 'primary', 'submit_user_profile'); ?>
    </form>
    <?php
}


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


// Function to remove unnecessary meta boxes for musicians
function deia_metaboxes() {
    if (current_user_can('musician')) {
        remove_meta_box('categorydiv', 'post', 'side'); // Categories meta box
        remove_meta_box('commentstatusdiv', 'post', 'normal'); // Discussion meta box
        remove_meta_box('commentsdiv', 'post', 'normal'); // Comments meta box
        remove_meta_box('formatdiv', 'post', 'side'); // Format meta box
        remove_meta_box('astra_settings_meta_box', 'post', 'side'); // Astra Settings meta box

        remove_meta_box('submitdiv', 'post', 'side'); // Remove the publish box
        remove_meta_box('postimagediv', 'post', 'side'); // Remove the featured image box
        remove_meta_box('tagsdiv-post_tag', 'post', 'side'); // Tags meta box
        remove_meta_box('postcustom', 'post', 'normal'); // Custom fields meta box
        remove_meta_box('slugdiv', 'post', 'normal'); // Slug meta box
        remove_meta_box('edit-slug-box', 'post', 'normal'); // Slug meta box
        remove_meta_box('authordiv', 'post', 'normal'); // Author meta box
        remove_meta_box('revisionsdiv', 'post', 'normal'); // Revisions meta box
        remove_meta_box('postexcerpt', 'post', 'normal'); // Excerpt meta box
        remove_meta_box('trackbacksdiv', 'post', 'normal'); // Trackbacks meta box

        // remove_meta_box('wpfooter', 'post', 'normal'); // WP footer meta box
        // remove_meta_box('screen-meta', 'post', 'normal');
        // remove_meta_box('screen-meta-links', 'post', 'normal');
    }

    // Register the musician_details meta box
    add_meta_box(
        'musician_details',
        'Musician Details',
        'deia_musician_callback',
        'post',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'deia_metaboxes', 99); // High Priority


// Callback function for musician details meta box
function deia_musician_callback($post) {
    wp_nonce_field('deia_save_musician_details', 'musician_details_nonce');

    $fields = array(
        'musician_email' => 'Email',
        'musician_website' => 'Website',
        'musician_spotify' => 'Spotify',
        'musician_youtube' => 'YouTube',
        'musician_vimeo' => 'Vimeo',
        'musician_twitter' => 'Twitter',
        'musician_facebook' => 'Facebook',
        'musician_tiktok' => 'TikTok',
        'musician_events' => 'Events',
        'musician_bio' => 'Biography',
        'musician_image' => 'Upload Image',
    );

    echo '<div class="deia-form">';
    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        $musician_image = get_post_meta($post->ID, 'musician_image', true);
        $musician_image_url = $musician_image ? wp_get_attachment_image_url($musician_image, 'thumbnail') : '';

        echo '<div class="row">';
        echo '<label for="' . $key . '">' . $label . ':</label>';

        if ($key == 'musician_events' || $key == 'musician_bio') {
            echo '<textarea id="' . $key . '" name="' . $key . '" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
        } elseif ($key == 'musician_image') {
            echo '<input type="hidden" name="musician_image" id="musician_image" value="' . esc_attr($musician_image) . '">';
            echo '<img src="' . esc_url($musician_image_url) . '" style="max-width: 150px; height: auto; margin-bottom: 10px;"><br>';
            echo '<input type="button" id="upload_musician_image_button" class="button" value="Upload Image">';
        } else {
            echo '<input type="text" id="' . $key . '" name="' . $key . '" value="' . esc_attr($value) . '" size="25" />';
        }

        echo '</div>';
    }
    echo '</div>';
}


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

// Disable block editor for musicians
function deia_disable_block_editor_for_musician($use_block_editor, $post_type) {
    if (current_user_can('musician')) {
        return false; // Disable block editor for musicians
    }
    return $use_block_editor; // Use default behavior for other users or post types
}
add_filter('use_block_editor_for_post', 'deia_disable_block_editor_for_musician', 10, 2);
