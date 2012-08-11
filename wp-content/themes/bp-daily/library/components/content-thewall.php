<?php 
	$wall_num = get_option('dev_buddydaily_wall_number');
?>
<div class="wall-content">
		<?php query_posts('posts_per_page='. $wall_num . ''); $count == 1;?>
			<?php while (have_posts()) : the_post(); ?>
				<div class="wall-block">
					<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">	<span class="attach-post-image-wall" style="height:100px;width:213px;display:block;background:#e8e8e8 url('<?php the_post_image_url($cat_image_display); ?>') center center no-repeat">&nbsp;</span></a>
						<div class="dark-container">		
																	
					<h6><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h6>	<span class="byline"><?php comments_popup_link( __( 'No Comments &#187;', 'bp-daily' ), __( '1 Comment &#187;', 'bp-daily' ), __( '% Comments &#187;', 'bp-daily' ) ); ?></span>
						</div>
				</div>
				<?php if (($count == 2) || ($count == 5) || ($count == 8) || ($count == 11) || ($count == 14) || ($count == 17)){
					?>
					<div class="clear"></div>
					<?php
				}
				?>
				<?php $count++; ?>
			<?php endwhile; ?>
			<div class="clear"></div>
</div>