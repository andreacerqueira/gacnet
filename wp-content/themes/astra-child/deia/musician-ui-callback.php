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

        if ($key == 'musician_bio') {
            echo '<textarea id="' . $key . '" name="' . $key . '" rows="15" cols="50">' . esc_textarea($value) . '</textarea>';
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