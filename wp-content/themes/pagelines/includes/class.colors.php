<?php
/**
 * 
 *
 *  PageLines Color Calculations and Handling
 *
 *
 *  @package PageLines Framework
 *  @subpackage Post Types
 *  @since 1.3.0
 *
 */
class PageLinesColor {

	var $tabs = array();	// Controller for drawing meta options

    /**
     * PHP5 constructor
     *
     * @param $hex
     * @param string $id
     */
	function __construct( $hex, $id = '' ) {
	
		$this->id = $id;
	
		$this->base_hex = str_replace('#', '', $hex);
	
		$this->base_rgb = $this->hex_to_rgb( $this->base_hex  );
		
		$this->base_hsl = $this->rgb_to_hsl( $this->base_rgb  );
	
	}


    /**
     * Get HSL
     *
     * Takes hex color value, converts to RGB, then convers to HSL and returns value
     *
     * @param   $hex - color value
     * @param   $type
     *
     * @uses    hex_to_rgb
     * @uses    rgb_to_hsl
     *
     * @return  string - returns HSL value
     */
	function get_hsl( $hex, $type ){
		
		$hex = str_replace('#', '', $hex);

		$rgb = $this->hex_to_rgb( $hex  );

		$hsl = $this->rgb_to_hsl( $rgb );
		
		return $hsl[$type];
	}


    /**
     * Get Color
     *
     * @param   null $mode
     * @param   string $difference
     * @param   null $alt
     * @param   null $id
     *
     * @uses    adjust
     * @uses    darkadjust
     * @uses    mix_colors
     *
     * @return  mixed|string
     */
	function get_color( $mode = null, $difference = '10%', $alt = null, $id = null){
	
		$alt = str_replace('#', '', $alt);
		
		if(is_string($difference)){
			$dp = (int) str_replace('%', '', $difference);
			$diff = $dp/100;
		} else 
			$diff = $difference;
		
			
		if($mode == 'lighter')
			$color = $this->adjust($diff); 
		elseif($mode == 'darker')
			$color =  $this->adjust(-$diff);
		elseif($mode == 'light-contrast'){
			
			if($this->base_hsl['lightness'] > .92)
				$color =  $this->adjust(-$diff);
			else {
				
				$diff = $this->darkadjust($diff);
				
				$color =  $this->adjust($diff);
			}
				
				
		} elseif($mode == 'contrast'){
			
			if( $this->base_hsl['lightness'] < .4 || ($this->base_hsl['lightness'] < .7 && $this->base_hsl['hue'] > .6) || ($this->base_hsl['saturation'] > .8 && $this->base_hsl['lightness'] < .4)){
				
				$diff = $this->darkadjust($diff);
			
			
				$color =  $this->adjust($diff);
			}else
				$color =  $this->adjust(-$diff);
				
		
		}elseif( $mode == 'mix' ){
		
			$color = $this->mix_colors($this->base_hex, $alt, $diff);
				
		}elseif( $mode == 'shadow' ){
			
			$color =  $this->adjust($diff, 'lightness', $alt);
		
		} else {
			$color = $this->base_hex;
		}
			
			
		return $color;	
	}


    /**
     * Dark Adjust
     *
     * @param   $diff
     *
     * @return  float|int
     */
	function darkadjust( $diff ){
		if($this->base_hsl['lightness'] < .05)
			$diff = 4*$diff;
		elseif($this->base_hsl['lightness'] < .1)
			$diff = 2*$diff;
		elseif($this->base_hsl['lightness'] < .2)
			$diff = 1.5*$diff;
			
		return $diff;
	}


    /**
     * Load Color
     *
     * @param   $base
     * @param   $type
     * @param   $difference
     */
	function loadcolor( $base, $type, $difference ){
		
		$base = str_replace('#', '', $base);
		
		if(is_string($difference)){
			$dp = (int) str_replace('%', '', $difference);
			$diff = $dp/100;
		} else 
			$diff = $difference;
		
	}


    /**
     * Adjust
     *
     * @param   $adjustment
     * @param   string $mode
     * @param   null $hex
     *
     * @uses    base_hsl
     * @uses    hex_to_rgb
     * @uses    rgb_to_hsl
     * @uses    hsl_to_hex
     *
     * @return  string
     *
     * @version 2.2 - corrected typo hugh -> hue
     */
	function adjust( $adjustment, $mode = 'lightness', $hex = null){

		if(isset($hex)){
			
			$althex = str_replace('#', '', $hex);

			$altrgb = $this->hex_to_rgb( $althex  );

			$althsl = $this->rgb_to_hsl( $altrgb  );
			
			$h = $althsl['hue'];
			$s = $althsl['saturation'];
			$l = $althsl['lightness'];
			
		}else{
			$h = $this->base_hsl['hue'];
			$s = $this->base_hsl['saturation'];
			$l = $this->base_hsl['lightness'];
		}
		
		if( is_array($adjustment) ){
			
			$l = $l + $adjustment['lightness']; 
			
			$h = $h + $adjustment['hue'];
			
			$s = $s + $adjustment['saturation']; 
			
			
		} else {
			
			if($mode == 'hue')
				$h = $h + $adjustment; 
			elseif($mode == 'saturation')
				$s = $s + $adjustment; 
			else 
				$l = $l + $adjustment; 

		}
		
	
		// Adjust for hue 180* scale
		if ($h > 1) $h -= 1;
		if ($s > 1) $s = 1;
		if ($l > 1) $l = 1;
		
		if ($h < 0) $h += 1;
		if ($s < 0) $s = 0;
		if ($l < 0) $l = 0;
		
		
		$new_hsl = array( 'hue' => $h, 'saturation' => $s, 'lightness' => $l );
		
		return $this->hsl_to_hex( $new_hsl );
	}


    /**
     * HEX to RGB
     *
     * Coverts HEX color value to RGB color value
     *
     * @param   $hexcode
     * @return  array - individual Red, Greeb, and Blue values
     */
	function hex_to_rgb( $hexcode ){
		
		$redhex  = substr( $hexcode, 0, 2 );
		$greenhex = substr( $hexcode, 2, 2 );
		$bluehex = substr( $hexcode, 4, 2 );

		// $var_r, $var_g and $var_b are the three decimal fractions to be input to our RGB-to-HSL conversion routine

		$r = hexdec($redhex);
		$g = hexdec($greenhex);
		$b = hexdec($bluehex);
		
		return array( 'red' => $r, 'green' => $g, 'blue' => $b );
		
	}


    /**
     * RGB to HSL
     *
     * Converts RGB color value to HSL color value
     *
     * @param   $rgb
     * @return  array - individual Hue, Saturation, and Lightness values
     */
	function rgb_to_hsl( $rgb ){
	
	
		$clrR = $rgb['red'];
		$clrG = $rgb['green'];
		$clrB = $rgb['blue'];
		
		$clrMin = min($clrR, $clrG, $clrB);
		$clrMax = max($clrR, $clrG, $clrB);
		$deltaMax = $clrMax - $clrMin;
		
		$L = ($clrMax + $clrMin) / 510;

		if (0 == $deltaMax){
			$H = 0;
			$S = 0;
		}else{
			if (0.5 > $L)
			    $S = $deltaMax / ($clrMax + $clrMin);	
			else
			    $S = $deltaMax / (510 - $clrMax - $clrMin);
			
			if ($clrMax == $clrR)
			    $H = ($clrG - $clrB) / (6.0 * $deltaMax);
			elseif ($clrMax == $clrG)
			    $H = 1/3 + ($clrB - $clrR) / (6.0 * $deltaMax);
			else
			    $H = 2 / 3 + ($clrR - $clrG) / (6.0 * $deltaMax);

			if (0 > $H) $H += 1;
			if (1 < $H) $H -= 1;
		
		}
		
		
		return array( 'hue' => $H, 'saturation' => $S, 'lightness' => $L );
	}


    /**
     * HSL to HEX
     *
     * Converts HSL color value (to RGB) to HEX color value
     *
     * @param   $hsl
     *
     * @uses    hsl_to_rgb
     * @uses    rgb_to_hex
     *
     * @return  string - hex color value
     */
	function hsl_to_hex( $hsl ){
		
		$rgb = $this->hsl_to_rgb($hsl);

		$hex = $this->rgb_to_hex($rgb);
			
		return $hex;
	}


    /**
     * HSL to RGB
     *
     * Converts HSL color value to RGB color value (as an array)
     * Input is HSL; output is RGB
     *
     * @param   $hsl - value of complementary colour, held in $h2, $s, $l as fractions of 1
     *
     * @uses    _hue_to_rgb
     *
     * @return  array - in normal 255 255 255 format, held in $r, $g, $b
     */
	function hsl_to_rgb( $hsl ){

		$h = $hsl['hue'];
		$s = $hsl['saturation'];
		$l = $hsl['lightness'];

		if ($s == 0) {
			$r = $l * 255;
			$g = $l * 255;
			$b = $l * 255;
		} else {
			if ($l < 0.5)
				$var_2 = $l * (1 + $s);
			else
				$var_2 = ($l + $s) - ($s * $l);
			

			$var_1 = 2 * $l - $var_2;
			$r = 255 * $this->_hue_to_rgb( $var_1, $var_2, $h + (1 / 3) );
			$g = 255 * $this->_hue_to_rgb( $var_1, $var_2, $h );
			$b = 255 * $this->_hue_to_rgb( $var_1, $var_2, $h - (1 / 3) );
		};
		
		return array( 'red' => $r, 'green' => $g, 'blue' => $b );
		
	}


    /**
     * HUE to RGB
     *
     * @param   $v1
     * @param   $v2
     * @param   $vh
     *
     * @return
     */
	function _hue_to_rgb( $v1, $v2, $vh ) {
		
		if ($vh < 0) {
			$vh += 1;
		};

		if ($vh > 1) {
			$vh -= 1;
		};

		if ((6 * $vh) < 1) {
			return ($v1 + ($v2 - $v1) * 6 * $vh);
		};

		if ((2 * $vh) < 1) {
			return ($v2);
		};

		if ((3 * $vh) < 2) {
			return ($v1 + ($v2 - $v1) * ((2 / 3 - $vh) * 6));
		};

		return ($v1);
	}


    /**
     * RGB to HEX
     *
     * Converts RGB (array) color values to HEX color value
     *
     * @param   $rgb
     *
     * @return  string
     */
	function rgb_to_hex($rgb){
		
		$r = $rgb['red'];
		$g = $rgb['green'];
		$b = $rgb['blue'];
		
		$rhex = sprintf( '%02X', round($r) );
		$ghex = sprintf( '%02X', round($g) );
		$bhex = sprintf( '%02X', round($b) );

		$hex = $rhex.$ghex.$bhex;
		
		return $hex;
		
	}


    /**
     * Mix Colors
     *
     * Mixes two HEX color values at a specified ratio (default being equal parts of each)
     *
     * @param   $c1
     * @param   $c2
     * @param   float $ratio
     *
     * @uses    hex_to_rgb
     * @uses    rgb_to_hex
     *
     * @return  string
     */
	function mix_colors($c1, $c2, $ratio = .5){
		
		$r1 = $ratio * 2;
		$r2 = 2 - $r1;

		$c1_rgb = $this->hex_to_rgb($c1);
		$c2_rgb = $this->hex_to_rgb($c2);
		
		
		$rmix = ( ( $c1_rgb['red'] * $r1 ) + ( $c2_rgb['red'] * $r2 ) ) / 2;
		$gmix = ( ( $c1_rgb['green'] * $r1 ) + ( $c2_rgb['green'] * $r2 ) ) / 2;
		$bmix = ( ( $c1_rgb['blue'] * $r1 ) + ( $c2_rgb['blue'] * $r2 ) ) / 2;
		
		$new_rgb = array('red' => $rmix, 'green' => $gmix, 'blue' => $bmix);

	 	return $this->rgb_to_hex( $new_rgb );
	
	}


    /**
     * C(olor)
     *
     * Returns the color value as a hashed (HEX) value
     *
     * @param   string $mode
     * @param   string $difference
     * @param   null $alt
     * @param   null $id
     *
     * @uses    get_color
     *
     * @return  string - HEX color value
     */
	function c($mode = 'null', $difference = '10%', $alt = null, $id = null ){
		
		$color = $this->get_color($mode, $difference, $alt, $id );
		
		return '#'.$color;
		
	}


    /**
     * C(olor) E(cho)
     *
     * Displays the color value
     *
     * @param   string $mode
     * @param   string $difference
     * @param   null $alt
     * @param   null $id
     *
     * @uses    c
     */
	function ce($mode = 'null', $difference = '10%', $alt = null, $id = null ){
		
		echo $this->c($mode, $difference, $alt, $id );
		
	}


    /**
     * Shadow
     *
     * Applies a shadow to text, enabled by default
     *
     * @param   $mix
     * @param   string $type
     * @param   null $diff
     * @param   bool $echo
     *
     * @uses    c
     * @uses    get_hsl
     * @uses    ploption( 'disable_text_shadow' )
     *
     * @return  string
     */
	function shadow( $mix, $type = 'text', $diff = null, $echo = true ){

		if( $type == 'text'){
			
			if( ploption('disable_text_shadow') )
				return;
			
			$difference =  ( $this->get_hsl($mix, 'lightness') - $this->base_hsl['lightness'] );

			$difference = ($difference > 0 ) ? .1 : -.2;
			
			$prop = ( $difference < 0 ) ?  'text-shadow: 0 -1px 0 %s;' : 'text-shadow: 0 1px 0 %s;';
			 	
		} elseif( $type == 'box' ){
			
			$difference = -.3;
			
			$prop = '%s';
			
			
		}
	
		$rule = sprintf( $prop, $this->c( 'shadow', $difference, $mix ) );
		
		if($echo)
			echo $rule;
		else
			return $rule;
	}


    /**
     * Gradient
     *
     * Applies a gradient to the color of a specified range as determined by a percentage difference
     *
     * @param   null $mode
     * @param   string $diff
     * @param   string $direction
     * @param   bool $echo
     *
     * @uses    adjust
     * @uses    c
     *
     * @return  string
     */
	function gradient( $mode = null, $diff = '10%', $direction = 'top', $echo = true ){

		$hex = (isset($mode)) ? $this->c( $mode, $diff ) : $this->c(); 

		$hex = str_replace('#', '', $hex);
		
		$lighter = '#'.$this->adjust( .03, 'lightness', $hex);
		$darker  = '#'.$this->adjust( -.03, 'lightness', $hex);

		if($direction == 'bottom'){
			$dir = 'bottom';
			$op_dir = 'top';
		} else {
			$dir = 'top';
			$op_dir = 'bottom';
		}

		$rule = sprintf(
				'background: %1$s;
				background:-webkit-gradient(linear, center %5$s, center %4$s, from(%3$s), to(%2$s));
				background:-moz-linear-gradient(%4$s, %2$s, %3$s);
				-pie-background:linear-gradient(%4$s, %2$s, %3$s);
				background:linear-gradient(%4$s, %2$s, %3$s);',
				$hex, 
				$lighter, 
				$darker,
				$dir, 
				$op_dir
			);

		if($echo)
			echo $rule;
		else
			return $rule;
	}
	
}
//-------- END OF CLASS --------//


/**
 * Do Color Math
 *
 * @param   $oid
 * @param   $o
 * @param   $val
 * @param   string $format
 *
 * @uses    base_hsl
 * @uses    get_color
 * @uses    get_hsl
 * @uses    load_the_props
 * @uses    set_factory_key
 * @uses    store_set_color
 *
 * @internal uses class PageLinesCSS
 * @internal uses filter 'pl_math_array'
 *
 * @return  string
 * @todo confirm if removing the return after "if( ploption('disable_text_shadow') )" is correct
 */
function do_color_math($oid, $o, $val, $format = 'css'){

	$default = (isset($o['default'])) ? $o['default'] : $val;

	$output = '';
	
	$id = (isset($o['id'])) ? $o['id'] : null;
	
	$math_array = ( isset($o['math']) ) ? $o['math'] : array();
	
	$math_array = apply_filters('pl_math_array', $math_array, $oid, $o);
	
	if( !empty($math_array) ){
		
		
		// Set the base.
		// If no option value, use the depends cascade
		foreach( $o['math'] as $key => $k ){
		
			if(!$val){
			 	if(isset($k['depends'])){
					foreach($k['depends'] as $d){

						if( isset($d) && !empty($d)){
							$base = $d;
							break;
						}
					}
				} 

			} else 
				$base = str_replace('#', '', $val);
		
		}
		
		// Set the base color 
		$base = (isset($base)) ? $base : $default;			
	
		if(isset($id))
			store_set_color($id, $base);
			
		// Set up the base color for editing
		$math = new PageLinesColor( $base, $id);
		
		// Process math array
		foreach( $o['math'] as $key => $k ){

			$id = (isset($k['id'])) ? $k['id'] : '';

			$difference = isset($k['diff']) ? $k['diff'] : '10%';

			if($k['mode'] == 'mix' || $k['mode'] == 'shadow'){
				
				if( isset($k['mixwith']) && is_array($k['mixwith']) ){
					
					foreach($k['mixwith'] as $mkey => $m){
						
						if( isset($m) && !empty($m)){
							$mix_color = $m;
							break;
						} else 
							$mix_color = $base;
							
					}
					
				} elseif( isset($k['mixwith']) )
					$mix_color = $k['mixwith'];
					
				if($k['mode'] == 'shadow'){
					
					//if( ploption('disable_text_shadow') )
					/** commented out return as part of if statement */
					// return;
					
					$difference =  ($math->get_hsl($mix_color, 'lightness') - $math->base_hsl['lightness']);
			
					$difference = ($difference > 0 ) ? .1 : -.2;
				
					$k['css_prop'] = ( $difference < 0) ?  array('text-shadow-top') : array('text-shadow');
					
				}
				
				$color = $math->get_color($k['mode'], $difference, $mix_color, $id);
					
			} else 
				$color = $math->get_color($k['mode'], $difference, null, $id);

			$css = new PageLinesCSS;

			if(isset($o['selectors']) && $o['selectors'] != ''){
				
				$output .= $css->load_the_props( $k['css_prop'], '#'.$color );
				
			} else {
			
				// If using cssgroups
				

				$cssgroup = $k['cssgroup'];

				if(is_array($cssgroup))
					foreach($cssgroup as $cgroup)
						$css->set_factory_key($cgroup, $css->load_the_props( $k['css_prop'], '#'.$color ));
				else
					$css->set_factory_key($cssgroup, $css->load_the_props( $k['css_prop'], '#'.$color ));
				
				
			}
			
			
			
			// Recursion
			if( isset($k['math']) )
				do_color_math($key, $k, $color, $format);
			
			
		}
	}
	
	return $output;
}

/**
 * Store Set Color
 *
 * Changes HEX color value to string without hash character and saves it to the Set Colors array
 *
 * @param   $id
 * @param   $color
 */
function store_set_color($id, $color){
	
	global $set_colors;
	
	$color = str_replace('#', '', $color);
	
	$set_colors[ $id ] = $color;
	
}

/**
 * Get Set Color
 *
 * @param $id
 *
 * @return string|bool - value of set_color[id] or false when no value has been established
 */
function get_set_color( $id ){
	
	global $set_colors;
	
	if(isset($set_colors[ $id ]))
		return $set_colors[ $id ];
	else
		return false;
	
}


/**
 * Load Math
 *
 * @param   $color
 *
 * @return  \PageLinesColor (class)
 */
function loadmath( $color ){
	
	return new PageLinesColor( $color );
	
}

/**
 * Set Math
 *
 * @param   $type
 * @param   null $option
 * @param   array $oset
 *
 * @uses    loadmath
 * @uses    pl_base_color
 * @uses    pl_link_color
 * @uses    pl_text_color
 * @uses    ploption
 *
 * @return  \PageLinesColor (class)
 */
function setmath($type, $option = null, $oset = array()){
	
	if( $type == 'txt' )
		$backup = pl_text_color(); 
	elseif( $type == 'lnk' )
		$backup = pl_link_color();
	else
		$backup = pl_base_color();
	
	$color = ( isset($option) && ploption($option, $oset) ) ? ploption($option, $oset) : $backup;
	
	return loadmath( $color );
	
}
