<?php /* Template Name: Homepage */
get_header();
?>
<div id="post-entry" class="home-column">

<?php
$home_featured_block = get_option('tn_buddycorp_home_featured_block');
$home_featured_block_style = get_option('tn_buddycorp_home_featured_block_style');
?>

<?php if($home_featured_block != 'hide') { ?>

<?php if($home_featured_block_style != 'slideshow') { ?>

<?php locate_template( array( 'lib/templates/wp-template/featured-events.php'), true ); ?>

<?php } else { ?>

<?php locate_template( array( 'lib/templates/wp-template/slideshow.php'), true ); ?>

<?php } ?>

<?php } ?>

<?php if ( is_active_sidebar( __('left-column', TEMPLATE_DOMAIN ) ) ) : ?>
<?php dynamic_sidebar( __('left-column', TEMPLATE_DOMAIN ) ); ?>
<?php endif; ?>

</div>

<?php locate_template( array( 'home-sidebar.php'), true ); ?>

<?php get_footer(); ?>