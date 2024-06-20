<?php
//// Redirect musicians to their custom page upon login
// function deia_redirect_musician_dashboard() {
//     if ( is_user_logged_in() && current_user_can('musician') ) {
//         wp_redirect( admin_url('admin.php?page=musicians_bands') );
//         exit;
//     }
// }
// add_action( 'admin_init', 'deia_redirect_musician_dashboard' );