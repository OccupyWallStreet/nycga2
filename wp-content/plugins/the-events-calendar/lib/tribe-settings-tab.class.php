<?php

// Don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );

if ( !class_exists( 'TribeSettingsTab' ) ) {
	/**
	 * helper class that creates a settings tab
	 * this is a public API, use it to create tabs
	 * simply by instantiating this class
	 *
	 * @since 2.0.5
	 * @author jkudish
	 */
	class TribeSettingsTab {

		/**
		 * Tab ID, used in query string and elsewhere
		 * @var string
		 */
		public $id;

		/**
		 * Tab's name
		 * @var string
		 */
		public $name;

		/**
		 * Tab's arguments
		 * @var array
		 */
		public $args;

		/**
		 * Defaults for tabs
		 * @var array
		 */
		public static $defaults;

		/**
		 * class constructor
		 *
		 * @since 2.0.5
		 * @author jkudish
		 * @param string $id the tab's id (no spaces or special characters)
		 * @param string $name the tab's visible name
		 * @param array $args additional arguments for the tab
		 * @return void
		 */
		public function __construct( $id, $name, $args = array() ) {

			// seetup the defaults
			$this->defaults = array(
				'fields' => array(),
				'priority' => 50,
				'show_save' => true,
				'display_callback' => false,
			);

			// parse args with defaults and extract them
			$args = wp_parse_args( $args, $this->defaults );
			extract( $args );

			// set each instance variable and filter
			$this->id = apply_filters( 'tribe_settings_tab_id', $id );
			$this->name = apply_filters( 'tribe_settings_tab_name', $name );
			foreach ( $this->defaults as $key => $value ) {
				$this->{$key} = apply_filters( 'tribe_settings_tab_'.$key, $$key );
			}


			// run actions & filters
			add_filter( 'tribe_settings_tabs', array( $this, 'addTab' ), $priority );

		}

		/**
		 * filters the tabs array from TribeSettings
		 * and adds the current tab to it
		 * does not add a tab if it's empty
		 *
		 * @since 2.0.5
		 * @author jkudish
		 * @param array $tabs the $tabs from TribeSettings
		 * @return array $tabs the filtered tabs
		 */
		public function addTab( $tabs ) {
			if ( isset( $this->fields ) || has_action( 'tribe_settings_content_tab_' . $this->id ) ) {
				$tabs[$this->id] = $this->name;
				add_filter( 'tribe_settings_fields', array( $this, 'addFields' ) );
				add_filter( 'tribe_settings_no_save_tabs', array( $this, 'showSaveTab' ) );
				add_filter( 'tribe_settings_content_tab_'.$this->id, array( $this, 'doContent' ) );
			}
			return $tabs;
		}

		/**
		 * filters the fields array from TribeSettings
		 * and adds the current tab's fields to it
		 *
		 * @since 2.0.5
		 * @author jkudish
		 * @param array $field the $fields from TribeSettings
		 * @return array $fields the filtered fields
		 */
		public function addFields( $fields ) {
			if ( !empty ($this->fields ) ) {
				$fields[$this->id] = $this->fields;
			} elseif ( has_action( 'tribe_settings_content_tab_' . $this->id ) ) {
				$fields[$this->id] = $this->fields = array( 0 => null ); // just to trick it
			}
			return $fields;
		}

		/**
		 * sets whether the current tab should show the save
		 * button or not
		 *
		 * @since 2.0.5
		 * @author jkudish
		 * @param array $noSaveTabs the $noSaveTabs from TribeSettings
		 * @return array $noSaveTabs the filtered non saving tabs
		 */
		public function showSaveTab( $noSaveTabs ) {
			if ( !$this->show_save || empty( $this->fields ) )
				$noSaveTabs[$this->id] = $this->id;
			return $noSaveTabs;
		}

		/**
		 * displays the content for the tab
		 *
		 * @since 2.0.5
		 * @author jkudish
		 * @return void
		 */
		public function doContent() {
			if ( $this->display_callback && function_exists( $this->display_callback ) ) {
				call_user_func( $this->display_callback ); return;
			}

			$sent_data = get_option( 'tribe_settings_sent_data', array() );

			if ( is_array( $this->fields ) && !empty( $this->fields ) ) {
				foreach ( $this->fields as $key => $field ) {
					if ( isset( $sent_data[$key] ) ) {
						// if we just saved [or attempted to], get the value that was inputed
						$value = $sent_data[$key];
					} else {
						// get the field's parent_option in order to later get the field's value
						$parent_option = ( isset( $field['parent_option'] ) ) ? $field['parent_option'] : TribeEvents::OPTIONNAME;
						$parent_option = apply_filters( 'tribe_settings_do_content_parent_option', $parent_option, $key );
						$default = ( isset( $field['default'] ) ) ? $field['default'] : null;
						$default = apply_filters( 'tribe_settings_field_default', $default, $field );

						if ( !$parent_option ) {
							// no parent option, get the straight up value
							$value = get_option( $key, $default );
						} else {
							// there's a parent option
							if ( $parent_option == TribeEvents::OPTIONNAME ) {
								// get the options from TribeEvents if we're getting the main array
								$value = TribeEvents::getOption( $key, $default );
							} else {
								// else, get the parent option normally
								$options = (array) get_option( $parent_option );
								$value = ( isset( $options[$key] ) ) ? $options[$key] : $default;
							}
						}
					}

					// escape the value for display
					if ( !empty( $field['esc_display'] ) && function_exists( $field['esc_display'] ) ) {
						$value = $field['esc_display']( $value );
					} elseif ( is_string( $value ) ) {
						$value = esc_attr( stripslashes( $value ) );
					}

					// filter the value
					$value = apply_filters( 'tribe_settings_get_option_value_pre_display', $value, $key, $field );

					// create the field
					new TribeField( $key, $field, $value );
				}
			} else {
				// no fields setup for this tab yet
				echo '<p>' . __( 'There are no fields setup for this tab yet.', 'tribe-events-calendar' ) . '</p>';
			}
		}

	} // end class
} // endif class_exists