<?php
/**
* WP-Tabs tinymce plugin.
*/
class wpui_editor_buttons
{
	
	function wpui_editor_buttons( )
	{
		$options = get_option( 'wpUI_options' );
		
		if ( ! is_admin() || ! $this->do_edit() ) return;
			
			// Do Tinymce
			if ( isset( $options['enable_tinymce_menu'] ) && $options['enable_tinymce_menu'] == 'on' ) {
				add_filter( 'mce_external_plugins', array( &$this, 'mce_external_plugins' ) );
				add_filter( 'mce_buttons', array( &$this, 'mce_buttons' ) );
			}
			
			// Do QTags.
			if ( isset( $options['enable_quicktags_buttons'] ) && $options[ 'enable_quicktags_buttons' ] == 'on' ) {
				if ( $this->ls3point3() ) {
					add_action( 'edit_form_advanced', array(&$this, 'wptabs_quicktags_buttons'));
					add_action( 'edit_page_form', array(&$this, 'wptabs_quicktags_buttons'));
				} else {
					add_action( 'admin_print_footer_scripts', array(&$this, 'wpui_QTags'));
				}
			}
			
			// get and set tour.
			if ( ! $this->ls3point3()
				&& isset( $options['tour'] ) 
				&& $options['tour'] == 'on'
				) {
				@include wpui_dir( 'inc/wpui_tour.php' );
			}
	}
	
	// Is the wordpress version - less than 3.3?
	function ls3point3() {
		return ( floatval( get_bloginfo( 'version' ) ) < 3.3 );
	}
	
	function do_edit() {
		$cond = false;
		global $current_user;
		if (
		( in_array( basename( $_SERVER['PHP_SELF'] ), array( 'post-new.php', 'page-new.php', 'post.php', 'page.php' ) ) ) && 
		( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) 
		) $cond = true;
		return $cond;	
	}
	
	function mce_buttons( $buttons ) {
		array_push( $buttons, 'separator', 'wpuimce');
		return $buttons;
	}
	
	// function tinymce_vars() {
	// 	wp_enqueue_script('editor');
	// 	wp_localize_script( 'editor', 'pluginVars', array(
	// 		'wpUrl'		=>	site_url(),
	// 		'pluginUrl'	=>	plugins_url()
	// 	));
	// 
	// }
		
	function mce_external_plugins( $plugin_array ) {
		if ( file_exists( wpui_dir('/inc/wpuimce/editor_plugin.js') ) )
		$plugin_array['wpuimce'] = wpui_url('/inc/wpuimce/editor_plugin.js');
		return $plugin_array;
	}

	function wpui_QTags() {
		?>
		<script type="text/javascript">
		var wpui_qt_open_dialog = function( panel ) {
			diaObj = { mode : panel };
			if ( panel == 'wraptab' )
				diaObj[ 'selection' ] = 'multiple';
			jQuery('#wpui-editor-dialog').wpuiEditor( diaObj );
		};			

		if ( typeof QTags == 'function' ) {
			QTags.addButton( 'ed_wp_addtab', 'Add tab', wpui_dia );	
			QTags.addButton( 'ed_wp_wraptab', 'Wrap tabs', wpui_dia );	
			QTags.addButton( 'ed_wp_spoiler', 'Spoiler', wpui_dia );	
			QTags.addButton( 'ed_wp_dialog', 'Dialog', wpui_dia );	
		}
		
		function wpui_dia() {
			var args = {};
			args.mode = this.id.replace( /ed_wp_/,  '' );
			if ( args.mode == 'wraptab' ) args['selection'] = "multiple";
			jQuery('#wpui-editor-dialog').wpuiEditor( args );
		}

		</script>
		<?php
	}

	function wptabs_quicktags_buttons() {
		?>
		<script type="text/javascript">
			var ebl, ebl_t, edBar, edHTML;
			ebl = edButtons.length;
			ebl_t = ebl;
			
			edHTML = '<select class="ed_button" id="ed_wpui">';
			edHTML += '<option style="background : #C9D0DE" value="do-none">WP UI</option>';
			edHTML += ' <option id="wpui_add_tabset" class="ed_button" value="addtab" title="Add a new tabset">Add Tabset</option>';
			edHTML += ' <option id="wpui_wrap_tabset" class="ed_button" value="wraptab" title="Wrap the tabsets you created.">Wrap Tabs</option> | ';
			edHTML += ' <option id="wpui_add_spoiler" class="ed_button" value="spoiler" title="Add a spoiler.">Spoiler</option>';
			edHTML += ' <option id="wpui_add_dialog" class="ed_button" value="dialog" title="Add a Dialog.">Dialog</option>';			
			
			
			edHTML += '</select>';
			jQuery( document ).ready(function() {
				jQuery( '#ed_wpui' ).change(function( e ) {
					valiey = jQuery( this ).val()
					if ( valiey != 'do-none' ) {
					wpui_qt_open_dialog( valiey );
					jQuery( this ).val( 'do-none' );
				}
				});
			});
			
			
			edBar = document.getElementById('ed_toolbar');
			
			edBar.innerHTML += edHTML;

			function wpuiEditorHelp() {
				editorHelp = '<?php admin_url() ?>admin-ajax.php?action=editorButtonsHelp&TB_iframe=true';
				tb_show('WP UI - A brief guide', editorHelp);
			}
			
			function wpui_qt_open_dialog( panel ) {
				diaObj = { mode : panel };
				if ( panel == 'wraptab' )
					diaObj[ 'selection' ] = 'multiple';
				jQuery('#wpui-editor-dialog').wpuiEditor( diaObj );
			}
			
		</script>
		<?php
	}



	
} // END Class wp_tabs_tinymce


add_action( 'init', 'wpui_ed_buttons' );

function wpui_ed_buttons() {
	global $wpui_buttons;
	if ( !current_user_can( 'edit_posts' ) && 
		!current_user_can( 'edit_pages' ) ) return false;
		
	if ( 'true' == get_user_option( 'rich_editing' ) )	
		$wpui_buttons = new wpui_editor_buttons();
} // END wptabs_buttons
?>