<?php
// Redirect musicians to a specific page in admin
function custom_redirect_musician_to_specific_page() {
    // Check if the current user is logged in and has the 'musician' role
    if (is_user_logged_in() && current_user_can('musician')) {
        // Get the current page URL
        $current_url = $_SERVER['REQUEST_URI'];
        
        // Define the admin URL for the musicians_bands page
        $target_admin_url = admin_url('admin.php?page=musicians_bands');
        
        // Check if the current URL is exactly the main admin URL
        if ($current_url === '/wp-admin/' || $current_url === '/wp-admin') {
            wp_redirect($target_admin_url);
            exit;
        }
    }
}
add_action('admin_init', 'custom_redirect_musician_to_specific_page');


