<?php
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

