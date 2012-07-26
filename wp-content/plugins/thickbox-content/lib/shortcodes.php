<?php

/**
 * @author Max Chirkov
 * @copyright 2009
 * @description Use WordPress Shortcode API for more features
 * @Docs http://codex.wordpress.org/Shortcode_API
 */

class thkBoxContent_shortcodes {
	
	var $count = 1;
	
	// register the new shortcodes
	function thkBoxContent_shortcodes() {
	
		add_shortcode( 'thkBC', array(&$this, 'thkBC_link') );
			
	}

	
	function thkBC_link( $atts ) {
	
		global $thkBoxContent;
	
		extract(shortcode_atts(array(
			'id' 		=> false,
			'width'		=> 500,
			'height'	=> 300,
			'anchortext'=> 'ThickBox Content Link',
			'title'		=> false,
			'url' 		=> false,
			'type'		=> "iframe",
			'html_wrap'	=> false,
			'html_id'	=> false,
			'html_class'=> false,
			'inline_id' => false,
		), $atts ));
		
		if(!$url && $id){
			$url = get_page_link($id);
		}
		
		if($type == "iframe"){
			$iframe = "&TB_iframe=true";
		}
		if($html_wrap){
			$opentag = '<'.$html_wrap.'>';
			$closetag = '</'.$html_wrap.'>';
		}
		if($html_id){
			$hid = " id=\"$html_id\"";
		}
		if($html_class){
			$class = " class=\"$html_class thickbox\"";
		}
		if($type = "inline" && $inline_id != ''){
			
			$out = '<style type="text/css"><!-- #' . $inline_id . '{ display: none; } --></style>' . "\n";
			$out .= $opentag . '<a' . $class . ' href="#TB_inline?height=' . $height . '&width=' . $width .'&inlineId=' . $inline_id .'" title="' . $title . '" class="thickbox"' . $hid . '>' . $anchortext .'</a>' . $closetag;
		}else{
			// Suggested by istarnet.com
			// Inline href strings might contain querystring arguments.
			// If the $url contains a ? then postpend with a &
			(strstr($url,'?')) ? $conjunctive = '&' : $conjunctive = '?';
			
			$out = $opentag . '<a' . $hid . $class . ' href="' . $url . $conjunctive . 'keepThis=true'.$iframe.'&height=' . $height . '&width=' . $width .'" title="' . $title . '" class="thickbox">' . $anchortext .'</a>' . $closetag;
		}	
		return $out;
	}

	
}

// let's use it
$thkBoxContentShortcodes = new thkBoxContent_Shortcodes;	

?>