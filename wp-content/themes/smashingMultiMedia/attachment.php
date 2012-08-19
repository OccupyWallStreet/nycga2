<?php get_header();?> 
<div id="floatswrap" class="smallftfl clearfix">

	<div class="container clearfix">
		<?php the_post(); ?>

		
		<h1 class="entry-title"><a href="<?php echo get_permalink($post->post_parent) ?>" title="<?php printf( __( 'Return to %s', 'smashingMultiMedia' ), wp_specialchars( get_the_title($post->post_parent), 1 ) ) ?>" rev="attachment"><span class="meta-nav">&laquo; </span><?php echo get_the_title($post->post_parent) ?></a></h1>

		
		<div id="main_col">
			<div <?php post_class('single_post clearfix'); ?> id="post-<?php the_ID(); ?>">
				<h2><?php the_title(); ?></h2>

				<a href="#nogo" class="metaInfo">Info</a>
					
				<div class="meta tooltip">
					<p><?php _e('Published: ', 'smashingMultiMedia'); ?><?php the_time( get_option( 'date_format' ) ); ?> | <?php _e('By ', 'smashingMultiMedia'); ?><a href="<?php echo get_author_link( false, $authordata->ID, $authordata->user_nicename ); ?>" title="<?php printf( __( 'View all posts by %s', 'smashingMultiMedia' ), $authordata->display_name ); ?>"><?php the_author(); ?></a></p>
					<p><?php _e( 'Posted in: ', 'smashingMultiMedia' ); ?><?php echo get_the_category_list(', '); ?></p>
					<?php the_tags( '<p>' . __('Tagged as: ', 'smashingMultiMedia' ), ',' , '</p>' ) ?>
					<p><?php previous_post_link( '%link', '<span class="meta-nav">&laquo;</span> Previous: %title' ) ?> | <?php next_post_link( '%link', 'Next: %title <span class="meta-nav">&raquo;</span>' ) ?></p>
				</div>
				
				                                
				<?php if ( wp_attachment_is_image( $post->id ) ) : $att_image = wp_get_attachment_image_src( $post->id, "full"); ?>
                    <p class="attachment">
						<a href="<?php echo wp_get_attachment_url($post->id); ?>" title="<?php the_title(); ?>" rel="attachment">
							<img src="<?php echo $att_image[0];?>" width="<?php echo $att_image[1];?>" height="<?php echo $att_image[2];?>"  class="attachment-full" alt="<?php $post->post_excerpt; ?>" />
						</a>
                    </p>
				<?php else : ?>         
                    <a href="<?php echo wp_get_attachment_url($post->ID) ?>" title="<?php echo wp_specialchars( get_the_title($post->ID), 1 ) ?>" rel="attachment"><?php echo basename($post->guid) ?></a>          
				<?php endif;        
                                        
				if ( !empty($post->post_excerpt) ) the_excerpt();
                                                
				the_content( __( 'Continue reading <span class="meta-nav">&raquo;</span>', 'smashingMultiMedia' )  );
				wp_link_pages('before=<div class="page-link">' . __( 'Pages:', 'smashingMultiMedia' ) . '&after=</div>') ?>

						
			</div><!-- single_post -->
			<div id="social">
				<ul id="socialtabs" class="clearfix">
					<li class="showSubscribe" ><a href="#" rel="#subscribeoverlay"><img src="<?php bloginfo('wpurl'); ?>/wp-content/themes/<?php echo get_option('wps_child_theme'); ?>/images/socialIcons/rss_48.jpg" alt="Subscribe"/><?php _e('Subscribe','smashingMultiMedia');?></a></li>
					<li class="showShare" ><a href="#" rel="#shareoverlay"><img src="<?php bloginfo('wpurl'); ?>/wp-content/themes/<?php echo get_option('wps_child_theme'); ?>/images/socialIcons/heart_48.jpg" alt="Share"/><?php _e('Share','smashingMultiMedia');?></a></li>
				</ul>
						
				<div id="subscribeoverlay" class="overlay">
					<h2><?php _e('Stay Informed','smashingMultiMedia');?></h2>
					<p><?php echo get_option('wps_subscribe_text'); ?></p>
					<p class="ico clearfix subscribe_ico">
						<a href="<?php echo get_option('wps_feedburner_rsslink'); ?>" target="_blank"><img src="<?php bloginfo('wpurl'); ?>/wp-content/themes/<?php echo get_option('wps_child_theme'); ?>/images/socialIcons/rss.png" alt="Subscribe to the RSS feed"/></a>
						<a href="<?php echo get_option('wps_feedburner_emaillink'); ?>" target="_blank"><img src="<?php bloginfo('wpurl'); ?>/wp-content/themes/<?php echo get_option('wps_child_theme'); ?>/images/socialIcons/mail.png" alt="Sign up for Email alerts"/></a>
						<a href="http://twitter.com/<?php echo get_option('wps_twitter'); ?>" target="_blank"><img src="<?php bloginfo('wpurl'); ?>/wp-content/themes/<?php echo get_option('wps_child_theme'); ?>/images/socialIcons/twitterbird.png" alt="Follow on Twitter"/></a>
					</p>
				</div>
						
				<div id="shareoverlay" class="overlay">
					<h2><?php _e('Bookmark &amp; Share','smashingMultiMedia');?></h2>
					<p><?php echo get_option('wps_share_text'); ?></p>
					<p class="ico clearfix share_ico">
						<a href="http://delicious.com/save?url=<?php the_permalink() ?>&title=<?php the_title() ?>" rel="nofollow" target="_blank"><img src="<?php bloginfo('wpurl'); ?>/wp-content/themes/<?php echo get_option('wps_child_theme'); ?>/images/socialIcons/delicious.png" alt="del.icio.us"/><?php _e('del.icio.us','smashingMultiMedia'); ?></a>
						<a href="http://digg.com/submit?phase=2&amp;url=<?php the_permalink() ?>&amp;title=<?php the_title(); ?>" rel="nofollow" target="_blank"><img src="<?php bloginfo('wpurl'); ?>/wp-content/themes/<?php echo get_option('wps_child_theme'); ?>/images/socialIcons/digg.png" alt="Digg"/><?php _e('Digg','smashingMultiMedia'); ?></a>
						<a href="http://www.facebook.com/sharer.php?u=<?php the_permalink();?>" rel="nofollow" target="_blank"><img src="<?php bloginfo('wpurl'); ?>/wp-content/themes/<?php echo get_option('wps_child_theme'); ?>/images/socialIcons/facebook.png" alt="Facebook"/><?php _e('Facebook','smashingMultiMedia'); ?></a>
						<a href="http://www.mixx.com/submit?page_url=<?php the_permalink() ?>" rel="nofollow" target="_blank"><img src="<?php bloginfo('wpurl'); ?>/wp-content/themes/<?php echo get_option('wps_child_theme'); ?>/images/socialIcons/mixx.png" alt="Mixx"/><?php _e('Mixx','smashingMultiMedia'); ?></a>
						<a href="http://reddit.com/submit?url=<?php the_permalink(); ?>&amp;title=<?php the_title(); ?>" rel="nofollow" target="_blank"><img src="<?php bloginfo('wpurl'); ?>/wp-content/themes/<?php echo get_option('wps_child_theme'); ?>/images/socialIcons/reddit.png" alt="Reddit"/><?php _e('Reddit','smashingMultiMedia'); ?></a>
						<a href="http://www.stumbleupon.com/submit?url=<?php the_permalink(); ?>&amp;title=<?php the_title(); ?>" rel="nofollow" target="_blank"><img src="<?php bloginfo('wpurl'); ?>/wp-content/themes/<?php echo get_option('wps_child_theme'); ?>/images/socialIcons/stumbleupon.png" alt="Stumbleupon"/><?php _e('Stumbleupon','smashingMultiMedia'); ?></a>
						<a href="http://technorati.com/ping/?url=<?php the_permalink() ?>" rel="nofollow" target="_blank"><img src="<?php bloginfo('wpurl'); ?>/wp-content/themes/<?php echo get_option('wps_child_theme'); ?>/images/socialIcons/technorati.png" alt="Technorati"/><?php _e('Technorati','smashingMultiMedia'); ?></a>
						<?php $shortenedurl = file_get_contents('http://tinyurl.com/api-create.php?url=' . urlencode(get_permalink())); ?>
						<a href="http://www.twitter.com/home?status=<?php echo str_replace(' ', '+', the_title_attribute('echo=0')); echo '+' . $shortenedurl; ?>" title="<?php _e('Twitter','smashingMultiMedia'); ?>" rel="nofollow" target="_blank"><img src="<?php bloginfo('wpurl'); ?>/wp-content/themes/<?php echo get_option('wps_child_theme'); ?>/images/socialIcons/twitter.png" alt="Twitter"/><?php _e('Twitter','smashingMultiMedia'); ?></a>
					</p>
				</div>
			</div><!--social-->
			
			<?php //printf( __( 'Follow any comments here with the <a href="%2$s" title="Comments RSS to %1$s" rel="alternate" type="application/rss+xml">RSS feed for this post</a>.', 'smashingMultiMedia' ),the_title_attribute('echo=0'),comments_rss() ) ?>
			

			
			<?php if ('open' == $post-> comment_status) { comments_template('', true); } ?>

		</div><!-- main_col -->
		<?php get_sidebar(); ?>
	</div><!-- container -->
</div><!-- floatswrap-->
<?php get_footer(); ?>