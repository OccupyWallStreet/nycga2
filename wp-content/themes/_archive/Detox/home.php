<?php get_header(); ?>

<?php get_template_part('feature'); ?>
<div class="clearfix"></div><hr class="clear" />

<?php
if ( function_exists( 'bp_is_active' ) ){
	get_template_part('slidebp');
} else {
	get_template_part('slide');
}
?>

<div class="clearfix"></div><hr class="clear" />

<?php get_template_part('middle'); ?>
<?php
if ( function_exists( 'bp_is_active' ) ){
	get_template_part('slide');
} else {
}
?>
<?php get_footer(); ?>