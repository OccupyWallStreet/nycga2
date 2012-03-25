<?php get_header(); ?>

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<h1 class="page-title"><?php _e('The Blog', 'themejunkie'); ?></h1>
        <div id="content">
		 	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h1 class="entry-title"><?php the_title(); ?></h1>
		
				<div class="entry-meta">
					<span class="entry-author"><?php the_author_posts_link(); ?></span>
					<span class="entry-date"><?php the_time('M jS, Y') ?></span> 
					<span class="entry-comment"><?php comments_popup_link( __( '0 Comment', 'themejunkie' ), __( '1 Comment', 'themejunkie' ), __( '% Comments', 'themejunkie' ) ); ?></span>
				</div><!-- .entry-meta -->
			   	<div class="entry-content">
					<?php if(get_option('workspace_integrate_singletop_enable') == 'on') echo (get_option('workspace_integration_single_top')); ?>	   	
		            <?php the_content(''); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'themejunkie' ), 'after' => '</div>' ) ); ?>
					<?php if(get_option('workspace_integrate_singlebottom_enable') == 'on') echo (get_option('workspace_integration_single_bottom')); ?>				    	
			    	<div class="clear"></div>
					<?php printf(the_tags(__('<div class="entry-tags"><span>Tags:</span>&nbsp;','themejunkie'),', ','</div>')); ?>
					<?php edit_post_link('('.__('Edit', 'themejunkie').')', '<span class="entry-edit">', '</span>'); ?>
			  	</div><!-- .entry-content -->
		  	</div><!-- #post-<?php the_ID(); ?> -->
		  	
		  	<div class="clear"></div>
		  	
		  	<?php if(get_option('workspace_show_author_box') == 'on') { ?>	  	
			  	<div class="authorbox">
					<p><?php echo get_avatar( get_the_author_meta('email'), '48' ); ?>
					<strong><?php the_author_posts_link(); ?></strong><br />
					<?php the_author_meta( 'description' ); ?></p>
					<div class="clear"></div>
				</div><!-- .authorbox-->
			<?php } ?>
			
	    	<?php if(get_option('workspace_show_post_comments') == 'on') { ?>
		  		<?php comments_template(); ?> 	
		  	<?php } ?>
	  	</div><!-- #content -->
	  	
	<?php endwhile; else: ?>
	<?php endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
