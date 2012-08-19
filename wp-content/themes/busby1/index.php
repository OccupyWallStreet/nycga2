<?php get_header(); ?>
<div id="left">
<?php 
// slider div 
custom_slider();  
?>

<?php /* Start the Loop */ ?>
<?php while ( have_posts() ) : the_post(); ?>
<?php get_template_part( 'content', get_post_format() ); ?>
<?php endwhile; ?>

<?php kriesi_pagination(); ?>

</div>




<?php get_sidebar(); ?>
<?php get_footer(); ?>