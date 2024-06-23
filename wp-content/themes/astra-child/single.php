<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Check if the post is in the "musicians" category
if ( in_category( 'musicians' ) ) {
    get_template_part( 'single-musicians' );
    return;
}

get_header(); ?>

	<div id="primary" <?php astra_primary_class(); ?>>

		<h1 class="page-title line"><?php echo get_the_title(); ?></h1>

		<?php
		$categories = get_the_category();
		if ( ! empty( $categories ) ) {
			echo '<h2 class="category-title">';
			foreach ( $categories as $category ) {
				echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a> ';
			}
			echo '</h2>';
		}
		?>

		<div class="deia-single" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php
			while ( have_posts() ) :
				the_post();
				?>

					<?php if ( has_post_thumbnail() ) : ?>
						<div class="image">
							<a href="<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>" data-fancybox="gallery" data-caption="<?php the_title_attribute(); ?>">
								<?php the_post_thumbnail('medium_large'); ?>
							</a>
						</div><!-- .image -->
					<?php endif; ?>

					<div class="entry-content clear" <?php echo astra_attr( 'entry-content' ); ?>>

						<?php the_content(); ?>

					</div><!-- .entry-content -->

					<?php astra_edit_post_link( 'Edit this post' ); ?>

				<?php
			endwhile;
			?>
		</div><!-- .deia-single -->

	</div><!-- #primary -->

<?php get_footer(); ?>
