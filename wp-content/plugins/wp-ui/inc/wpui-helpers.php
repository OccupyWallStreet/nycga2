<?php
/**
 *	WP UI Helper functions
 *	
 *		
 * @since $Id$
 * @package wp-ui
 * @subpackage wpui-helper
 **/

/**
 * Get the list of jQuery UI themes.
 */
function wpui_get_jqui_themes_list()
{
	return array(
		'ui-lightness', 'ui-darkness', 'smoothness', 'start', 'redmond',	
		'sunny', 'overcast', 'le-frog',	'flick', 'pepper-grinder', 'eggplant',
		'dark-hive', 'cupertino', 'south-street', 'blitzer', 'humanity',
		'hot-sneaks', 'excite-bike', 'vader', 'dot-luv', 'mint-choc',
		'black-tie', 'trontastic', 'swanky-purse'	
	);	
} // END function wpui_get_jqui_themes_list
	
/**
 * Get the list of CSS3 styles.
 */
function wpui_get_css3_styles_list()
{
	return array( 
		'wpui-light', 'wpui-blue', 'wpui-red', 'wpui-green', 'wpui-dark', 'wpui-quark',
		'wpui-cyaat9', 'wpui-android', 'wpui-safle', 'wpui-alma', 'wpui-macish',
		'wpui-achu', 'wpui-redmond', 'wpui-sevin', 'wpui-gene', 'wpui-narrow'
	);
} // END function wpui_get_css3_styles_list



/**
 * Is it windows?
 */
function wpui_is_windows( ) {
	return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
}

function wpui_adjust_path( $path ) {
	if ( wpui_is_windows() )
		$path = str_ireplace( '/', '\\', $path ); 
	return $path;		
}


// function wpui_dir( $app='' ) {
// 	$is_Windows = strtoupper( substr(php_uname('s'), 0, 3 )) == 'WIN';
// 	$sep = $is_Windows ? '\\' : '/'; 
// 	$path = WP_PLUGIN_DIR . $sep . basename( dirname( __FILE__ ) );
// 	if ( $is_Windows && stristr( $app, '/' ) )
// 	$app = str_replace('/', "\\", $app );
// 	if ( $app != '' ) $path = $path . $sep . $app;
// 	return $path;
// }



/**
 *  Returns the WP UI directory path. 
 * 	
 */
function wpui_dir( $app='' ) {
	$path = str_replace( basename(dirname( __FILE__ )), '', dirname( __FILE__ )  );
	if ( $app != '' ) $path = $path . $app;
	if ( wpui_is_windows( ) ) 
		$path = wpui_adjust_path( $path );
	return $path;
}


/**
 * Plugin directory URL
 * 	Trailing slash? OH Yeah.
 */
function wpui_url( $append='' ) {
	return plugins_url( '/wp-ui/' . $append );
}



/**
 * Check for custom themes, enqueue of list them if any.
 */
function wpui_get_custom_themes_list( $values=false ) {
	$opts = get_option( 'wpUI_options' );
	$themes_list = array();
	
	if ( $opts[ 'jqui_custom_themes' ] == '' )
		return false;
	if ( $opts[ 'jqui_custom_themes' ] == '{}' )
		return $themes_list;
	 
	$cust_themes = json_decode( $opts[ 'jqui_custom_themes' ], true );
	if ( $cust_themes !== null ) {
		$returnArray = ( ! $values ) ? array_keys( $cust_themes ) : $cust_themes;
		return $returnArray;
	}	
} // END wpui



add_action('wp_ajax_WPUIstyles', 'choose_wpui_style');

function choose_wpui_style() {
	echo wpui_get_file( wpui_url( 'js/wpui-choosestyles.php' )  );
	die();
}


add_action('wp_ajax_JQUIstyles', 'choose_jqui_style');

function choose_jqui_style() {
	echo wpui_get_file( wpui_url( 'js/wpui-choose-jquistyles.php' ) );
	die();
}


add_action('wp_ajax_editorButtonsHelp', 'editor_buttons_help');

function editor_buttons_help() {
	echo wpui_get_file( wpui_url( 'admin/wpui-help.php' ) );
	die();	
}



add_action('wp_ajax_wpui_clean_cache', 'wpui_clean_cache_images_scripts');
/**
 *	Clean the cache. I mean empty it.
 */
function wpui_clean_cache_images_scripts() 
{
	$upload_dir = wp_upload_dir();
		
	$cudir = wpui_adjust_path(preg_replace( '/(\d){4}\/(\d){2}/i' , '' , $upload_dir['path'] ) . 'wp-ui/cache');
	
	$upnonce = $_POST['Cnonce'];

	if ( ! wp_verify_nonce( $upnonce, 'wpui-cache-nonce' ) )
		return false;
		
	// if ( ! is_dir( $cudir ) ) {
	// 	echo "Cache directory, was not found in the following location. <pre>" . $cudir . '</pre>';
	// } else {
		try {
			system( '/bin/rm -rf ' . escapeshellarg( $cudir ) );
			// mkdir( $cudir, 0755 );
			if (!@mkdir($cudir, 0755)) {
			    $error = error_get_last();
			    echo "Error - <code>" . $error['message'] . '</code>. Try creating the directory manually with permissions ( 755 ).';
			} else {
				echo "Cache files were cleaned successfully.";
				
			}
			
		} catch( Exception $e ) {
			// echo "Error : " . $e->getMessage() . "\n";
			die ();
		}
	// }
	

	die();
} // END wpui_clean_cache_images_scripts


add_action('wp_ajax_wpui_clean_postmeta', 'wpui_clean_post_meta_fix_for_wpscetc');
/**
 *	Clean the old post meta last version. Removed in the next two
 */
function wpui_clean_post_meta_fix_for_wpscetc() 
{
	$posts = get_posts('numberposts=-1&post_type=post&post_status=any');
	foreach( $posts as $post ) {
		delete_post_meta( $post->ID, 'wp-ui-load' );
		delete_post_meta( $post->ID, '_wp-ui-load' );
	}
	echo "Old Post meta information found were removed.";
	die();
} // END wpui_clean_post_meta_fix_for_wpscetc




function wpui_exception( $errnum, $errstr, $errfile, $errline ) {
	throw new ErrorException( $errstr, 0, $errnum, $errfile, $errline );
}

add_action('wp_ajax_jqui_css', 'wpui_search_for_stylesheets');
/**
 *	Documentation
 */
function wpui_search_for_stylesheets() 
{
	$upload_dir = wp_upload_dir();
		
	$udir = wpui_adjust_path( WP_CONTENT_DIR . '/uploads/wp-ui/' );
	
	$_status = '';
	
	$someArr = false;
	
	file_exists( $udir ) || @mkdir( $udir, 0755 );
	
	if ( ! is_dir( $udir ) ) {
		$someArr = array();
		$someArr[ 'status' ] = 'error';
		$someArr[ 'description' ] = __( 'The folder wp-ui was not found and could not be created. Please check the uploads folder permissions.', 'wp-ui' );
		$someArr[ 'link' ] = $udir;
		echo json_encode( $someArr );
		die();
	}
	
	$upnonce = $_POST['upNonce'];

	if ( ! wp_verify_nonce( $upnonce, 'wpui-jqui-custom-themes' ) )
		wp_die( 'Just what do you think you\'re doing, Dave?' );

	$results = wpui_jqui_dirs( $udir );
	echo $results;

	die();
} // END wpui_search_for_stylesheets



// Helper function. uses wp_remote_get()
function wpui_get_file( $url, $args=array(), $output='return' ) {
	$response = wp_remote_get( $url, $args );
	if ( is_wp_error( $response ) ) {
		echo '<pre>';
		var_export($response);
		echo '</pre>';
		echo __( 'Failed to load File. Double check URL or maybe the installation didn\'t go well?', 'wp-ui' );
	} else {
		if ( $response[ 'body' ] ) {
			if ( $output == 'echo' ) echo $response[ 'body' ];
			else return $response[ 'body' ];
		} 
	}	
}




add_action('wp_ajax_wpui_query_posts', 'wpui_query_posts');
/**
 *	Get the posts for display on the editor screen.
 */
function wpui_query_posts() 
{
	$pts = get_post_types( array( 'public' => true ), 'objects' );
	$pt_names = array_keys( $pts );
	$args = array();
	
	$nonce = $_POST[ '_ajax_post_nonce' ];
	if ( ! wp_verify_nonce( $nonce, 'wpui-editor-post-nonce' ) )
		return;	
	else
		$retStr = '<!--secure-->';
	
	if ( isset( $_POST[ 'search' ] ) ) 
		$args[ 's' ] = stripslashes( $_POST[ 'search' ] );

	$args[ 'posts_per_page' ] = ( isset( $_POST[ 'number' ] ) ) ? $_POST[ 'number' ] : 5;

	if ( isset( $_POST[ 'page' ] ) && isset( $_POST[ 'number' ] ) && (! isset( $_POST[ 'search' ] ) || $_POST[ 'search' ] == '' ) ) 
		 $args[ 'offset' ] = $_POST['page'] > 1 ? ($_POST['number'] * $_POST['page']) : 0;
		
	$args['post_type'] = 'any';
	
	$wpui_posts = get_posts( $args );

	foreach( $wpui_posts as $post ) {
		$title = ( $post->post_title != '' ) ? $post->post_title : 'Untitled Post';
		$retStr .= '<li>';
		$retStr .= '<a href="#" title="' . $title .  '" rel="post-' . $post->ID  . '">' . $post->post_title . '</a><span class="info">';
		if ( 'post' == $post->post_type ) {
		$retStr .= mysql2date( __( 'Y/m/d' ), $post->post_date );
		} else {
		$retStr .= $pts[ $post->post_type ]->labels->singular_name;			
		}
		$retStr .= '</span></li>';
		
		
	}
	
	echo $retStr;
	die();
} // END wpui_query_posts




add_action('wp_ajax_wpui_query_meta', 'wpui_query_meta');
/**
 *	Query categories and tags
 */
function wpui_query_meta() 
{
	$type = ( ! isset( $_POST['type'] ) ) ? 'cat' : $_POST['type' ];
	$sear = ( isset( $_POST['search'] ) ) ? $_POST[ 'search' ] : false;

	$nonce = $_POST[ '_ajax_tax_nonce' ];
	if ( ! wp_verify_nonce( $nonce, 'wpui-editor-tax-nonce' ) )
		return;
	else
		$retStr = '<!--secure-->';
			
	// $retStr = '';
	if ( $type == 'cat' ) {
		$getArr = get_categories();
		foreach( $getArr as $get ) {
			if ( $sear && ( strpos( $get->category_nicename, $sear ) === FALSE ) ) continue;
			$retStr .= '<li><a href="#" title="Select the category ' . $get->category_nicename . '" rel="cat-' . $get->term_id . '">' . $get->category_nicename . '</a></li>';		
		}
	
	} elseif ( $type == 'tag' ) {
		$getArr = get_tags();
		foreach( $getArr as $get ) {
			if ( $sear && ( strpos( $get->name, $sear ) === FALSE ) ) continue;
			$retStr .= '<li><a href="#" title="select the tag ' . $get->name . '" rel="tag-' . $get->term_id . '">' . $get->name . '</a></li>';		
		}			
		
	} elseif ( $type == 'recent' ) {
		$getArr = get_posts( array( "posts_per_page" => 5 ) );
		$retStr .= '<li class="no-select"><strong>Recent posts - Click insert button to continue.</strong></li>';
		foreach( $getArr as $get ) {
			$retStr .= '<li class="no-select"><a href="#">' . $get->post_title . '</a></li>';
		}
	} elseif ( $type == 'popular' ) {
		$getArr = get_posts( array( 'orderby' => 'comment_count' ) );
		$retStr .= '<li class="no-select"><strong>Popular posts - Click insert button to continue.</strong></li>';
		foreach( $getArr as $get ) {
			$retStr .= '<li class="no-select"><a href="#">' . $get->post_title . '</a></li>';
		}		
	} elseif ( $type == 'random' ) {
		$getArr = get_posts( array( 'orderby' => 'rand' , 'posts_per_page' => 5 ) );
		$retStr .= '<li class="no-select"><strong>Random posts - Click insert button to continue.</strong></li>';
		foreach( $getArr as $get ) {
			$retStr .= '<li class="no-select"><a href="#">' . $get->post_title . '</a></li>';
		}		
	}
	
	echo $retStr;
	die();
} // END wpui_query_meta


add_action( 'wp_ajax_wpui_validate_feed', 'wpui_validate_feed' ); 
/**
 * Check if the given feed exists.
 */
function wpui_validate_feed() {
	$options = get_option( 'wpui_options' );
	if ( ! isset( $_POST ) ) die( "-1" );
	
	$data = array();
	
	if ( ! isset( $_POST[ 'feed_url' ] ) ) { 
		$data['status'] = 'error';
		$data['description'] = "Parameter feed_url required.";
		echo json_encode( $data );
		die( "-1" );
	}
	$feedURL = $_POST[ 'feed_url' ];
	$getFeed = @fetch_feed( $feedURL );
	
	if ( is_wp_error( $getFeed ) ) {
		$data[ 'status' ] = 'error';
		$data[ 'description' ] = 'Not a valid Feed URL';
		echo json_encode( $data );
	} else {
		$data['status'] = 'success';
		$data[ 'description'] = 'This is valid RSS 2.0 Feed.';
		echo json_encode( $data );
	}	
	die();
} // END function wpui_validate_feed




/**
 * Get the theme options.
 */
function get_wpui_option( $value ) {
	$options = get_option( 'wpUI_options' );
	
	if ( isset( $options[$value] ) )
		return $options[$value];
	else 
		return false;	
} // END FUNCTION get_wpui_option.



/**
 * 	Set options via AJAX.
 */
add_action( 'wp_ajax_wpui_setopts', 'set_wpui_option' );

function set_wpui_option( $value ) {
	$options = get_option( 'wpUI_options' );
	
	$nonce = $_POST[ 'nonce' ];
	
	if ( ! isset( $_POST[ 'option' ] ) || ! wp_verify_nonce( $nonce, 'wpui-setopts-nonce' ) )
		die( "-1" ); 
	
	if ( ! isset( $_POST[ 'option' ] ) || ! current_user_can( 'manage_options' ) ) die( '-1' ); 
	
	$ajaxOpts = $_POST[ 'option' ];
	
	foreach( $ajaxOpts as $key=>$value ) {
		echo 'Option "' . $key . '" set to ' . $value;
		$options[ $key ] = $value;
	}
	update_option( 'wpUI_options', $options );
	
	die();
}


/**
 * 	Get the css3 styles.
 */
add_action('wp_ajax_selectstyles_list', 'wpui_selectstyles_list');

function wpui_selectstyles_list() {
	$results = wpui_get_css3_styles_list();
	
	if ( is_array( $results ) ) {
		echo json_encode( $results );
	} else {
		_e( '404', 'wp-ui' );
	}	
	die();
}



/**
 * 	WP UI get skins list.
 */
function wpui_get_skins_list() {
	
	$wpui_skins = array(
		'startoptgroup1'=>	'WP UI CSS3 Themes',
	);

	// $css3_list = 
	
	$css3_list = wpui_get_css3_styles_list();
	
	foreach ( $css3_list as $list=>$css ) {
		$wpui_skins[ $css ] = ucwords( str_ireplace( '-', ' ', $css ) );
	}	
	

	$wpui_skins['endoptgroup1'] = '';
	
	$wpui_skins[ 'startoptgroup2'] = 'jQuery UI Themes';


	$jq_list = wpui_get_jqui_themes_list();

	foreach ( $jq_list as $list=>$jq ) {
		$wpui_skins[ $jq ] = ucwords( str_ireplace( '-', ' ', $jq ) );
	}
	
	$wpui_skins['endoptgroup2'] = '';
	
	return apply_filters( 'wpui_get_skins_list', $wpui_skins );
} // END function wpui_get_skins_list



add_filter( 'wpui_get_skins_list', 'wpui_add_custom_skins' );

function wpui_add_custom_skins( $skins ) {
	$opts = get_option( 'wpUI_options' );
	if ( ! $skins || ! is_array( $skins ) || ! isset( $opts ) ) return $skins;
	
	if ( ! empty( $opts[ 'jqui_custom_themes' ] ) ) {
		$customs = json_decode( $opts[ 'jqui_custom_themes'], true );
		if ( ! is_array( $customs ) || empty( $customs ) ) return $skins;
		$customs = array_keys( $customs );
		$skins[ 'startoptgroup3' ] = __( 'Custom themes', 'wp-ui' );
		foreach ( $customs as $key=>$value ) {
			$dName = ucwords( str_ireplace( '-', ' ', $value ) );
			$skins[ $value ] = $dName;
		}
		$skins = array_merge( $skins, apply_filters( 'wpui_custom_skins', array() ) );
		$skins[ 'endoptgroup3'] = '';	
	} // end isset for custom themes.	
	
	return $skins;
}



/**
 * Is the wordpress version less than 3.3? Really? Is this necessary? Uh.
 */
function wpui_less_33( $version='3.3' ) {
	$version = floatval( $version );
	return ( floatval( get_bloginfo( 'version' ) ) < $version );
}


add_action('admin_notices', 'add_wpui_update_notification');
/**
 *	Notify the user to verify the options after update.
 */
function add_wpui_update_notification( $output ) 
{
	$opts = get_option( 'wpUI_options' );
	if ( isset( $opts[ 'version'] ) ) return;
	global $pagenow;
	echo '<div class="error update-nag"><p>';
	
	if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wpUI-options' ) {
		echo sprintf( __( 'Options need a manual save. Please click %1$sSave options%2$s to do so.', 'wp-ui' ), '<b>', '</b>' );
	} else {
		echo sprintf( __( '%1$sImportant : WP UI Options%2$s tables structure has been changed and requires a manual save. Please take a moment to %3$sVisit Options page%4$s', 'wp-ui' ), '<b>', '</b>', '<a href="' . admin_url( 'options-general.php?page=wpUI-options' ) . '">', '</a>' );
		
/*	   echo '<p>Important : <b>WP UI</b> Option tables structure has been changed and requires a manual save. <a href="' . admin_url( 'options-general.php?page=wpUI-options' ) . '">Visit Options page</a></p>';*/
	}
	echo '</p></div>';
} // END add_wpui_update_notification




function wpui_jqui_dirs( $dir, $format='array' ) {

	$valid = array();
	$is_Windows = strtoupper( substr(php_uname('s'), 0, 3 )) == 'WIN';
	
	$someArr = array();
	
	$ndir = ( $is_Windows ) ? str_ireplace( '/', '\\', $dir ) : $dir;
		
	if ( ! is_dir( $dir ) ){
		$someArr[ 'status' ] = 'error';
		$someArr[ 'description' ] = __( 'No directory found.', 'wp-ui' );
		$someArr[ 'link' ] = $ndir;
		return json_encode( $someArr );
	}	
	
	try {
		$it = new DirectoryIterator( $dir );
	} catch ( Exception $e ) {
		$someArr[ 'status' ] = 'error';
		$someArr[ 'description' ] = $e->getMessage();
		$someArr[ 'link' ] = $ndir;
		return json_encode( $someArr );
	}
	
	$abspath = ABSPATH;
	$i = 0;
	foreach( $it as $fi ) {
		if ( $fi->isDir() &&
		 	! $fi->isDot() )
		  {
	$itt = new DirectoryIterator( $fi->getPathname() );
		foreach( $itt as $fii ) {
				if ( $fii->isFile() ) {
					if( 'css' == substr( $fii->getFilename() , -3 ) ) {
						$valid[ $fi->getBasename() ] = $fii->getPathName();
						$i++;
					}
				}
			}
			$i++;
		}
	}	ksort( $valid );
	foreach( $valid as $key=>$value ) {
		if ( $is_Windows ) $abspath = str_ireplace( '/', '\\', ABSPATH );
		$valURL = str_ireplace( $abspath, '', $value );
		$valURL = ( $is_Windows ) ? str_ireplace( '\\', '/', $valURL ) : $valURL;
		$valid[ $key ] = get_bloginfo('wpurl') . '/' . $valURL;
		
	}

	if ( empty( $valid ) ) {
		$someArr[ 'status' ] = 'error';
		$someArr[ 'description' ] = __( 'No themes found inside folder.', 'wp-ui' );
		$someArr[ 'link' ] = $ndir;
	} else {
		$someArr[ 'status' ] = 'success';
		$someArr[ 'links' ] = $valid;
	}
	return json_encode( $someArr );	
	// if ( $format == 'array' ) {
	// 	return $valid;
	// } else {
	// }		
} // END update CSS dirs.



add_shortcode('wimg', 'wpui_img_shortcode' );

function wpui_img_shortcode( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'title'		=>	false,
	), $atts ) );
	
	if ( ! $title ) return false;

	$img = wpui_get_media_item( $title );
	
	if ( ! is_array( $img ) ) return false;	
	
	$output = '<img src="' . $img[ 'image' ] . '" title="' . $img[ 'title' ] . '">';

	return $output;

} // 


function wpui_get_media_item( $title ) {

	$get_media_items = array(
	 	'post_type' => 'attachment',
	 	'post_mime_type' =>'image',
		'post_status' => 'inherit',
		'posts_per_page' => -1,
	);

	$output = array(
			'image'		=> '',
			'title'		=>	'',
			'size'		=>	array()
			
			);

	$media_items = new WP_Query( $get_media_items );
	$images = array();
	$i = 0;
	
	foreach ( $media_items->posts as $image ) {
		if ( $i > 0 ) break;
		if ( $image->post_title != $title ) continue;
		else {
			$output[ 'image' ] .= $image->guid;
			$output[ 'title' ] .= $image->post_title;
			break;
		}
		$i++;
	}	
	
	return $output;	
} // function get_media_item.






?>