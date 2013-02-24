<?php do_action( 'bp_before_sidebar' ) ?>

<div id="leftsidebar">
  <div class="paddersidebar left-sidebar-padder">
  	<?php if( ! dynamic_sidebar( 'groupsidebarleft' )) :  ?>
  		<?php locate_template( array( 'groups/single/group-header-sidebar.php' ), true, false ); ?>
	<?php endif;?>
  </div><!-- #padder -->	
</div><!-- #sidebar -->
<div class="v_line v_line_left visible-desktop"></div>
<?php do_action( 'bp_after_sidebar' ) ?>
