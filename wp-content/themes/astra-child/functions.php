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
 * NEWS - Add custom content after the main content loop
 */
function custom_content_after_loop() {
	// Check if the current page is the front page
    if ( is_front_page() ) {
		?>
		<div id="news-slider" class="news-slider">
			<ul class="news-slider-list">
				<?php
				// Query posts from the "news" category and limit to the last 5 posts
				$args = array(
					'post_type' => 'post',
					'post_status' => 'publish',
					'posts_per_page' => 5, // Limit to 5 posts
					'category_name' => 'news', // Change 'news' to your category slug
					'order' => 'DESC', // Show latest posts first
					'orderby' => 'date', // Order by date
				);

				$query = new WP_Query($args);

				if ($query->have_posts()) :
					while ($query->have_posts()) :
						$query->the_post();
						?>
						<li>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?> - <?php the_excerpt(); ?></a>
						</li>
						<?php
					endwhile;

					// Duplicate the news slider list to ensure continuous looping
					$query->rewind_posts();
					while ($query->have_posts()) :
						$query->the_post();
						?>
						<li>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?> - <?php the_excerpt(); ?></a>
						</li>
						<?php
					endwhile;

					wp_reset_postdata();
				else :
					echo '<li>No news found.</li>';
				endif;
				?>
			</ul>
		</div>
    	<?php
    }
}
add_action('astra_primary_content_bottom', 'custom_content_after_loop');

function child_enqueue_styles() {
    // Enqueue parent theme stylesheet
    // wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );
	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css');

    // Add custom CSS for the news slider
    wp_add_inline_style('astra-theme-css', '
        .news-slider {
			position: fixed;
			bottom: 0;
            margin: 0 -2rem;
            overflow: hidden;
            width: 100%;
            min-height: 30px;
            padding: 10px;
            background: var(--ast-global-color-0);
  			z-index: 1000;
        }

        .news-slider-list {
            position: relative;
            display: flex;
            gap: 50px;
            list-style: none;
            padding: 0;
            margin: 0;
            white-space: nowrap; /* Keep items in a single line */
        }

        .news-slider-list li {
            display: inline-block;
        }

        .news-slider-list li:last-child {
            padding-right: 50px;
        }

        .news-slider-list li a {
            display: flex;
            flex-direction: row;
            color: var(--ast-global-color-2);
        }
        
        .news-slider-list li p {
            margin: 0;
        }

        @media (max-width: 921px) {
            .news-slider {
                margin: 0 -1rem;
            }
        }
    ');

    // Enqueue jQuery
    wp_enqueue_script('jquery');

    // Add custom JavaScript for the news slider only on the front page
    if (is_front_page()) {
        wp_add_inline_script('jquery', '
            jQuery(document).ready(function($) {
                // Set the base speed of the auto-scroll (milliseconds)
                var baseScrollSpeed = 15000; // Adjust the base scroll speed here
                var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

                // Adjust scroll speed for mobile devices
                if (isMobile) {
                    baseScrollSpeed = 5000; // Faster scroll speed for mobile
                }

                // Get the news slider element and its width
                var newsSlider = $(\'#news-slider\');
                var newsSliderList = $(\'#news-slider .news-slider-list\');
                var sliderWidth = newsSlider.width(); // Use the width of the slider
                var totalWidth = newsSliderList[0].scrollWidth; // Calculate the total width of the news slider list

                // Clone the news slider list to ensure continuous looping
                newsSliderList.append(newsSliderList.html());

                // Calculate the scroll speed based on the width of the news slider list
                var scrollSpeed = baseScrollSpeed * totalWidth / sliderWidth;

                // Animate the slider list to scroll left
                function scrollNews() {
                    newsSliderList.animate({
                        left: \'-=\' + totalWidth // Scroll the entire width of the news slider list
                    }, scrollSpeed, \'linear\', function() {
                        // Reset position when the entire list is scrolled
                        newsSliderList.css(\'left\', 0);
                        // Call the function again after the animation completes
                        scrollNews();
                    });
                }

                // Start scrolling
                scrollNews();
            });
        ');
    }
}
add_action('wp_enqueue_scripts', 'child_enqueue_styles', 15);


/**
 * CONTACT US MODAL - Enqueue script for modal functionality
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