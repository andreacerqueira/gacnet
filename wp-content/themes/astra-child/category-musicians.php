<?php
/*
Template Name: Deia Musicians Category Template
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

<div id="primary" <?php astra_primary_class(); ?>>

    <div class="heading-search">
        <h1 class="page-title line"><?php single_cat_title(); ?></h1>

        <!-- Search Form -->
        <form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url(home_url('/')); ?>">
            <label class="screen-reader-text" for="s"><?php _e('Search for:'); ?></label>
            <input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" placeholder="Search musicians..."/>
            <input type="hidden" name="cat" value="<?php echo get_cat_ID('musicians'); ?>"/>
            <input type="submit" id="searchsubmit" value="<?php echo esc_attr__('Search'); ?>"/>
        </form>
    </div>

	<?php
	$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
	$search_query = get_search_query();

	$args = array(
		'category_name' => 'musicians',
		'posts_per_page' => 10,
		'paged' => $paged,
		's' => $search_query,
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

					<?php
					if ( has_excerpt() ) {
						the_excerpt();
					} else {
						echo '<p>' . esc_html( get_limited_musician_bio(get_the_ID()) ) . '</p>';
					}
					?>

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

<?php get_footer(); ?>