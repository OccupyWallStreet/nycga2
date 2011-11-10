<?php 

add_action( 'bizz_widgets', 'bizz_widgets_area' );

function bizz_widgets_area() { 

?>

<?php bizz_widgets_before(); ?>

<div class="grid_4 sidebar <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignleft' ) { ?>sidebar_left<?php } ?> equalh">
	
	<?php 
		
		global $wp_query, $post;
		$templateid = get_post_meta($wp_query->post->ID, '_wp_page_template'); // check for page template
		
		if ( ( function_exists('dynamic_sidebar') && (is_sidebar_active(1)) && is_front_page() ) ) :
		    dynamic_sidebar(1);
		elseif ( ( function_exists('dynamic_sidebar') && (is_sidebar_active(2)) && ( is_single() or is_page() ) && !is_page_template('template-blog.php') && !is_page_template('template-faqs.php') ) ) :
		    dynamic_sidebar(2);
		elseif ( ( function_exists('dynamic_sidebar') && is_sidebar_active(3) && is_archive() or is_search() or ( is_page_template('template-blog.php') or is_page_template('template-faqs.php') ) ) ) :
		    dynamic_sidebar(3);
		endif; 
		
	?>
	
</div><!-- /sidebar -->

<?php bizz_widgets_after(); ?>

<?php } ?>