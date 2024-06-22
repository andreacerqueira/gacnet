<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );


/**
 * MUSICIANS - Include musician user type logic --------------------------------------------------------------------------
 */
require get_stylesheet_directory() . '/deia/musician.php';


/**
 * NEWS - Add custom content after the main content loop -----------------------------------------------------------------
 */
require get_stylesheet_directory() . '/deia-news-slider/deia-news.php';


/**
 * STYLE - Import CSS ----------------------------------------------------------------------------------------------------
 */
function child_enqueue_styles() {
    // Enqueue parent theme stylesheet
    // wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );
	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'child_enqueue_styles', 15);


/**
 * CONTACT US MODAL - Enqueue script for modal functionality -------------------------------------------------------------
 */
// Enqueue script for modal functionality
function deia_modal_scripts() {
    // Enqueue jQuery if it's not already loaded
    wp_enqueue_script('jquery');

    $contact_us_page_id = 804;

    // Define the JavaScript code for modal functionality
    $script = "
    jQuery(document).ready(function($) {
        // Attach click event handler to the menu item
        $('.deia-open-modal').on('click', function() {
            // Load the content of the contact us page into the modal
            $.ajax({
                url: '" . get_permalink( $contact_us_page_id ) . "',
                success: function(data) {
                    $('#modal-content-placeholder').html(data);
                    $('#deiaContactModal').show(); // Show the modal
                }
            });
        });

        // Close the modal when the close button is clicked
        $('.close').on('click', function() {
            $('#deiaContactModal').hide(); // Hide the modal
        });
    });
    ";

    // Add the JavaScript code inline
    wp_add_inline_script( 'jquery', $script );
}
add_action( 'wp_enqueue_scripts', 'deia_modal_scripts' );


// Enqueue your custom CSS file, so it will load the contact us css in all pages, as it is a modal without a header
function enqueue_modal_styles() {
    // 804 is the ID of Contact Us Page
    $css_url_modal = 'https://gacnetca.com/wp-content/uploads/bb-plugin/cache/804-layout.css';
    $css_url_form = 'https://gacnetca.com/wp-content/plugins/wpforms-lite/assets/css/frontend/classic/wpforms-base.min.css';

    // Enqueue the CSS file
    wp_enqueue_style( 'custom-modal-css-modal', $css_url_modal );
    wp_enqueue_style( 'custom-modal-css-form', $css_url_form );
}
add_action( 'wp_enqueue_scripts', 'enqueue_modal_styles' );


/**
 * SCROLL TO TOP - Remove from Front Page
 */
function custom_css_to_hide_scroll_top() {
    if ( is_front_page() ) { ?>
        <style type="text/css">
            #ast-scroll-top {
                display: none !important;
            }
        </style>
    <?php }
}
add_action( 'wp_head', 'custom_css_to_hide_scroll_top' );

