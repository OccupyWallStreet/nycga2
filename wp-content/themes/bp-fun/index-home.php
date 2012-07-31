<?php /* Template Name: Homepage */
get_header();
?>

<div id="post-entry">

<?php
$home_featured_block_style = get_option('tn_buddyfun_home_featured_block_style');
$home_featured_block = get_option('tn_buddyfun_home_featured_block'); if($home_featured_block != 'hide') { ?>

<?php if($home_featured_block_style != 'slideshow') { ?>
<?php locate_template( array( 'lib/templates/wp-template/main-column.php'), true ); ?>
<?php } else { ?>
<?php locate_template( array( 'lib/templates/wp-template/slideshow.php'), true ); ?>
<?php } ?>

<?php } ?>

<div class="ad-spot"><?php do_action('ads_spot'); ?></div>
<div id="bottom-entry">

<?php if ( is_active_sidebar( __('left-column', TEMPLATE_DOMAIN ) ) ) : ?>
<div class="bpside" id="left-column">
<?php dynamic_sidebar( __('left-column', TEMPLATE_DOMAIN ) ); ?>
</div>
<?php endif; ?>

<?php if ( is_active_sidebar( __('center-column', TEMPLATE_DOMAIN ) ) ) : ?>
<div class="bpside" id="right-column">
<?php dynamic_sidebar( __('center-column', TEMPLATE_DOMAIN ) ); ?>
</div>
<?php endif; ?>

</div>

</div>

<?php get_sidebar('home'); ?>

<?php get_footer(); ?>