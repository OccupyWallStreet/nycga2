<?php

// ==============================
// = PageLines Function Library =
// ==============================

/**
 *  Determines if on a foreign integration page
 *
 * @since 2.0.0
 */
function pl_is_integration(){
	global $pl_integration;
	
	return (isset($pl_integration) && $pl_integration) ? true : false;
}

/**
 *  returns the integration slug if viewing an integration page
 *
 * @since 2.0.0
 */
function pl_get_integration(){
	global $pl_integration;
	
	return (isset($pl_integration) && $pl_integration) ? sprintf('%s', $pl_integration) : false;
}

/**
 *  Determines if this page is showing several posts.
 *
 * @since 2.0.0
 */
function pagelines_is_posts_page(){	
	if(is_home() || is_search() || is_archive() || is_category() || is_tag()) return true; 
	else return false;
}


/**
 *
 * @TODO document
 *
 */
function pagelines_non_meta_data_page(){
	if(pagelines_is_posts_page() || is_404()) return true; 
	else return false;
}


/**
 *
 * @TODO document
 *
 */
function is_pagelines_special($args = array()){
	if(is_404() || is_home() || is_author() || is_search() || is_archive() || is_category() || is_tag() ) 
		return true; 
	elseif( isset($args['type']) && (
				$args['type'] == 'posts'
				|| $args['type'] == 'archive'
				|| $args['type'] == 'category'
				|| $args['type'] == 'search'
				|| $args['type'] == 'tag'
				|| $args['type'] == 'author'
				|| $args['type'] == '404_page'
			))
		return true;
	elseif(pl_is_integration())
		return true;
	else 
		return false;
}



/**
 *
 * @TODO document
 *
 */
function pagelines_special_pages(){
	return array('posts', 'search', 'archive', 'tag', 'category', '404');
}


/**
 *
 * @TODO document
 *
 */
function pl_meta_set_url( $tab = null ){
	
	global $post; 
	
	$tab = (isset($tab)) ? '#'.$tab : '';
	
	$url = (is_pagelines_special()) ? admin_url(PL_SPECIAL_OPTS_URL) : get_edit_post_link( $post->ID );
		
	return $url.$tab;
}

/**
 * PageLines Body Classes
 * 
 * Sets up classes for controlling design and layout and is used on the body tag
 *
 * @package     PageLines Framework
 * @subpackage  Functions Library
 *
 * @since       1.1.0
 *
 * @link        http://www.pagelines.com/wiki/Pagelines_body_classes
 *
 * @uses        ploption
 * @uses        CHILDTHEMENAME (constant)
 *
 * @return      string $body_classes - PageLines default body classes
 */
function pagelines_body_classes(){
	
	global $pagelines_template;
	
	$special_body_class = (ploption('special_body_class')) ? ploption('special_body_class') : '';
	
	$canvas_shadow = (ploption('canvas_shadow')) ? 'content-shadow' : '';
	
	$responsive = (ploption('layout_handling') == 'pixels' || ploption('layout_handling') == 'percent') ? 'responsive' : 'static';
	
	$design_mode = (ploption('site_design_mode') && !pl_is_disabled('color_control')) ? ploption('site_design_mode') : 'full_width';

	$body_classes = sprintf(
		'custom %s %s %s %s %s %s', 
		$canvas_shadow, 
		$responsive, 
		strtolower(CHILDTHEMENAME), 
		$pagelines_template->template_type, 
		$design_mode, 
		$special_body_class
	);
	
	global $pagelines_addclasses;
	
	if ( isset( $pagelines_addclasses ) )
		$body_classes .= sprintf( ' %s', $pagelines_addclasses ); 
	
	return $body_classes;
}


/**
 *
 * @TODO document
 *
 */
function pagelines_add_bodyclass( $class ) {
	
	global $pagelines_addclasses;
	
	if ( !isset( $pagelines_addclasses ) )
		$pagelines_addclasses = '';
	
	if ( isset( $class ) )
		$pagelines_addclasses .= sprintf( ' %s', $class ); 
	
}

/**
 * 
 *  Sets up global post ID and $post global for handling, reference and consistency
 *
 *  @package PageLines Framework
 *  @subpackage Functions Library
 *  @since 1.0.0
 *
 */
function pagelines_id_setup(){
	global $post;
	global $pagelines_ID;
	global $pagelines_post;

	if(isset($post) && is_object($post)){
		$pagelines_ID = $post->ID;
		$pagelines_post = $post;	
	}
	else {
		$pagelines_post = '';
		$pagelines_ID = '';
	}
	
}

/**
 * PageLines Register Hook
 *
 * Stores for reference or use elsewhere.
 *
 * @package     PageLines Framework
 * @subpackage  Functions Library
 *
 * @since       1.3.3
 *
 * @link        http://www.pagelines.com/wiki/Pagelines_register_hook
 *
 * @param       $hook_name
 * @param       null $hook_area_id
 */
function pagelines_register_hook( $hook_name, $hook_area_id = null){

	/** Do The Hook	*/
	do_action( $hook_name, $hook_name, $hook_area_id);
		
}

/**
 * PageLines Template Area
 *
 * Does hooks for template areas
 *
 * @package     PageLines Framework
 * @subpackage  Functions Library
 *
 * @since       1.3.3
 *
 * @link        http://www.pagelines.com/wiki/Pagelines_template_area
 *
 * @param       $hook_name
 * @param       null $hook_area_id
 */
function pagelines_template_area( $hook_name, $hook_area_id = null){

	/** Do The Hook	*/
	do_action( $hook_name, $hook_area_id);

}

/**
 * Check Authority
 *
 * Check the authentication level for administrator status (security)
 *
 * @package     PageLines Framework
 * @subpackage  Functions Library
 * @since       1.x.x
 */
function checkauthority(){
	if (!current_user_can('edit_themes'))
	wp_die( __( 'Sorry, but you don&#8217;t have the administrative privileges needed to do this.', 'pagelines' ) );
}

/**
 * IE Version
 *
 * Checks for IE and Returns Version
 *
 * @package     PageLines Framework
 * @subpackage  Functions Library
 * @since       1.0.0
 *
 * @return      bool|float
 */
function ie_version() {
  $match=preg_match('/MSIE ([0-9]\.[0-9])/',$_SERVER['HTTP_USER_AGENT'],$reg);
  if($match==0)
    return false;
  else
    return floatval($reg[1]);
}

/**
 * PageLines ShortURL
 *
 * Gets a 'tiny' url. Returns URL if response is not 200
 *
 * @package     PageLines Framework
 * @subpackage  Functions Library
 * @since       1.5.0
 *
 * @uses        pagelines_option
 * @uses        pagelines_format_tweet
 *
 * @internal    uses filter 'pagelines_shorturl_provider'
 * @internal    uses filter 'pagelines_shorturl_cachetimeout'
 *
 * @param       $url
 * @param       int $timeout
 *
 * @return      string
 */
function pagelines_shorturl( $url, $timeout = 86400 ) {

	global $post;
	if ( !pagelines_option( 'share_twitter_cache' ) )
		return pagelines_format_tweet( get_the_title(), $url );

	$provider = 'http://pln.so/api.php?action=shorturl&format=json&url=';

	// If cache exists send it back
	$cache = get_transient( 'pagelines_shorturl_cache' );
	if ( is_array( $cache) && array_key_exists( md5($url), $cache ) ) {
		return pagelines_format_tweet ( get_the_title(), $cache[md5($url)] );
	}

	// Fetch the short url from the api
	$response = wp_remote_get(  apply_filters( 'pagelines_shorturl_provider' , $provider ) . $url );

	if( is_wp_error( $response ) ) return pagelines_format_tweet( get_the_title(), $url ); // return original url if there is an error

	// Check the body from the api is actually a url and not a 400 error
	// If its OK we will cache it and return it, othwise return original url
	
	$out = ( $response['response']['code'] == 200 ) ? $response['body'] : false; 
	if ( !is_object( $out = json_decode( $out ) ) ) return pagelines_format_tweet( get_the_title(), $url );

	if ( $cache == false ) {
		unset( $cache );
		$cache = array();
	}
	delete_transient( 'pagelines_shorturl_cache' );
	$cache = array_merge( $cache, array( md5($url) => $out->shorturl ) );
	set_transient( 'pagelines_shorturl_cache', $cache, apply_filters( 'pagelines_shorturl_cachetimeout', $timeout ) );
	return pagelines_format_tweet( get_the_title(), $out->shorturl );
}


/**
 * PageLines Format Tweet
 *
 * @param   $title
 * @param   $url
 *
 * @return  string
 */
function pagelines_format_tweet( $title, $url ) {

	return sprintf( '%1$s - %2$s', $title, $url );
}

/**
 * PageLines Background Cascade
 *
 * Sets background cascade for use in color mixing - default: White
 *
 * @since       2.0.b6
 *
 * @uses        ploption
 * @internal    uses filter background_cascade
 *
 * @return      mixed|void
 */
function pl_background_cascade(){
	
	$cascade = array(
		ploption('contentbg'),
		ploption('pagebg'),
		ploption('bodybg'),
		'#ffffff'
	);
	
	return apply_filters('background_cascade', $cascade);
}

/**
 * PageLines Body Background
 * 
 * Body Background - default: White
 *
 * @uses        ploption
 * @internal    uses filter body_bg
 *
 * @since       2.0.b6
 *
 * @return      mixed|void
 */
function pl_body_bg(){
	
	$cascade = array( ploption('bodybg'), '#ffffff' );
	
	return apply_filters('body_bg', $cascade);
}


/**
 * PageLines Strip
 *
 * Strips White Space
 *
 * @since   2.0.b13
 *
 * @param   string $t - input string
 * @return  mixed
 */
function plstrip( $t ){	
	
	if( is_pl_debug() )
		return $t;

	return preg_replace( '/\s+/', ' ', $t );
}


/**
 * Show Posts Nave
 *
 * Checks to see if there is more than one page for nav.
 *
 * @since 1.0.0
 *
 * @return bool
 * @TODO does this add a query?
 */
function show_posts_nav() {
	global $wp_query;
	return ($wp_query->max_num_pages > 1);
}


/**
 * PageLines bbPress Forum
 *
 * Checks for a bbPress installation by pulling a global identifier from bbPress
 *
 * @since 3.x.x
 *
 * @return bool
 */
function pagelines_bbpress_forum(){
	global $bbpress_forum;
	if($bbpress_forum ){
		return true;
	} else return false;
}


/**
 * Displays query information in footer (For testing - NOT FOR PRODUCTION)
 * @since 4.0.0
 */
function show_query_analysis(){
	if (current_user_can('administrator')){
	    global $wpdb;
	    echo '<pre>';
	    print_r($wpdb->queries);
	    echo '</pre>';
	}
}


/**
 * Custom Trim Excerpt
 *
 * Returns the excerpt at a user-defined length
 *
 * @param   $text - input text
 * @param   $length - number of words
 *
 * @return  string - concatenated with an ellipsis
 */
function custom_trim_excerpt($text, $length) {
	
	$text = strip_shortcodes( $text ); // optional
	$text = strip_tags($text);
	
	$words = explode(' ', $text, $length + 1);
	if ( count($words) > $length) {
		array_pop($words);
		$text = implode(' ', $words);
	}
	return $text.'&nbsp;<span class="hellip">[&hellip;]</span>';
}
	




/**
 *
 * @TODO document
 *
 */
function pagelines_add_page($file, $name){
	global $pagelines_user_pages;
	
	$pagelines_user_pages[$file] = array('name' => $name);
	
}

/**
 * Used for Callback calls, returns nothing
 * 
 * @since 1.0.0
 */
function pagelines_setup_menu() {
	echo __( 'Add links using WordPress menus in your site admin.', 'pagelines' );
}

/**
 * Setup PageLines Template
 *
 * Includes the loading template that sets up all PageLines templates
 * 
 * @since   1.1.0
 *
 * @link    http://www.pagelines.com/wiki/Setup_pagelines_template
 *
 * @uses    pagelines_template_area
 */
function setup_pagelines_template() {
	
	
	// if not true, then a non pagelines template is being rendered (wrap with .content)
	$GLOBALS['pagelines_render'] = true;
	
	get_header();

	if(!has_action('override_pagelines_body_output'))
		pagelines_template_area('pagelines_template', 'templates');
	
	get_footer();
}


/**
 * PageLines Add Page Callback
 *
 * Adds pages from the child theme.
 *
 * @since   1.1.0
 *
 * @param   $page_array
 * @param   $template_area
 *
 * @return  array
 */
function pagelines_add_page_callback( $page_array, $template_area ){
	global $pagelines_user_pages;
	
	if( is_array($pagelines_user_pages) ){
		foreach($pagelines_user_pages as $file => $pageinfo){
			$page_array[$file] = array('name'=>$pageinfo['name']);
		}
	}
	
	return $page_array;
}

/**
 * Overrides default excerpt handling so we have more control
 * 
 * @since 1.2.4
 */
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'improved_trim_excerpt');

/**
 *
 * @TODO document
 *
 */
function improved_trim_excerpt($text) {
	
	// Group options at top :)
	$allowed_tags = (ploption('excerpt_tags')) ? ploption('excerpt_tags') : '';
	$excerpt_len = (ploption('excerpt_len')) ? ploption('excerpt_len') : 55;
	
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');

		$text = strip_shortcodes( $text );


		$text = apply_filters('the_content', $text);
		
		$text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text); // PageLines - Strip JS
		
		$text = str_replace(']]>', ']]&gt;', $text);
		
		$text = strip_tags($text, $allowed_tags); // PageLines - allow more tags
		

		$excerpt_length = apply_filters('excerpt_length', $excerpt_len );
		$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
		
		$words = preg_split('/[\n\r\t ]+/', $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		
		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $excerpt_more;
		} else
			$text = implode(' ', $words);
			
	}
	return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}

/**
 * Generates an really short excerpt from the content or postid for tweets, facebook etc
 *
 * @param int|object $post_or_id can be the post ID, or the actual $post object itself
 * @param int $words the amount of words to allow
 * @param string $excerpt_more the text that is applied to the end of the excerpt if we algorithically snip it
 * @return string the snipped excerpt or the manual excerpt if it exists         
 */
function pl_short_excerpt($post_or_id, $words = 10, $excerpt_more = ' [...]') {

	if ( is_object( $post_or_id ) )
		$postObj = $post_or_id;
	else $postObj = get_post($post_or_id);

	$raw_excerpt = $text = $postObj->post_excerpt;
	if ( '' == $text ) {
		$text = $postObj->post_content;

		$text = strip_shortcodes( $text );

		$text = sanitize_text_field( $text );

		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		$excerpt_length = $words;

		$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $excerpt_more;
		} else {
			$text = implode(' ', $words);
		}
	}
	return $text;
}


/**
 * PageLines Nav Classes
 * 
 * Returns nav menu class `sf-menu` which will allow the "superfish" JavaScript to work
 *
 * @package     PageLines Framework
 * @subpackage  Functions Library
 * @since       1.1.0
 *
 * @internal    see ..\sections\nav\script.superfish.js
 * @internal    see ..\sections\nav\style.superfish.css
 *
 * @return      string - CSS classes
 */
function pagelines_nav_classes(){ 
	
	$additional_menu_classes = '';
		
	if(ploption('enable_drop_down'))
		$additional_menu_classes .= ' sf-menu';
	
	return $additional_menu_classes;
}

/**
 * 
 *  Loads Special PageLines CSS Files, Optimized
 *
 *  @package PageLines Framework
 *  @subpackage Functions Library
 *  @since 1.2.0
 *
 */
function pagelines_draw_css( $css_url, $id = '', $enqueue = false){ 
	echo '<link rel="stylesheet" href="'.$css_url.'" />'."\n";
}


/**
 * 
 *  Abstracts the Enqueue of Stylesheets, fixes bbPress issues with dropping hooks
 *
 *  @package PageLines Framework
 *  @since 1.3.0
 *
 */
function pagelines_load_css( $css_url, $id, $hash = CORE_VERSION, $enqueue = true){
	
	
	if(pagelines_bbpress_forum()){
		printf('<link rel="stylesheet" id="%s"  href="%s?ver=%s" type="text/css" />%s', $id, $css_url, $hash, "\n");
	} else {
		wp_register_style($id, $css_url, array(), $hash, 'all');
	    wp_enqueue_style( $id );
	}
	

}

/**
 * 
 *  Loading CSS using relative path to theme root. This allows dynamic versioning, overriding in child theme
 *
 *  @package PageLines Framework
 *  @since 1.4.0
 *
 */
function pagelines_load_css_relative( $relative_style_url, $id){
	
	$rurl = '/' . $relative_style_url;
	
	if( is_file(get_stylesheet_directory() . $rurl ) ){
		
		$cache_ver = pl_cache_version( get_stylesheet_directory() . $rurl );
		
		pagelines_load_css( CHILD_URL . $rurl , $id, $cache_ver);
		 
	} elseif(is_file(get_template_directory() . $rurl) ){
		
		$cache_ver = pl_cache_version( get_template_directory() . $rurl ); 
		
		pagelines_load_css( PARENT_URL . $rurl , $id, $cache_ver);
		
	} 
	

}

/**
 * 
 * Get cache version number
 *
 *
 */
function pl_cache_version( $path, $version = CORE_VERSION ){
	$date_modified = filemtime( $path );
	$cache_ver = str_replace('.', '', $version) . '-' . date('mdGis', $date_modified);
	
	return $cache_ver;
}

/**
 * 
 *  Get Stylesheet Version
 *
 *  @package PageLines Framework
 *  @since 1.4.0
 *
 */
function pagelines_get_style_ver( $tpath = false ){
	
	// Get cache number that accounts for edits to base.css or style.css
	if( is_file(get_stylesheet_directory() .'/base.css') && !$tpath ){
		$date_modified = filemtime( get_stylesheet_directory() .'/base.css' );
		$cache_ver = str_replace('.', '', CHILD_VERSION) . '-' . date('mdGis', $date_modified); 
	} elseif(is_file(get_stylesheet_directory() .'/style.css') && !$tpath ){
		$date_modified = filemtime( get_stylesheet_directory() .'/style.css' );
		$cache_ver = str_replace('.', '', CORE_VERSION) .'-'.date('mdGis', $date_modified);
	} elseif(is_file(get_template_directory() .'/style.css')){
		$date_modified = filemtime( get_template_directory() .'/style.css' );
		$cache_ver = str_replace('.', '', CORE_VERSION) .'-'.date('mdGis', $date_modified);
	} else {
		$cache_ver = CORE_VERSION;
	}
	
	
	return $cache_ver;

}

/**
 * Debugging, prints nice array.
 * Sends to the footer in all cases.
 * 
 * @since 1.5.0
 */
function plprint( $data, $title = false, $echo = false){

	if( is_pl_debug() && current_user_can('manage_options') ){
		
		ob_start();
	
			echo 'echo "<pre class=\'plprint\'>';
	
			if($title) 
				echo sprintf('<h3>%s</h3>', $title);
		
			echo esc_html( print_r( $data, TRUE ) );
		
			echo '</pre>";';
		
			$data = ob_get_contents();
		
		ob_end_clean();

		if( $echo )
			echo $data;
		else 
			add_action( 'shutdown', create_function( '', $data ) );
		
	}

}

function plcomment( $data, $title = 'DEBUG', $type = 'html' ) {
	
	
	if( is_pl_debug() ){
	$open	= ( 'html' == $type ) ? "\n<!-- " : "\n/* ";
	$close	= ( 'html' == $type ) ? " -->\n" : "*/\n";	
	
	$pre = sprintf( '%s START %s %s', $open, $title, $close );
	$post = sprintf( '%s END %s %s', $open, $title, $close );
	
	return $pre . $data . $post;
	
	} else {
		return $data;
	}
}

/**
 * Creates Upload Folders for PageLines stuff
 *
 * @return true if successful
 **/
function pagelines_make_uploads($txt = 'Load'){
add_filter('request_filesystem_credentials', '__return_true' );

	$method = '';
	$url = 'themes.php?page=pagelines';

	if (is_writable(PAGELINES_DCSS)){
		$creds = request_filesystem_credentials($url, $method, false, false, null);
		if ( ! WP_Filesystem($creds) ) {
			// our credentials were no good, ask the user for them again
			request_filesystem_credentials($url, $method, true, false, null);
			return false;
		}

		global $wp_filesystem;
		if ( ! $wp_filesystem->put_contents( PAGELINES_DCSS, $txt, FS_CHMOD_FILE) ) {
			echo 'error saving file!';
			return false;
		}
	}

	return true;
}
/**
 * return array of PageLines plugins.
 * Since 2.0
 */
function pagelines_register_plugins() {
	
	$pagelines_plugins = array();
	$plugins = get_option('active_plugins');
	if ( $plugins ) {
		foreach( $plugins as $plugin ) {
			$a = get_file_data( WP_PLUGIN_DIR . '/' . $plugin, $default_headers = array( 'pagelines' => 'PageLines' ) );
			if ( !empty( $a['pagelines'] ) ) {
				$pagelines_plugins[] = str_replace( '.php', '', basename($plugin) );
			}

		}
	}
	return $pagelines_plugins;
}

/**
 *
 * Return sorted array based on supplied key
 * 
 * @since 2.0
 * @return sorted array
 */
function pagelines_array_sort( $a, $subkey, $pre = null, $dec = null ) {

	if ( ! is_array( $a) || ( is_array( $a ) && count( $a ) <= 1 ) )
		return $a;

	foreach( $a as $k => $v ) {
		$b[$k] = ( $pre ) ? strtolower( $v[$pre][$subkey] ) : strtolower( $v[$subkey] );
	}
	( !$dec ) ? asort( $b ) : arsort($b);
	foreach( $b as $key => $val ) {
		$c[] = $a[$key];
	}
	return $c;
}

/**
 *
 * Polishes a Key for UI presentation
 * 
 * @since 2.0
 * @return String
 */
function ui_key($key){
	
	return ucwords( str_replace( '_', ' ', str_replace( 'pl_', ' ', $key) ) );
}

/**
 *
 * Return latest tweet as (array) or single tweet text.
 * Tweets are stored in the db
 * 
 * @since 2.0b13
 * @return array
 */
function pagelines_get_tweets( $username, $latest = null) {
	

		// Fetch the tweets from the db
		// Set the array into a transient for easy reuse
		// If we get an error store it.
		
		if ( false === ( $tweets = get_transient( 'section-twitter-' . $username ) ) ) {
			$params = array(
				'screen_name'=>$username, // Twitter account name
				'trim_user'=>true, // only basic user data (slims the result)
				'include_entities'=>false, // as of Sept 2010 entities were not included in all applicable Tweets. regex still better
				'include_rts' => true
			);

			/**
			 * The exclude_replies parameter filters out replies on the server. If combined with count it only filters that number of tweets (not all tweets up to the requested count)
			 * If we are not filtering out replies then we should specify our requested tweet count
			 */

			$twitter_json_url = esc_url_raw( 'http://api.twitter.com/1/statuses/user_timeline.json?' . http_build_query( $params ), array( 'http', 'https' ) );
			unset( $params );
			$response = wp_remote_get( $twitter_json_url, array( 'User-Agent' => 'WordPress.com Twitter Widget' ) );
			$response_code = wp_remote_retrieve_response_code( $response );
			if ( 200 == $response_code ) {
				$tweets = wp_remote_retrieve_body( $response );
				$tweets = json_decode( $tweets, true );
				$expire = 900;
				if ( !is_array( $tweets ) || isset( $tweets['error'] ) ) {
					$tweets = 'error';
					$expire = 300;
				}
			} else {
				$tweets = 'error';
				$expire = 300;
				set_transient( 'section-twitter-response-code-' . $username, $response_code, $expire );
			}

			set_transient( 'section-twitter-' . $username, $tweets, $expire );
		}

		// We should have a list of tweets for $username or an error code to return.

		if ( 'error' != $tweets ) { // Tweets are good, return the array or a single if asked for ($latest)
			
			if ( $latest && is_array( $tweets ) && isset( $tweets[0]['text'] ) )
				return  $tweets[0]['text'];
			elseif ( is_array( $latest ) )
				return $latest;				
		} else {
			
			// We couldnt get the tweets so lets cycle through the possible errors.
			
			$error = get_transient( 'section-twitter-response-code-' . $username );		
			switch( $error ) {

				case 401:
					$text = wp_kses( sprintf( __( "Error: Please make sure the Twitter account is <a href='%s'>public</a>.", 'pagelines' ), 'http://support.twitter.com/forums/10711/entries/14016' ), array( 'a' => array( 'href' => true ) ) );
				break;
				
				case 403:
					$text = __( 'Error 403: Your IP is being rate limited by Twitter.', 'pagelines' );
				break;

				case 404:
					$text = __( 'Error 404: Your username was not found on Twitter.', 'pagelines' );
				break;

				case 420:
					$text = __( 'Error 420: Your IP is being rate limited by Twitter.', 'pagelines' );
				break;

				case 502:
					$text = __( 'Error 502: Twitter is being upgraded.', 'pagelines' );
				break;

				default:
					$text = __( 'Unknown Twitter error.', 'pagelines' );
				break;
			}
			return $text;			
		}	
}


/**
 *
 * @TODO document
 *
 */
function pagelines_tweet_clickable( $tweet ) {

	$regex = '/http([s]?):\/\/([^\ \)$]*)/';
	$link_pattern = '<a href="http$1://$2" rel="nofollow" title="$2">http$1://$2</a>';
	$tweet = preg_replace($regex,$link_pattern,$tweet);
	$regex = '/@([a-zA-Z0-9_]*)/';
	$link_pattern = '<a href="http://twitter.com/$1" title="$1 profile on Twitter" rel="nofollow">@$1</a>';
	$tweet = preg_replace($regex,$link_pattern,$tweet);
	$regex = '/\#([a-zA-Z0-9_]*)/';
	$link_pattern = '<a href="http://search.twitter.com/search?q=%23$1" title="search for $1 on Twitter" rel="nofollow">#$1</a>';
	$tweet = preg_replace($regex,$link_pattern,$tweet);
	
	return $tweet;
	
}


/**
 *
 * @TODO document
 *
 */
function pl_admin_is_page(){
	global $post;
	
	if( (isset($_GET['post_type']) && $_GET['post_type'] == 'page')  || (isset($post) && $post->post_type == 'page') )
		return true; 
	else
		return false;

}


/**
 *
 * @TODO document
 *
 */
function pl_file_get_contents( $filename ) {

	if ( is_file( $filename ) ) {
		
		$file = file( $filename, FILE_SKIP_EMPTY_LINES );
		$out = '';
		if( is_array( $file ) )
			foreach( $file as $contents )
				$out .= $contents;

		if( $out )
			return $out;
		else
			return false;	
	}
}


/**
 *
 * @TODO document
 *
 */
function pl_detect_ie( $version = false ) {
	
	global $is_IE;
	if ( ! $version && $is_IE ) {
		
		return round( substr($_SERVER['HTTP_USER_AGENT'], strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') + 5, 3) );
	}
	
	if ( $is_IE && is_int( $version ) && stristr( $_SERVER['HTTP_USER_AGENT'], sprintf( 'msie %s', $version ) ) )
		return true;
	else
		return false;
}

/** 
 * Search in an array, return full info.
 */
function array_search_ext($arr, $search, $exact = true, $trav_keys = null)
{
  if(!is_array($arr) || !$search || ($trav_keys && !is_array($trav_keys))) return false;
  $res_arr = array();
  foreach($arr as $key => $val)
  {
    $used_keys = $trav_keys ? array_merge($trav_keys, array($key)) : array($key);
    if(($key === $search) || (!$exact && (strpos(strtolower($key), strtolower($search)) !== false))) $res_arr[] = array('type' => "key", 'hit' => $key, 'keys' => $used_keys, 'val' => $val);
    if(is_array($val) && ($children_res = array_search_ext($val, $search, $exact, $used_keys))) $res_arr = array_merge($res_arr, $children_res);
    else if(($val === $search) || (!$exact && (strpos(strtolower($val), strtolower($search)) !== false))) $res_arr[] = array('type' => "val", 'hit' => $val, 'keys' => $used_keys, 'val' => $val);
  }
  return $res_arr ? $res_arr : false;
}

/**
 * PageLines Register Sidebar
 *
 * Registers sidebars with an optional priority.
 *
 * @param   $args
 * @param   null $priorty - numeric value
 *
 * @uses    plotion( 'enable_sidebar_reorder' ) - i.e.: prioritization
 * @uses    pagelines_sidebars class
 *
 * @link    http://www.pagelines.com/wiki/Pagelines_register_sidebar
 */
function pagelines_register_sidebar( $args, $priorty = null ) {

	register_sidebar( $args );
}

/** 
 * Insert into array at a position.
 *
 * @param $orig array Original array.
 * @param $new array Array to insert.
 * @param $offset int Offset
 * @return array
 */
function pl_insert_into_array( $orig, $new, $offset ) {
	
	$newArray = array_slice($orig, 0, $offset, true) +
	            $new +
	            array_slice($orig, $offset, NULL, true);
	return $newArray;
}


/** 
 * Insert into array before or after a key.
 *
 * @param $array array Original array.
 * @param $key str Key to find.
 * @param $insert_array array The array data to insert.
 * @param $before bool Insert before or after.
 * @return array
 */
function pl_array_insert( $array, $key, $insert_array, $before = FALSE ) {
	$done = FALSE;
	foreach ($array as $array_key => $array_val) {
		if (!$before) {
			$new_array[$array_key] = $array_val;
		}
		if ($array_key == $key && !$done) {
			foreach ($insert_array as $insert_array_key => $insert_array_val) {
				$new_array[$insert_array_key] = $insert_array_val;
			}
			$done = TRUE;
		}
		if ($before) {
			$new_array[$array_key] = $array_val;
		}
	}
	if (!$done) {
		$new_array = array_merge($array, $insert_array);
	}
	// Put the new array in the place of the original.
	return $new_array;
}

/** 
 * Display a banner if suppoerted plugin is detected during template_redirect.
 *
 */
function pl_check_integrations() {
	
	$integrations = array(
		
		'bbpress' => array(
			
			'function'	=> 'is_bbpress',
			'plugin'	=> 'pagelines-bbpress',
			'url'		=> 'http://www.pagelines.com/store/plugins/pagelines-bbpress/',
			'name'		=> 'bbPress',
			'class'		=> 'PageLinesBBPress'
			
		),
		'jigoshop' => array(
			
			'function'	=> 'is_jigoshop',
			'plugin'	=> 'pagelines-jigoshop',
			'url'		=> 'http://www.pagelines.com/store/plugins/pagelines-jigoshop/',
			'name'		=> 'Jigoshop',
			'class'		=> 'PageLinesJigoShop'		
		),		
	);
	
	foreach( $integrations as $i => $data ) {
		
		if( function_exists( $data['function'] ) ) {
			
			if( $data['function']() && ! class_exists( $data['class'] ) )
				pl_check_integrations_banner( $data );
		}	
	}
}

function pl_check_integrations_banner( $data ) {
	
	if( current_user_can('edit_themes') ){
	
		$banner_title = sprintf( '<h3 class="banner_title wicon">%s was detected.</h3>', $data['name'] );
		
		$text = 'Looks like your running a supported plugin but your not using our integrations whatsit.';
		
		$link_text = 'Get it from the store now!';
		
		$link = sprintf('<a href="%s">%s</a>', $data['url'], $link_text . ' &rarr;');
		
		echo sprintf('<div class="banner setup_area"><div class="banner_pad">%s <div class="banner_text subhead">%s<br/> %s</div></div></div>', $banner_title, $text, $link);
	
			
	}		
}

/** 
 * Return the raw URI
 *
 * @param $full bool Show full or just request.
 * @return string
 */
function pl_get_uri( $full = true ) {
	
	if ( $full )
		return  $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	else
		return  $_SERVER["REQUEST_URI"];	
}

/** 
 * Is framework in debug mode?
 *
 * @return bool
 */
function is_pl_debug() {
	
	if ( defined( 'PL_DEV' ) && PL_DEV )
		return true;
	if ( ploption( 'enable_debug' ) )
		return true;
}

/** 
 * Show debug info in footer ( wrapped in tags )
 *
 */
function pl_debug( $text = '', $before = "\n/*", $after = '*/' ) {
	
	if ( ! is_pl_debug() )
		return;
		
	$out = sprintf( 'echo "%s %s %s";', $before, $text, $after );	
	add_action( 'shutdown', create_function( '', $out ), 9999 );

}

/**
*
* @TODO do
*
*/
function inline_css_markup($id, $css, $echo = true){
	$mark = sprintf('%2$s<style type="text/css" id="%3$s">%2$s %1$s %2$s</style>%2$s', $css, "\n", $id);
	
	if($echo) 
		echo $mark;
	else
		return $mark;	
}

function pl_get_plus_link() {
	
	if( VDEV )
		return ADD_PLUS_DEV;
	else
		return ADD_PLUS_PRO;	
}
