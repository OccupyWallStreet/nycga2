<?php
if ( defined( 'BP_FADMIN_IS_INSTALLED' ) ) {

	function bp_fadmin_setup_nav_main_menu() {
		global $bp;


		/* Create sub nav item for this component */
		bp_core_new_subnav_item( array(
			'name' => __( 'Main', 'bp-fadmin' ),
			'slug' => 'menu',
			'parent_slug' => $bp->fadmin->slug,
			'parent_url' => $bp->loggedin_user->domain . $bp->fadmin->slug . '/',
			'screen_function' => 'bp_fadmin_screen_main_menu',
			'position' => 10
		) );

	}
	add_action( 'wp', 'bp_fadmin_setup_nav_main_menu', 2 );
	add_action( 'admin_menu', 'bp_fadmin_setup_nav_main_menu', 2 );




	function bp_fadmin_screen_main_menu() {
		global $bp;

		do_action( 'bp_fadmin_screen_main_menu' );

		add_action( 'bp_template_title', 'bp_fadmin_screen_main_menu_title' );
		add_action( 'bp_template_content', 'bp_fadmin_screen_main_menu_content' );

		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	function bp_fadmin_screen_main_menu_title() {
		_e( 'Welcome to BuddyPress Frontend Admin', 'bp-fadmin' );
	}

	function bp_fadmin_screen_main_menu_content() {
		global $bp; ?>

		<h4><?php _e( 'Welcome to BuddyPress Frontend Admin', 'bp-fadmin' ); ?></h4>

		<p><?php _e( 'This area of your profile contains powerful administration controls to enable you to manage various site-wide options easily.', 'bp-fadmin' ); ?></p>
		
		<table style="width:75%;">
		<tr>
			<td><strong><?php _e( 'Extension', 'bp-fadmin' ); ?></strong></td>
			<td><strong><?php _e( 'Description', 'bp-fadmin' ); ?></strong></td>
		</tr>
		<?php
		
		$extensions = bp_fadmin_registered_extensions();
		
		foreach ( $extensions as $key=>$extension ){
			if ( $key%2 != 0 ) {
				echo '<tr>';
			} else {
				echo '<tr style="background-color:#EBEBEB;">';
			}
			echo '<td><a href="' . $bp->loggedin_user->domain . $bp->fadmin->slug . '/' . $extension->slug . '">' . $extension->name . '</a></td>';
			echo '<td>' . $extension->description . '</td>';
			echo '</tr>';
		}
		?>
		</table>
		<?php
	}
	
}
?>