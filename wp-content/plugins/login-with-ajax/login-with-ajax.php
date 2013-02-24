<?php
/*
Plugin Name: Login With Ajax
Plugin URI: http://netweblogic.com/wordpress/plugins/login-with-ajax/
Description: Ajax driven login widget. Customisable from within your template folder, and advanced settings from the admin area.
Author: NetWebLogic
Version: 3.0.4.1
Author URI: http://netweblogic.com/
Tags: Login, Ajax, Redirect, BuddyPress, MU, WPMU, sidebar, admin, widget

Copyright (C) 2009 NetWebLogic LLC

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class LoginWithAjax {

	/**
	 * If logged in upon instantiation, it is a user object.
	 * @var WP_User
	 */
	var $current_user;
	/**
	 * List of templates available in the plugin dir and theme (populated in init())
	 * @var array
	 */
	var $templates = array();
	/**
	 * Name of selected template (if selected)
	 * @var string
	 */
	var $template;
	/**
	 * lwa_data option
	 * @var array
	 */
	var $data;
	/**
	 * Location of footer file if one is found when generating a widget, for use in loading template footers.
	 * @var string
	 */
	var $footer_loc;
	/**
	 * URL for the AJAX Login procedure in templates (including callback and template parameters)
	 * @var string
	 */
	var $url_login;
	/**
	 * URL for the AJAX Remember Password procedure in templates (including callback and template parameters)
	 * @var string
	 */
	var $url_remember;
	/**
	 * URL for the AJAX Registration procedure in templates (including callback and template parameters)
	 * @var string
	 */
	var $url_register;



	// Class initialization
	function LoginWithAjax() {
		//Set when to run the plugin
		add_action( 'widgets_init', array(&$this,'init') );
	}

	// Actions to take upon initial action hook
	function init(){
		//Load LWA options
		$this->data = get_option('lwa_data');
		//Remember the current user, in case there is a logout
		$this->current_user = wp_get_current_user();

		//Get Templates from theme and default by checking for folders - we assume a template works if a folder exists!
		//Note that duplicate template names are overwritten in this order of precedence (highest to lowest) - Child Theme > Parent Theme > Plugin Defaults
		//First are the defaults in the plugin directory
		$this->find_templates( path_join( WP_PLUGIN_DIR , basename( dirname( __FILE__ ) ). "/widget/") );
		//Now, the parent theme (if exists)
		if( get_stylesheet_directory() != get_template_directory() ){
			$this->find_templates( get_template_directory().'/plugins/login-with-ajax/' );
		}
		//Finally, the child theme
		$this->find_templates( get_stylesheet_directory().'/plugins/login-with-ajax/' );

		//Generate URLs for login, remember, and register
		$this->url_login = $this->template_link(site_url('wp-login.php', 'login_post'));
		$this->url_register = $this->template_link(site_url('wp-login.php?action=register', 'login_post'));
		$this->url_remember = $this->template_link(site_url('wp-login.php?action=lostpassword', 'login_post'));

		//Make decision on what to display
		if ( isset($_REQUEST["login-with-ajax"]) ) { //AJAX Request
			$this->ajax();
		}elseif ( isset($_REQUEST["login-with-ajax-widget"]) ) { //Widget Request via AJAX
			$instance = ( !empty($_REQUEST["template"]) ) ? array('template' => $_REQUEST["template"]) : array();
			$instance['is_widget'] = false;
			$instance['profile_link'] = ( !empty($_REQUEST["lwa_profile_link"]) ) ? $_REQUEST['lwa_profile_link']:0;
			$this->widget( array(), $instance );
			exit();
		}elseif ( function_exists('register_widget') ){ //WP < 2.8 safety check
			$plugin_url = path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) ));
			//Enqueue scripts - Only one script enqueued here.... theme JS takes priority, then default JS
			if( !is_admin() ) {
				if( file_exists(get_stylesheet_directory().'/plugins/login-with-ajax/login-with-ajax.js') ){ //Child Theme (or just theme)
					wp_enqueue_script( "login-with-ajax", get_stylesheet_directory_uri()."/plugins/login-with-ajax/login-with-ajax.js", array( 'jquery' ) );
				}else if( file_exists(get_template_directory().'/plugins/login-with-ajax/login-with-ajax.js') ){ //Parent Theme (if parent exists)
					wp_enqueue_script( "login-with-ajax", get_template_directory_uri()."/plugins/login-with-ajax/login-with-ajax.js", array( 'jquery' ) );
				}else{ //Default file in plugin folder
					wp_enqueue_script( "login-with-ajax", $plugin_url."/widget/login-with-ajax.js", array( 'jquery' ) );
				}

				//Enqueue stylesheets - Only one style enqueued here.... theme CSS takes priority, then default CSS
				//The concept here is one stylesheet is loaded which will work for multiple templates.
				if( file_exists(get_stylesheet_directory().'/plugins/login-with-ajax/widget.css') ){ //Child Theme (or just theme)
					wp_enqueue_style( "login-with-ajax", get_stylesheet_directory_uri().'/plugins/login-with-ajax/widget.css' );
				}else if( file_exists(get_template_directory().'/plugins/login-with-ajax/widget.css') ){ //Parent Theme (if parent exists)
					wp_enqueue_style( "login-with-ajax", get_template_directory_uri().'/plugins/login-with-ajax/widget.css' );
				}else{ //Default file in plugin folder
					wp_enqueue_style( "login-with-ajax", $plugin_url."/widget/widget.css" );
				}
			}

			//Register widget
			register_widget("LoginWithAjaxWidget");

			//Add logout/in redirection
			add_action('login_form_register', array(&$this, 'register'));
			add_action('wp_logout', array(&$this, 'logoutRedirect'));
			add_action('login_redirect', array(&$this, 'loginRedirect'), 1, 3);
			add_shortcode('login-with-ajax', array(&$this, 'shortcode'));
			add_shortcode('lwa', array(&$this, 'shortcode'));

		}
	}

	/*
	 * LOGIN OPERATIONS
	 */

	// Decides what action to take from the ajax request
	function ajax(){
		switch ( $_REQUEST["login-with-ajax"] ) {
			case 'login': //A login has been requested
				$return = $this->json_encode($this->login());
				break;
			case 'remember': //Remember the password
				$return = $this->json_encode($this->remember());
				break;
			default: //Don't know
				$return = $this->json_encode(array('result'=>0, 'error'=>'Unknown command requested'));
				break;
		}
		echo $return;
		exit();
	}

	// Reads ajax login creds via POSt, calls the login script and interprets the result
	function login(){
		$return = array(); //What we send back
		if( !empty($_REQUEST['log']) && !empty($_REQUEST['pwd']) && trim($_REQUEST['log']) != '' && trim($_REQUEST['pwd'] != '') ){
			$loginResult = wp_signon();
			$user_role = 'null';
			if ( strtolower(get_class($loginResult)) == 'wp_user' ) {
				//User login successful
				$this->current_user = $loginResult;
				/* @var $loginResult WP_User */
				$return['result'] = true;
				$return['message'] = __("Login Successful, redirecting...",'login-with-ajax');
				//Do a redirect if necessary
				$redirect = $this->getLoginRedirect($this->current_user);
				if( $redirect != '' ){
					$return['redirect'] = $redirect;
				}
				//If the widget should just update with ajax, then supply the URL here.
				if( !empty($this->data['no_login_refresh']) && $this->data['no_login_refresh'] == 1 ){
					//Is this coming from a template?
					$query_vars = ($_GET['template'] != '') ? "&template={$_GET['template']}" : '';
					$query_vars .= ($_REQUEST['lwa_profile_link'] == '1') ? "&lwa_profile_link=1" : '';
					$return['widget'] = get_bloginfo('wpurl')."?login-with-ajax-widget=1$query_vars";
					$return['message'] = __("Login successful, updating...",'login-with-ajax');
				}
			} elseif ( strtolower(get_class($loginResult)) == 'wp_error' ) {
				//User login failed
				/* @var WP_Error $loginResult */
				$return['result'] = false;
				$return['error'] = $loginResult->get_error_message();
			} else {
				//Undefined Error
				$return['result'] = false;
				$return['error'] = __('An undefined error has ocurred', 'login-with-ajax');
			}
		}else{
			$return['result'] = false;
			$return['error'] = __('Please supply your username and password.', 'login-with-ajax');
		}
		//Return the result array with errors etc.
		return $return;
	}

	/**
	 * Checks post data and registers user
	 * @return string
	 */
	function register(){
		if( !empty($_REQUEST['lwa']) ) {
			$return = array();
			if ('POST' == $_SERVER['REQUEST_METHOD']) {
				require_once( ABSPATH . WPINC . '/registration.php');
				$errors = register_new_user($_POST['user_login'], $_POST['user_email']);
				if ( !is_wp_error($errors) ) {
					//Success
					$return['result'] = true;
					$return['message'] = __('Registration complete. Please check your e-mail.');
				}else{
					//Something's wrong
					$return['result'] = false;
					$return['error'] = $errors->get_error_message();
				}
			}
			echo $this->json_encode($return);
			exit();
		}
	}

	// Reads ajax login creds via POSt, calls the login script and interprets the result
	function remember(){
		$return = array(); //What we send back
		$result = retrieve_password();

		if ( $result === true ) {
			//Password correctly remembered
			$return['result'] = true;
			$return['message'] = __("We have sent you an email", 'login-with-ajax');
		} elseif ( strtolower(get_class($result)) == 'wp_error' ) {
			//Something went wrong
			/* @var $result WP_Error */
			$return['result'] = false;
			$return['error'] = $result->get_error_message();
		} else {
			//Undefined Error
			$return['result'] = false;
			$return['error'] = __('An undefined error has ocurred', 'login-with-ajax');
		}
		//Return the result array with errors etc.
		return $return;
	}

	/*
	 * Redirect Functions
	 */

	function logoutRedirect(){
		$redirect = $this->getLogoutRedirect();
		if($redirect != ''){
			wp_redirect($redirect);
			exit();
		}
	}

	function getLogoutRedirect(){
		$data = $this->data;
		if( !empty($data['logout_redirect']) ){
			$redirect = $data['logout_redirect'];
		}
		if( strtolower(get_class($this->current_user)) == "wp_user" ){
			//Do a redirect if necessary
			$data = $this->data;
			$user_role = array_shift($this->current_user->roles); //Checking for role-based redirects
			if( !empty($data["role_logout"]) && is_array($data["role_logout"]) && isset($data["role_logout"][$user_role]) ){
				$redirect = $data["role_logout"][$user_role];
			}
		}
		$redirect = str_replace("%LASTURL%", $_SERVER['HTTP_REFERER'], $redirect);
		return $redirect;
	}

	function loginRedirect( $redirect, $redirect_notsurewhatthisis, $user ){
		$data = $this->data;
		if(is_user_logged_in()){
			$lwa_redirect = $this->getLoginRedirect($user);
			if( $lwa_redirect != '' ){
				wp_redirect($lwa_redirect);
				exit();
			}
		}
		return $redirect;
	}

	function getLoginRedirect($user){
		$data = $this->data;
		if($data['login_redirect'] != ''){
			$redirect = $data["login_redirect"];
		}
		if( strtolower(get_class($user)) == "wp_user" ){
			$user_role = array_shift($user->roles); //Checking for role-based redirects
			if( isset($data["role_login"][$user_role]) ){
				$redirect = $data["role_login"][$user_role];
			}
		}
		//Do string replacements
		$redirect = str_replace('%USERNAME%', $user->user_login, $redirect);
		$redirect = str_replace("%LASTURL%", $_SERVER['HTTP_REFERER'], $redirect);
		return $redirect;
	}

	/*
	 * WIDGET OPERATIONS
	 */

	function widget($args, $instance = array() ){
		//Extract widget arguments
		extract($args);
		//Merge instance options with global default options
		$lwa_data = $this->data;
		$lwa_data = wp_parse_args($instance, $lwa_data);
		//Deal with specific variables
		$lwa_data['profile_link'] = ( $lwa_data['profile_link'] != false && $lwa_data['profile_link'] != "false" );
		$is_widget = ( isset($lwa_data['is_widget']) ) ? ($lwa_data['is_widget'] != false && $lwa_data['is_widget'] != "false") : true ;
		//Add template logic
		$this->template = ( !empty($lwa_data['template']) && array_key_exists($lwa_data['template'], $this->templates) ) ? $lwa_data['template']:'default';
		//Choose the widget content to display.
		if(is_user_logged_in()){
			//Firstly check for template in theme with no template folder (legacy)
			$template_loc = locate_template( array('plugins/login-with-ajax/widget_in.php') );
			//Then check for custom templates or theme template default
			$template_loc = ($template_loc == '' && $this->template) ? $this->templates[$this->template].'/widget_in.php':$template_loc;
			include ( $template_loc != '' ) ? $template_loc : 'widget/default/widget_in.php';
		}else{
			//Firstly check for template in theme with no template folder (legacy)
			$template_loc = locate_template( array('plugins/login-with-ajax/widget_out.php') );
			//First check for custom templates or theme template default
			$template_loc = ($template_loc == '' && $this->template) ? $this->templates[$this->template].'/widget_out.php' : $template_loc;
			include ( $template_loc != '' ) ? $template_loc : 'widget/default/widget_out.php';
		}
	}

	function shortcode($atts){
		$defaults = array( 'is_widget' => false, 'profile_link' => false, 'registration' => 1 );
		$atts = shortcode_atts($defaults, $atts);
		ob_start();
		$this->widget(array(), $atts );
		return ob_get_clean();
	}

	function new_user_notification($user_login, $plaintext_pass, $user_email, $blogname){
		//Copied out of /wp-includes/pluggable.php
		$message = $this->data['notification_message'];
		$message = str_replace('%USERNAME%', $user_login, $message);
		$message = str_replace('%PASSWORD%', $plaintext_pass, $message);
		$message = str_replace('%BLOGNAME%', $blogname, $message);
		$message = str_replace('%BLOGURL%', get_bloginfo('wpurl'), $message);

		$subject = $this->data['notification_subject'];
		$subject = str_replace('%BLOGNAME%', $blogname, $subject);
		$subject = str_replace('%BLOGURL%', get_bloginfo('wpurl'), $subject);

		wp_mail($user_email, $subject, $message);
	}

	/*
	 * Auxillary Functions
	 */

	//Checks a directory for folders and populates the template file
	function find_templates($dir){
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
		            if(is_dir($dir . $file) && $file != '.' && $file != '..' && $file != '.svn'){
		            	//Template dir found, add it to the template array
		            	$this->templates[$file] = path_join($dir, $file);
		            }
		        }
		        closedir($dh);
		    }
		}
	}

	//Add template link and JSON callback var to the URL
	function template_link( $content ){
		if(strstr($content, '?')){
			$content .= '&amp;callback=?&amp;template='.$this->template;
		}else{
			$content .= '?callback=?&amp;template='.$this->template;
		}
		return $content;
	}

	//PHP4 Safe JSON encoding
	function json_encode($array){
		if( !function_exists("json_encode") ){
			$return = json_encode($array);
		}else{
			$return = $this->array_to_json($array);
		}
		if( isset($_REQUEST['callback']) && preg_match("/^jQuery[_a-zA-Z0-9]+$/", $_REQUEST['callback']) ){
			$return = $_GET['callback']."($return)";
		}
		return $return;
	}
	//PHP4 Compatible json encoder function
	function array_to_json($array){
		//PHP4 Comapatability - This encodes the array into JSON. Thanks go to Andy - http://www.php.net/manual/en/function.json-encode.php#89908
		if( !is_array( $array ) ){
	        return false;
	    }
	    $associative = count( array_diff( array_keys($array), array_keys( array_keys( $array )) ));
	    if( $associative ){
	        $construct = array();
	        foreach( $array as $key => $value ){
	            // We first copy each key/value pair into a staging array,
	            // formatting each key and value properly as we go.
	            // Format the key:
	            if( is_numeric($key) ){
	                $key = "key_$key";
	            }
	            $key = "'".addslashes($key)."'";
	            // Format the value:
	            if( is_array( $value )){
	                $value = $this->array_to_json( $value );
	            }else if( is_bool($value) ) {
	            	$value = ($value) ? "true" : "false";
	            }else if( !is_numeric( $value ) || is_string( $value ) ){
	                $value = "'".addslashes($value)."'";
	            }
	            // Add to staging array:
	            $construct[] = "$key: $value";
	        }
	        // Then we collapse the staging array into the JSON form:
	        $result = "{ " . implode( ", ", $construct ) . " }";
	    } else { // If the array is a vector (not associative):
	        $construct = array();
	        foreach( $array as $value ){
	            // Format the value:
	            if( is_array( $value )){
	                $value = $this->array_to_json( $value );
	            } else if( !is_numeric( $value ) || is_string( $value ) ){
	                $value = "'".addslashes($value)."'";
	            }
	            // Add to staging array:
	            $construct[] = $value;
	        }
	        // Then we collapse the staging array into the JSON form:
	        $result = "[ " . implode( ", ", $construct ) . " ]";
	    }
	    return $result;
	}
}
//Add translation
load_plugin_textdomain('login-with-ajax', false, "login-with-ajax/langs");

//Include admin file if needed
if(is_admin()){
	include_once('login-with-ajax-admin.php');
}
//Include widget
include_once('login-with-ajax-widget.php');

//Include pluggable functions file if user specifies in settings
$lwa_data = get_option('lwa_data');
if( !empty($lwa_data['notification_override']) && $lwa_data['notification_override'] == '1' ){
	include_once('pluggable.php');
}

//Template Tag
function login_with_ajax($atts = ''){
	global $LoginWithAjax;
	$atts = shortcode_parse_atts($atts);
	echo $LoginWithAjax->shortcode($atts);
}

// Start plugin
global $LoginWithAjax;
$LoginWithAjax = new LoginWithAjax();

?>