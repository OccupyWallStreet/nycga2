	<?php include (get_template_directory() . '/library/options/options.php'); ?>
	<?php if($bp_existed == 'true') : ?>
	<?php do_action( 'bp_before_sidebar' ) ?>
	<?php endif; ?>
	<div id="sidebar">
		<div class="padder">
			<?php if($bp_existed == 'true') : ?>
			
	<?php /* Show forum tags on the forums directory */
	if ( BP_FORUMS_SLUG == bp_current_component() && bp_is_directory() ) : ?>
		<div id="forum-directory-tags" class="widget tags">

			<h3 class="widgettitle"><?php _e( 'Forum Topic Tags', 'business-portfolio' ) ?></h3>
			<?php if ( function_exists('bp_forums_tag_heat_map') ) : ?>
				<div id="tag-text"><?php bp_forums_tag_heat_map(); ?></div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php endif ?>
	<ul class="sidebar_list">
					<?php if ( is_active_sidebar( 'members-sidebar' ) ) : ?>
							<?php dynamic_sidebar( 'members-sidebar' ); ?>
					<?php endif; ?>
						<?php if($bp_existed == 'true') : ?>
						<?php do_action( 'bp_inside_after_sidebar' ) ?>
						<?php endif; ?>
	</ul>
	</div><!-- .padder -->
</div><!-- #sidebar -->
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_after_sidebar' ) ?>
<?php endif; ?>
