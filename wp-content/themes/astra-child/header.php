<?php
/**
 * The header for Astra Theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?><!DOCTYPE html>
<?php astra_html_before(); ?>
<html <?php language_attributes(); ?>>
<head>
<?php astra_head_top(); ?>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php 
if ( apply_filters( 'astra_header_profile_gmpg_link', true ) ) {
	?>
	 <link rel="profile" href="https://gmpg.org/xfn/11"> 
	 <?php
} 
?>
<?php wp_head(); ?>
<?php astra_head_bottom(); ?>
</head>

<body <?php astra_schema_body(); ?> <?php body_class(); ?>>
<?php astra_body_top(); ?>
<?php wp_body_open(); ?>

<a
	class="skip-link screen-reader-text"
	href="#content"
	role="link"
	title="<?php echo esc_attr( astra_default_strings( 'string-header-skip-link', false ) ); ?>">
		<?php echo esc_html( astra_default_strings( 'string-header-skip-link', false ) ); ?>
</a>

<?php if ( is_singular('post') && in_category('musicians') ) : ?>
    <?php
    $image_header = get_post_meta(get_the_ID(), 'musician_header_image', true);
    $musician_image_header_url = $image_header ? wp_get_attachment_image_url($image_header, 'full') : '';
    ?>
    <?php if ( !empty($musician_image_header_url) ) : ?>
    <style>
        .musician-header {
			position: absolute;
			top: 0;
			left: 0;
            background-image: url('<?php echo esc_url($musician_image_header_url); ?>');
            background-size: cover;
            background-position: top center;
			height: 1000px;
			width: 100%;
			z-index: 0;
        }
		.musician-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
			/* Adjust the 50% value to occupy more space */
            /* background: linear-gradient(to bottom, rgba(0, 0, 0, 1) 10%, rgba(0, 0, 0, 0.7) 60%); */
			background: linear-gradient(to bottom, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 1) 90%);
            z-index: 1;
        }
        .musician-header-content {
            position: relative;
            z-index: 2; /* Ensure the content is above the gradient overlay */
        }
    </style>
    <div class="musician-header">
        <div class="musician-header-content">
            <!-- You can place any additional content here if needed -->
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>

<div
<?php
	$musician_header_image_style = '';
	if ( is_singular('musicians') ) {
		$musician_header_image_style = 'style="background-image: url(' . esc_url(get_post_meta(get_the_ID(), 'musician_header_image', true)) . ');"';
	}

	echo astra_attr(
		'site',
		array(
			'id'    => 'page',
			'class' => 'hfeed site',
		)
	);
	?>
>
	<?php
	astra_header_before();

	astra_header();

	astra_header_after();

	astra_content_before();
	?>
	<div id="content" class="site-content">
		<div class="ast-container">
		<?php astra_content_top(); ?>
