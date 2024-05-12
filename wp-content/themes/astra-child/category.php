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
		while ( have_posts() ) :
			the_post();
			?>

			<article id="post-<?php the_ID(); ?>" class="deia-cat-list">

                
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="entry-thumbnail">
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                        <?php the_post_thumbnail(); ?>
                    </a>
                    </div><!-- .entry-thumbnail -->
                <?php endif; ?>

                <div class="right-content">

                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                    <a href="<?php the_permalink(); ?>" class="entry-content clear" <?php echo astra_attr( 'entry-content' ); ?>>

                        <?php the_excerpt(); ?>

                    </a><!-- .entry-content -->

                    <div class="share-icons">
                        <a href="https://twitter.com/intent/tweet?url=<?php urlencode('https://www.youtube.com/watch?v=' . $videoId); ?>&text=<?php urlencode($title); ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>-child/assets/images/icon-twitter.svg"/></a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php urlencode('https://www.youtube.com/watch?v=' . $videoId); ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>-child/assets/images/icon-facebook.svg"/></a>
                        <a href="https://www.tiktok.com/share/video/<?php $videoId; ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>-child/assets/images/icon-tiktok.svg"/></a>
                        <a href="https://wa.me/?text=<?php urlencode('Check out this video: https://www.youtube.com/watch?v=' . $videoId); ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>-child/assets/images/icon-whatsapp.svg"/></a>
                        <!-- <a href="instagram' . urlencode('https://www.youtube.com/watch?v=' . $videoId) . '" target="_blank"><img src="<?php //echo get_template_directory_uri(); ?>-child/assets/images/icon-instagram.svg"/></a> -->
                    </div><!-- .share-icons -->

                    <?php astra_edit_post_link( 'Edit this post' ); ?>

                </div><!-- .right-content -->

			</article><!-- #post-## -->

			<?php
		endwhile;
		?>

	</div><!-- #primary -->

<?php if ( astra_page_layout() == 'right-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

<?php get_footer(); ?>