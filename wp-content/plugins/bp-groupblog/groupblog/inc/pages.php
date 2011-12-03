<?php $current = $post->ID; ?> 
<?php $nav_query = new WP_Query( 'post_type=page&showposts=-1&order=ASC' );	?>

<?php if ( $nav_query->have_posts() ) :?>
	
	<div class="page" id="blog-pages">
	
	    <div id="groupblog-pages">
	      	
				<ul id="groupblog-page-list">	
				
				<?php while ( $nav_query->have_posts() ) : $nav_query->the_post(); ?>
				
		    	<li><a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
		    	
				<?php endwhile; ?>
				
				</ul>
		  
		  </div>
		  
	</div>
	
<?php endif; ?>