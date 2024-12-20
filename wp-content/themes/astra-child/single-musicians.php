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
            
            // Custom fields for musicians
            $email = get_post_meta(get_the_ID(), 'musician_email', true);
            $website = get_post_meta(get_the_ID(), 'musician_website', true);
            $spotify = get_post_meta(get_the_ID(), 'musician_spotify', true);
            $apple_music = get_post_meta(get_the_ID(), 'musician_apple_music', true);
            $youtube_music = get_post_meta(get_the_ID(), 'musician_youtube_music', true);
            $youtube = get_post_meta(get_the_ID(), 'musician_youtube', true);
            $twitter = get_post_meta(get_the_ID(), 'musician_twitter', true);
            $facebook = get_post_meta(get_the_ID(), 'musician_facebook', true);
            $instagram = get_post_meta(get_the_ID(), 'musician_instagram', true);
            $tiktok = get_post_meta(get_the_ID(), 'musician_tiktok', true);
            $bio = get_post_meta(get_the_ID(), 'musician_bio', true);
            $player = get_post_meta(get_the_ID(), 'musician_player', true);
            $image = get_post_meta(get_the_ID(), 'musician_image', true);
            $musician_image_url = $image ? wp_get_attachment_image_url($image, 'medium_large') : '';
            $musician_image_full_url = $image ? wp_get_attachment_image_url($image, 'full') : '';

            // Display the content (don't show this, it's the main content)
            // the_content();
        ?>
            <div class="musician-details">
                <div class="left">
                    <?php if ( $image ) : ?>
                        <div class="image">
                            <a href="<?php echo esc_url( $musician_image_full_url ); ?>" data-fancybox="gallery" data-caption="<?php the_title_attribute(); ?>">
                                <img src="<?php echo esc_url( $musician_image_url ); ?>"/>
							</a>
                        </div>
                    <?php endif; ?>
                    <ul class="details">
                        <?php if ( $website ) : ?>
                            <li><a href="<?php echo esc_url( $website ); ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>-child/assets/images/icon-website.svg"/></a></li>
                        <?php endif; ?>
                        <?php if ( $spotify ) : ?>
                            <li><a href="<?php echo esc_url( $spotify ); ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>-child/assets/images/icon-spotify.svg"/></a></li>
                        <?php endif; ?>
                        <?php if ( $apple_music ) : ?>
                            <li><a href="<?php echo esc_url( $apple_music ); ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>-child/assets/images/icon-apple-music.svg"/></a></li>
                        <?php endif; ?>
                        <?php if ( $youtube_music ) : ?>
                            <li><a href="<?php echo esc_url( $youtube_music ); ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>-child/assets/images/icon-youtube-music.svg"/></a></li>
                        <?php endif; ?>
                        <?php if ( $youtube ) : ?>
                            <li><a href="<?php echo esc_url( $youtube ); ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>-child/assets/images/icon-youtube.svg"/></a></li>
                        <?php endif; ?>
                        <?php if ( $twitter ) : ?>
                            <li><a href="<?php echo esc_url( $twitter ); ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>-child/assets/images/icon-twitter.svg"/></a></li>
                        <?php endif; ?>
                        <?php if ( $facebook ) : ?>
                            <li><a href="<?php echo esc_url( $facebook ); ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>-child/assets/images/icon-facebook.svg"/></a></li>
                        <?php endif; ?>
                        <?php if ( $instagram ) : ?>
                            <li><a href="<?php echo esc_url( $instagram ); ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>-child/assets/images/icon-instagram.svg"/></a></li>
                        <?php endif; ?>
                        <?php if ( $tiktok ) : ?>
                            <li><a href="<?php echo esc_url( $tiktok ); ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>-child/assets/images/icon-tiktok.svg"/></a></li>
                        <?php endif; ?>
                        <?php if ( $email ) : ?>
                            <li><a href="mailto:<?php echo esc_html( $email ); ?>"><img src="<?php echo get_template_directory_uri(); ?>-child/assets/images/icon-email.svg"/></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="right">
                    <?php the_title('<h1 class="page-title line">', '</h1>'); ?>
                    <?php if ( $bio ) : ?>
                        <p><?php echo wpautop( $bio ); ?></p>
                    <?php endif; ?>
                    <?php
                        if ( $player ) :
                            $allowed_html = array(
                                'iframe' => array(
                                    'src' => true,
                                    'width' => true,
                                    'height' => true,
                                    'frameborder' => true,
                                    'allowfullscreen' => true,
                                ),
                                'div' => array(
                                    'class' => true,
                                    'id' => true,
                                    'style' => true,
                                ),
                            );
                        ?>
                        <div class="deia-player"><?php echo wp_kses($player, $allowed_html); ?></div>
                    <?php endif; ?>
                    <!-- space for players -->
                </div>
            </div>
        <?php
        endwhile;
    else :
        echo '<p>No posts found</p>';
    endif;
    ?>

</div><!-- #primary -->

<?php get_footer(); ?>