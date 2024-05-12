<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

<?php if ( astra_page_layout() == 'left-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

	<div id="primary" <?php astra_primary_class(); ?>>

		<?php astra_primary_content_top(); ?>

		<?php
		while ( have_posts() ) :
			the_post();
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<?php astra_entry_content_before(); ?>

				<div class="entry-content clear" <?php echo astra_attr( 'entry-content' ); ?>>

					<?php the_content(); ?>

				</div><!-- .entry-content -->

				<?php
				astra_edit_post_link( 'Edit this post' );

				// Verifique se estamos na página "podcast"
				if (is_page('podcast')) {
					// Use a função do_shortcode() para chamar o shortcode do seu plugin
					echo do_shortcode('[youtube_data]');
				}
				?>

			</article><!-- #post-## -->

			<?php
		endwhile;
		?>

		<?php astra_primary_content_bottom(); ?>

	</div><!-- #primary -->

<?php if ( astra_page_layout() == 'right-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

<?php get_footer(); ?>
