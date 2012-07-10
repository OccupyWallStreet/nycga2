<?php
/**
 *	Plugin Options template
 *	
 *	Plugin options class using WP Settings API. 
 * 
 * 	Initially derived from the theme options class works by Alison
 * 	Barret( @alisothegeek @link: http://alisothegeek.com ). Much Thanks to her. 
 *		
 * @since $Id$
 * @package wp-ui
 * @subpackage admin-options
**/

/**
*	Plugin Options class.
*/
if ( ! class_exists( 'quark_admin_options') ) {
class quark_admin_options
{
	
	public $sections, $fields, $page_id, $admin_scripts, $plugin_details, $plugin_db_prefix, $plugin_page_prefix, $help_tabs, $options;
	
	private $defaults = array(
		'id'		=>	'default_field',
		'title'		=>	'Default Field',
		'desc'		=>	'Description',
		'type'		=>	'text',
		'std'		=>	'',
		'section'	=>	'general',
		'choices'	=>	array(),
		'class'		=>	'',
		'extras'	=>	'',
		'fields'	=>	array(),
		'enclose'	=>	array( 'before' => '', 'after' => '' )
	);
	
	function __construct( array $plugin_details=array() )
	{
		$this->plugin_details = $plugin_details;
		foreach ( $plugin_details as $key => $value ) {
			$this->{$key} = $value;
		}
		$this->quark_admin_options();
		
		$this->options = get_option( $this->db_prefix . '_options' );
	}
	
	
	public function quark_admin_options() {
		add_action( 'admin_menu' , array(&$this, 'menu_admin'));
		add_action( 'admin_init' , array(&$this, 'init_admin'));
		$this->set_page_id($this->page_id);	
	}


	public function init_admin() {
		$this->register_options();
	}
	
	public function menu_admin() {
		$page_str = add_options_page( $this->name . ' Options', $this->name, 'manage_options', $this->page_prefix . '-options', array(&$this, 'render_options_page') );
		if ( floatval( get_bloginfo( 'version' ) ) >= 3.3 )
		add_action( 'admin_print_styles-' . $page_str, array( &$this, 'provide_help' ) );
		$this->page_id = $page_str;
		add_action( 'admin_print_styles-' . $page_str , array( &$this, 'script_action' ) );
	}
	
	function script_action() {
		do_action( 'plugin_' . $this->page_prefix . '_load_scripts' );
	}
	
	public function render_options_page() {

		echo '<div class="wrap">
				<div class="icon32" id="icon-options-general"></div>
				<h2>' . $this->name . ' Options</h2>';

		/**
		 * Hook for inserting info *above* your plugin's option page.
		 * 	Can be used for information about the plugin, warnings etc.
		 */
		// do_meta_boxes( 'top-' . $this->db_prefix, 'normal', null );
		do_action( $this->page_prefix . '_above_options_page' );

		/**
		 * Start the form tag.
		 */
		echo '<form id="optionsform" action="options.php" method="post">
				<div id="options-wrap">';

			/**
			 * Display the options.
			 */
			settings_fields( $this->db_prefix . '_options');
			do_settings_sections( $_GET['page'] );
			
		echo '</div><!-- end #options-wrap -->
				<p class="submit">
					<input name="' . $this->db_prefix . '_options[submit]" type="submit" class="button-primary" value="' . __( 'Save Options' ) . '" />
					<input name="' . $this->db_prefix . '_options[reset]" type="submit" class="button-secondary" value="' . __( 'Reset Defaults' ) . '" />
					</p><!-- end p.submit -->
			</form><!-- end form#optionsform -->';

		/**
		 * Hook for inserting info *below* your plugin's option page.
		 * 	Useful for credits and similar.
		 */
		// do_meta_boxes( 'below-' . $this->db_prefix, 'normal', null );
		do_action( $this->page_prefix . '_below_options_page' );
			
	}

	public function register_options() {
		register_setting( $this->db_prefix . '_options', $this->db_prefix . '_options', array(&$this, 'validate_options'));
		
		foreach( $this->sections as $slug => $title ) {
			add_settings_section( $slug, $title , array( &$this, 'display_section'), $this->page_prefix . '-options');
		}
			
		foreach ( $this->fields as $field ) {
			$this->create_option( $field );
		}
		
	} // END method register_options.
	
	public function display_section() {
		
	}
	
	function postbox( $id, $title, $content ) {
		?>
			<div id="<?php echo $id; ?>" class="postbox wpui-postbox">
				<div class="handlediv" title="Click To Toggle"><br /></div>
				<h3 class="hndle"><span><?php echo $title ?></span></h3>
				<div class="inside"><?php echo $content ?></div>				
			</div><!-- end .wpui-postbox -->
		<?php
	}	
	
	public function create_option( $args = array() ) {
		
		$defaults = array(
			'id'			=>	'default_field',
			'title'			=>	'Default Field',
			'desc'			=>	'Description, nonetheless.',
			'type'			=>	'text',
			'subtype'		=>	'',
			'std'			=>	'',
			'section'		=>	'general',
			'choices'		=>	array(),
			'label_for'		=>	'',
			'field_class'	=>	'',
			'text_length'	=>	'',
			'textarea_size'	=>	array(),
			'extras'		=>	'',
			'fields'		=>	array(),
			'enclose'		=>	array( 'before' => '', 'after' => '' )
		);
		
		extract( wp_parse_args( $args, $defaults) );
		
		$option_args = array(
			'type'					=>	$type,
			'subtype'				=>	$subtype,
			'id'					=>	$id,
			'desc'					=>	$desc,
			'std'					=>	$std,
			'choices'				=>	$choices,
			'label_for'				=>	$id,
			'field_class'			=>	$field_class,
			'text_length'			=>	$text_length,
			'textarea_size'			=>	$textarea_size,
			'extras'				=>	$extras,
			'fields'				=>	$fields,
			'enclose'				=>	$enclose
		);

		add_settings_field( $id, $title, array( &$this, 'display_option'), $this->page_prefix . '-options', $section, $option_args);
		
		
		
	} // END method create_option.


	/**
	 * Regular checkbox.
	 */
	private function checkbox( $args=array() ) {
		$defs = array(
				'id'	=>	'',
				'name'	=>	'',
				'desc'	=>	'',
				'nested'=>	false );	
		extract(wp_parse_args( $args, $defs ));

		$checked = '';
		if( isset( $this->options ) && $this->opt( $id ) == 'on' ) $checked = ' checked="checked"';
		echo '<input id="' . $id . '" type="checkbox" name="' . $name . '" value="on"' . $checked . '/><label for="' . $id . '"> ' . $desc . '</label>';
	} // end checkbox.
	
	
	/**
	 * 	Select or Combo box.
	 */
	private function select($args=array()) {
		$defs = array(
				'id'		=>	'',
				'name'		=>	'',
				'desc'		=>	'',
				'choices'	=> array(),
				'extras'	=>	'',
				'nested'	=>	false );	
		extract(wp_parse_args( $args, $defs ));
		
		echo '<select id="' . $id . '" name="' . $name . '">';
		foreach ( $choices as $value=>$label ) {
			$selected = '';
			if ( $this->opt($id) == $value ) $selected = ' selected';
			
			if ( stristr( $value, 'startoptgroup' ) ) {
				echo '<optgroup label="' . $label . '">';
			} else if ( stristr( $value, 'endoptgroup') ) {
				echo '</optgroup>';
			} else {
				echo '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
			}
		}
		echo '</select>';
		if ( $extras != '' && ! $nested )
			echo $extras;
		if( $desc != '' && ! $nested )
			echo '<br /> ' . $desc;		
	}
	
	
	/**
	 * Radio boxes
	 */
	private function radio( $args=array() ) {
		$defs = array(
				'id'		=>	'',
				'name'		=>	'',
				'desc'		=>	'',
				'choices'	=>	array(),
				'extras'	=>	'',
				'subtype'	=>	'normal',
				'nested'	=>	false
				);	
			extract(wp_parse_args( $args, $defs ));
		
		if ( $subtype == 'descriptive' ) {
			if( $desc != '' )
				echo $desc . '<br /><br />';
			$style_elem = "style='float:left;margin-right:20px;margin-bottom:20px;border: 1px solid #bbb;-moz-box-shadow: 0px 1px 2px #AAA;-webkit-box-shadow: 2px 2px 2px #777;box-shadow: 2px 2px 2px #777;'";
		foreach ( $choices as $choice )
		{
			$active = ( $this->opt( $id ) == $choice['slug']) ? 'class="active-layout"' : '';
			echo "<dl style='float:left; padding:5px; text-align:center; max-width:160px' " . $active . ">";
			$checked = ( $this->opt( $id ) == $choice['slug']) ? ' checked ' : '';
			echo "<dt>" . $choice['name'] . "</dt>";
			echo "<dd style='text-align:center'><img src='". $choice['image'] ."' /></dd>";
			echo "<dd>";
			echo "<input name='" . $name . "' " . $checked . " id='" . $id .  "' value='" . $choice['slug'] . "' type='radio' />";
			echo "</dd>";
			echo "<dd>" . $choice['description'] . "</dd>";
			echo '</dl>';
			}
		}
		else // Regular radio buttons.
		{
		$i = 0;
		foreach( $choices as $value => $label ) {
			$selected = '';
			if ( $this->opt( $id ) == $value )
				$selected = ' checked="checked"';
		echo '<input type="radio" name="' . $name . '" value="' . $value . '"' . $selected . '><label for="' . $id . $i . '">' . $label . '</label>';
		if ( $i < count( $choices ) -1 )
			echo '<br />';
		$i++;	
		}
		if( $desc != '' && ! $nested  )
			echo '<br /> ' . $desc;
		}		
	} // I am radio. End.
	
	
	/**
	 * Textareas
	 */
	private function textarea($args=array()) {
		$defs = array(
				'id'			=>	'',
				'name'			=>	'',
				'desc'			=>	'',
				'textarea_size'	=>	array(),
				'nested'		=>	false );	
			extract(wp_parse_args( $args, $defs ));
			
		$text_cols = ''; $text_rows = ''; $autocomplete = 'on';
		if (!empty($textarea_size)) {
			$text_cols = ' cols="' . $textarea_size['cols'] . '"';
			$text_rows = ' rows="' . $textarea_size['rows'] . '"';
			if( isset( $textarea_size[ 'autocomplete' ] ) )
				$autocomplete = $textarea_size[ 'autocomplete' ];
		}	
		echo '<textarea' . $text_cols . $text_rows . ' autocomplete="' . $autocomplete . '" id="' . $id . '" name="' .  $name . '">' . $this->opt( $id ) . '</textarea>';
		if( $desc != ''  && ! $nested )
			echo '<br /> ' . $desc;		
	} // end fun textarea.


	private function fileinput( $id, $name, $desc, $nested=false ) {
		$defs = array(
				'id'	=>	'',
				'name'	=>	'',
				'desc'	=>	'',
				'nested'=>	false );	
		wp_parse_args( $args, $defs );
		echo '<input type="file" id="' . $id . '" name="' . $id . '" />';
		if ( $desc != ''  && ! $nested )
			echo '<br /> ' . $desc;
		if ( $file = $this->opt( $id ) ) {
			// var_dump($file);
			echo '<br /> <br /><a class="thickbox" href=' . $file['url'] . '>' .  __('Currently uploaded image', 'wp-ui' ) . '</a>';
		}		
	}
	
	/**
	 * Regular text input - Default.
	 */
	private function textinput( $args=array() ) {
		$defs = array(
				'id'	=>	'',
				'name'	=>	'',
				'desc'	=>	'',
				'text_length'	=>	'',
				'type'	=>	'text',
				'nested'=>	false );	
		extract(wp_parse_args( $args, $defs ));

		$style = '';
		if ( $type == 'farbtastic' )
			$style = ' style="position:relative" ';
		elseif( $type == 'jscolor' )
			$style = ' class="color {hash:true}" ';
		elseif ( $type == 'media-upload' )
			$style = ' style="text-align : right;"';
			
		if ($text_length != '') {
			$text_length = ' size="' . $text_length . '"';
		}
		$thisVal = ($this->opt( $id )) ? $this->opt( $id ) : '';
		
		echo '<input' . $text_length . $style  . ' type="text" id="' . $id . '" name="' . $name . '" value="' . $thisVal . '" />';
		$nid = $id;

		if ( $type == 'farbtastic' ) {
			// Init farbtastic color-picker.
			echo '<div id="colorpicker"></div>';
			echo '<script type="text/javascript">
				// Hide the colorpicker first.
				jQuery("#colorpicker").hide();
				// Open the color picker on clicking the textfield.
				jQuery("#' . $nid . '").click(function() {
					jQuery("#colorpicker").farbtastic("#' . $nid . '").slideDown(500);
				});
				// Hide the color-picker on Double click.
				jQuery("#' . $nid . '").dblclick(function() {
					jQuery("#colorpicker").slideUp(300);
				});
		</script><!-- End farbtastic init script. -->';
		} else if( $type == 'jscolor' ) {
			// Jscolor, chosen.
			$optjsurl = get_bloginfo('template_url'). '/lib/options/js/';
			wp_enqueue_script('jscolor', $optjsurl . '/jscolor/jscolor.js');
		} else if( $type == 'media-upload' ) {
			echo '<input id="' . $nid . '_trigger" type="button" class="button-secondary" value="Upload" />';
			$post_id = 0;
			echo "<script type=\"text/javascript\">	
			instance = 0;
			jQuery('#" . $nid . "_trigger').click(function() {
				instance++; if ( instance > 1 ) return false;
				backup_send = window.send_to_editor;
				formfield = jQuery('label[for=$nid]').text();
				window.send_to_editor = window.send_to_editor_$nid;
				
				tb_show('Upload images for ' + formfield, 'media-upload.php?post_id=0&type=image&amp;TB_iframe=true');
				return false;
				
				
			});
			window.send_to_editor_$nid  = function(html) {
				imgURL = jQuery('img', html).attr('src');
				jQuery('#$nid').val(imgURL);
				tb_remove();
				reverseSend();
				return false;
			}
			var reverseSend = function() {
				window.send_to_editor = backup_send;
			};
			</script>";
			if ( $this->opt($id) != '' ) {
				echo '<br /> <br /><a class="thickbox" href=' . $this->opt($id) . '>' .  __('Currently uploaded image') . '</a>';
			}			
		}	
		if ( $desc != '' && ! $nested )
			echo '<br /> ' . $desc;		
	} // END good ol` regular text input.


	
	public function display_option( $args = array() ) {
		extract( $args );
		
		$options = get_option( $this->db_prefix . '_options');
		
		if ( !isset( $options[$id] ) && 'type' != 'checkbox' )
			$options[$id] = $std;
		
		echo $enclose[ 'before' ];
		switch( $type ) {		

			////////////////////////////////////////////////
			//////////////// Checkbox //////////////////////
			////////////////////////////////////////////////
			case 'checkbox':
			$this->checkbox( array(
				'id'	=>	$id,
				'name'	=>	$this->db_prefix . '_options[' . $id . ']',
				'desc'	=>	$desc,
				 ));
			// $this->checkbox( $id, $this->db_prefix . '_options[' . $id . ']', $desc );
			break;
			
			////////////////////////////////////////////////
			/////////// Combo boxes (select) ///////////////
			////////////////////////////////////////////////
			case 'select':
			$this->select( array(
				'id'	=>	$id,
				'name'	=>	$this->db_prefix . '_options[' . $id . ']',
				'desc'	=>	$desc,
				'choices'	=> $choices,
				'extras'	=>	$extras
				
				 ));
			// $this->select( $id, $this->db_prefix . '_options[' . $id . ']', $desc, $choices, $extras );
			break;
			
			
			////////////////////////////////////////////////
			//////////////// Radio buttons /////////////////
			////////////////////////////////////////////////
			case 'radio':
			$this->radio( array(
				'id'	=>	$id,
				'name'	=>	$this->db_prefix . '_options[' . $id . ']',
				'desc'	=>	$desc,
				'choices'	=> $choices,
				'extras'	=>	$extras
				 ));			
			break;
			
			
			////////////////////////////////////////////////
			//////////////// Text areas ////////////////////
			////////////////////////////////////////////////
			case 'textarea':
			$this->textarea( array(
				'id'	=>	$id,
				'name'	=>	$this->db_prefix . '_options[' . $id . ']',
				'desc'	=>	$desc,
				'textarea_size'	=> $textarea_size,
				 ));
			break;

			
			////////////////////////////////////////////////
			//////////////// Rich text edit ////////////////
			////////////////////////////////////////////////
			// case 'richtext':
			// if (!empty($textarea_size)) {
			// 	$text_cols = ' cols="' . $textarea_size['cols'] . '"';
			// 	$text_rows = ' rows="' . $textarea_size['rows'] . '"';
			// 	if( isset( $textarea_size[ 'autocomplete' ] ) )
			// 		$autocomplete = $textarea_size[ 'autocomplete' ];
			// }
			// echo '<p class="switch-editors" align="right"><a class="toggleVisual">Visual</a><a class="toggleHTML">HTML</a></p>';
			// echo '<textarea' . $field_class . $text_cols . $text_rows . ' id="' . $id . '" class="rich-text-editor" name="' . $this->db_prefix . '_options[' . $id . ']">' . $options[$id] . '</textarea>';
			// if( $desc != '' )
			// 	echo '<br /> ' . $desc;
			// if( function_exists( 'wp_tiny_mce' ) ) wp_tiny_mce(false, array( 'editor_selector' => 'rich-text-editor' , 'height' => 300, 'mce_external_plugins' => array()));
			// echo '<script type="text/javascript">
			// jQuery(document).ready(function() {
			// jQuery("a.toggleVisual").click(function(){
			// 		tinyMCE.execCommand("mceAddControl", false, "' . $id . '");
			// });
			// jQuery("a.toggleHTML").click(function(){
			// 		tinyMCE.execCommand("mceRemoveControl", false, "' . $id .'");
			// });				
			// 	
			// }); // END document ready
			// 
			// </script>';
			// break;


			////////////////////////////////////////////////
			//////////////// Password //////////////////////
			////////////////////////////////////////////////
			case 'password':
			echo '<input id="' . $id . '" type="password" name="' . $this->db_prefix . '_options[' . $id . ']" value="' . $options[$id] . '" />';
			if( $desc != '' )
				echo '<br /> ' . $desc;			
			break;
			

			////////////////////////////////////////////////
			/////////// Regular PHP file uploader //////////
			////////////////////////////////////////////////
			case 'file':
			$this->fileinput( $id, $this->db_prefix . '_options[' . $id . ']', $desc, $file );			
			break;					
		
		
			////////////////////////////////////////////////
			/////////// Wordpress Media uploader ///////////
			////////////////////////////////////////////////
			case 'media-upload':
			$this->textinput(array(
					'id' => $id,
					'name' => $this->db_prefix . '_options[' . $id . ']',
					'desc' => $desc,
					'text_length' => $text_length,
					'type' => 'media-upload'
			));		
			break;
		
		
			////////////////////////////////////////////////
			//////////////// Color picker //////////////////
			////////////////////////////////////////////////
			case 'color':
			$this->textinput(array(
					'id' => $id,
					'name' => $this->db_prefix . '_options[' . $id . ']',
					'desc' => $desc,
					'text_length' => $text_length,
					'type' => $this->color_picker ));
			break;

			case 'separator':
				echo '<br /></tr></table><hr color="#D5D5D5"><table class="form-table"><tbody><tr>';
			break;
			
			case 'multiple':
				foreach( $fields as $field ) {
					if ( isset( $field[ 'enclose' ] ) )
					echo $field[ 'enclose' ]['before' ];
					
					$args_arr = array( 
						'id' => $id . 'KKKKK' . $field[ 'idkey' ],
						'name' => $this->db_prefix . '_options[' . $id . '][' . $field[ 'idkey' ] . ']',
						'desc' => $field[ 'desc' ]
						);

					if ( $field['type'] == 'textinput' ) {
						$args_arr['text_length'] = $field[ 'text_length' ];
						$args_arr['type'] = 'text';
					} elseif ( $field['type'] == 'media-upload' ) {
						$args_arr['text_length'] = $field[ 'text_length' ];
						$args_arr['type'] = 'media-upload';
						$field[ 'type' ] = 'textinput';
					} elseif ( $field['type'] == 'select' ) {
						$args_arr['choices'] = $field[ 'choices' ];
						if ( isset( $field['extras'] ) )
						$args_arr['extras'] = $field[ 'extras' ];
					} elseif ( $field['type'] == 'radio' ) {
						$args_arr['choices'] = $field[ 'choices' ];
					}
					// Nested
					$args_arr['nested'] = true;
					
					call_user_func_array( array( &$this, $field[ 'type' ] ), array($args_arr) );
					if ( isset( $field[ 'enclose' ] ) )
					echo $field[ 'enclose' ]['after' ];
					
				}
				if ( $desc != '' )
				echo $desc;	
											
			break;			
			
			
			////////////////////////////////////////////////
			////////////////// Textbox /////////////////////
			////////////////////////////////////////////////
			case 'text':
			default:
			$this->textinput(array(
					'id' => $id,
					'name' => $this->db_prefix . '_options[' . $id . ']',
					'desc' => $desc,
					'text_length' => $text_length,
					'type' => 'text' ));
			break;


		} // END switch $type.
		echo $enclose[ 'after' ];
		
	}
	
	
	private function opt( $id, $just=false ) {
		if ( ! isset( $this->options ) ) return false;
		$arr = explode( 'KKKKK', $id );
		if ( $just && count( $arr ) > 1) return $arr[ 1 ]; 		
		if ( is_array( $arr) && count( $arr ) > 1 && isset( $this->options[ $arr[ 0 ] ]))
			return $this->options[ $arr[ 0 ] ][ $arr[ 1 ] ];
		else
			return ( isset( $this->options ) && isset( $this->options[ $id ] ) )
						? $this->options[ $id ]
						: false;
	}

	public function validate_options( $input ) {
		// echo '<pre>';
		// print_r($input);
		// 
		// echo '</pre>';
		return $input;
	}
	
	public function provide_help( $input ) {
		
		$screen = get_current_screen();

		if ( ! is_array( $this->help_tabs ) ) return;
		
		foreach( (array)$this->help_tabs as $help=>$tab ) {
			if ( ! isset( $tab[ 'id'] ) || !isset( $tab[ 'title' ]) || ! isset( $tab[ 'content' ] ) ) continue;
			$screen->add_help_tab( array(
				'id' => strip_tags($tab[ 'id' ]),
				'title' => $tab['title'],
				'content' => $tab['content']
			));
			
		}
	
		
	}

	
	public function set_sections( $sections ) {
		return $this->sections = $sections;
	}
	public function get_sections() {
		return $this->sections;
	}
	
	public function set_fields( $fields ) {
		return $this->fields = $fields;
	}
	public function get_fields() {
		return $this->fields;
	}
	
	public function set_plugin_details( $plugin_details = array() ) {
		return $this->plugin_details = $plugin_details;
	}
	public function get_plugin_details() {
		return $this->plugin_details;
	}
	
	public function set_page_id( $page_id ) {
		return $this->page_id = $page_id;
	}
	
	public function get_page_id() {
		return $this->page_id;
	}
	
	public function set_admin_scripts( $admin_scripts = array() ) {
		return $this->admin_scripts = $admin_scripts;
	}
	
	public function get_admin_scripts() {
		return $this->admin_scripts;
	}
	
	public function set_help_tabs( $help_tabs=array() ) {
		return $this->help_tabs = $help_tabs;
	}

	
} // END class quark_admin_options.



} // END if class_exists check for quark_admin_options.
?>