<?php global $bp_existed; ?>

<div id="topbar">

<?php

		$arg = array(
		  'menu'            => '1', 
		  'container'       => '', 
		  'container_class' => '', 
		  'container_id'    => '', 
		  'menu_class'      => 'sf-menu sf-js-enabled sf-shadow', 
		  'menu_id'         => 'topbar-navmenu',
		  'echo'            => true,
		  'fallback_cb'     => 'wp_page_menu',
		  'before'          => '',
		  'after'           => '',
		  'link_before'     => '',
		  'link_after'      => '',
		  'depth'           => 0,
		  'walker'          => '',
		  'theme_location'  => '');
		
		   wp_nav_menu($arg); 
			
?>

<?php if($bp_existed == 'true') : 

?>

<?php do_action( 'bp_before_header' ) ?>
<?php locate_template( array( '/library/components/buddypress/buddypress-navigation-topnav.php' ), true ); ?>
<?php do_action( 'bp_header' ) ?>

<?php endif; ?>
		
</div>