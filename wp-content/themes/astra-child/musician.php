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
    $role->add_cap('publish_posts');
    $role->add_cap('upload_files');
}
add_action('admin_init', 'deia_add_musician_capabilities');

// Hide admin menu items for musicians and add a custom page for musicians
function deia_customize_musician_admin_menu() {
    if (current_user_can('musician')) {
        remove_menu_page('index.php'); // Dashboard
        remove_menu_page('edit.php'); // Posts
        remove_menu_page('upload.php'); // Media
        remove_menu_page('edit-comments.php'); // Comments
        remove_menu_page('tools.php'); // Tools
        remove_menu_page('options-general.php'); // Settings
        remove_menu_page('themes.php'); // Appearance
        remove_menu_page('users.php'); // Users
        remove_menu_page('plugins.php');
        remove_menu_page('tools.php');
        remove_menu_page('profile.php');

        // Add a custom page for musicians/bands
        add_menu_page(
            'Musicians/Bands', // Page title
            'Musicians/Bands', // Menu title
            'read', // Capability
            'musicians_bands', // Menu slug
            'deia_musician_posts_page', // Callback function
            'dashicons-format-audio', // Icon
            2 // Menu position
        );

        // Add a custom page for profile
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

// Callback function for "My Posts" page
function deia_musician_posts_page() {
    echo '<div class="wrap">';
    echo '<h1>Musicians/Bands</h1>';
    echo '<p>Published artists:</p>';

    // Display musician's posts
    deia_display_musician_posts(); // Function to display posts

    echo '</div>';
}

// Diable items from Admin top bar
function deia_customize_admin_bar($wp_admin_bar) {
    if (current_user_can('musician')) {
        // Remove default items
        $wp_admin_bar->remove_node('wp-logo'); // WordPress logo
        $wp_admin_bar->remove_node('site-name'); // Site name
        $wp_admin_bar->remove_node('updates'); // Updates
        $wp_admin_bar->remove_node('comments'); // Comments
        $wp_admin_bar->remove_node('new-content'); // New content

        // Remove Hostinger admin bar item
        $wp_admin_bar->remove_node('hostinger_admin_bar');

        // Leave only the logoff item
        // $wp_admin_bar->remove_node('edit-profile'); // Profile submenu

        // Add a custom Website Title
        $wp_admin_bar->add_node(array(
            'id' => 'gacnet_home',
            'title' => '&#x2730; GACNET',
            'href' => home_url(),
            'meta' => array(
                'class' => 'gacnet-home-button', // Optional CSS class for styling
                'target' => '_blank', // Optional target attribute for the link
            ),
        ));

        // Add a custom logoff item if needed
        // $wp_admin_bar->add_node(array(
        //     'id' => 'log-out',
        //     'title' => 'Log Out',
        //     'href' => wp_logout_url(),
        // ));
    }
}
add_action('admin_bar_menu', 'deia_customize_admin_bar', 999);

// Disable block editor for musicians
function deia_disable_block_editor_for_musician($use_block_editor, $post_type) {
    if (current_user_can('musician')) {
        return false; // Disable block editor for musicians
    }
    return $use_block_editor; // Use default behavior for other users or post types
}
add_filter('use_block_editor_for_post', 'deia_disable_block_editor_for_musician', 10, 2);

// Redirect musicians to their custom page upon login
function deia_redirect_musician_dashboard() {
    if (current_user_can('musician') && !isset($_GET['page']) && $_GET['page'] !== 'musician_profile' && !isset($_GET['post'])) {
        wp_redirect(admin_url('admin.php?page=musician_profile'));
        exit;
    }
}
add_action('admin_init', 'deia_redirect_musician_dashboard');

// Display the user posts
function deia_display_musician_posts() {
    $current_user_id = get_current_user_id();
    $current_user = wp_get_current_user();
    
    $args = array(
        'post_type' => 'post',
        'author' => $current_user_id,
        'posts_per_page' => -1,
    );

    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        echo '<ul>';
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $post_title = get_the_title();

            if (current_user_can('edit_post', $post_id)) {
                $edit_post_url = get_edit_post_link($post_id);
                $delete_post_url = get_delete_post_link($post_id);

                echo '<li>';
                echo '<a href="' . esc_url($edit_post_url) . '" target="_blank">' . esc_html($post_title) . '</a>';
                echo ' | <a href="' . esc_url($delete_post_url) . '" onclick="return confirm(\'Are you sure you want to delete this post?\')">Delete</a>';
                echo '</li>';
            } else {
                echo '<li>' . esc_html($post_title) . '</li>';
            }
        }
        echo '</ul>';
        wp_reset_postdata();
    } else {
        echo '<p>No posts found.</p>';
    }
}

// Display the profile form 
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

// Create a custom page for musicians to manage their profile
function deia_musician_profile_page() {
    echo '<div class="wrap">';
    echo '<h1>Profile</h1>';

    // Include a form for musicians to edit their information
    deia_musician_profile_form(); // Function to display profile form

    echo '</div>';
}

// Show only the musician's own posts
function deia_show_only_musician_own_posts($query) {
    if (is_admin() && $query->is_main_query() && current_user_can('musician')) {
        $query->set('author', get_current_user_id());
    }
}
add_action('pre_get_posts', 'deia_show_only_musician_own_posts');

// Add and Remove custom meta boxes
function deia_metaboxes() {
    if (current_user_can('musician')) {
        error_log('Removing meta boxes for musician');
        remove_meta_box('categorydiv', 'post', 'side'); // Categories meta box
        remove_meta_box('commentstatusdiv', 'post', 'normal'); // Discussion meta box
        remove_meta_box('commentsdiv', 'post', 'normal'); // Comments meta box
        remove_meta_box('formatdiv', 'post', 'side'); // Format meta box
        remove_meta_box('astra_settings_meta_box', 'post', 'side'); // Astra Settings meta box
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
add_action('add_meta_boxes', 'deia_metaboxes', 99 ); // High Priority

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
        // 'musician_genres' => 'Genres',
        // 'musician_instruments' => 'Instruments',
        // 'musician_location' => 'Location',
        // 'musician_years_active' => 'Years Active',
        // 'musician_record_label' => 'Record Label',
    );

    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        echo '<label for="' . $key . '">' . $label . ':</label>';
        if ($key == 'musician_events') {
            echo '<textarea id="' . $key . '" name="' . $key . '" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
        } else {
            echo '<input type="text" id="' . $key . '" name="' . $key . '" value="' . esc_attr($value) . '" size="25" />';
        }
        echo '<br>';
    }
}

function deia_save_musician_details($post_id) {
    if (!isset($_POST['musician_details_nonce']) || !wp_verify_nonce($_POST['musician_details_nonce'], 'deia_save_musician_details')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

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
        'musician_bio'
        // 'musician_genres',
        // 'musician_instruments',
        // 'musician_location',
        // 'musician_years_active',
        // 'musician_record_label'
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'deia_save_musician_details');

// Allow musicians to edit their own posts only
function deia_allow_musician_edit_own_posts($allcaps, $cap, $args) {
    if (isset($args[2]) && 'edit_post' == $args[0]) {
        $post = get_post($args[2]);
        if ($post->post_author == get_current_user_id()) {
            $allcaps['edit_posts'] = true;
        }
    }
    return $allcaps;
}
add_filter('user_has_cap', 'deia_allow_musician_edit_own_posts', 10, 3);
