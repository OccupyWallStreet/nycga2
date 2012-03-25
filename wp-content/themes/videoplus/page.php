<?php get_header(); ?>

	<div id="content">
	    <article>
		<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<h1 class="page-title"><?php the_title(); ?></h1>
			<div class="entry-content">
				<?php the_content(''); ?>
				<?php edit_post_link('('.__('Edit', 'themejunkie').')', '<span class="entry-edit">', '</span>'); ?>
				<div class="clear"></div>	  		</div><!-- .entry-content -->
	    	<?php if(get_option('videoplus_show_page_comments') == 'on') { ?>
		  		<?php comments_template(); ?>
		  	<?php } ?>  		
		<?php endwhile; ?>
		<?php else : ?>
		<?php endif; ?>
	    </article><!-- article -->
	</div> <!-- #content -->
	
<?php get_sidebar(); ?>
<?php get_footer(); ?>