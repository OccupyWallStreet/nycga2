<?php get_header(); ?>
<div id="main" class="group">
	<section id="posts">
		<?php while (have_posts() ) : the_post(); ?>
            <article class="post group">
            	
				<p class="tags">
				<?php 
					$isTutorial = get_post_meta($post->ID, 'ci_post_tutorial', true) == 'selected' ? true : false;
					if ($isTutorial):
						foreach(get_the_tags() as $the_tag)
						{
							echo '<a href="'.get_home_url().'/tag/'.$the_tag->slug.'/">';
							echo '<img src="'.get_bloginfo('template_url').'/images/icon_'. $the_tag->slug.'.png" />';
							echo '</a>';
						}
					endif;
				?>
				</p>
				<h2><a href="<?php the_permalink(); ?>" title="<?php echo __('Permalink to', CI_DOMAIN).' '.get_the_title(); ?>"><?php the_title(); ?></a></h2>
				<p class="date"><?php echo get_the_date(); ?></p>
				<?php if ($isTutorial): ?>
					<?php the_post_thumbnail('post-thumbnail', array('class'=>'post-thumb alignleft')); ?>
					<?php echo wpautop( get_post_meta($post->ID, 'ci_post_description', true) ); ?>
					<section class="meta group">
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
					</section>
				<?php endif; ?>

				<?php if ( ! $isTutorial ) ci_e_content(); ?>

				<section class="post-footer">
					<p><?php comments_popup_link(); ?></p>
					<p class="more"><a href="<?php the_permalink(); ?>">
						<?php 
							if ($isTutorial)
								_e('Read the tutorial', CI_DOMAIN); 
							else
								_e('Read the post', CI_DOMAIN);
						?>
					</a></p>
				</section><!-- .post-footer -->
            </artice><!-- .post -->
   			<?php if (is_single() or is_page()) comments_template(); ?>
		<?php endwhile; ?>

		<?php ci_pagination(); ?>
	</section><!-- #posts -->


    <aside id="sidebar">
		<?php dynamic_sidebar('sidebar-right'); ?>
    </aside><!-- #sidebar -->
</div><!-- #main -->

<?php get_footer(); ?>
