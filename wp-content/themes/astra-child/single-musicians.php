<?php
/**
 * Template for displaying single posts in the 'musicians' category.
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

get_header(); ?>

<div id="primary" <?php astra_primary_class(); ?>>

    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();

            the_title('<h1>', '</h1>');
            
            // Custom fields for musicians
            $contact = get_post_meta(get_the_ID(), 'musician_contact', true);
            $spotify = get_post_meta(get_the_ID(), 'musician_spotify', true);
            $youtube = get_post_meta(get_the_ID(), 'musician_youtube', true);
            $twitter = get_post_meta(get_the_ID(), 'musician_twitter', true);
            $events = get_post_meta(get_the_ID(), 'musician_events', true);

            // Display the content
            the_content();
        ?>
            <div class="musician-details">
                <?php if ( $contact ) : ?>
                    <p>Contact: <?php echo esc_html( $contact ); ?></p>
                <?php endif; ?>
                <?php if ( $spotify ) : ?>
                    <p>Spotify: <a href="<?php echo esc_url( $spotify ); ?>" target="_blank">Listen</a></p>
                <?php endif; ?>
                <?php if ( $youtube ) : ?>
                    <p>YouTube: <a href="<?php echo esc_url( $youtube ); ?>" target="_blank">Watch</a></p>
                <?php endif; ?>
                <?php if ( $twitter ) : ?>
                    <p>Twitter: <a href="<?php echo esc_url( $twitter ); ?>" target="_blank">Follow</a></p>
                <?php endif; ?>
                <?php if ( $events ) : ?>
                    <h3>Upcoming Events:</h3>
                    <ul>
                        <?php foreach ( $events as $event ) : ?>
                            <li><?php echo esc_html( $event ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php
        endwhile;
    else :
        echo '<p>No posts found</p>';
    endif;
    ?>

</div><!-- #primary -->

<?php get_footer(); ?>


<!-- .musician-details {
    margin-top: 20px;
}

.musician-details p {
    font-size: 16px;
    margin-bottom: 10px;
}

.musician-details ul {
    list-style-type: none;
    padding: 0;
}

.musician-details ul li {
    background: #f4f4f4;
    margin-bottom: 5px;
    padding: 10px;
} -->
