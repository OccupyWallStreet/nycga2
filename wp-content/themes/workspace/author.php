<?php get_header(); ?>

	<?php
		$author = get_userdata(get_query_var('author'));
		$author_id = $author->ID;
		$author_name = $author->display_name;
	
		if ( isset($author->twitter) ) { $author_twitter = $author->twitter; } else { $author_twitter = ''; }
		if ( isset($author->facebook) ) { $author_facebook = $author->facebook; } else { $author_facebook = ''; }
	?>
	
	<h1 class="page-title"><?php printf( esc_attr__( 'Posts by %s', 'themejunkie' ), $author_name ); ?></h1>
	<div id="content">
		<div id="author-info">
			<div class="post-author-box clear <?php if($twitter) { echo "with-twitter"; } ?>">
				<h3 class="title"><?php printf( esc_attr__( 'About %s', 'themejunkie' ), $author_name ); ?></h3>
				<?php echo get_avatar( $author_id, '70' ); ?>
				<div class="profile-description">
					<?php the_author_meta( 'description', $author_id ); ?>
		
					<div class="profile-social">
						<ul>
							<?php if ($author_twitter != '') { ?>
								<li class="twitter">
									<a href="http://www.twitter.com/<?php echo $author_twitter; ?>"><?php _e( 'Twitter', 'themejunkie' ); ?></a>
								</li><?php } ?>
							<?php if ($author_facebook != '') { ?>
								<li class="facebook">
									<a href="<?php echo $author_facebook; ?>"><?php _e( 'Facebook', 'themejunkie' ); ?></a>
								</li>
							<?php } ?>
						</ul>
					</div><!-- .profile-social -->
				</div><!-- .profile-description	-->
			</div><!-- #post-author -->
		</div><!-- #author-info -->
		<?php if(have_posts()): while (have_posts()) : the_post(); ?>
			<?php get_template_part('includes/loop'); ?>
		<?php endwhile; else:?>
			<p><?php _e( 'Sorry, no posts matched your criteria.', 'themejunkie' ); ?></p>
		<?php endif;?>
		<?php if (function_exists('wp_pagenavi')) wp_pagenavi(); else { ?>
		<div class="pagination">
			<div class="left"><?php previous_posts_link(__('Newer Entries', 'themejunkie')) ?></div>
			<div class="right"><?php next_posts_link(__('Older Entries', 'themejunkie')) ?></div>
			<div class="clear"></div>
		</div><!-- .pagination -->
		<?php } ?>
	</div><!-- #content -->
	
<?php get_sidebar(); ?>      
<?php get_footer(); ?>