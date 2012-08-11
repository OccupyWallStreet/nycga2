	<?php include (get_template_directory() . '/library/options/options.php'); ?>
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_before_sidebar' ) ?>
<?php endif; ?>
<!-- start member / buddypress sidebar -->
<div id="sidebar"><!-- start #sidebar -->
	<div class="padder">
		<?php if($bp_existed == 'true') : ?>
		<?php if ( BP_FORUMS_SLUG == bp_current_component() && bp_is_directory() ) : ?>
		<div id="forum-directory-tags" class="widget tags"><!-- start #forum-directory-tags -->
			<h3 class="widgettitle">
				<?php _e( 'Forum Topic Tags', 'studio' ) ?>
			</h3><?php if ( function_exists('bp_forums_tag_heat_map') ) : ?>
			<div id="tag-text"><!-- start #tag-text -->
				<?php bp_forums_tag_heat_map(); ?>
			</div><!-- end #tag-text -->
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<?php endif; ?>
			<?php if ( is_active_sidebar( 'sidebar-members' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-members' ); ?>
			<?php endif; ?>
				<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_inside_after_sidebar' ) ?>
				<?php endif; ?>
	</div>
</div><!-- end #sidebar -->
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_after_sidebar' ) ?>
<?php endif; ?>
<!-- end member / buddypress sidebar -->