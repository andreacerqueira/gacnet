<?php
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


/*
// Callback function for musician profile page
function deia_musician_profile_page() {
    echo '<div class="wrap">';
    echo '<h1>Profile</h1>';

    // Display profile form
    deia_musician_profile_form();

    echo '</div>';
}
*/


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


/*
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
*/