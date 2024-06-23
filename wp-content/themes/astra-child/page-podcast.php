<?php
/**
 * Template Name: Podcast
 * 
 * The template for displaying the PODCASTS coming from Youtube.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Astra
 * @since 1.0.0
 */

 if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header(); ?>

    <div id="primary" <?php astra_primary_class(); ?>>

        <div class="heading-search">
            <h1 class="page-title line"><?php echo get_the_title(); ?></h1>

            <!-- Search Form -->
            <!-- <form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url(home_url('/')); ?>">
                <label class="screen-reader-text" for="s"><?php _e('Search for:'); ?></label>
                <input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" placeholder="Search musicians..."/>
                <input type="hidden" name="cat" value="<?php echo get_cat_ID('musicians'); ?>"/>
                <input type="submit" id="searchsubmit" value="<?php echo esc_attr__('Search'); ?>"/>
            </form> -->
        </div>

        <div>
            <!-- We Develop and promote Caribbean Jazz podcasts within the Caribbean &amp; beyond.<br>
            Check our <a href="https://www.youtube.com/channel/UCbw85HIvNBkAuyawpAmZ__A" target="_blank" rel="noopener"><strong>YOUTUBE CHANNEL</strong></a><br>
            <h3>Last Podcasts:</h3> -->
        </div>

		<?php
		while ( have_posts() ) :
			the_post();
			?>

                <?php
                the_content();

				// Verifique se estamos na página "podcast"
				if (is_page('podcast')) {
					// Use a função do_shortcode() para chamar o shortcode do seu plugin
					echo do_shortcode('[youtube_data]');
				}
				?>

			<?php
		endwhile;
		?>

	</div><!-- #primary -->

<?php get_footer(); ?>