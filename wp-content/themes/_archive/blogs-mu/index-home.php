<?php /* Template Name: Homepage */
get_header();
?>

<?php include ( TEMPLATEPATH . '/options-var.php' ); ?>
                            
<?php if($tn_blogsmu_home_service_block == "disable") { ?>

<?php } else { ?>

<?php if($tn_blogsmu_home_service_intro_text != '') { ?>
<h2 class="services-brief">
<?php echo stripslashes( $tn_blogsmu_home_service_intro_text ); ?>
</h2>
<?php } ?>

<?php if($tn_blogsmu_home_service_style == "service-mode" || $tn_blogsmu_home_service_style == "") { ?>
<?php locate_template( array('lib/templates/wp-template/service-mode.php'), true); ?>
<?php } else { ?>
<?php locate_template( array('lib/templates/wp-template/post-mode.php'), true); ?>
<?php } ?>


<?php } ?>

<?php get_footer(); ?>