<?php
// Add a body class to indicate the user role for more precise targeting in JavaScript
function deia_add_body_class_for_musicians($classes) {
    if (current_user_can('musician')) {
        $classes .= ' role-musician';
    }
    return $classes;
}
add_filter('admin_body_class', 'deia_add_body_class_for_musicians');


// Diable items from Admin TOP bar
function deia_customize_admin_bar($wp_admin_bar) {
    if (current_user_can('musician')) {
        // Remove default items
        $wp_admin_bar->remove_node('wp-logo'); // WordPress logo
        // $wp_admin_bar->remove_node('site-name'); // Site name
        $wp_admin_bar->remove_node('updates'); // Updates
        $wp_admin_bar->remove_node('comments'); // Comments
        $wp_admin_bar->remove_node('new-content'); // New content
        $wp_admin_bar->remove_node('hostinger_admin_bar'); // Hostinger admin bar item
        $wp_admin_bar->remove_node('hostinger_admin_bar'); // Hostinger admin bar item
        $wp_admin_bar->remove_node('zip-ai-assistant'); // AI Assistant
    }
}
add_action('admin_bar_menu', 'deia_customize_admin_bar', 999);


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


// Disable block editor for musicians
function deia_disable_block_editor_for_musician($use_block_editor, $post_type) {
    if (current_user_can('musician')) {
        return false; // Disable block editor for musicians
    }
    return $use_block_editor; // Use default behavior for other users or post types
}
add_filter('use_block_editor_for_post', 'deia_disable_block_editor_for_musician', 10, 2);


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
        // remove_menu_page('profile.php'); // Profile

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

        // add_menu_page(
        //     'Profile', // Page title
        //     'Profile', // Menu title
        //     'read', // Capability
        //     'musician_profile', // Menu slug
        //     'deia_musician_profile_page', // Callback function
        //     'dashicons-admin-users', // Icon
        //     3 // Menu position
        // );
    }
}
add_action('admin_menu', 'deia_customize_musician_admin_menu', 999); // High priority


// Remove specific profile fields for musicians (I'm also hiding via css so it doesnt show up when loading)
function deia_remove_musician_profile_fields($contactmethods) {
    if (current_user_can('musician')) {
        // Remove Visual Editor
        remove_action('personal_options', 'default_personal_options');
        // Remove Admin Color Scheme
        remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
        // Remove Keyboard Shortcuts
        remove_action('personal_options', 'personal_options');
        // Remove Language
        add_filter('user_can_edit_user_language', '__return_false');
        // Remove About Yourself fields
        remove_action('show_user_profile', 'user_profile_personal_options');
        remove_action('edit_user_profile', 'user_profile_personal_options');
    }
    return $contactmethods;
}
add_filter('user_contactmethods', 'deia_remove_musician_profile_fields', 10, 1);


// Remove Keyboard Shortcuts and Admin Color Scheme
function deia_remove_personal_options($profileuser) {
    if (current_user_can('musician')) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Remove the entire "Personal Options" section
                $('form#your-profile').find('h2').filter(function() {
                    return $(this).text().trim() === 'Personal Options';
                }).next('.form-table').remove();
                $('form#your-profile').find('h2').filter(function() {
                    return $(this).text().trim() === 'Personal Options';
                }).remove();

                // Remove the entire "About Yourself" section
                $('form#your-profile').find('h2').filter(function() {
                    return $(this).text().trim() === 'About Yourself';
                }).next('.form-table').remove();
                $('form#your-profile').find('h2').filter(function() {
                    return $(this).text().trim() === 'About Yourself';
                }).remove();

                // Remove the "Application Passwords" section
                $('#application-passwords-section').remove();
            });
        </script>
        <?php
    }
}
add_action('admin_head', 'deia_remove_personal_options');


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