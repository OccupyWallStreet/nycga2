<div id="sidebar" class="aside" name="<?php echo get_template_directory_uri();?>">

	<?php if(!is_page() && !is_tax('portfolio-type') && get_post_type() != 'portfolio') :?>

	    <?php if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('blog-sidebar') ) :?>
	        <?php the_widget('WP_Widget_Text', 'title=Blog Sidebar&text=You could add some widgets in this area.', 'before_title=<h3 class="widget-title">&after_title=</h3>'); ?>
	    <?php endif; ?>

	<?php elseif(is_page() && !is_page_template('template-blog.php')) : ?>

	    <?php if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('page-sidebar') ): ?>
	        <?php the_widget('WP_Widget_Text', 'title=Page Sidebar&text=You could add some widgets in this area.', 'before_title=<h3 class="widget-title">&after_title=</h3>'); ?>
	    <?php endif; ?>

	<?php elseif(is_page_template('template-portfolio.php') || is_tax('portfolio-type') || get_post_type() == 'portfolio') :?>

	    <?php if ( !function_exists( 'dynamic_sidebar' ) ) :?>
			<div class="widget">
			    <h3 class="widget-title"><?php _e('Portfolio Types', 'themejunkie'); ?></h3>
			    <ul id="filter">
			      <?php wp_list_categories(array('title_li' => '', 'taxonomy' => 'portfolio-type')); ?>
			    </ul>
			</div><!-- .widget -->
	    <?php endif; ?>

    <?php elseif(is_page_template('template-blog.php')) : ?>

        <?php if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('blog-sidebar') ) :?>
	        <?php the_widget('WP_Widget_Text', 'title=Blog Sidebar&text=You could add some widgets in this area.', 'before_title=<h3 class="widget-title">&after_title=</h3>'); ?>
	    <?php endif; ?>    

	<?php endif; ?>

</div><!-- #sidebar .aside -->
