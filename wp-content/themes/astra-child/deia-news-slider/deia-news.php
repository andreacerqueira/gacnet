<?php
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

function news_enqueue() {
    // Enqueue parent theme stylesheet
    wp_enqueue_style( 'deia-news-css', get_stylesheet_directory_uri() . '/deia-news-slider/deia-news.css');

    // Enqueue jQuery
    wp_enqueue_script('jquery');

    // Add custom JavaScript for the news slider only on the front page
    if (is_front_page()) {
        wp_enqueue_script('deia-news-js', get_stylesheet_directory_uri() . '/deia-news-slider/deia-news.js', array(), false, true);
    }
}
add_action('wp_enqueue_scripts', 'news_enqueue', 15);