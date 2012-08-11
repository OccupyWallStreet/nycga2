<?php include (get_template_directory() . '/library/options/options.php'); ?>	
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_before_sidebar' ) ?>
<?php endif; ?>
<div id="sidebar">
	<div id="sidebar-top">
<div id="sidebar-content">
	<div class="padder">
			<?php
				$twitter = get_option('dev_businessfeature_twitter');
				$twitter_text = get_option('dev_businessfeature_twitter_text');
				$twitter_link = get_option('dev_businessfeature_twitter_link');
				$twitter_linktitle = get_option('dev_businessfeature_twitter_linktext');
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
				<?php if ( is_active_sidebar( 'blog-sidebar' ) ) : ?>
						<?php dynamic_sidebar( 'blog-sidebar' ); ?>
				<?php endif; ?>
					<?php if($bp_existed == 'true') : ?>
					<?php do_action( 'bp_inside_after_sidebar' ) ?>
					<?php endif; ?>
	</div><!-- .padder -->
</div><!-- #sidebar -->
	</div><!-- .padder -->
</div><!-- #sidebar -->
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_after_sidebar' ) ?>
<?php endif; ?>