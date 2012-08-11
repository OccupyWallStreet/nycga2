<?php /* Template Name: Homepage */
get_header();
?>

<?php locate_template( array( 'lib/templates/wp-template/front-left.php'), true ); ?>
<?php locate_template( array( 'lib/templates/wp-template/front-right.php'), true ); ?>

<?php get_footer(); ?>