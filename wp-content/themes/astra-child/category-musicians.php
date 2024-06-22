<?php
/*
Template Name: Deia Category Template
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

        <h1 class="page-title line"><?php single_cat_title(); ?></h1>

		<?php
		$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

		$args = array(
			'category_name' => 'musicians',
			'posts_per_page' => 10,
			'paged' => $paged
		);

		$musicians_query = new WP_Query($args);

		if ($musicians_query->have_posts()) :
			while ($musicians_query->have_posts()) : $musicians_query->the_post();
		    ?>

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

                    <a href="<?php the_permalink(); ?>" class="entry-content clear" <?php echo astra_attr( 'entry-content' ); ?>>

                        <?php the_excerpt(); ?>

                    </a><!-- .entry-content -->

                    <?php astra_edit_post_link( 'Edit this post' ); ?>

                </div><!-- .right-content -->

			</article><!-- #post-## -->

			<?php
			endwhile;
		?>

			<!-- Pagination -->
			<div class="pagination">
				<?php
				echo paginate_links(array(
					'total' => $musicians_query->max_num_pages,
					'current' => $paged,
					'prev_text' => __('← Previous Page'),
					'next_text' => __('Next Page →'),
				));
				?>
			</div>

		<?php
			else :
				echo '<p>No posts found.</p>';
			endif;
			
			// Reset post data
			wp_reset_postdata();
		?>

	</div><!-- #primary -->

<?php if ( astra_page_layout() == 'right-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

<?php get_footer(); ?>