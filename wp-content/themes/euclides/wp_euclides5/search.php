<?php get_header(); ?>
<div id="main" class="group">
	<section id="posts">
		<article class="post group">
			<?php 
				global $wp_query;
				$found = $wp_query->found_posts;
				$none = __('No results found. Please broaden your terms and search again.', CI_DOMAIN);
				$one = __('Just one result found. We either nailed it, or you might want to broaden your terms and search again.', CI_DOMAIN);
				$many = sprintf(__("%d results found.", CI_DOMAIN), $found);
			?>
			<h2 class="search-message"><?php ci_e_inflect($found, $none, $one, $many); ?></h2>	
		</article>

		<?php while (have_posts() ) : the_post(); ?>
            <article class="post group">
				<p class="tags">
				<?php 
					if (is_single() and !is_page())
					{
						foreach(get_the_tags() as $the_tag)
						{
							echo '<a href="'.get_home_url().'/tag/'.$the_tag->slug.'/">';
							echo '<img src="'.get_bloginfo('template_url').'/images/icon_'. $the_tag->slug.'.png" />';
							echo '</a>';
						}
					}
				?>
				</p>
				<h1><a href="<?php the_permalink(); ?>" title="<?php echo __('Permalink to', CI_DOMAIN).' '.get_the_title(); ?>"><?php the_title(); ?></a></h1>
				<p class="date"><?php echo get_the_date(); ?></p>
				<?php the_post_thumbnail('post-thumbnail', array('class'=>'post-thumb alignleft')); ?>
				<?php echo wpautop( get_post_meta($post->ID, 'ci_post_description', true) ); ?>
				<section class="meta group">
					<p>
						<a class="meta-button" href="<?php echo get_post_meta($post->ID, 'ci_post_demo_link', true); ?>"><span><?php _e('View Demo', CI_DOMAIN); ?></span></a>
						<a class="meta-button" href="<?php echo get_post_meta($post->ID, 'ci_post_download_link', true); ?>"><span><?php _e('Download', CI_DOMAIN); ?></span></a>
					</p>
					<ul>
						<li><?php _e('Level:', CI_DOMAIN); ?> <span><?php echo get_post_meta($post->ID, 'ci_post_level', true); ?></span></li>
						<li><?php _e('Duration:', CI_DOMAIN); ?> <span><?php echo get_post_meta($post->ID, 'ci_post_duration', true); ?></span></li>
						<li><?php _e('Author:', CI_DOMAIN); ?> <span><?php the_author_link(); ?></span></li>
					</ul>
				</section>
				<section class="post-footer">
					<p><?php comments_popup_link(); ?></p>
					<p class="more"><a href="<?php the_permalink(); ?>"><?php _e('Read the tutorial', CI_DOMAIN); ?></a></p>
				</section>
            </article>
   			<?php if (is_single() or is_page()) comments_template(); ?>
		<?php endwhile; ?>

		<?php ci_pagination(); ?>
	</section><!-- #posts -->


    <aside id="sidebar">
		<?php dynamic_sidebar('sidebar-right'); ?>
    </aside><!-- #sidebar -->
</div><!-- #main -->

<?php get_footer(); ?>
