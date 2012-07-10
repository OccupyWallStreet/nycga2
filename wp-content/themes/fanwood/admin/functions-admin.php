<?php
/*
 * Theme Settings
 * 
 * @package Fanwood
 * @subpackage Template
 */
add_action( 'admin_menu', 'fanwood_theme_admin_setup' );

function fanwood_theme_admin_setup() {
    
	global $theme_settings_page;
	
	/* Get the theme settings page name */
	$theme_settings_page = 'appearance_page_theme-settings';

	/* Get the theme prefix. */
	$prefix = hybrid_get_prefix();

	/* Create a settings meta box only on the theme settings page. */
	add_action( 'load-appearance_page_theme-settings', 'fanwood_theme_settings_meta_boxes' );

	/* Add a filter to validate/sanitize your settings. */
	add_filter( "sanitize_option_{$prefix}_theme_settings", 'fanwood_theme_validate_settings' );
	
	/* Enqueue styles */
	add_action( 'admin_enqueue_scripts', 'fanwood_admin_styles' );
}

/* Adds custom meta boxes to the theme settings page. */
function fanwood_theme_settings_meta_boxes() {

	/* Add a custom meta box. */
	add_meta_box(
		'fanwood-theme-meta-box',			// Name/ID
		__( 'General', 'fanwood' ),	// Label
		'fanwood_theme_meta_box',			// Callback function
		'appearance_page_theme-settings',		// Page to load on, leave as is
		'normal',					// Which meta box holder?
		'high'					// High/low within the meta box holder
	);
	
	/* Add a custom meta box. */
	add_meta_box(
		'fanwood-theme-meta-box-2',			// Name/ID
		__( 'Layout', 'fanwood' ),	// Label
		'fanwood_theme_meta_box_2',			// Callback function
		'appearance_page_theme-settings',		// Page to load on, leave as is
		'side',					// Which meta box holder?
		'high'					// High/low within the meta box holder
	);	

	/* Add additional add_meta_box() calls here. */
}

/* Function for displaying the first meta box. */
function fanwood_theme_meta_box() { ?>

	<table class="form-table">
		
		<!-- Header search -->
		
		
		<!-- Show Primary Search -->
		<tr>
			<th>
			    <label for="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_header_primary_search' ) ); ?>"><?php _e( 'Show Primary Search:', 'fanwood' ); ?></label>
			</th>
			<td>
				<input class="checkbox" type="checkbox" <?php checked( hybrid_get_setting( 'fanwood_header_primary_search' ), true ); ?> id="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_header_primary_search' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'fanwood_header_primary_search' ) ); ?>" />
			</td>
		</tr>	

		<!-- Show Secondary Search -->
		<tr>
			<th>
			    <label for="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_header_secondary_search' ) ); ?>"><?php _e( 'Show Secondary Search:', 'fanwood' ); ?></label>
			</th>
			<td>
				<input class="checkbox" type="checkbox" <?php checked( hybrid_get_setting( 'fanwood_header_secondary_search' ), true ); ?> id="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_header_secondary_search' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'fanwood_header_secondary_search' ) ); ?>" />
			</td>
		</tr>	
		
		<tr>
			<td colspan="2">
				<p class="description"><?php _e( 'By default, search forms in the header are not displayed. Use this option to make them visible.', 'fanwood' ); ?></p>
				<p class="description"><?php _e( 'Primary Search is dependent on whether Header Primary Menu is active. If Header Primary Menu is not active, Primary Search will not show regardless of your setting.', 'fanwood' ); ?></p>
				<p class="description"><?php _e( 'The same goes for Secondary Search and Header Secondary Menu.', 'fanwood' ); ?></p>
			</td>
		</tr>
		

		<!-- Author biography -->
		
		
		<!-- Show On Posts -->
		<tr>
			<th>
			    <label for="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_author_bio_posts' ) ); ?>"><?php _e( 'Show On Posts:', 'fanwood' ); ?></label>
			</th>
			<td>
				<input class="checkbox" type="checkbox" <?php checked( hybrid_get_setting( 'fanwood_author_bio_posts' ), true ); ?> id="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_author_bio_posts' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'fanwood_author_bio_posts' ) ); ?>" />
			</td>
		</tr>	

		<!-- Show On Pages -->
		<tr>
			<th>
			    <label for="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_author_bio_pages' ) ); ?>"><?php _e( 'Show On Pages:', 'fanwood' ); ?></label>
			</th>
			<td>
				<input class="checkbox" type="checkbox" <?php checked( hybrid_get_setting( 'fanwood_author_bio_pages' ), true ); ?> id="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_author_bio_pages' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'fanwood_author_bio_pages' ) ); ?>" />
			</td>
		</tr>	
		
		<tr>
			<td colspan="2">
				<p class="description"><?php _e( 'This controls the display of the author biography box, which includes an avatar and biography (from your user profile page).', 'fanwood' ); ?></p>
			</td>
		</tr>

		<!-- End custom form elements. -->
	</table><!-- .form-table --><?php
	
}

/* Function for displaying the second meta box. */
function fanwood_theme_meta_box_2() { ?>

	<table class="form-table">
		
		<!-- Global Layout -->
		<tr>
			<th>
			    <label for="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_global_layout' ) ); ?>"><?php _e( 'Global Layout:', 'fanwood' ); ?></label>
			</th>
			<td>
			    <select id="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_global_layout' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'fanwood_global_layout' ) ); ?>">
				<option value="layout_default" <?php selected( hybrid_get_setting( 'fanwood_global_layout' ), 'layout_default' ); ?>> <?php echo __( 'layout_default', 'fanwood' ) ?> </option>
				<option value="layout_1c" <?php selected( hybrid_get_setting( 'fanwood_global_layout' ), 'layout_1c' ); ?>> <?php echo __( 'layout_1c', 'fanwood' ) ?> </option>
				<option value="layout_2c_l" <?php selected( hybrid_get_setting( 'fanwood_global_layout' ), 'layout_2c_l' ); ?>> <?php echo __( 'layout_2c_l', 'fanwood' ) ?> </option>
				<option value="layout_2c_r" <?php selected( hybrid_get_setting( 'fanwood_global_layout' ), 'layout_2c_r' ); ?>> <?php echo __( 'layout_2c_r', 'fanwood' ) ?> </option>
				<option value="layout_3c_c" <?php selected( hybrid_get_setting( 'fanwood_global_layout' ), 'layout_3c_c' ); ?>> <?php echo __( 'layout_3c_c', 'fanwood' ) ?> </option>
				<option value="layout_3c_l" <?php selected( hybrid_get_setting( 'fanwood_global_layout' ), 'layout_3c_l' ); ?>> <?php echo __( 'layout_3c_l', 'fanwood' ) ?> </option>
				<option value="layout_3c_r" <?php selected( hybrid_get_setting( 'fanwood_global_layout' ), 'layout_3c_r' ); ?>> <?php echo __( 'layout_3c_r', 'fanwood' ) ?> </option>
				<option value="layout_hl_1c" <?php selected( hybrid_get_setting( 'fanwood_global_layout' ), 'layout_hl_1c' ); ?>> <?php echo __( 'layout_hl_1c', 'fanwood' ) ?> </option>
				<option value="layout_hl_2c_l" <?php selected( hybrid_get_setting( 'fanwood_global_layout' ), 'layout_hl_2c_l' ); ?>> <?php echo __( 'layout_hl_2c_l', 'fanwood' ) ?> </option>
				<option value="layout_hl_2c_r" <?php selected( hybrid_get_setting( 'fanwood_global_layout' ), 'layout_hl_2c_r' ); ?>> <?php echo __( 'layout_hl_2c_r', 'fanwood' ) ?> </option>
				<option value="layout_hr_1c" <?php selected( hybrid_get_setting( 'fanwood_global_layout' ), 'layout_hr_1c' ); ?>> <?php echo __( 'layout_hr_1c', 'fanwood' ) ?> </option>
				<option value="layout_hr_2c_l" <?php selected( hybrid_get_setting( 'fanwood_global_layout' ), 'layout_hr_2c_l' ); ?>> <?php echo __( 'layout_hr_2c_l', 'fanwood' ) ?> </option>
				<option value="layout_hr_2c_r" <?php selected( hybrid_get_setting( 'fanwood_global_layout' ), 'layout_hr_2c_r' ); ?>> <?php echo __( 'layout_hr_2c_r', 'fanwood' ) ?> </option>
			    </select>
			    <span class="description"><?php _e( 'Set the layout for the entire site. The default layout is 2 columns with content on the left.', 'fanwood' ); ?></span>
			</td>
		</tr>
		
		<!-- bbPress Layout -->
		<tr>
			<th>
			    <label for="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_bbpress_layout' ) ); ?>"><?php _e( 'bbPress Layout:', 'fanwood' ); ?></label>
			</th>
			<td>
			    <select id="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_bbpress_layout' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'fanwood_bbpress_layout' ) ); ?>">
				<option value="layout_default" <?php selected( hybrid_get_setting( 'fanwood_bbpress_layout' ), 'layout_default' ); ?>> <?php echo __( 'layout_default', 'fanwood' ) ?> </option>
				<option value="layout_1c" <?php selected( hybrid_get_setting( 'fanwood_bbpress_layout' ), 'layout_1c' ); ?>> <?php echo __( 'layout_1c', 'fanwood' ) ?> </option>
				<option value="layout_2c_l" <?php selected( hybrid_get_setting( 'fanwood_bbpress_layout' ), 'layout_2c_l' ); ?>> <?php echo __( 'layout_2c_l', 'fanwood' ) ?> </option>
				<option value="layout_2c_r" <?php selected( hybrid_get_setting( 'fanwood_bbpress_layout' ), 'layout_2c_r' ); ?>> <?php echo __( 'layout_2c_r', 'fanwood' ) ?> </option>
				<option value="layout_3c_c" <?php selected( hybrid_get_setting( 'fanwood_bbpress_layout' ), 'layout_3c_c' ); ?>> <?php echo __( 'layout_3c_c', 'fanwood' ) ?> </option>
				<option value="layout_3c_l" <?php selected( hybrid_get_setting( 'fanwood_bbpress_layout' ), 'layout_3c_l' ); ?>> <?php echo __( 'layout_3c_l', 'fanwood' ) ?> </option>
				<option value="layout_3c_r" <?php selected( hybrid_get_setting( 'fanwood_bbpress_layout' ), 'layout_3c_r' ); ?>> <?php echo __( 'layout_3c_r', 'fanwood' ) ?> </option>
				<option value="layout_hl_1c" <?php selected( hybrid_get_setting( 'fanwood_bbpress_layout' ), 'layout_hl_1c' ); ?>> <?php echo __( 'layout_hl_1c', 'fanwood' ) ?> </option>
				<option value="layout_hl_2c_l" <?php selected( hybrid_get_setting( 'fanwood_bbpress_layout' ), 'layout_hl_2c_l' ); ?>> <?php echo __( 'layout_hl_2c_l', 'fanwood' ) ?> </option>
				<option value="layout_hl_2c_r" <?php selected( hybrid_get_setting( 'fanwood_bbpress_layout' ), 'layout_hl_2c_r' ); ?>> <?php echo __( 'layout_hl_2c_r', 'fanwood' ) ?> </option>
				<option value="layout_hr_1c" <?php selected( hybrid_get_setting( 'fanwood_bbpress_layout' ), 'layout_hr_1c' ); ?>> <?php echo __( 'layout_hr_1c', 'fanwood' ) ?> </option>
				<option value="layout_hr_2c_l" <?php selected( hybrid_get_setting( 'fanwood_bbpress_layout' ), 'layout_hr_2c_l' ); ?>> <?php echo __( 'layout_hr_2c_l', 'fanwood' ) ?> </option>
				<option value="layout_hr_2c_r" <?php selected( hybrid_get_setting( 'fanwood_bbpress_layout' ), 'layout_hr_2c_r' ); ?>> <?php echo __( 'layout_hr_2c_r', 'fanwood' ) ?> </option>
			    </select>
			    <span class="description"><?php _e( 'If bbPress (message board) is installed then use this option if you want to set a custom layout for all bbPress pages. For setting per forum or per topic layout, find the forum or topic to edit via the All Forums or All Topics management page, click on the Edit link, then set the custom layout.', 'fanwood' ); ?></span>			
			</td>
		</tr>
		
		<!-- BuddyPress Layout -->
		<tr>
			<th>
			    <label for="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_buddypress_layout' ) ); ?>"><?php _e( 'BuddyPress Layout:', 'fanwood' ); ?></label>
			</th>
			<td>
			    <select id="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_buddypress_layout' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'fanwood_buddypress_layout' ) ); ?>">
				<option value="layout_default" <?php selected( hybrid_get_setting( 'fanwood_buddypress_layout' ), 'layout_default' ); ?>> <?php echo __( 'layout_default', 'fanwood' ) ?> </option>
				<option value="layout_1c" <?php selected( hybrid_get_setting( 'fanwood_buddypress_layout' ), 'layout_1c' ); ?>> <?php echo __( 'layout_1c', 'fanwood' ) ?> </option>
				<option value="layout_2c_l" <?php selected( hybrid_get_setting( 'fanwood_buddypress_layout' ), 'layout_2c_l' ); ?>> <?php echo __( 'layout_2c_l', 'fanwood' ) ?> </option>
				<option value="layout_2c_r" <?php selected( hybrid_get_setting( 'fanwood_buddypress_layout' ), 'layout_2c_r' ); ?>> <?php echo __( 'layout_2c_r', 'fanwood' ) ?> </option>
				<option value="layout_3c_c" <?php selected( hybrid_get_setting( 'fanwood_buddypress_layout' ), 'layout_3c_c' ); ?>> <?php echo __( 'layout_3c_c', 'fanwood' ) ?> </option>
				<option value="layout_3c_l" <?php selected( hybrid_get_setting( 'fanwood_buddypress_layout' ), 'layout_3c_l' ); ?>> <?php echo __( 'layout_3c_l', 'fanwood' ) ?> </option>
				<option value="layout_3c_r" <?php selected( hybrid_get_setting( 'fanwood_buddypress_layout' ), 'layout_3c_r' ); ?>> <?php echo __( 'layout_3c_r', 'fanwood' ) ?> </option>
				<option value="layout_hl_1c" <?php selected( hybrid_get_setting( 'fanwood_buddypress_layout' ), 'layout_hl_1c' ); ?>> <?php echo __( 'layout_hl_1c', 'fanwood' ) ?> </option>
				<option value="layout_hl_2c_l" <?php selected( hybrid_get_setting( 'fanwood_buddypress_layout' ), 'layout_hl_2c_l' ); ?>> <?php echo __( 'layout_hl_2c_l', 'fanwood' ) ?> </option>
				<option value="layout_hl_2c_r" <?php selected( hybrid_get_setting( 'fanwood_buddypress_layout' ), 'layout_hl_2c_r' ); ?>> <?php echo __( 'layout_hl_2c_r', 'fanwood' ) ?> </option>
				<option value="layout_hr_1c" <?php selected( hybrid_get_setting( 'fanwood_buddypress_layout' ), 'layout_hr_1c' ); ?>> <?php echo __( 'layout_hr_1c', 'fanwood' ) ?> </option>
				<option value="layout_hr_2c_l" <?php selected( hybrid_get_setting( 'fanwood_buddypress_layout' ), 'layout_hr_2c_l' ); ?>> <?php echo __( 'layout_hr_2c_l', 'fanwood' ) ?> </option>
				<option value="layout_hr_2c_r" <?php selected( hybrid_get_setting( 'fanwood_buddypress_layout' ), 'layout_hr_2c_r' ); ?>> <?php echo __( 'layout_hr_2c_r', 'fanwood' ) ?> </option>
			    </select>
			    <span class="description"><?php _e( 'If BuddyPress (social networking) is installed then use this option if you want to set a custom layout for all BuddyPress pages.', 'fanwood' ); ?></span>
			</td>
		</tr>
		
		<!-- Jigoshop Layout -->
		<tr>
			<th>
			    <label for="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_jigoshop_layout' ) ); ?>"><?php _e( 'Jigoshop Layout:', 'fanwood' ); ?></label>
			</th>
			<td>
			    <select id="<?php echo esc_attr( hybrid_settings_field_id( 'fanwood_jigoshop_layout' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'fanwood_jigoshop_layout' ) ); ?>">
				<option value="layout_default" <?php selected( hybrid_get_setting( 'fanwood_jigoshop_layout' ), 'layout_default' ); ?>> <?php echo __( 'layout_default', 'fanwood' ) ?> </option>
				<option value="layout_1c" <?php selected( hybrid_get_setting( 'fanwood_jigoshop_layout' ), 'layout_1c' ); ?>> <?php echo __( 'layout_1c', 'fanwood' ) ?> </option>
				<option value="layout_2c_l" <?php selected( hybrid_get_setting( 'fanwood_jigoshop_layout' ), 'layout_2c_l' ); ?>> <?php echo __( 'layout_2c_l', 'fanwood' ) ?> </option>
				<option value="layout_2c_r" <?php selected( hybrid_get_setting( 'fanwood_jigoshop_layout' ), 'layout_2c_r' ); ?>> <?php echo __( 'layout_2c_r', 'fanwood' ) ?> </option>
				<option value="layout_3c_c" <?php selected( hybrid_get_setting( 'fanwood_jigoshop_layout' ), 'layout_3c_c' ); ?>> <?php echo __( 'layout_3c_c', 'fanwood' ) ?> </option>
				<option value="layout_3c_l" <?php selected( hybrid_get_setting( 'fanwood_jigoshop_layout' ), 'layout_3c_l' ); ?>> <?php echo __( 'layout_3c_l', 'fanwood' ) ?> </option>
				<option value="layout_3c_r" <?php selected( hybrid_get_setting( 'fanwood_jigoshop_layout' ), 'layout_3c_r' ); ?>> <?php echo __( 'layout_3c_r', 'fanwood' ) ?> </option>
				<option value="layout_hl_1c" <?php selected( hybrid_get_setting( 'fanwood_jigoshop_layout' ), 'layout_hl_1c' ); ?>> <?php echo __( 'layout_hl_1c', 'fanwood' ) ?> </option>
				<option value="layout_hl_2c_l" <?php selected( hybrid_get_setting( 'fanwood_jigoshop_layout' ), 'layout_hl_2c_l' ); ?>> <?php echo __( 'layout_hl_2c_l', 'fanwood' ) ?> </option>
				<option value="layout_hl_2c_r" <?php selected( hybrid_get_setting( 'fanwood_jigoshop_layout' ), 'layout_hl_2c_r' ); ?>> <?php echo __( 'layout_hl_2c_r', 'fanwood' ) ?> </option>
				<option value="layout_hr_1c" <?php selected( hybrid_get_setting( 'fanwood_jigoshop_layout' ), 'layout_hr_1c' ); ?>> <?php echo __( 'layout_hr_1c', 'fanwood' ) ?> </option>
				<option value="layout_hr_2c_l" <?php selected( hybrid_get_setting( 'fanwood_jigoshop_layout' ), 'layout_hr_2c_l' ); ?>> <?php echo __( 'layout_hr_2c_l', 'fanwood' ) ?> </option>
				<option value="layout_hr_2c_r" <?php selected( hybrid_get_setting( 'fanwood_jigoshop_layout' ), 'layout_hr_2c_r' ); ?>> <?php echo __( 'layout_hr_2c_r', 'fanwood' ) ?> </option>
			    </select>
			    <span class="description"><?php _e( 'If Jigoshop (e-commerce) is installed then use this option if you want to set a custom layout for all Jigoshop pages.', 'fanwood' ); ?></span>
			</td>
		</tr>		
		
		<!-- End custom form elements. -->
	</table><!-- .form-table -->		
	
<?php }		

/* Validate theme settings. */
function fanwood_theme_validate_settings( $input ) {
		
	$input['fanwood_header_primary_search'] = ( isset( $input['fanwood_header_primary_search'] ) ? 1 : 0 );
	$input['fanwood_header_secondary_search'] = ( isset( $input['fanwood_header_secondary_search'] ) ? 1 : 0 );
	$input['fanwood_author_bio_posts'] = ( isset( $input['fanwood_author_bio_posts'] ) ? 1 : 0 );
	$input['fanwood_author_bio_pages'] = ( isset( $input['fanwood_author_bio_pages'] ) ? 1 : 0 );
	$input['fanwood_global_layout'] = wp_filter_nohtml_kses( $input['fanwood_global_layout'] );
	$input['fanwood_bbpress_layout'] = wp_filter_nohtml_kses( $input['fanwood_bbpress_layout'] );
	$input['fanwood_buddypress_layout'] = wp_filter_nohtml_kses( $input['fanwood_buddypress_layout'] );
	$input['fanwood_jigoshop_layout'] = wp_filter_nohtml_kses( $input['fanwood_jigoshop_layout'] );

    /* Return the array of theme settings. */
    return $input;
}


/* Enqueue scripts (and related stylesheets) */
function fanwood_admin_styles( $hook_suffix ) {
    
    global $theme_settings_page;
	
    if ( $theme_settings_page == $hook_suffix ) {		
	    
	    /* Enqueue Styles */
	    wp_enqueue_style( 'fanwood_functions-admin', get_template_directory_uri() . '/admin/functions-admin.css', false, 1.0, 'screen' );
    }
}


?>