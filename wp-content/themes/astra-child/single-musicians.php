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

$image_header = get_post_meta(get_the_ID(), 'musician_header_image', true);
$musician_image_header_url = $image_header ? wp_get_attachment_image_url($image_header, 'full') : '';

get_header(); ?>

<div id="primary" <?php astra_primary_class(); ?>>

    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();

            the_title('<h1>', '</h1>');
            
            // Custom fields for musicians
            $email = get_post_meta(get_the_ID(), 'musician_email', true);
            $website = get_post_meta(get_the_ID(), 'musician_website', true);
            $spotify = get_post_meta(get_the_ID(), 'musician_spotify', true);
            $youtube = get_post_meta(get_the_ID(), 'musician_youtube', true);
            $vimeo = get_post_meta(get_the_ID(), 'musician_youtube_music', true);
            $twitter = get_post_meta(get_the_ID(), 'musician_twitter', true);
            $facebook = get_post_meta(get_the_ID(), 'musician_facebook', true);
            $tiktok = get_post_meta(get_the_ID(), 'musician_tiktok', true);
            $bio = get_post_meta(get_the_ID(), 'musician_bio', true);
            $image_header = get_post_meta(get_the_ID(), 'musician_header_image', true);
            $musician_image_header_url = $image ? wp_get_attachment_image_url($image_header, 'full') : '';
            $image = get_post_meta(get_the_ID(), 'musician_image', true);
            $musician_image_url = $image ? wp_get_attachment_image_url($image, 'full') : '';

            // Display the content (don't show this, it's the main content)
            // the_content();
        ?>
            <div class="musician-details">
                <?php if ( $email ) : ?>
                    <p>Email: <?php echo esc_html( $email ); ?></p>
                <?php endif; ?>
                <?php if ( $website ) : ?>
                    <p>Website: <?php echo esc_url( $website ); ?></p>
                <?php endif; ?>
                <?php if ( $spotify ) : ?>
                    <p>Spotify: <a href="<?php echo esc_url( $spotify ); ?>" target="_blank">Listen</a></p>
                <?php endif; ?>
                <?php if ( $youtube ) : ?>
                    <p>YouTube: <a href="<?php echo esc_url( $youtube ); ?>" target="_blank">Watch</a></p>
                <?php endif; ?>
                <?php if ( $vimeo ) : ?>
                    <p>Vimeo: <a href="<?php echo esc_url( $vimeo ); ?>" target="_blank">Watch</a></p>
                <?php endif; ?>
                <?php if ( $twitter ) : ?>
                    <p>Twitter: <a href="<?php echo esc_url( $twitter ); ?>" target="_blank">Follow</a></p>
                <?php endif; ?>
                <?php if ( $facebook ) : ?>
                    <p>Facebook: <a href="<?php echo esc_url( $facebook ); ?>" target="_blank">Follow</a></p>
                <?php endif; ?>
                <?php if ( $tiktok ) : ?>
                    <p>Tiktok: <a href="<?php echo esc_url( $tiktok ); ?>" target="_blank">Follow</a></p>
                <?php endif; ?>
                <?php if ( $bio ) : ?>
                    <p><?php echo esc_html( $bio ); ?></p>
                <?php endif; ?>
                <?php if ( $image ) : ?>
                    <div><img src="<?php echo esc_url( $musician_image_url ); ?>"/></div>
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
