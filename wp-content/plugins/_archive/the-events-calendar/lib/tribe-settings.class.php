<?php

// Don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );

if ( !class_exists( 'TribeSettings' ) ) {
	/**
	 * helper class that allows registration of settings
	 * this is a static class & uses the singleton design method
	 * instantiation takes place in TribeEvents
	 *
	 * @since 2.0.5
	 * @author jkudish
	 */
	class TribeSettings {

		/**
		 * singleton instance var
		 * @var stdClass
		 */
		public static $instance;

		/**
		 * the tabs that will appear in the settings page
		 * filtered on class construct
		 * @var array
		 */
		public static $tabs;

		/**
		 * multidimentional array of the fields that will be generated
		 * for the entire settings panel, tabs are represented in the array keys
		 * @var array
		 */
		public static $fields;

		/**
		 * the default tab for the settings panel
		 * this should be a tab ID
		 * @var string
		 */
		public static $defaultTab;

		/**
		 * the current tab being displayed
		 * @var string
		 */
		public static $currentTab;

		/**
		 * tabs that shouldn't show the save button
		 * @var array
		 */
		public static $noSaveTabs;

		/**
		 * the slug used in the admin to generate the settings page
		 * @var string
		 */
		public static $adminSlug;

		/**
		 * the menu name used for the settings page
		 * @var string
		 */
		public static $menuName;

		/**
		 * the required capability for the settings page
		 * @var string
		 */
		public static $requiredCap;

		/**
		 * errors that occur after a save operation
		 * @var mixed
		 */
		public static $errors;

		/**
		 * POST data before/after save
		 * @var mixed
		 */
		public static $sent_data;

		/**
		 * the $current_screen name corresponding to the admin page
		 * @var string
		 */
		public static $admin_page;

		/**
		 * true if a major error that prevents saving occurred
		 * @var bool
		 */
		public static $major_error;

		/**
		 * holds validated fields
		 * @var array
		 */
		public static $validated;

		/**
		 * Static Singleton Factory Method
		 *
		 * @since 2.0.5
		 * @author jkudish
		 * @return void
		 */
		public static function instance() {
			if ( !isset( self::$instance ) ) {
				$className = __CLASS__;
				self::$instance = new $className;
			}
			return self::$instance;
		}

		/**
		 * Class constructor
		 *
		 * @since 2.0.5
		 * @author jkudish
		 * @return void
		 */
		public function __construct() {

			// set instance variables
			$this->menuName = apply_filters( 'tribe_settings_menu_name', __( 'The Events Calendar', 'tribe-events-calendar' ) );
			$this->requiredCap = apply_filters( 'tribe_settings_req_cap', 'manage_options' );
			$this->adminSlug = apply_filters( 'tribe_settings_admin_slug', 'tribe-events-calendar' );
			$this->errors = get_option( 'tribe_settings_errors', array() );
			$this->major_error = get_option( 'tribe_settings_major_error', false );
			$this->sent_data = get_option( 'tribe_settings_sent_data', array() );
			$this->validated = array();

			// run actions & filters
			add_action( 'admin_menu', array( $this, 'addPage' ) );
			add_action( 'admin_init', array( $this, 'initTabs' ) );
			add_action( 'tribe_settings_below_tabs', array( $this, 'displayErrors' ) );
			add_action( 'tribe_settings_below_tabs', array( $this, 'displaySuccess' ) );
			add_action( 'shutdown', array( $this, 'deleteOptions' ) );
		}

		/**
		 * create the main option page
		 *
		 * @since 2.0.5
		 * @author jkudish
		 * @return void
		 */
		public function addPage() {
			$this->admin_page = add_submenu_page( 'edit.php?post_type=' . TribeEvents::POSTTYPE, __( 'The Events Calendar Settings', 'tribe-events-calendar'), __('Settings', 'tribe-events-calendar'), $this->requiredCap, $this->adminSlug, array( $this, 'generatePage' ) );
		}

		/**
		 * init all the tabs
		 *
		 * @since 2.0.5
		 * @author jkudish
		 * @return void
		 */
		public function initTabs() {
			if ( isset( $_GET['page'] ) && $_GET['page'] == $this->adminSlug ) {
				do_action( 'tribe_settings_do_tabs' ); // this is the hook to use to add new tabs
				$this->tabs = (array) apply_filters( 'tribe_settings_tabs', array() );
				$this->defaultTab = apply_filters( 'tribe_settings_default_tab', 'general' );
				$this->currentTab = apply_filters( 'tribe_settings_current_tab', ( isset( $_GET['tab'] ) && $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : $this->defaultTab );
				$this->url = apply_filters( 'tribe_settings_url', add_query_arg( array( 'page' => $this->adminSlug, 'tab' => $this->currentTab ), add_query_arg( array( 'post_type' => TribeEvents::POSTTYPE ), admin_url( 'edit.php' ) ) ) );
				$this->noSaveTabs = (array) apply_filters( 'tribe_settings_no_save_tabs', array() );
				$this->fields_for_save = (array) apply_filters( 'tribe_settings_fields', array() );
				do_action( 'tribe_settings_after_do_tabs' );
				$this->fields = (array) apply_filters( 'tribe_settings_fields', array() );
				$this->validate();
			}
		}


		/**
		 * generate the main option page
		 * includes the view file
		 *
		 * @since 2.0.5
		 * @author jkudish
		 * @return void
		 */
		public function generatePage() {
			$tec = TribeEvents::instance();
			do_action( 'tribe_settings_top' );
			echo '<div class="tribe_settings wrap">';
				screen_icon();
				echo '<h2>';
					printf( _x( '%s Settings', 'The Event Calendar settings heading', 'tribe-events-calendar' ), $this->menuName );
				echo '</h2>';
				do_action( 'tribe_settings_above_tabs' );
				$this->generateTabs( $this->currentTab );
				do_action( 'tribe_settings_below_tabs' );
				do_action( 'tribe_settings_below_tabs_tab_'.$this->currentTab );
				echo '<div class="tribe-settings-form form">';
					do_action( 'tribe_settings_above_form_element' );
					do_action( 'tribe_settings_above_form_element_tab_'.$this->currentTab );
					echo apply_filters( 'tribe_settings_form_element_tab_'.$this->currentTab, '<form method="post">' );
						do_action( 'tribe_settings_before_content' );
						do_action( 'tribe_settings_before_content_tab_'.$this->currentTab );
						do_action( 'tribe_settings_content_tab_'.$this->currentTab );
						if ( !has_action( 'tribe_settings_content_tab_'.$this->currentTab ) ) {
							echo '<p>' . __( "You've requested a non-existent tab.", 'tribe-events-calendar' ) . '</p>';
						}
						do_action( 'tribe_settings_after_content_tab_'.$this->currentTab );
			 			do_action( 'tribe_settings_after_content' );
			  		if ( has_action( 'tribe_settings_content_tab_'.$this->currentTab ) && !in_array( $this->currentTab, $this->noSaveTabs ) ) {
							wp_nonce_field( 'saving', 'tribe-save-settings' );
							echo '<div class="clear"></div>';
		    			echo '<input type="hidden" name="current-settings-tab" id="current-settings-tab" value="'.$this->currentTab.'" />';
		    			echo '<input id="tribeSaveSettings" class="button-primary" type="submit" name="tribeSaveSettings" value="' . __( ' Save Changes', 'tribe-events-calendar' ) . '" />';
						}
					echo apply_filters( 'tribe_settings_closing_form_element', '</form>' );
					do_action( 'tribe_settings_after_form_element' );
					do_action( 'tribe_settings_after_form_element_tab_'.$this->currentTab );
				echo '</div>';
				do_action( 'tribe_settings_after_form_div' );
			echo '</div>';
			do_action( 'tribe_settings_bottom' );
		}

		/**
		 * generate the tabs in the settings screen
		 *
		 * @since 2.0.5
		 * @author PaulHughes01, jkudish
		 * @return void
		 */
		public function generateTabs() {
			if ( is_array( $this->tabs ) && !empty( $this->tabs ) ) {
				echo '<h2 id="tribe-settings-tabs" class="nav-tab-wrapper">';
					foreach ( $this->tabs as $tab => $name ) {
						$tab = esc_attr( $tab );
						$name = esc_attr( $name );
						$class = ( $tab == $this->currentTab ) ? ' nav-tab-active' : '';
						echo '<a id="' . $tab . '" class="nav-tab' . $class . '" href="?post_type=' .TribeEvents::POSTTYPE . '&page=' . $this->adminSlug . '&tab=' . urlencode( $tab ) . '">' . $name . '</a>';
					}
					do_action( 'tribe_settings_after_tabs' );
				echo '</h2>';
			}
		 }


		/**
		 * validate the settings
		 *
		 * @since 2.0.5
		 * @author jkudish
		 * @return void
		 */
		public function validate() {

			do_action( 'tribe_settings_validate_before_checks' );

			// check that the right POST && variables are set
			if ( isset( $_POST['tribeSaveSettings'] ) && isset( $_POST['current-settings-tab'] ) ) {
				// check permissions
				if ( !current_user_can( 'manage_options' ) ) {
					$this->errors[] = __( "You don't have permission to do that.", 'tribe-events-calendar' );
					$this->major_error = true;
				}

				// check the nonce
				if ( !wp_verify_nonce( $_POST['tribe-save-settings'], 'saving' ) ) {
					$this->errors[] = __( 'The request was sent insecurely.', 'tribe-events-calendar' );
					$this->major_error = true;
				}

				// check that the request originated from the current tab
				if ( $_POST['current-settings-tab'] != $this->currentTab ) {
					$this->errors[] = __( "The request wasn't sent from this tab.", 'tribe-events-calendar' );
					$this->major_error = true;
				}

				// bail if we have errors
				if ( count( $this->errors ) ) {
					remove_action( 'shutdown', array( $this, 'deleteOptions' ) );
					add_option( 'tribe_settings_errors', $this->errors );
					add_option( 'tribe_settings_major_error', $this->major_error );
					wp_redirect( $this->url ); exit;
				}

				// some hooks
				do_action( 'tribe_settings_validate' );
				do_action( 'tribe_settings_validate_tab_'.$this->currentTab );

				// set the current tab and current fields
				$tab = $this->currentTab;
				$fields = $this->fields_for_save[$tab];

				if ( is_array( $fields ) ) {
					// loop through the fields and validate them
					foreach ( $fields as $field_id => $field ) {
						// get the value
						$value = ( isset( $_POST[$field_id] ) ) ? $_POST[$field_id] : null;
						$value = apply_filters( 'tribe_settings_validate_field_value', $value, $field_id, $field );

						// make sure it has validation set up for it, else do nothing
						if ( ( !isset( $field['conditional'] ) || $field['conditional'] ) && ( !empty( $field['validation_type'] ) || !empty( $field['validation_callback'] ) ) ) {
							// some hooks
							do_action( 'tribe_settings_validate_field', $field_id, $value, $field );
							do_action( 'tribe_settings_validate_field_'.$field_id, $value, $field );

							// validate this sucka
							$validate = new TribeValidate( $field_id, $field, $value );

							if ( isset( $validate->result->error ) ) {
								// uh oh; validation failed
								$this->errors[$field_id] = $validate->result->error;
							} elseif ( $validate->result->valid ) {
								// validation passed
								$this->validated[$field_id] = new stdClass;
								$this->validated[$field_id]->field = $validate->field;
								$this->validated[$field_id]->value = $validate->value;
							}
						}
					}

					// run the saving method
					$this->save();
				}
			}

		}

		/**
		 * save the settings
		 *
		 * @since 2.0.5
		 * @author jkudish
		 * @return void
		 */
		public function save() {

			// some hooks
			do_action( 'tribe_settings_save' );
			do_action( 'tribe_settings_save_tab_' . $this->currentTab );

			// we'll need this later
			$parent_options = array();

			/**
			 * loop through each validated option and either
			 * save it as is or figure out its parent option ID
			 * (in that case, it's a serialized option array and
			 * will be saved in the next loop)
			 */
			if ( !empty( $this->validated ) ) {
				foreach ( $this->validated as $field_id => $validated_field ) {
					// get the value and filter it
					$value = $validated_field->value;
					$value = apply_filters( 'tribe_settings_save_field_value', $value, $field_id, $validated_field );

					// figure out the parent option [could be set to false] and filter it
					$parent_option = ( isset( $validated_field->field['parent_option'] ) ) ? $validated_field->field['parent_option'] : TribeEvents::OPTIONNAME;
					$parent_option = apply_filters( 'tribe_settings_save_field_parent_option', $parent_option, $field_id );

					// some hooks
					do_action( 'tribe_settings_save_field', $field_id, $value, $validated_field );
					do_action( 'tribe_settings_save_field_' . $field_id, $value, $validated_field );

					if ( !$parent_option ) {
						// if no parent option, then just save the option
						update_option( $field_id, $value );
					} else {
						// set the parent option
						$parent_options[$parent_option][$field_id] = $value;
					}
				}
			}

			/**
			 * loop through parent option arrays
			 * and save them
			 * NOTE: in the case of the main option Tribe Options,
			 * this will save using the TribeEvents:setOptions method.
			 */
			foreach ( $parent_options as $option_id => $new_options ) {
				// get the old options
				$old_options = (array) get_option( $option_id );

				// set the options by parsing old + new and filter that
				$options = apply_filters( 'tribe_settings_save_option_array', wp_parse_args( $new_options, $old_options ), $option_id );

				if ( $option_id == TribeEvents::OPTIONNAME ) {
					// save using the TribeEvents method
					TribeEvents::setOptions( $options );
				} else {
					// save using regular WP method
					update_option( $option_id, $options );
				}
			}

			do_action( 'tribe_settings_after_save' );
			do_action( 'tribe_settings_after_save_' . $this->currentTab );
			remove_action( 'shutdown', array( $this, 'deleteOptions' ) );
			add_option( 'tribe_settings_sent_data', $_POST );
			add_option( 'tribe_settings_errors', $this->errors );
			add_option( 'tribe_settings_major_error', $this->major_error );
			wp_redirect( add_query_arg( array( 'saved' => true ), $this->url ) ); exit;

		}

		/**
		 * display errors, if any, after saving
		 *
		 * @since 2.0.5
		 * @author PaulHughes01, jkudish
		 * @return void
		 */
		public function displayErrors() {

			// fetch the errors and filter them
			$errors = (array) apply_filters( 'tribe_settings_display_errors', $this->errors );
			$count = apply_filters( 'tribe_settings_count_errors', count( $errors ) );

			if ( apply_filters( 'tribe_settings_display_errors_or_not', ( $count > 0) ) ) {
				// output a message if we have errors

				$output = '<div id="message" class="error"><p><strong>';
				$output .= __( 'Your form had the following errors:', 'tribe-events-calendar' );
				$output .= '</strong></p><ul class="tribe-errors-list">';

				// loop through each error
				foreach ( $errors as $error ) {
					$output .= '<li>' . (string) $error . '</li>';
				}

				if ( count( $errors ) ) {
					$message = ( isset( $this->major_error ) && $this->major_error ) ? __( 'None of your settings were saved. Please try again.' ) : _n( 'The above setting was not saved. Other settings were successfully saved.', 'The above settings were not saved. Other settings were successfully saved.', $count, 'tribe-events-calendar' );
				}

				$output .= '</ul><p>'.$message.'</p></div>';

				// final output, filtered of course
				echo apply_filters( 'tribe_settings_error_message', $output );
			}
		}

		/**
		 * display success message after saving
		 *
		 * @since 2.0.5
		 * @author PaulHughes01, jkudish
		 * @return void
		 */
		public function displaySuccess() {
			$errors = (array) apply_filters( 'tribe_settings_display_errors', $this->errors );
			$count = apply_filters( 'tribe_settings_count_errors', count( $errors ) );

			// are we coming from the saving place?
			if ( isset( $_GET['saved'] ) && !apply_filters( 'tribe_settings_display_errors_or_not', ( $count > 0 ) ) ) {
				// output the filtered message
				$message = __( 'Settings saved.', 'tribe-events-calendar' );
				$output = '<div id="message" class="updated"><p><strong>' . $message . '</strong></p></div>';
				echo apply_filters( 'tribe_settings_success_message', $output, $this->currentTab );
			}
		}

		/**
		 * delete temporary options
		 *
		 * @since 2.0.6
		 * @author jkudish
		 * @return void
		 */
		public function deleteOptions() {
			delete_option( 'tribe_settings_errors' );
			delete_option( 'tribe_settings_major_error' );
			delete_option( 'tribe_settings_sent_data' );
		}


	} // end class
} // endif class_exists