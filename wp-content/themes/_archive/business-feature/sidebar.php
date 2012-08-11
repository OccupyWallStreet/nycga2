<?php include (get_template_directory() . '/library/options/options.php'); ?>	
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_before_sidebar' ) ?>
<?php endif; ?>
	<div id="sidebar">

			<div id="sidebar-top">
		<div id="sidebar-content">
		<div class="padder">
				<?php
					$twitter = get_option('dev_business-feature_twitter');
					$twitter_text = get_option('dev_business-feature_twitter_text');
					$twitter_link = get_option('dev_business-feature_twitter_link');
					$twitter_linktitle = get_option('dev_business-feature_twitter_linktext');
				?>
				<?php

				if ($twitter == "yes"){

				?>
					<div id="tweet">
						<div class="tweet-info">
							<p>
								<?php echo stripslashes($twitter_text); ?>
							</p>
							<a href="<?php echo $twitter_link; ?>" rel="bookmark" title="<?php echo $twitter_linktitle; ?>" class="button"><?php echo stripslashes($twitter_linktitle); ?></a></div>
					</div>
				<?php	
				}
				?>

			<?php if($bp_existed == 'true') : ?>
			
	<?php /* Show forum tags on the forums directory */
	if ( BP_FORUMS_SLUG == bp_current_component() && bp_is_directory() ) : ?>
		<div id="forum-directory-tags" class="widget tags">

			<h3 class="widgettitle"><?php _e( 'Forum Topic Tags', 'business-feature' ) ?></h3>
			<?php if ( function_exists('bp_forums_tag_heat_map') ) : ?>
				<div id="tag-text"><?php bp_forums_tag_heat_map(); ?></div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php endif ?>
						<?php if ( is_active_sidebar( 'members-sidebar' ) ) : ?>
								<?php dynamic_sidebar( 'members-sidebar' ); ?>
						<?php endif; ?>
							<?php if($bp_existed == 'true') : ?>
							<?php do_action( 'bp_inside_after_sidebar' ) ?>
							<?php endif; ?>

	
	</div><!-- .padder -->
</div><!-- #sidebar -->

</div>
</div>
		<?php if($bp_existed == 'true') : ?>
		<?php do_action( 'bp_after_sidebar' ) ?>
		<?php endif; ?>
