<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
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
        <h1 class="page-title line"><?php printf(esc_html__('Search Results for: %s', 'astra'), '<span class="term">"' . get_search_query() . '"</span>'); ?></h1>

        <!-- Search Form -->
        <form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url(home_url('/')); ?>">
            <label class="screen-reader-text" for="s"><?php _e('Search for:'); ?></label>
            <input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" placeholder="Search..."/>
            <input type="submit" id="searchsubmit" value="<?php echo esc_attr__('Search'); ?>"/>
        </form>
    </div>

    <?php
    global $wp_query;
    $is_musician_search = false;
    $musician_cat_id = get_cat_ID('musicians');
    if (isset($_GET['cat']) && $_GET['cat'] == $musician_cat_id) {
        $is_musician_search = true;
    }

    if (have_posts()) :
        while (have_posts()) : the_post();
            ?>

            <article id="post-<?php the_ID(); ?>" class="deia-cat-list">

                <div class="right-content">

                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                    <a href="<?php the_permalink(); ?>" class="entry-content clear" <?php echo astra_attr('entry-content'); ?>>

                        <?php
                        if (has_excerpt()) {
                            the_excerpt();
                        } else {
                            if ($is_musician_search) {
                                echo '<p>' . esc_html(get_limited_musician_bio(get_the_ID())) . '</p>';
                            } else {
                                the_content();
                            }
                        }
                        ?>

                    </a><!-- .entry-content -->

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

        <p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'astra'); ?></p>

    <?php endif; ?>

</div><!-- #primary -->

<?php get_footer(); ?>
