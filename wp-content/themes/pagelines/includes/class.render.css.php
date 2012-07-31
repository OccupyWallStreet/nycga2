<?php


class PageLinesRenderCSS {
	
	var $lessfiles;
	var $types;
	var $ctimeout;
	var $btimeout;
	
	function __construct() {
		
		$this->url_string = '%s/?pageless=%s';
		$this->ctimeout = 86400;
		$this->btimeout = 604800;
		$this->types = array( 'sections', 'core', 'custom' );
		$this->lessfiles = $this->get_core_lessfiles();
		self::actions();		
	}
	
	/**
	 * 
	 *  Load LESS files
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function get_core_lessfiles(){
		
		$files = array(
			'grid',
			'alerts',
			'labels-badges',
			'tooltip-popover',
			'buttons',
			'type',
			'dropdowns',
			'accordion',
			'carousel',
			'responsive',
			'navs',
			'modals',
			'component-animations',
			'utilities',
			'pl-objects',
			'pl-tables',
			'wells',
			'forms',
			'blockquotes',
			'color', // HAS TO BE LAST	
		);
		return $files;
	}

	/**
	 * 
	 *  Dynamic mode, CSS is loaded to a file using wp_rewrite
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	private function actions() {

		global $pagelines_template;
				
		add_filter( 'query_vars', array( &$this, 'pagelines_add_trigger' ) );
		add_action( 'template_redirect', array( &$this, 'pagelines_less_trigger' ) , 15);
		
		if( defined( 'LESS_FILE_MODE' ) && LESS_FILE_MODE )
			add_action( 'template_redirect', array( &$this, 'less_file_mode' ) );

		add_action( 'wp_print_styles', array( &$this, 'load_less_css' ), 11 );
		add_action( 'pagelines_head_last', array( &$this, 'draw_inline_custom_css' ) , 25 );
		add_action( 'wp_head', array( &$pagelines_template, 'print_template_section_head' ), 12 );
		add_action( 'wp_head', array( &$this, 'do_background_image' ), 13 );
		add_action( 'extend_flush', array( &$this, 'flush_version' ) );	
		add_filter( 'pagelines_insert_core_less', array( &$this, 'pagelines_insert_core_less_callback' ) );
		add_action( 'admin_notices', array(&$this,'less_error_report') );
		add_action( 'wp_before_admin_bar_render', array( &$this, 'less_css_bar' ) );
	}
	
	function less_file_mode() {
		
		$upload_dir = wp_upload_dir();
		$folder = $upload_dir['basedir'] . '/pagelines/';
		$url = $upload_dir['baseurl'] . '/pagelines/';
		$file = sprintf( 'compiled-css-%s.css', ploption( 'pl_save_version' ) );

		if( file_exists( $folder . $file ) ){			
			define( 'DYNAMIC_FILE_URL', $url . $file );
			return;
		}
		
		$a = $this->get_compiled_core();
		$b = $this->get_compiled_sections();
		$gfonts = preg_match( '#(@import[^;]*;)#', $a['type'], $g ); 
		$out = '';
		if ( $gfonts ) {
			$out .= $g[1];
			$a['type'] = str_replace( $g[1], '', $a['type'] );
		}
		$out .= $this->minify( $a['core'] );
		$out .= $this->minify( $b['sections'] );
		$out .= $this->minify( $a['type'] );
		$out .= $this->minify( $a['dynamic'] );
		$mem = ( function_exists('memory_get_usage') ) ? round( memory_get_usage() / 1024 / 1024, 2 ) : 0;
		$out .= sprintf( __( 'CSS was compiled at %s and took %s seconds using %sMB of unicorn dust.', 'pagelines' ), date( DATE_RFC822, $a['time'] ), $a['c_time'],  $mem );
		$this->write_css_file( $out );	
	}
	
	function write_css_file( $txt ){
		add_filter('request_filesystem_credentials', '__return_true' );

		$method = '';
		$url = 'themes.php?page=pagelines';

		$upload_dir = wp_upload_dir();
		$User = posix_getpwuid( posix_geteuid() );
		$File = posix_getpwuid( fileowner( __FILE__ ) );
		if( $User['name'] !== $File['name'] )
			return;
		
		
		$folder = $upload_dir['basedir'] . '/pagelines';
		
		$file = sprintf( 'compiled-css-%s.css', ploption( 'pl_save_version' ) );
		
		if( !is_dir( $folder ) )
			wp_mkdir_p( $folder );

		include_once( ABSPATH . 'wp-admin/includes/file.php' );

	if ( is_writable( $folder ) ){
		$creds = request_filesystem_credentials($url, $method, false, false, null);
		if ( ! WP_Filesystem($creds) )
			return false;
	}

			global $wp_filesystem;
			if( is_object( $wp_filesystem ) )
				$wp_filesystem->put_contents( trailingslashit( $folder ) . $file, $txt, FS_CHMOD_FILE);
			else
				return false;

			define( 'DYNAMIC_FILE_URL', sprintf( '%s/pagelines/%s', $upload_dir['baseurl'], $file ) );
	}

	function do_background_image() {
			
		global $pagelines_ID;
		if ( is_archive() || is_home() )
			$pagelines_ID = null;
		$oset = array( 'post_id' => $pagelines_ID );
		$oid = 'page_background_image';
		$sel = '.full_width #page .page-canvas, body.fixed_width';		
		if( !ploption('supersize_bg', $oset) && ploption( $oid . '_url', $oset )){
			
			$bg_repeat = (ploption($oid.'_repeat', $oset)) ? ploption($oid.'_repeat', $oset) : 'no-repeat';
			$bg_attach = (ploption($oid.'_attach', $oset)) ? ploption($oid.'_attach', $oset): 'scroll';
			$bg_pos_vert = (ploption($oid.'_pos_vert', $oset) || ploption($oid.'_pos_vert', $oset) == 0 ) ? (int) ploption($oid.'_pos_vert', $oset) : '0';
			$bg_pos_hor = (ploption($oid.'_pos_hor', $oset) || ploption($oid.'_pos_hor', $oset) == 0 ) ? (int) ploption($oid.'_pos_hor', $oset) : '50';
			$bg_selector = (ploption($oid.'_selector', $oset)) ? ploption($oid.'_selector', $oset) : $sel;
			$bg_url = ploption($oid.'_url', $oset);
			
			$css = sprintf('%s{ background-image:url(%s);', $bg_selector, $bg_url);
			$css .= sprintf('background-repeat: %s;', $bg_repeat);
			$css .= sprintf('background-attachment: %s;', $bg_attach);
			$css .= sprintf('background-position: %s%% %s%%;}', $bg_pos_hor, $bg_pos_vert);
			echo inline_css_markup( 'pagelines-page-bg', $css );
			
		}	
	}

	
	function less_css_bar() {
		foreach ( $this->types as $t ) {		
			if ( ploption( "pl_less_error_{$t}" ) ) {
				
				global $wp_admin_bar;
				$wp_admin_bar->add_menu( array(
					'parent' => false, 
					'id' => 'less_error',
					'title' => sprintf( '<span style="color:red">%s</span>', __( 'LESS Compile error!', 'pagelines' ) ),
					'href' => admin_url( PL_SETTINGS_URL ),
					'meta' => false
				));
				$wp_admin_bar->add_menu( array(
					'parent' => 'less_error',
					'id' => 'less_message',
					'title' => sprintf( __( 'Error in %s Less code: %s', 'pagelines' ), $t, ploption( "pl_less_error_{$t}" ) ),
					'href' => admin_url( PL_SETTINGS_URL ),
					'meta' => false
				));
			}
		}
	}
	
	function less_error_report() {
		
		$default = '<div class="updated fade update-nag"><div style="text-align:left"><h4>PageLines %s LESS/CSS error.</h4>%s</div></div>';

		foreach ( $this->types as $t ) {		
			if ( ploption( "pl_less_error_{$t}" ) ) 
				printf( $default, ucfirst( $t ), ploption( "pl_less_error_{$t}" ) );
		}					
	}

	/**
	 * 
	 * Get custom CSS
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function draw_inline_custom_css() {

			$a = $this->get_compiled_custom();
			if ( '' != $a['custom'] )
				return inline_css_markup( 'pagelines-custom', rtrim( $this->minify( $a['custom'] ) ) );
	}

	/**
	 * 
	 *  Draw dynamic CSS inline.
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function draw_inline_dynamic_css() {

		if( has_filter( 'disable_dynamic_css' ) )
			return;

		$css = $this->get_dynamic_css();
		inline_css_markup('dynamic-css', $css['dynamic'] );
	}

	/**
	 * 
	 *  Get Dynamic CSS
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 *
	 */
	function get_dynamic_css(){
		
		$pagelines_dynamic_css = new PageLinesCSS;

		$pagelines_dynamic_css->typography();

		$typography = $pagelines_dynamic_css->css;

		unset( $pagelines_dynamic_css->css );
		$pagelines_dynamic_css->layout();
		$pagelines_dynamic_css->options();

		$out = array(
			'type'		=>	$typography,
			'dynamic'	=>	apply_filters('pl-dynamic-css', $pagelines_dynamic_css->css)	
		);
		return $out;
	}

	/**
	 * 
	 *  Enqueue the dynamic css file.
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function load_less_css() {
		
		wp_register_style( 'pagelines-less',  $this->get_dynamic_url(), false, null, 'all' );
		wp_enqueue_style( 'pagelines-less' );
	}

	function get_dynamic_url() {
		
		$version = ploption( "pl_save_version" );
		if ( ! $version )
			$version = '1';
		if ( '' != get_option('permalink_structure') && ! $this->check_compat() )
			$url = sprintf( '%s/pagelines-compiled-css-%s/', PARENT_URL, $version );
		else {
			
			if ( false !== ( strpos( $this->get_base_url(), '?' ) ) )
				$this->url_string = '%s&pageless=%s';
			
			$url = sprintf( $this->url_string, untrailingslashit( $this->get_base_url() ), $version );
		}
		if ( defined( 'DYNAMIC_FILE_URL' ) )
			$url = DYNAMIC_FILE_URL;
		
		if ( has_action( 'pl_force_ssl' ) )
			$url = str_replace( 'http://', 'https://', $url );
		
		return apply_filters( 'pl_dynamic_css_url', $url );
	}

	function get_base_url() {
		
		if ( defined( 'PLL_INC') ) {
			
			global $post;
			
			$lang = Polylang_Base::get_post_language( $post->ID );
						
			return sprintf( '%s/%s/', get_home_url(), $lang->slug );
		}

		if(function_exists('icl_get_home_url')) {
		    return icl_get_home_url();
		  }

		return get_home_url();		
	}

	function check_compat() {
		
		if ( function_exists( 'icl_get_home_url' ) )
			return true;

		if ( defined( 'PLL_INC') )
			return true;
			
		if ( ! VPRO )
			return true;

		if ( defined( 'PL_NO_DYNAMIC_URL' ) )
			return true;
			
		if( site_url() !== get_home_url() )
			return true;
		
		if ( 'nginx' == substr($_SERVER['SERVER_SOFTWARE'], 0, 5) )
			return false;
			
		global $is_apache;
		if ( ! $is_apache )
			return true;
	}

	/**
	 * 
	 *  Get compiled/cached CSS
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function get_compiled_core() {
		
		if ( is_array(  $a = get_transient( 'pagelines_core_css' ) ) ) {
			return $a;
		} else {
			
			$start_time = microtime(true);
			build_pagelines_layout();

			$dynamic = $this->get_dynamic_css();

			$core_less = $this->get_core_lesscode();
				
			$pless = new PagelinesLess();			

			$core_less = $pless->raw_less( $core_less  );

			$end_time = microtime(true);			
			$a = array(				
				'dynamic'	=> $dynamic['dynamic'],
				'type'		=> $dynamic['type'],
				'core'		=> $core_less,
				'c_time'	=> round(($end_time - $start_time),5),
				'time'		=> time()		
			);
			if ( strpos( $core_less, 'PARSE ERROR' ) === false ) {
				set_transient( 'pagelines_core_css', $a, $this->ctimeout );
				set_transient( 'pagelines_core_css_backup', $a, $this->btimeout );
				return $a;
			} else {
				return get_transient( 'pagelines_core_css_backup' );
			}			
		}		
	}

	/**
	 * 
	 *  Get compiled/cached CSS
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function get_compiled_sections() {
		
		if ( is_array(  $a = get_transient( 'pagelines_sections_css' ) ) ) {
			return $a;
		} else {
			
			$start_time = microtime(true);
			build_pagelines_layout();

			$sections = $this->get_all_active_sections();

			$pless = new PagelinesLess();
			$sections =  $pless->raw_less( $sections, 'sections' );
			$end_time = microtime(true);			
			$a = array(				
				'sections'	=> $sections,
				'c_time'	=> round(($end_time - $start_time),5),
				'time'		=> time()		
			);
			if ( strpos( $sections, 'PARSE ERROR' ) === false ) {
				set_transient( 'pagelines_sections_css', $a, $this->ctimeout );
				set_transient( 'pagelines_sections_css_backup', $a, $this->btimeout );
				return $a;
			} else {
				return get_transient( 'pagelines_sections_css_backup' );
			}		
		}		
	}
	
		
	/**
	 * 
	 *  Get compiled/cached CSS
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function get_compiled_custom() {
		
		if ( is_array(  $a = get_transient( 'pagelines_custom_css' ) ) ) {
			return $a;
		} else {
			
			$start_time = microtime(true);
			build_pagelines_layout();

			$custom = pl_strip_js( ploption( 'customcss' ) );

			$pless = new PagelinesLess();
			$custom =  $pless->raw_less( $custom, 'custom' );
			$end_time = microtime(true);			
			$a = array(				
				'custom'	=> $custom,
				'c_time'	=> round(($end_time - $start_time),5),
				'time'		=> time()		
			);
			if ( strpos( $custom, 'PARSE ERROR' ) === false ) {
				set_transient( 'pagelines_custom_css', $a, $this->ctimeout );
				set_transient( 'pagelines_custom_css_backup', $a, $this->btimeout );
				return $a;
			} else {
				return get_transient( 'pagelines_custom_css_backup' );
			}			
		}		
	}

	/**
	 * 
	 *  Get Core LESS code
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function get_core_lesscode() {
		
			global $disabled_settings;
			
			$add_color = (isset($disabled_settings['color_control'])) ? false : true;
			
			if ( ! $add_color ) {
				array_pop( $this->lessfiles );
			}			
			return $this->load_core_cssfiles( apply_filters( 'pagelines_core_less_files', $this->lessfiles ) );	
	}

	/**
	 * 
	 *  Helper for get_core_less_code()
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function load_core_cssfiles( $files ) {
	
		$code = '';
		foreach( $files as $less ) {
			
			$file = sprintf( '%s/%s.less', CORE_LESS, $less );
			$code .= pl_file_get_contents( $file );
		}
		return apply_filters( 'pagelines_insert_core_less', $code );
	}

	/**
	 * 
	 *  Add rewrite.
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function pagelines_less_rewrite( $wp_rewrite ) {

	    $less_rule = array(
	        '(.*)pagelines-compiled-css' => '/?pageless=1'
	    );
	    $wp_rewrite->rules = $less_rule + $wp_rewrite->rules;
	}

	// flush_rules() if our rules are not yet included
	function check_rules(){
		$rules = get_option( 'rewrite_rules' );
		if ( ! isset( $rules['(.*)pagelines-compiled-css'] ) ) {
			global $wp_rewrite;
		   	$wp_rewrite->flush_rules();
		}
	}

	function pagelines_add_trigger( $vars ) {
	    $vars[] = 'pageless';
	    return $vars;
	}
	
	function pagelines_less_trigger() {
		if( intval( get_query_var( 'pageless' ) ) ) {
			header( 'Content-type: text/css' );
			header( 'Expires: ' );
			header( 'Cache-Control: max-age=604100, public' );
			
			$a = $this->get_compiled_core();
			$b = $this->get_compiled_sections();
			$gfonts = preg_match( '#(@import[^;]*;)#', $a['type'], $g ); 
			
			if ( $gfonts ) {
				echo $g[1];
				$a['type'] = str_replace( $g[1], '', $a['type'] );
			}
			echo $this->minify( $a['core'] );
			echo $this->minify( $b['sections'] );
			echo $this->minify( $a['type'] );
			echo $this->minify( $a['dynamic'] );
			$mem = ( function_exists('memory_get_usage') ) ? round( memory_get_usage() / 1024 / 1024, 2 ) : 0;
			pl_debug( sprintf( __( 'CSS was compiled at %s and took %s seconds using %sMB of unicorn dust.', 'pagelines' ), date( DATE_RFC822, $a['time'] ), $a['c_time'],  $mem ) );		
			die();
		}
	}

	/**
	 * 
	 *  Minify
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function minify( $css ) {
		if( is_pl_debug() )
			return $css;
		
		if( ! ploption( 'pl_minify') )
			return $css;
		
		$min = preg_replace('@({)\s+|(\;)\s+|/\*.+?\*\/|\R@is', '$1$2 ', $css);
		
		if ( ! preg_last_error() )
			return $min;
		else
			return $css;
	}

	/**
	 * 
	 *  Flush rewrites/cached css
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function flush_version( $rules = true ) {

		$types = array( 'sections', 'core', 'custom' );
		
		$upload_dir = wp_upload_dir();
		$folder = $upload_dir['basedir'] . '/pagelines/';
		
		$file = sprintf( 'compiled-css-%s.css', ploption( 'pl_save_version' ) );
		if( is_file( $folder . $file ) )
			@unlink( $folder . $file );
		
		if( $rules )
			flush_rewrite_rules( true );
		plupop( 'pl_save_version', time() );

		$types = array( 'sections', 'core', 'custom' );
		
		foreach( $types as $t ) {
			
			$compiled = get_transient( "pagelines_{$t}_css" );
			$backup = get_transient( "pagelines_{$t}_css_backup" );
			
			if ( ! is_array( $backup ) && is_array( $compiled ) && strpos( $compiled[$t], 'PARSE ERROR' ) === false )
				set_transient( "pagelines_{$t}_css_backup", $compiled, 604800 );
		
			delete_transient( "pagelines_{$t}_css" );	
		}
		
		
	}
	
	function pagelines_insert_core_less_callback( $code ) {

		global $pagelines_raw_lesscode_external;	
		$out = '';
		if ( is_array( $pagelines_raw_lesscode_external ) && ! empty( $pagelines_raw_lesscode_external ) ) {

			foreach( $pagelines_raw_lesscode_external as $file ) {
				
				if( is_file( $file ) )
					$out .= pl_file_get_contents( $file );
			}
			return $code . $out;
		}
		return $code;
	}
	
	function get_all_active_sections() {
		
		$out = '';
		global $load_sections;
		$available = $load_sections->pagelines_register_sections( true, true );

		$disabled = get_option( 'pagelines_sections_disabled', array() );

		foreach( $disabled as $type => $class ) 			
			unset( $available[$type][key( $class )] );
		/*
		* We need to reorder the array so sections css is loaded in the right order.
		* Core, then pagelines-sections, followed by anything else. 
		*/
		$sections = array();
		$sections['parent'] = $available['parent'];
		unset( $available['parent'] );
		$sections['child'] = $available['child'];
		unset( $available['child'] );
		if ( is_array( $available ) )
			$sections = array_merge( $sections, $available );

		foreach( $sections as $t ) {		
			foreach( $t as $key => $data ) {
				if ( $data['less'] ) {
					if ( is_file( $data['base_dir'] . '/style.less' ) )
						$out .= pl_file_get_contents( $data['base_dir'] . '/style.less' );
					elseif( is_file( $data['base_dir'] . '/color.less' ))
						$out .= pl_file_get_contents( $data['base_dir'] . '/color.less' );	
				}
			}	
		}
		return apply_filters('pagelines_lesscode', $out);
	}
	
} //end of PageLinesRenderCSS

function pagelines_insert_core_less( $file ) {
	
	global $pagelines_raw_lesscode_external;
	
	if( !is_array( $pagelines_raw_lesscode_external ) )
		$pagelines_raw_lesscode_external = array();
	
	$pagelines_raw_lesscode_external[] = $file;
}
