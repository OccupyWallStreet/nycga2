	<div id="item-header">
		<?php include( WP_PLUGIN_DIR . '/buddypress/bp-themes/bp-default/groups/single/group-header.php' ) ?>
	</div>
						
	<div id="item-nav">
		<div class="item-list-tabs no-ajax" id="object-nav">
			<ul>
				<?php bp_groupblog_options_nav() ?>
	
				<?php do_action( 'bp_group_options_nav' ) ?>
			</ul>
		</div>
	</div>