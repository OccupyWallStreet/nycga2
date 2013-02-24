<?php do_action( 'bp_before_sidebar' ) ?>
<div class="v_line v_line_right visible-desktop"></div>
	<div id="sidebar" class="widgetarea">
	<div class="paddersidebar right-sidebar-padder">
	<?php if( ! dynamic_sidebar( 'groupsidebarright' )) : ?>
		<?php locate_template( array( 'groups/single/group-header-sidebar.php' ), true, false ); ?>
	<?php endif ?>
  </div><!-- #padder -->	
</div><!-- #sidebar -->

<?php do_action( 'bp_after_sidebar' ) ?>
