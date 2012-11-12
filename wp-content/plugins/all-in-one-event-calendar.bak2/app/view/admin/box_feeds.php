<?php
global $ai1ec_importer_plugin_helper;
do_action( 'ai1ec_feeds_before' );
?>
<div class="timely">
	<ul class="nav nav-tabs">
		<?php $ai1ec_importer_plugin_helper->render_tab_headers() ?>
	</ul>
	<div class="tab-content">
		<?php $ai1ec_importer_plugin_helper->render_tab_contents() ?>
	</div>
</div>
<?php do_action( 'ai1ec_feeds_after' ); ?>
