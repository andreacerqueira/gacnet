<?php
/**
 * Template Name: Modal Clean Pages
 * 
 * The template for displaying the CONTACT US modal.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Check if Beaver Builder is active in edit mode
if (isset($_GET['fl_builder'])) {
    // Beaver Builder is active in edit mode, include header
    get_header();
}
?>

<!-- Your form goes here -->
<?php
	while ( have_posts() ) :
		the_post();
		?>

		<div class="deia-post-content">

			<?php the_content(); ?>

		</div><!-- #post-## -->

		<?php
	endwhile;
	?>
<?php //echo do_shortcode('[wpforms id="8"]'); ?>

<?php
// Check if Beaver Builder is active in edit mode
if (isset($_GET['fl_builder'])) {
    // Beaver Builder is active in edit mode, include footer
	get_footer();
}
?>