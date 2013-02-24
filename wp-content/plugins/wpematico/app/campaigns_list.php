<?php 
// don't load directly 
if ( !defined('ABSPATH') ) 
	die('-1');

 if( strstr($_SERVER['REQUEST_URI'], 'wp-admin/edit.php?post_type=wpematico')  
  || strstr($_SERVER['REQUEST_URI'], 'wp-admin/admin.php?action=wpematico_') ) 
	add_action( 'init', array( 'WPeMatico_Campaigns', 'init' ) );
	else return;
 	
if ( class_exists( 'WPeMatico_Campaigns' ) ) return;

class WPeMatico_Campaigns {

	public function init() {
		new self();
	}
	
	public function __construct( $hook_in = FALSE ) {
		$cfg = get_option( WPeMatico :: OPTION_KEY);
	
		add_filter( 'post_updated_messages', array( &$this , 'wpematico_updated_messages') );
		add_filter('manage_edit-wpematico_columns' , array( &$this, 'set_edit_wpematico_columns'));
		add_action('manage_wpematico_posts_custom_column',array(&$this,'custom_wpematico_column'),10,2);
		add_filter('post_row_actions' , array( &$this, 'wpematico_quick_actions'), 10, 2);
		add_filter("manage_edit-wpematico_sortable_columns", array( &$this, "sortable_columns") );
		//QUICK ACTIONS
		add_action('admin_action_wpematico_copy_campaign', array( &$this, 'wpematico_copy_campaign'));
		add_action('admin_action_wpematico_toggle_campaign', array(&$this, 'wpematico_toggle_campaign'));
		if ( $cfg['enabledelhash'])    // Si está habilitado en settings, lo muestra 
			add_action('admin_action_wpematico_delhash_campaign', array(&$this, 'wpematico_delhash_campaign'));
		add_action('admin_action_wpematico_reset_campaign', array(&$this, 'wpematico_reset_campaign'));
		add_action('admin_action_wpematico_clear_campaign', array(&$this, 'wpematico_clear_campaign'));

		add_action('admin_print_styles-edit.php', array(&$this,'list_admin_styles'));
		add_action('admin_print_scripts-edit.php', array(&$this,'list_admin_scripts'));
	}
	
  	function list_admin_styles(){
		wp_enqueue_style('campaigns-list',WPeMatico :: $uri .'app/css/campaigns_list.css');
//		add_action('admin_head', array( &$this ,'campaigns_admin_head_style'));
	}
	function list_admin_scripts(){
		add_action('admin_head', array( __CLASS__ ,'campaigns_list_admin_head'));
//		wp_register_script('jquery-input-mask', 'js/jquery.maskedinput-1.2.2.js', array( 'jquery' ));
//		wp_enqueue_script('color-picker', 'js/colorpicker.js', array('jquery'));
			
	}

	function campaigns_list_admin_head() {
		global $post;
		if($post->post_type != 'wpematico') return $post_id;
		?>
		<script type="text/javascript" language="javascript">
			function run_now(c_ID) {
				jQuery.ajaxSetup({async:false});
				jQuery('#fieldserror').remove();
				msgdev="<img width='12' src='<?php echo get_bloginfo('wpurl'); ?>/wp-admin/images/wpspin_light.gif' class='mt2'> <?php _e('Running Campaign...', WPeMatico :: TEXTDOMAIN ); ?>";
				jQuery(".subsubsub").prepend('<div id="fieldserror" class="updated fade he20">'+msgdev+'</div>');
				var data = {
					campaign_ID: c_ID ,
					action: "runnowx"
				};
				jQuery.post(ajaxurl, data, function(msgdev) {  //si todo ok devuelve LOG sino 0
					jQuery('#fieldserror').remove();
					if( msgdev.substring(0, 5) == 'ERROR' ){
						jQuery(".subsubsub").prepend('<div id="fieldserror" class="error fade">'+msgdev+'</div>');
					}else{
						jQuery(".subsubsub").prepend('<div id="fieldserror" class="updated fade">'+msgdev+'</div>');
					}
				});
			};
		</script>
		<?php
	}

	/**
	 ************ACCION COPIAR 
	 */
	function copy_duplicate_campaign($post, $status = '', $parent_id = '') {
		// We don't want to clone revisions
		if ($post->post_type != 'wpematico') return;
		$prefix = "";
		$suffix = __("(Copy)",  WPeMatico :: TEXTDOMAIN) ;
		if (!empty($prefix)) $prefix.= " ";
		if (!empty($suffix)) $suffix = " ".$suffix;
		$status = 'publish';

		$new_post = array(
		'menu_order' => $post->menu_order,
		'guid' => $post->guid,
		'comment_status' => $post->comment_status,
		'ping_status' => $post->ping_status,
		'pinged' => $post->pinged,
		'post_author' => @$post->author,
		'post_content' => $post->post_content,
		'post_excerpt' => $post->post_excerpt,
		'post_mime_type' => $post->post_mime_type,
		'post_parent' => $post->post_parent,
		'post_password' => $post->post_password,
		'post_status' => $status,
		'post_title' => $prefix.$post->post_title.$suffix,
		'post_type' => $post->post_type,
		'to_ping' => $post->to_ping, 
		'post_date' => $post->post_date,
		'post_date_gmt' => get_gmt_from_date($post->post_date)
		);	

		$new_post_id = wp_insert_post($new_post);

		$post_meta_keys = get_post_custom_keys($post->ID);
		if (!empty($post_meta_keys)) {
			foreach ($post_meta_keys as $meta_key) {
				$meta_values = get_post_custom_values($meta_key, $post->ID);
				foreach ($meta_values as $meta_value) {
					$meta_value = maybe_unserialize($meta_value);
					add_post_meta($new_post_id, $meta_key, $meta_value);
				}
			}
		}
		$campaign_data = WPeMatico :: get_campaign( $new_post_id );
		$campaign_data['activated'] = false;

		WPeMatico :: update_campaign( $new_post_id, $campaign_data );

		// If the copy is not a draft or a pending entry, we have to set a proper slug.
		/*if ($new_post_status != 'draft' || $new_post_status != 'auto-draft' || $new_post_status != 'pending' ){
			$post_name = wp_unique_post_slug($post->post_name, $new_post_id, $new_post_status, $post->post_type, $new_post_parent);

			$new_post = array();
			$new_post['ID'] = $new_post_id;
			$new_post['post_name'] = $post_name;

			// Update the post into the database
			wp_update_post( $new_post );
		} */

		return $new_post_id;
	}

	function wpematico_copy_campaign($status = ''){
		if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'wpematico_copy_campaign' == $_REQUEST['action'] ) ) ) {
			wp_die(__('No campaign ID has been supplied!',  WPeMatico :: TEXTDOMAIN));
		}

		// Get the original post
		$id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
		$post = get_post($id);

		// Copy the post and insert it
		if (isset($post) && $post!=null) {
			$new_id = self :: copy_duplicate_campaign($post, $status);

			if ($status == ''){
				// Redirect to the post list screen
				wp_redirect( admin_url( 'edit.php?post_type='.$post->post_type) );
			} else {
				// Redirect to the edit screen for the new draft post
				wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
			}
			exit;

		} else {
			$post_type_obj = get_post_type_object( $post->post_type );
			wp_die(esc_attr(__('Copy campaign failed, could not find original:',  WPeMatico :: TEXTDOMAIN)) . ' ' . $id);
		}
	}

	/**
	************FIN ACCION COPIAR 
	*/

	/**
	************ACCION TOGGLE 
	*/
	function wpematico_toggle_campaign($status = ''){
		if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'wpematico_toggle_campaign' == $_REQUEST['action'] ) ) ) {
			wp_die(__('No campaign ID has been supplied!',  WPeMatico :: TEXTDOMAIN));
		}
		// Get the original post
		$id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);

		$campaign_data =   WPeMatico :: get_campaign( $id );
		$campaign_data['activated'] = !$campaign_data['activated'];
		WPeMatico :: update_campaign( $id, $campaign_data );
		
		// Redirect to the post list screen
		wp_redirect( admin_url( 'edit.php?post_type=wpematico') );
	}

	/*********FIN ACCION TOGGLE 	*/
	
	/**	************ACCION RESET 	*/
	function wpematico_reset_campaign($status = ''){
		if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'wpematico_reset_campaign' == $_REQUEST['action'] ) ) ) {
			wp_die(__('No campaign ID has been supplied!',  WPeMatico :: TEXTDOMAIN));
		}
		// Get the original post
		$id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
		$campaign_data =   WPeMatico :: get_campaign( $id );
		$campaign_data['postscount'] = 0;
		$campaign_data['lastpostscount'] = 0;
		WPeMatico :: update_campaign( $id, $campaign_data );

		// Redirect to the post list screen
		wp_redirect( admin_url( 'edit.php?post_type=wpematico') );
	}

	/**************FIN ACCION RESET 	*/
	
	/**	************ACCION DELHASH	 	*/
	function wpematico_delhash_campaign(){
		if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'wpematico_delhash_campaign' == $_REQUEST['action'] ) ) ) {
			wp_die(__('No campaign ID has been supplied!',  WPeMatico :: TEXTDOMAIN));
		}
		// Get the original post
		$id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
		$campaign_data =   WPeMatico :: get_campaign( $id );
		foreach($campaign_data['campaign_feeds'] as $feed) {    // Grabo el ultimo hash de cada feed con 0
			$campaign_data[$feed]['lasthash']="0"; 
		}
		WPeMatico :: update_campaign( $id, $campaign_data );

		// Redirect to the post list screen
		wp_redirect( admin_url( 'edit.php?post_type=wpematico') );
	}
	/**************FIN ACCION DELHASH	*/
	
	/**	************ACCION CLEAR: ABORT CAMPAIGN	 	*/
	function wpematico_clear_campaign(){
		if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'wpematico_clear_campaign' == $_REQUEST['action'] ) ) ) {
			wp_die(__('No campaign ID has been supplied!',  WPeMatico :: TEXTDOMAIN));
		}
		
		// Get the original post
		$id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
		$campaign_data =   WPeMatico :: get_campaign( $id );

		$campaign_data['cronnextrun']= WPeMatico :: time_cron_next($campaign_data['cron']); //set next run
		$campaign_data['stoptime']   = current_time('timestamp');
		$campaign_data['lastrun']  	 = $campaign_data['starttime'];
		$campaign_data['lastruntime']= $campaign_data['stoptime']-$campaign_data['starttime'];
		$campaign_data['starttime']  = '';

		WPeMatico :: update_campaign( $id, $campaign_data );

		// Redirect to the post list screen
		wp_redirect( admin_url( 'edit.php?post_type=wpematico') );
	}
	/**************FIN ACCION DELHASH	*/
	
	public function wpematico_action_link( $id = 0, $context = 'display', $actionslug ) {
		if ( !$post = &get_post( $id ) ) return;
		switch ($actionslug){ 
		case 'copy':
			$action_name = "wpematico_copy_campaign";
			break;
		case 'toggle':
			$action_name = "wpematico_toggle_campaign";
			break;
		case 'reset':
			$action_name = "wpematico_reset_campaign";
			break;
		case 'delhash':
			$action_name = "wpematico_delhash_campaign";
			break;
		case 'clear':
			$action_name = "wpematico_clear_campaign";
			break;			
		}
		if ( 'display' == $context ) 
			$action = '?action='.$action_name.'&amp;post='.$post->ID;
		else 
			$action = '?action='.$action_name.'&post='.$post->ID;
			
		$post_type_object = get_post_type_object( $post->post_type );
		if ( !$post_type_object )	return;
		
		return apply_filters( 'wpematico_action_link', admin_url( "admin.php". $action ), $post->ID, $context );
	}

	//change actions from custom post type list
	function wpematico_quick_actions( $actions ) {
		global $post;
		$cfg = get_option( WPeMatico :: OPTION_KEY);
		if( $post->post_type == 'wpematico' && $post->post_status != "trash" ) {
	//		unset( $actions['edit'] );
			unset( $actions['view'] );
	//		unset( $actions['trash'] );
			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['clone'] );
			unset( $actions['edit_as_new_draft'] );
			//++++++Toggle
			$campaign_data = WPeMatico :: get_campaign( $post->ID );
			$starttime = @$campaign_data['starttime']; 
			if (empty($starttime)) {
				$acnow = (bool)$campaign_data['activated'];
				$atitle = ( $acnow ) ? esc_attr(__("Deactivate this campaign", WPeMatico :: TEXTDOMAIN)) : esc_attr(__("Activate schedule", WPeMatico :: TEXTDOMAIN));
				$alink = ($acnow) ? __("Deactivate", WPeMatico :: TEXTDOMAIN): __("Activate",WPeMatico :: TEXTDOMAIN);
				$actions['toggle'] = '<a href="'.self :: wpematico_action_link( $post->ID , 'display','toggle').'" title="' . $atitle . '">' .  $alink . '</a>';
				//++++++Copy
				$actions['copy'] = '<a href="'.self :: wpematico_action_link( $post->ID , 'display','copy').'" title="' . esc_attr(__("Clone this item", WPeMatico :: TEXTDOMAIN)) . '">' .  __('Copy', WPeMatico :: TEXTDOMAIN) . '</a>';
				//++++++Reset
				$actions['reset'] = '<a href="'.self :: wpematico_action_link( $post->ID , 'display','reset').'" title="' . esc_attr(__("Reset post count", WPeMatico :: TEXTDOMAIN)) . '">' .  __('Reset', WPeMatico :: TEXTDOMAIN) . '</a>';
				//++++++runnow
				$actions['runnow'] = '<a href="JavaScript:run_now(' . $post->ID . ');" title="' . esc_attr(__("Run Now this campaign", WPeMatico :: TEXTDOMAIN)) . '">' .  __('Run Now', WPeMatico :: TEXTDOMAIN) . '</a>';
				//++++++delhash
				if ( @$cfg['enabledelhash'])    // Si está habilitado en settings, lo muestra 
					$actions['delhash'] = '<a href="'.self :: wpematico_action_link( $post->ID , 'display','delhash').'" title="' . esc_attr(__("Delete hash code for duplicates", WPeMatico :: TEXTDOMAIN)) . '">' .  __('Del Hash', WPeMatico :: TEXTDOMAIN) . '</a>';
				//++++++seelog
				if ( @$cfg['enableseelog']) {   // Si está habilitado en settings, lo muestra 
					$nonce= wp_create_nonce  ('clog-nonce');
					$nombre = get_the_title($post->ID);
					$actionurl = WPeMatico :: $uri . 'app/campaign_log.php?p='.$post->ID.'&_wpnonce=' . $nonce;
					$actionjs = "javascript:window.open('$actionurl','$nombre','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=600');";
					
					$actions['seelog'] = '<a href="#" onclick="'.$actionjs.' return false;" title="' . esc_attr(__("See last log of campaign. (Open a PopUp window)", WPeMatico :: TEXTDOMAIN)) . '">' . __('See Log', WPeMatico :: TEXTDOMAIN) . '</a>';
				}
			} else {  // Está en ejecución o quedó a la mitad
				unset( $actions['edit'] );
				$actions['clear'] = '<a href="'.self :: wpematico_action_link( $post->ID , 'splay','clear').'" title="' . esc_attr(__("Clear fetching and restore campaign", WPeMatico :: TEXTDOMAIN)) . '">' .  __('Clear campaign', WPeMatico :: TEXTDOMAIN) . '</a>';
			}
		}
		return $actions;
	}


	function wpematico_updated_messages( $messages ) {
	  global $post, $post_ID;
	  $messages['wpematico'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Campaign updated.', WPeMatico :: TEXTDOMAIN)),
		2 => __('Custom field updated.', WPeMatico :: TEXTDOMAIN) ,
		3 => __('Custom field deleted.', WPeMatico :: TEXTDOMAIN),
		4 => __('Campaign updated.', WPeMatico :: TEXTDOMAIN),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Campaign restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Campaign published.', WPeMatico :: TEXTDOMAIN)),
		7 => __('Campaign saved.'),
		8 => sprintf( __('Campaign submitted.', WPeMatico :: TEXTDOMAIN)),
		9 => sprintf( __('Campaign scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview campaign</a>'),
		  // translators: Publish box date format, see http://php.net/date
		  date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Campaign draft updated. <a target="_blank" href="%s">Preview campaign</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	  );

	  return $messages;
	}


	function set_edit_wpematico_columns($columns) { //this function display the columns headings
		return array(
			'cb' => '<input type="checkbox" />',
			'title' => __('Campaign Name'),
			'status' => __('Status'),
			'count' => __('Posts'),
			'next' => __('Next Run'),
			'last' =>__('Last Run')
		);
	}
	
	function custom_wpematico_column( $column, $post_id ) {
		$campaign_data = WPeMatico :: get_campaign ( $post_id );

		switch ( $column ) {
		  case 'status':
			echo $campaign_data['campaign_posttype']; 
			break;
		  case 'count':
			echo $campaign_data['postscount']; 
			break;
		  case 'next':
			$starttime = @$campaign_data['starttime']; 
			$cronnextrun = $campaign_data['cronnextrun']; 
			//print_r($campaign_data);
			$activated = $campaign_data['activated']; 
			if ($starttime>0) {
				$runtime=current_time('timestamp')-$starttime;
				echo __('Running since:', WPeMatico :: TEXTDOMAIN ).' '.$runtime.' '.__('sec.', WPeMatico :: TEXTDOMAIN );
			} elseif ($activated) {
				echo date_i18n(get_option('date_format'),$cronnextrun).'-'. date_i18n(get_option('time_format'),$cronnextrun);
			} else {
				echo __('Inactive', WPeMatico :: TEXTDOMAIN );
			}
			break;
		  case 'last':
			$lastrun = @$campaign_data['lastrun']; 
			$lastruntime = @$campaign_data['lastruntime']; 
			if ($lastrun) {
				echo date_i18n(get_option('date_format'),$lastrun).'-'. date_i18n(get_option('time_format'),$lastrun); 
				if (isset($lastruntime))
					echo '<br />'.__('Runtime:', WPeMatico :: TEXTDOMAIN ).' '.$lastruntime.' '.__('sec.', WPeMatico :: TEXTDOMAIN );
			} else {
				echo __('None', WPeMatico :: TEXTDOMAIN );
			}
			break;
		}
	}

	// Make these columns sortable
	function sortable_columns() {
	  return array(
		'title'      => 'title',
		'status' => 'Status',
		'count'     => 'count',
		'next'     => 'next',
		'last'     => 'last'
	  );
	}
	
}
?>