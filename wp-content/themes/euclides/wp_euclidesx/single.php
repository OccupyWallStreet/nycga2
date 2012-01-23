<?php get_header(); ?>
<div id="main" class="group">
	<div id="posts">
		<?php while (have_posts() ) : the_post(); ?>
            <div class="post group">
            	
				<p class="tags">
				<?php 
					global $post;
					if (is_single() and !is_page())
					{
						$isTutorial = get_post_meta($post->ID, 'ci_post_tutorial', true) == 'selected' ? true : false;
						if ($isTutorial):
							foreach(get_the_tags() as $the_tag)
							{
								echo '<a href="'.get_home_url().'/tag/'.$the_tag->slug.'/">';
								echo '<img src="'.get_bloginfo('template_url').'/images/icon_'. $the_tag->slug.'.png" />';
								echo '</a>';
							}
						endif;
					}
				?>
				</p>
				<h1><a href="<?php the_permalink(); ?>" title="<?php echo __('Permalink to', CI_DOMAIN).' '.get_the_title(); ?>"><?php the_title(); ?></a></h1>
				<p class="date"><?php echo get_the_date(); ?></p>
				<?php if ($isTutorial): ?>
					<?php the_post_thumbnail('post-thumbnail', array('class'=>'post-thumb alignleft')); ?>
					<?php echo wpautop( get_post_meta($post->ID, 'ci_post_description', true) ); ?>
					<div class="meta group">
						<p>
							<?php 
								// Demo and Download buttons will have an extra class of 'buttons-1' or 'buttons-2'
								// This way they can be styled if one or both exist.
								$demolink = get_post_meta($post->ID, 'ci_post_demo_link', true);							
								$downlink = get_post_meta($post->ID, 'ci_post_download_link', true);
								$buttons = 0;
								if (!empty($demolink)) $buttons++;
								if (!empty($downlink)) $buttons++;
								$extraClass = "buttons-".$buttons;
							?>
							<?php if (!empty($demolink)): ?>
								<a class="meta-button <?php echo $extraClass; ?>" href="<?php echo $demolink; ?>"><span><?php _e('View Demo', CI_DOMAIN); ?></span></a>
							<?php endif; ?>
							<?php if (!empty($downlink)): ?>
								<a class="meta-button <?php echo $extraClass; ?>" href="<?php echo $downlink; ?>"><span><?php _e('Download', CI_DOMAIN); ?></span></a>
							<?php endif; ?>
						</p>
						<ul>
							<li><?php _e('Level:', CI_DOMAIN); ?> <span><?php echo get_post_meta($post->ID, 'ci_post_level', true); ?></span></li>
							<li><?php _e('Duration:', CI_DOMAIN); ?> <span><?php echo get_post_meta($post->ID, 'ci_post_duration', true); ?></span></li>
							<li><?php _e('Author:', CI_DOMAIN); ?> <span><?php the_author_link(); ?></span></li>
						</ul>
		                <!--
		                <p class="cats"><?php the_category(', '); ?></p>
						<p class="comments-no"><?php comments_popup_link(); ?></p>
						-->
					</div>
				<?php endif; ?>

				<?php ci_e_content(); ?>
				
				<?php if( function_exists("cpr_populate") ): ?>
					<?php $related = cpr_populate($post->ID); ?>
					<?php if( count($related) > 0 ): ?>
						<div id="related">
							<h3><?php _e('Related Posts', CI_DOMAIN); ?></h3>
							<ul class="group">
								<?php $thispost = $post; $i = 0; ?>
								<?php foreach($related as $post): ?>
									<?php setup_postdata($post); ?>
									<li class="<?php echo (++$i % 3 == 0 ? 'last' : ''); ?>">
										<?php the_post_thumbnail('ci_euclides_cptr'); ?>
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</li>
								<?php endforeach; ?>
								<?php $post = $thispost; setup_postdata($post); ?>
							</ul>
						</div>
					<?php endif; ?>
				<?php endif; ?>
	
	   			<?php if (is_single() or is_page()) comments_template(); ?>
            </div>
		<?php endwhile; ?>

		<?php ci_pagination(); ?>
	</div><!-- #posts -->


    <div id="sidebar">
		<?php dynamic_sidebar('sidebar-right'); ?>
    </div><!-- #sidebar -->
</div><!-- #main -->

<?php get_footer(); ?>
