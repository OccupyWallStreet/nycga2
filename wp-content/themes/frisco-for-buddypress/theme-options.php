<?php

add_action( 'admin_init', 'theme_options_init' );
add_action( 'admin_menu', 'theme_options_add_page' );
add_action( 'admin_bar_menu', 'theme_options_nav' );

/**
 * Init plugin options to white list our options
 */
function theme_options_init(){
	register_setting( 'frisco_options', 'frisco_theme_options', 'theme_options_validate' );
}

/**
 * Load up the menu page
 */
function theme_options_add_page() {
	add_theme_page( __( 'Theme Options', 'friscotheme' ), __( 'Theme Options', 'friscotheme' ), 'edit_theme_options', 'theme_options', 'theme_options_do_page' );
}


function theme_options_nav() {
 global $wp_admin_bar;
 $wp_admin_bar->add_menu( array(
 'parent' => 'appearance',
 'id' => 'theme-options',
 'title' => 'Theme Options',
 'href' => admin_url('themes.php?page=theme_options')
 ) );
}


/**
 * Create arrays for our select and radio options
 */
$select_options = array(
	'default' => array(
		'value' =>	'default',
		'label' => __( 'Default', 'friscotheme' )
	),
	'green' => array(
		'value' =>	'green',
		'label' => __( 'Green', 'friscotheme' )
	),
	'orange' => array(
		'value' => 'orange',
		'label' => __( 'Orange', 'friscotheme' )
	),
	'yellow' => array(
		'value' => 'yellow',
		'label' => __( 'Yellow', 'friscotheme' )
	),
	'grey' => array(
		'value' => 'grey',
		'label' => __( 'Grey', 'friscotheme' )
	),
	'purple' => array(
		'value' => 'purple',
		'label' => __( 'Purple', 'friscotheme' )
	)
);

$radio_options = array(
	'yes' => array(
		'value' => 'yes',
		'label' => __( 'Yes', 'friscotheme' )
	),
	'no' => array(
		'value' => 'no',
		'label' => __( 'No', 'friscotheme' )
	),
	'maybe' => array(
		'value' => 'maybe',
		'label' => __( 'Maybe', 'friscotheme' )
	)
);

$select_font_options = array(
	'Lobster Two' => array(
		'value' =>	'Lobster Two',
		'label' => __( 'Lobster Two', 'friscotheme' )
	),
	'Quattrocento' => array(
		'value' =>	'Quattrocento',
		'label' => __( 'Quattrocento', 'friscotheme' )
	),
	'Droid Sans' => array(
		'value' => 'Droid Sans',
		'label' => __( 'Droid Sans', 'friscotheme' )
	),
	'PT Sans' => array(
		'value' => 'PT Sans',
		'label' => __( 'PT Sans', 'friscotheme' )
	),
	'Yanone Kaffeesatz' => array(
		'value' => 'Yanone Kaffeesatz',
		'label' => __( 'Yanone Kaffeesatz', 'friscotheme' )
	),
	'Cabin' => array(
		'value' => 'Cabin',
		'label' => __( 'Cabin', 'friscotheme' )
	),
	'Black Ops One' => array(
		'value' => 'Black Ops One',
		'label' => __( 'Black Ops One', 'friscotheme' )
	),
	'Nixie One' => array(
		'value' => 'Nixie One',
		'label' => __( 'Nixie One', 'friscotheme' )
	),
	'Bangers' => array(
		'value' => 'Bangers',
		'label' => __( 'Bangers', 'friscotheme' )
	),
	'Monofett' => array(
		'value' => 'Monofett',
		'label' => __( 'Monofett', 'friscotheme' )
	)
);

/**
 * Create the options page
 */
function theme_options_do_page() {
	global $select_options, $radio_options, $select_font_options;

	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;

	?>
	<div class="wrap">
		<?php screen_icon(); echo "<h2>" . get_current_theme() . __( ' Theme Options', 'friscotheme' ) . "</h2>"; ?>

		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'Options saved', 'friscotheme' ); ?></strong></p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'frisco_options' ); ?>
			<?php $options = get_option( 'frisco_theme_options' ); ?>

			<table class="form-table">

			
			
				<?php
				/**
				 * Color Choices
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Select Theme Color', 'friscotheme' ); ?></th>
					<td>
						<select name="frisco_theme_options[themecolor]">
							<?php
								$selected = $options['themecolor'];
								$p = '';
								$r = '';

								foreach ( $select_options as $option ) {
									$label = $option['label'];
									if ( $selected == $option['value'] ) // Make default first in list
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr( $option['value'] ) . "'>$label</option>";
									else
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr( $option['value'] ) . "'>$label</option>";
								}
								echo $p . $r;
							?>
						</select>
						<label class="description" for="frisco_theme_options[themecolor]"><?php _e( 'Only a few color choices at the moment.', 'friscotheme' ); ?></label>
					</td>
				</tr>

				<?php
				/**
				 * Font Choices
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Select Google Font', 'friscotheme' ); ?></th>
					<td>
						<select name="frisco_theme_options[googlefont]">
							<?php
								$selected = $options['googlefont'];
								$p = '';
								$r = '';

								foreach ( $select_font_options as $option ) {
									$label = $option['label'];
									if ( $selected == $option['value'] ) // Make default first in list
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr( $option['value'] ) . "'>$label</option>";
									else
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr( $option['value'] ) . "'>$label</option>";
								}
								echo $p . $r;
							?>
						</select>
						<label class="description" for="frisco_theme_options[googlefont]"><?php _e( 'Choose a font for the site title.', 'friscotheme' ); ?></label>
					</td>
				</tr>

				<?php
				/**
				 * Use custom.css? 
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Custom Stylesheet', 'friscotheme' ); ?></th>
					<td>
						<input id="frisco_theme_options[customcss]" name="frisco_theme_options[customcss]" type="checkbox" value="1" <?php checked( '1', $options['customcss'] ); ?> />
						<label class="description" for="frisco_theme_options[customcss]"><?php _e( 'Check this box to use a custom stylesheet. Create <code>custom.css</code> in the main theme directory.', 'friscotheme' ); ?></label>
					</td>
				</tr>
				
				<?php
				/**
				 * Use functions-custom.php? 
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Custom Functions', 'friscotheme' ); ?></th>
					<td>
						<input id="frisco_theme_options[customphp]" name="frisco_theme_options[customphp]" type="checkbox" value="1" <?php checked( '1', $options['customphp'] ); ?> />
						<label class="description" for="frisco_theme_options[customphp]"><?php _e( 'Check this box to use a custom functions file. Create <code>functions-custom.php</code> in the main theme directory.', 'friscotheme' ); ?></label>
					</td>
				</tr>	
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'friscotheme' ); ?>" />
			</p>
		</form>
		
		<h3>Tips</h3>
		<p><strong>&#42;USE THE NEW ADMIN BAR:</strong> By default, BuddyPress is using the "BuddyBar". If you'd prefer to use the new admin bar, add <code>define('BP_USE_WP_ADMIN_BAR', true);</code> to your <code>wp-config.php</code> file just above where it says "That's all, stop editing! Happy blogging."</p>
		<p><strong>&#42;USE WIDGETS:</strong> This theme uses the same sidebar and footer widgets available to the BuddyPress default theme. <a href="<?php echo home_url(); ?>/wp-admin/widgets.php">Use them!</a> If you want to show different widgets on different pages, use the <a href="http://wordpress.org/extend/plugins/widget-logic/">Widget Logic Plugin</a> (<a href="<?php echo home_url(); ?>/wp-admin/plugin-install.php?tab=search&type=term&s=widget+logic&plugin-search-input=Search+Plugins">link to install</a>) along with some <a href="http://codex.wordpress.org/Conditional_Tags">WordPress conditional tags</a> or <a href="http://codex.buddypress.org/developer-docs/conditional-template-tags/">BuddyPress conditional tags</a>.</p>
		<p><strong>&#42;GET SUPPORT:</strong> This theme is free and support is not included. But, if you get stuck or if you have any questions, start a new thread in the <a href="http://wordpress.org/support/forum/themes-and-templates">WordPress theme forums</a> or the <a href="http://buddypress.org/community/groups/creating-extending/forum/">BuddyPress theme forums</a>. <em>IMPORTANT:</em> If you post in the WordPress or BuddyPress forums, make sure you add the tag "Frisco" or "Frisco Theme" to the post. This will make it easier for the theme author to become aware of your issue.</p>
		
		
	</div>
	<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function theme_options_validate( $input ) {
	global $select_options, $radio_options, $select_font_options;

	// Our checkbox value is either 0 or 1
	if ( ! isset( $input['customcss'] ) )
		$input['customcss'] = null;
	$input['customcss'] = ( $input['customcss'] == 1 ? 1 : 0 );

	// Our checkbox value is either 0 or 1
	if ( ! isset( $input['customphp'] ) )
		$input['customphp'] = null;
	$input['customphp'] = ( $input['customphp'] == 1 ? 1 : 0 );

	// Our select option must actually be in our array of select options
	if ( ! array_key_exists( $input['themecolor'], $select_options ) )
		$input['themecolor'] = null;
		
	// Our select option must actually be in our array of select options
	if ( ! array_key_exists( $input['googlefont'], $select_font_options ) )
		$input['googlefont'] = null;

	return $input;
}

// adapted from http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/