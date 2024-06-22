<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

<div id="primary" <?php astra_primary_class(); ?>>

    <div class="heading-search">
        <h1 class="page-title line"><?php printf( esc_html__( 'Search Results for: %s', 'astra' ), '<span class="term">"' . get_search_query() . '"</span>' ); ?></h1>

        <!-- Search Form -->
        <form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url(home_url('/')); ?>">
            <label class="screen-reader-text" for="s"><?php _e('Search for:'); ?></label>
            <input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" placeholder="Search musicians..."/>
            <input type="hidden" name="cat" value="<?php echo get_cat_ID('musicians'); ?>"/>
            <input type="submit" id="searchsubmit" value="<?php echo esc_attr__('Search'); ?>"/>
        </form>
    </div>

	<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" class="deia-cat-list">

                <?php
                $image_id = get_post_meta(get_the_ID(), 'musician_image', true);
                $musician_image_url = $image_id ? wp_get_attachment_image_url($image_id, 'deia-600-img') : '';
                if ( $musician_image_url ) : ?>
                    <div class="entry-thumbnail">
                        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                            <img src="<?php echo esc_url( $musician_image_url ); ?>" alt="<?php the_title_attribute(); ?>"/>
                        </a>
                    </div>
                <?php endif; ?>

				<div class="right-content">

					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

					<div class="entry-content clear" <?php echo astra_attr( 'entry-content' ); ?>>

						<?php the_excerpt(); ?>

					</div><!-- .entry-content -->

				</div><!-- .right-content -->

			</article><!-- #post-## -->

		<?php endwhile; ?>

		<!-- Pagination -->
		<div class="pagination">
			<?php
			echo paginate_links(array(
				'total' => $wp_query->max_num_pages,
				'current' => max(1, get_query_var('paged')),
				'prev_text' => __('← Previous Page'),
				'next_text' => __('Next Page →'),
			));
			?>
		</div>

	<?php else : ?>

		<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'astra' ); ?></p>
		<?php //get_search_form(); ?>

	<?php endif; ?>

</div><!-- #primary -->

<?php get_footer(); ?>
