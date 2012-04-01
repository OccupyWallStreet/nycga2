<div id="sidebar">

	<?php if(is_page() || is_archive() || is_search() ):?>

	    <?php if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('right-sidebar-pages') ) :?>
	        <?php the_widget('WP_Widget_Text', 'title=Right Sidebar - Pages&text=You could add some widgets in this area.', 'before_title=<h3 class="widget-title"><span>&after_title=</span></h3>'); ?>
	    <?php endif; ?>

	<?php elseif(is_single()) : ?>

	    <?php if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('right-sidebar-posts') ): ?>
	        <?php the_widget('WP_Widget_Text', 'title=Right Sidebar - Posts&text=You could add some widgets in this area.', 'before_title=<h3 class="widget-title"><span>&after_title=</span></h3>'); ?>
	    <?php endif; ?>

	<?php else :?>

	    <?php if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('right-sidebar-home') ): ?>
	        <?php the_widget('WP_Widget_Text', 'title=Right Sidebar - Home&text=You could add some widgets in this area.', 'before_title=<h3 class="widget-title"><span>&after_title=</span></h3>'); ?>
	    <?php endif; ?>

	<?php endif; ?>
	
</div><!-- #sidebar -->
