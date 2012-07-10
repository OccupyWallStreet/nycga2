<?php
/**
 *
 *  PageLines Less Language Parser
 *
 *  @package PageLines Framework
 *	@subpackage Less
 *  @since 2.0.b22
 *
 */
class PageLinesLess {
	
	private $lparser = null;
	private $constants = '';
	

	/**
     * Establish the default LESS constants and provides a filter to over-write them
     *
     * @uses    pl_hashify - adds # symbol to CSS color hex values
     * @uses    page_line_height - calculates a line height relevant to font-size and content width
     */
	function __construct() {
		
		global $less_vars;
		
		// The LESS Class
		$this->lparser = new lessc();
		
		$this->base_color = pl_hashify( pl_base_color() );
		
		/* Type */
		$fontsize = 15;
		$content_width = 600;
		
		// PageLines Variables
		$constants = array(
			'plRoot'				=> sprintf( "\"%s\"", PARENT_URL ),
			'plSectionsRoot'		=> sprintf( "\"%s\"", SECTION_ROOT ),
			'plChildRoot'			=> sprintf( "\"%s\"", CHILD_URL ),
			'plExtendRoot'            => sprintf( "\"%s\"", PL_EXTEND_URL ),
			'pl-base'				=> $this->base_color, 
			'pl-text'				=> pl_hashify( pl_text_color() ), 
			'pl-link'				=> pl_hashify( pl_link_color() ),
			'pl-header'   			=> pl_hashify( pl_header_color() ),
			'pl-footer'  		 	=> pl_hashify( pl_footer_color() ),
			'invert-dark'			=> $this->invert(),
			'invert-light'			=> $this->invert( 'light' ),
			'font-size'				=> $fontsize . 'px', 
			'line-height'			=> page_line_height( $fontsize, $content_width ) . 'px',
			'pl-page-width'			=> pl_page_width() . 'px',
			'pl-content-width'		=> pl_content_width() . 'px',
			'pl-responsive-width'	=> round( pl_responsive_width(), 2 ) . '%',
			'pl-sidebar-width'		=> pl_sidebar_width() . 'px',
			'pl-secondary-width'	=> pl_secondary_sidebar_width() . 'px'
		);	
		if(is_array($less_vars))
			$constants = array_merge($less_vars, $constants);
			
		$constants = array_merge($this->type_vars(), $constants);
		
		// Make Filterable
		$this->constants = apply_filters('pless_vars', $constants);
	}
	
	/**
     * Grabs and Adds Typography Variables
     *
     *
     * @param   $lesscode
     */
    public function type_vars( ){
		
		global $disabled_settings;
		$alt = false;
		if ( is_array( $disabled_settings ) )
			if( !empty( $disabled_settings['typography'] ) )
				$alt = true;
		
		$vars = array(
			'plBaseFont'		=> ($alt) ? '"Helvetica" Arial, serif' : pl_type_el('type_primary', 'stack'), 
			'plBaseWeight'		=> pl_type_el('type_primary', 'weight'), 
			'plAltFont'			=> ($alt) ? '"Helvetica" Arial, serif' : pl_type_el('type_secondary', 'stack'), 
			'plAltWeight'		=> pl_type_el('type_secondary', 'weight'), 
			'plHeaderFont'		=> ($alt) ? '"Helvetica" Arial, serif' : pl_type_el('type_headers', 'stack'), 
			'plHeaderWeight'	=> pl_type_el('type_headers', 'weight'),
		);
		return $vars;
	}
	
	public function raw_less( $lesscode, $type = 'core' ) {		

		return $this->raw_parse($lesscode, $type);
	}

	private function raw_parse( $pless, $type ) {
	
		$pless = $this->add_constants( '' ) . $this->add_bootstrap() . $pless;
		try {
			$css = $this->lparser->parse( $pless );
		} 
			catch ( Exception $e) {		
				plupop( "pl_less_error_{$type}", $e->getMessage() );
				return sprintf( "/* LESS PARSE ERROR in your %s CSS: %s */\r\n", ucfirst( $type ), $e->getMessage() );
			}
			
		// were good!
		plupop( "pl_less_error_{$type}", false );
		return $css;	
	}

	private function add_bootstrap( ) {
		
		$vars = pl_file_get_contents( sprintf( '%s/variables.less', CORE_LESS ) );
		$mixins = pl_file_get_contents( sprintf( '%s/mixins.less', CORE_LESS ) );
	
		return $vars . $mixins;
	}

	private function add_core_less($pless){
	
		global $disabled_settings;
		
		$add_color = (isset($disabled_settings['color_control'])) ? false : true;
		$color = ($add_color) ? pl_get_core_less() : '';			
		return $pless . $color;
		
	}

	private function add_constants( $pless ) {
		
		$prepend = '';
		
		foreach($this->constants as $key => $value)
			$prepend .= sprintf('@%s:%s;%s', $key, $value, "\n");
		
		return $prepend . $pless;
		
	}
	
	private function invert( $mode = 'dark', $delta = 5 ){
		
		if($mode == 'light'){
			
			if($this->color_detect() == -2)
				return 2*$delta;
			elseif($this->color_detect() == -1)
				return 1.5*$delta;
			elseif($this->color_detect() == 1)
				return -1.7*$delta;
			else
				return $delta;
			
		}else{
			if($this->color_detect() == -2)
				return -(2*$delta);
			elseif($this->color_detect() == -1)
				return -$delta;
			else
				return $delta;

		}
	}

	/**
     * Color Detect
     *
     * Takes the base color hex string and assigns a value to determine what "shade" the color is
     *
     * @return bool|int - a numeric value used in invert()
     */
	function color_detect(){
		
		$hex = str_replace('#', '', $this->base_color); 

		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));
		
		if($r + $g + $b > 750){
			
			// Light
		    return 1;
		
		}elseif($r + $g + $b < 120){

			// Really Dark
			return -2;

		}
		elseif($r + $g + $b < 300){
		
			// Dark
			return -1;
		
		}else{
			
			// Meh
		    return false;
		
		}
	}
}

/* 
 * Add Less Variables
 * 
 * Must be added before header.
 **************************/
function pagelines_less_var( $name, $value ){
	
	global $less_vars;
	
	$less_vars[$name] = $value;
	
}


/* 
 *  Color Fetch
 **************************/
function pl_base_color( $mode = '', $difference = '10%'){
	
	$base_color = PageLinesThemeSupport::BaseColor();

	if( !$base_color ){
	
		if( pl_check_color_hash( ploption('contentbg' ) ) )
			$base = pl_hash_strip( ploption('contentbg') );
		elseif( pl_check_color_hash( ploption('pagebg' ) ) )
			$base = pl_hash_strip( ploption('pagebg') );
		elseif( pl_check_color_hash( ploption('bodybg' ) ) )
			$base = pl_hash_strip( ploption('bodybg') );
		else
			$base = 'FFFFFF';
	
	} else
		$base = $base_color;
		
		
	if($mode != ''){
		
		$adjust_base = new PageLinesColor($base);
		
		$adjusted = $adjust_base->c($mode, $difference);
		
		return $adjusted;
		
	} else
		return $base;
}


/**
 * PageLines BackGround Color
 *
 * Use to set the background color, if set; if not set the background color is returned as #FFFFFF (White)
 *
 * @return bool|string - background color value
 */
function pl_bg_color(){
	
	if( pl_check_color_hash( get_set_color( 'the_bg' ) ) )
		return get_set_color( 'the_bg' );
	else 
		return 'FFFFFF';	
}

/**
 * PageLines Text Color
 *
 * Used to set the text color; if not set the default color #000000 is set
 *
 * @return mixed|string - text color value
 */
function pl_text_color(){
		
	$color = ( pl_check_color_hash( ploption( 'text_primary' ) ) ) ? pl_hash_strip( ploption( 'text_primary' ) ) : '000000';

	return $color;
}

/**
 * PageLines Link Color
 *
 * Used to set the link; if not set the default color is set to #225E9B
 *
 * @return mixed|string - link color
 */
function pl_link_color(){
	
	$color = ( pl_check_color_hash( ploption( 'linkcolor' ) ) ) ? pl_hash_strip( ploption( 'linkcolor' ) ) : '225E9B';
	
	return $color;	
}

/**
 * PageLines Header Color
 *
 * Used to set the header color; if not set the default color #000000 is set
 *
 * @return mixed|string - header color
 */
function pl_header_color(){
	
	$color = ( pl_check_color_hash( ploption( 'headercolor' ) ) ) ? pl_hash_strip( ploption( 'headercolor' ) ) : '000000';
	
	return $color;	
}

/**
 * PageLines Footer Color
 *
 * Used to set the footer text color; if not set the default color #999999 is set
 *
 * @return mixed|string
 */
function pl_footer_color(){
	
	$color = ( pl_check_color_hash( ploption( 'footer_text' ) ) ) ? pl_hash_strip( ploption( 'footer_text' ) ) : '999999';
	
	return $color;
}

/* 
 *  Helpers
 **************************/
function pl_hash_strip( $color ){
	
	return str_replace('#', '', $color);
}

function pl_check_color_hash( $color ) {
	
	if ( preg_match( '/^#?([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color ) ) 
		return true;
	else
		return false;
}

/**
 * PageLines Hashify
 *
 * Adds the # symbol to the hex value of the color being used
 *
 * @param $color
 *
 * @return string
 */
function pl_hashify( $color ){
	
	$clean_hex = str_replace('#', '', $color);
	
	return sprintf('#%s', $clean_hex);
}
