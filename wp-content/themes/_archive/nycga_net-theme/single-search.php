<?php get_header(); ?>

<div id="post-entry" class="single-column">

<?php do_action( 'bp_before_blog_home' ) ?>

<?php do_action("advance-search");?>

<?php do_action( 'bp_after_blog_home' ) ?>

<?php comments_template('',true); ?>

<?php locate_template( array( 'lib/templates/wp-template/paginate.php'), true ); ?>
<?php else: ?>
<?php locate_template( array( 'lib/templates/wp-template/result.php'), true ); ?>
<?php endif; ?>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>