	<?php include (get_template_directory() . '/library/options/options.php'); ?>
	<div id="breadcrumb">
				<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
			<?php bpbreadcrumbs(); ?>
				<?php } else { // if not bp detected..let go normal ?>
			<?php wpbreadcrumbs(); ?>
				<?php } ?>
	</div>