<?php
$parentPage_title 	= get_the_title($post->post_parent);
$currentPage_title 	= get_the_title($post->post_nicename);
get_header();?> 

	
	<div id="floatswrap" class="smallftfl clearfix">
			
	
		<div class="container clearfix">
		
			<h2 class="page-title"><?php if ($parentPage_title == $currentPage_title) { echo $currentPage_title;} else { echo $parentPage_title;?> &raquo; <?php echo $currentPage_title; }?></h2>
			
			
			<div id="main_col">
				
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<div <?php post_class('page_post');?> id="post-<?php the_ID(); ?>">
						<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); 
						wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					</div><!-- page_post -->
				<?php endwhile; endif; ?>
			
			</div><!-- main_col -->
			
			<?php get_sidebar(); ?> 
			
		</div><!-- container -->
	</div><!-- floatswrap-->
<?php get_footer(); ?>