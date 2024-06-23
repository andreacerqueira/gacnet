<?php
/*
Template Name: Deia Category Template
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
                <input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" placeholder="Search..."/>
                <input type="hidden" name="cat" value="<?php echo get_queried_object_id(); ?>"/>
                <input type="submit" id="searchsubmit" value="<?php echo esc_attr__('Search'); ?>"/>
            </form>
        </div>

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

                </div><!-- .right-content -->

			</article><!-- #post-## -->

			<?php
		endwhile;
		?>

	</div><!-- #primary -->

<?php get_footer(); ?>