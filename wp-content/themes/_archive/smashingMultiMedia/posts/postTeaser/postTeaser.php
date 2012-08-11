<?php 
if (is_category())  {
	switch(get_option('wps_postContent_catOption')){
						
		case 'content_btn': ?>
			<p><?php the_content_rss('', TRUE, '', $wordLimit); ?></p>
			<a class="readMore" href="<?php the_permalink(); ?>"><?php _e('Read More', 'smashingMultiMedia'); ?></a>
		<?php
		break;
						
		case 'content_link': ?>
			<p class="readMoreLink">
				<?php the_content_rss('', TRUE, '', $wordLimit); ?>
				<a href="<?php the_permalink(); ?>"><?php echo get_option('wps_readMoreLink'); ?></a>
			</p>
		<?php
		break;
														
		case 'excerpt_btn': 
			the_excerpt();?> 
			<a class="readMore" href="<?php the_permalink(); ?>"><?php _e('Read More', 'smashingMultiMedia'); ?></a>
		<?php 
		break;
		
		case 'excerpt_link': 
			the_excerpt();?> 
			<a href="<?php the_permalink(); ?>"><?php echo get_option('wps_readMoreLink'); ?></a>
		<?php 
		break;
	} 
} else {
	switch(get_option('wps_postContent_frPgOption')){
						
		case 'content_btn': ?>
			<p><?php the_content_rss('', TRUE, '', $wordLimit); ?></p>
			<a class="readMore" href="<?php the_permalink(); ?>"><?php _e('Read More', 'smashingMultiMedia'); ?></a>
		<?php
		break;
						
		case 'content_link': ?>
			<p class="readMoreLink">
				<?php the_content_rss('', TRUE, '', $wordLimit); ?>
				<a href="<?php the_permalink(); ?>"><?php echo get_option('wps_readMoreLink'); ?></a>
			</p>
		<?php
		break;
														
		case 'excerpt_btn': 
			the_excerpt();?> 
			<a class="readMore" href="<?php the_permalink(); ?>"><?php _e('Read More', 'smashingMultiMedia'); ?></a>
		<?php 
		break;
		
		case 'excerpt_link': 
			the_excerpt();?> 
			<a href="<?php the_permalink(); ?>"><?php echo get_option('wps_readMoreLink'); ?></a>
		<?php 
		break;
	} 
}?>