<?php /* Show forum tags on the forums directory */
if ( BP_FORUMS_SLUG == bp_current_component() && bp_is_directory() ) : ?>
	<div class="content-box-outer">
	<div id="forum-directory-tags" class="widget tags">

	<div class="h3-background">	<h3><?php _e( 'Forum Topic Tags', 'bp-scholar' ) ?></h3></div>
		<?php if ( function_exists('bp_forums_tag_heat_map') ) : ?>
			<div id="tag-text"><?php bp_forums_tag_heat_map(); ?></div>
		<?php endif; ?>
	</div>
<?php endif; ?>