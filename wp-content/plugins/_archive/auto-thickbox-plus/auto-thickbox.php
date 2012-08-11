<?php
/*
Plugin Name: Auto ThickBox Plus
Plugin URI: http://attosoft.info/en/blog/auto-thickbox-plus/
Description: Overlays linked image, inline, iFrame and AJAX content on the page in simple & fast effects. (improved version of Auto Thickbox plugin)
Version: 1.6
Author: attosoft
Author URI: http://attosoft.info/en/
License: GPL 2.0
Text Domain: auto-thickbox
Domain Path: /languages
*/

/*	Copyright 2010-2012 attosoft (contact@attosoft.info)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* This plugin is mainly based on Auto Thickbox plugin by Denis de Bernardy.
	http://www.semiologic.com/software/auto-thickbox/
*/

define('AUTO_THICKBOX_PLUS', 'Auto ThickBox Plus');
define('AUTO_THICKBOX_PLUS_VERSION', '1.6');

/**
 * auto_thickbox
 *
 * @package Auto Thickbox
 **/
class auto_thickbox {

	/**
	 * filter()
	 *
	 * @param array $anchor
	 * @return anchor $anchor
	 **/
	function filter($anchor) {
		if ( preg_match("/\.(?:jpe?g|gif|png|bmp|webp)\b/i", $anchor['attr']['href']) )
			return auto_thickbox::image($anchor);
		elseif ( !empty($anchor['attr']['class']) && in_array('thickbox', $anchor['attr']['class']) )
			return auto_thickbox::iframe($anchor);
		elseif ( strpos($anchor['attr']['href'], 'TB_iframe') !== false || strpos($anchor['attr']['href'], '#TB_inline') !== false )
			return $this->add_thickbox_class($anchor);
		else
			return $anchor;
	} # filter()

	function add_thickbox_class($anchor) {
		if ( !$anchor['attr']['class'] ) {
			$anchor['attr']['class'][] = 'thickbox';
			$anchor['attr']['class'][] = 'no_icon';
		} else {
			$no_thickbox_found;
			foreach ( explode(' ', trim($this->options['no_thickbox'])) as $no_thickbox ) {
				$no_thickbox_found = in_array($no_thickbox, $anchor['attr']['class']);
				if ( $no_thickbox_found )
					break;
			}
			if ( !in_array('thickbox', $anchor['attr']['class']) && !$no_thickbox_found )
				$anchor['attr']['class'][] = 'thickbox';
			if ( !in_array('no_icon', $anchor['attr']['class']) && !in_array('noicon', $anchor['attr']['class']) )
				$anchor['attr']['class'][] = 'no_icon';
		}
		return $anchor;
	}

	/**
	 * image()
	 *
	 * @param array $anchor
	 * @return anchor $anchor
	 **/
	function image($anchor) {
		if ( ($this->options['thickbox_text'] == 'off' && !preg_match("/^\s*<\s*img\s.+?>\s*$/is", $anchor['body']))
			|| ($this->options['thickbox_target'] == 'off' && !empty($anchor['attr']['target'])) )
			return $anchor;

		$anchor = $this->add_thickbox_class($anchor);

		if ( $this->options['thickbox_style'] == 'gallery' && in_the_loop() && !$anchor['attr']['rel'] )
			$anchor['attr']['rel'][] = 'gallery-' . get_the_ID();

		if ( empty($anchor['attr']['title']) ) {
			if ( preg_match("/\b(?:alt|title)\s*=\s*('|\")(.*?)\\1/i", $anchor['body'], $title) ) {
				$anchor['attr']['title'] = end($title);
			}
		}

		return $anchor;
	} # image()

	/**
	 * iframe()
	 *
	 * @return void
	 **/
	function iframe($anchor) {
		if ( strpos($anchor['attr']['href'], 'TB_iframe') !== false || strpos($anchor['attr']['href'], '#TB_inline') !== false )
			return $anchor;
		if ( strpos($anchor['attr']['href'], '://') === false || strpos($anchor['attr']['href'], $_SERVER['HTTP_HOST']) !== false )
			return $anchor; // not append 'TB_iframe' to URL in the same domain (i.e. display as not iframe but AJAX content)

		# strip anchor ref
		$href = explode('#', $anchor['attr']['href']);
		$anchor['attr']['href'] = array_shift($href);

		$anchor['attr']['href'] .= ( ( strpos($anchor['attr']['href'], '?') === false ) ? '?' : '&' )
			. 'TB_iframe' . ( count($href) == 0 ? '' : '#' . implode('#', $href) );

		return $anchor;
	} # iframe()

	/**
	 * scripts()
	 *
	 * @return void
	 **/
	function scripts() {
		if ( $this->options['builtin_res'] == 'off' ) {
			wp_deregister_script('thickbox');
			$in_footer = $this->options['script_place'] == 'footer';
			wp_register_script('thickbox', $this->plugins_url($this->thickbox_js, __FILE__), array('jquery'), AUTO_THICKBOX_PLUS_VERSION, $in_footer);
		}
		wp_enqueue_script('thickbox');
		if ( $this->options['builtin_res'] == 'off' ) {
			wp_localize_script('thickbox', 'thickboxL10n', array(
				'next' => $this->texts['next'],
				'prev' => $this->texts['prev'],
				'first' => $this->texts['first'],
				'last' => $this->texts['last'],
				'image' => $this->texts['image'],
				'of' => $this->texts['of'],
				'close' => $this->texts['close'],
				'noiframes' => __('This feature requires inline frames. You have iframes disabled or your browser does not support them.'),
				'loadingAnimation' => $this->options['img_load'] != 'none' ? $this->options['img_load'] : $this->options_def['img_load'],
				'closeImage' => $this->options['img_close_btn'] != 'none' ? $this->options['img_close_btn'] : $this->options_def['img_close_btn']
			));
		}
	} # scripts()

	/**
	 * styles()
	 *
	 * @return void
	 **/
	function styles() {
		if ( $this->options['builtin_res'] == 'off' ) {
			wp_deregister_style('thickbox');
			wp_register_style('thickbox', $this->plugins_url($this->thickbox_css, __FILE__), false, AUTO_THICKBOX_PLUS_VERSION);
		}
		wp_enqueue_style('thickbox');
	} # styles()

	function footer_scripts() {
		if ( $this->options['wp_gallery'] == 'on') {
?>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(function($) {
	$('div.gallery').each(function() {
		var id = $(this).attr('class').match(/galleryid-\d+/);
		$(this).find('a.thickbox').attr('rel', id);
	});
});
/* ]]> */
</script>
<?php
		}

		if ( $this->options['builtin_res'] == 'on') {
			if ( version_compare('3.2', get_bloginfo('version')) > 0 ) {
				$includes_url = includes_url();
				echo <<<SCRIPT
<script type="text/javascript">
//<![CDATA[
var tb_pathToImage = "{$includes_url}js/thickbox/loadingAnimation.gif";
var tb_closeImage = "{$includes_url}js/thickbox/tb-close.png";
//]]>
</script>
SCRIPT;
			}
			return;
		}

		$script = '';

		if ( !$this->is_default_options('auto_resize') )
			$script .= "tb_options.auto_resize = " . var_export($this->options['auto_resize'] == 'on', true) . ";\n";
		if ( !$this->is_default_options('effect_open') )
			$script .= "tb_options.effect_open = '{$this->options['effect_open']}';\n";
		if ( !$this->is_default_options('effect_close') )
			$script .= "tb_options.effect_close = '{$this->options['effect_close']}';\n";
		if ( !$this->is_default_options('effect_trans') )
			$script .= "tb_options.effect_trans = '{$this->options['effect_trans']}';\n";
		if ( !$this->is_default_options('effect_title') )
			$script .= "tb_options.effect_title = '{$this->options['effect_title']}';\n";
		if ( !$this->is_default_options('effect_cap') )
			$script .= "tb_options.effect_cap = '{$this->options['effect_cap']}';\n";
		if ( !$this->is_default_options('effect_speed') ) {
			$quot = is_numeric($this->options['effect_speed']) ? "" : "'";
			$script .= "tb_options.effect_speed = " . $quot . $this->options['effect_speed'] . $quot . ";\n";
		}
		if ( !$this->is_default_options('click_img') )
			$script .= "tb_options.click_img = '{$this->options['click_img']}';\n";
		if ( !$this->is_default_options('click_end') )
			$script .= "tb_options.click_end = '{$this->options['click_end']}';\n";
		if ( !$this->is_default_options('click_bg') )
			$script .= "tb_options.click_bg = '{$this->options['click_bg']}';\n";
		if ( !$this->is_default_options('wheel_img') )
			$script .= "tb_options.wheel_img = '{$this->options['wheel_img']}';\n";
		if ( !$this->is_default_options('drag_img_move') )
			$script .= "tb_options.move_img = " . var_export($this->options['drag_img_move'] == 'on', true) . ";\n";
		if ( !$this->is_default_options('drag_img_resize') )
			$script .= "tb_options.resize_img = " . var_export($this->options['drag_img_resize'] == 'on', true) . ";\n";
		if ( !$this->is_default_options('drag_content_move') )
			$script .= "tb_options.move_content = " . var_export($this->options['drag_content_move'] == 'on', true) . ";\n";
		if ( !$this->is_default_options('drag_content_resize') )
			$script .= "tb_options.resize_content = " . var_export($this->options['drag_content_resize'] == 'on', true) . ";\n";
		$keys_close = array();
		if ( $this->options['key_close_esc'] == 'on' ) $keys_close[] = 27;
		if ( $this->options['key_close_enter'] == 'on' ) $keys_close[] = 13;
		if ( !$this->is_default_options(array('key_close_esc', 'key_close_enter')) )
			$script .= "tb_options.keys_close = [" . implode(', ', $keys_close) . "];\n";
		$keys_prev = $keys_prev_shift = array();
		if ( $this->options['key_prev_angle'] == 'on' ) $keys_prev[] = 188;
		if ( $this->options['key_prev_left'] == 'on' ) $keys_prev[] = 37;
		if ( $this->options['key_prev_tab'] == 'on' ) $keys_prev_shift[] = 9;
		if ( $this->options['key_prev_space'] == 'on' ) $keys_prev_shift[] = 32;
		if ( $this->options['key_prev_bs'] == 'on' ) $keys_prev[] = 8;
		if ( !$this->is_default_options(array('key_prev_angle', 'key_prev_left', 'key_prev_tab', 'key_prev_space', 'key_prev_bs')) ) {
			$script .= "tb_options.keys_prev = [" . implode(', ', $keys_prev) . "];\n";
			$script .= "tb_options.keys_prev['shift'] = [" . implode(', ', $keys_prev_shift) . "];\n";
		}
		$keys_next = array();
		if ( $this->options['key_next_angle'] == 'on' ) $keys_next[] = 190;
		if ( $this->options['key_next_right'] == 'on' ) $keys_next[] = 39;
		if ( $this->options['key_next_tab'] == 'on' ) $keys_next[] = 9;
		if ( $this->options['key_next_space'] == 'on' ) $keys_next[] = 32;
		if ( !$this->is_default_options(array('key_next_angle', 'key_next_right', 'key_next_tab', 'key_next_space')) )
			$script .= "tb_options.keys_next = [" . implode(', ', $keys_next) . "];\n";
		$keys_first = $keys_last = array();
		if ( $this->options['key_end_home_end'] == 'on' ) { $keys_first[] = 36; $keys_last[] = 35; }
		if ( !$this->is_default_options('key_end_home_end') ) {
			$script .= "tb_options.keys_first = [" . implode(', ', $keys_first) . "];\n";
			$script .= "tb_options.keys_last = [" . implode(', ', $keys_last) . "];\n";
		}

		if ( !$this->is_default_options('position_title') )
			$script .= "tb_options.position_title = '{$this->options['position_title']}';\n";
		if ( !$this->is_default_options('position_cap') )
			$script .= "tb_options.position_cap = '{$this->options['position_cap']}';\n";

		if ( !$this->is_default_options('ref_title') )
			$script .= "tb_options.ref_title = [{$this->options['ref_title']}];\n";
		if ( !$this->is_default_options('ref_cap') )
			$script .= "tb_options.ref_cap = [{$this->options['ref_cap']}];\n";

		if ($script)
			echo "<script type='text/javascript'>\n/* <![CDATA[ */\njQuery(function() {\n$script});\n/* ]]> */\n</script>\n";
	}

	function footer_styles() {
		$style = '';

		if ( !$this->is_default_options('click_range') )
			$style .= "#TB_ImageClick a.TB_ImageLeft,#TB_ImageClick a.TB_ImageRight { width:{$this->options['click_range']}%; }\n";

		if ( !$this->is_default_options('position_win') )
			$style .= "#TB_window { position:{$this->options['position_win']}; }\n";
		if ( !$this->is_default_options('font_title') )
			$style .= "#TB_title { font-family:{$this->options['font_title']}; }\n";
		if ( !$this->is_default_options('font_cap') )
			$style .= "#TB_caption,#TB_secondLine { font-family:{$this->options['font_cap']}; }\n";
		if ( !$this->is_default_options('font_weight_title') )
			$style .= "#TB_title { font-weight:{$this->options['font_weight_title']}; }\n";
		if ( !$this->is_default_options('font_weight_cap') )
			$style .= "#TB_caption { font-weight:{$this->options['font_weight_cap']}; }\n";
		if ( !$this->is_default_options('font_size_title') )
			$style .= "#TB_title { font-size:{$this->options['font_size_title']}px; }\n";
		if ( !$this->is_default_options('font_size_cap') )
			$style .= "#TB_caption { font-size:{$this->options['font_size_cap']}px; }\n";
		if ( !$this->is_default_options('font_size_nav') )
			$style .= "#TB_secondLine { font-size:{$this->options['font_size_nav']}px; }\n";
		if ( !$this->is_default_options('color_title') )
			$style .= "#TB_title { color:{$this->options['color_title']}; }\n";
		if ( !$this->is_default_options('color_nav') )
			$style .= "#TB_secondLine,#TB_secondLine a:link,#TB_secondLine a:visited { color:{$this->options['color_nav']}; }\n";
		if ( !$this->is_default_options('color_cap') )
			$style .= "#TB_caption,#TB_secondLine a:hover { color:{$this->options['color_cap']}; }\n"; // :hover must be placed after :link and :visited
		if ( !$this->is_default_options('bgcolor_title') )
			$style .= "#TB_title { background-color:{$this->options['bgcolor_title']}; }\n";
		if ( !$this->is_default_options('bgcolor_cap') ) {
			$style .= "#TB_caption { background-color:{$this->options['bgcolor_cap']}; -moz-border-radius:4px; -webkit-border-radius:4px; -khtml-border-radius:4px; border-radius:4px; }\n";
			$style .= "#TB_CaptionBar { background-color:{$this->options['bgcolor_cap']}; }\n";
		}
		if ( !$this->is_default_options('bgcolor_img') )
			$style .= "#TB_window.TB_imageContent { background-color:{$this->options['bgcolor_img']}; }\n";
		if ( !$this->is_default_options('bgcolor_content') ) {
			$style .= "#TB_window.TB_ajaxContent,#TB_window.TB_iframeContent,#TB_ajaxContent,#TB_iframeContent,#TB_ajaxContentMarginTop,#TB_ajaxContentMarginBottom { background-color:{$this->options['bgcolor_content']}; }\n";
			$style .= "::-webkit-scrollbar-corner { background-color:{$this->options['bgcolor_content']}; }\n";
		}
		if ( !$this->is_default_options('bgcolor_bg') )
			$style .= ".TB_overlayBG { background-color:{$this->options['bgcolor_bg']}; }\n";
		if ( !$this->is_default_options('margin_img') ) {
			$style .= "#TB_window img#TB_Image { margin:{$this->options['margin_img']}px; }\n";
			$style .= "#TB_caption { margin-left:{$this->options['margin_img']}px; }\n";
			$style .= "#TB_closeWindow { margin-right:{$this->options['margin_img']}px; }\n";
		}
		if ( !$this->is_default_options('border_win') )
			$style .= "#TB_window { border:{$this->options['border_win']}; }\n";
		else {
			if ( !$this->is_default_options('border_width_win') )
				$style .= "#TB_window { border-width:{$this->options['border_width_win']}px; }\n";
			if ( !$this->is_default_options('border_style_win') )
				$style .= "#TB_window { border-style:{$this->options['border_style_win']}; }\n";
			if ( !$this->is_default_options('border_color_win') )
				$style .= "#TB_window { border-color:{$this->options['border_color_win']}; }\n";
		}
		if ( !$this->is_default_options('border_img_tl') )
			$style .= "#TB_window img#TB_Image { border-top:{$this->options['border_img_tl']}; border-left:{$this->options['border_img_tl']}; }\n";
		else {
			if ( !$this->is_default_options('border_width_img_tl') )
				$style .= "#TB_window img#TB_Image { border-top-width:{$this->options['border_width_img_tl']}px; border-left-width:{$this->options['border_width_img_tl']}px; }\n";
			if ( !$this->is_default_options('border_style_img_tl') )
				$style .= "#TB_window img#TB_Image { border-top-style:{$this->options['border_style_img_tl']}; border-left-style:{$this->options['border_style_img_tl']}; }\n";
			if ( !$this->is_default_options('border_color_img_tl') )
				$style .= "#TB_window img#TB_Image { border-top-color:{$this->options['border_color_img_tl']}; border-left-color:{$this->options['border_color_img_tl']}; }\n";
		}
		if ( !$this->is_default_options('border_img_br') )
			$style .= "#TB_window img#TB_Image { border-bottom:{$this->options['border_img_br']}; border-right:{$this->options['border_img_br']}; }\n";
		else {
			if ( !$this->is_default_options('border_width_img_br') )
				$style .= "#TB_window img#TB_Image { border-bottom-width:{$this->options['border_width_img_br']}px; border-right-width:{$this->options['border_width_img_br']}px; }\n";
			if ( !$this->is_default_options('border_style_img_br') )
				$style .= "#TB_window img#TB_Image { border-bottom-style:{$this->options['border_style_img_br']}; border-right-style:{$this->options['border_style_img_br']}; }\n";
			if ( !$this->is_default_options('border_color_img_br') )
				$style .= "#TB_window img#TB_Image { border-bottom-color:{$this->options['border_color_img_br']}; border-right-color:{$this->options['border_color_img_br']}; }\n";
		}
		if ( !$this->is_default_options('border_gallery') )
			$style .= ".gallery img { border:{$this->options['border_gallery']} !important; }\n";
		else {
			if ( !$this->is_default_options('border_width_gallery') )
				$style .= ".gallery img { border-width:{$this->options['border_width_gallery']}px !important; }\n";
			if ( !$this->is_default_options('border_style_gallery') )
				$style .= ".gallery img { border-style:{$this->options['border_style_gallery']} !important; }\n";
			if ( !$this->is_default_options('border_color_gallery') )
				$style .= ".gallery img { border-color:{$this->options['border_color_gallery']} !important; }\n";
		}
		if ( !$this->is_default_options('radius_win') )
			$style .= "#TB_window,#TB_title,#TB_ajaxContent,#TB_iframeContent { -moz-border-radius:{$this->options['radius_win']}px; -webkit-border-radius:{$this->options['radius_win']}px; -khtml-border-radius:{$this->options['radius_win']}px; border-radius:{$this->options['radius_win']}px; }\n";
		if ( !$this->is_default_options('radius_img') )
			$style .= "#TB_Image { -moz-border-radius:{$this->options['radius_img']}px; -webkit-border-radius:{$this->options['radius_img']}px; -khtml-border-radius:{$this->options['radius_img']}px; border-radius:{$this->options['radius_img']}px; }\n";
		if ( !$this->is_default_options('opacity_bg') ) {
			$opacity_bg100 = $this->options['opacity_bg'] * 100;
			$style .= ".TB_overlayBG { -ms-filter:\"progid:DXImageTransform.Microsoft.Alpha(Opacity={$opacity_bg100})\"; filter:alpha(opacity={$opacity_bg100}); -moz-opacity:{$this->options['opacity_bg']}; opacity:{$this->options['opacity_bg']}; }\n";
		}
		if ( !$this->is_default_options('opacity_thumb') ) {
			$opacity_thumb100 = $this->options['opacity_thumb'] * 100;
			$style .= "a.thickbox:hover img { -ms-filter:\"progid:DXImageTransform.Microsoft.Alpha(Opacity={$opacity_thumb100})\"; filter:alpha(opacity={$opacity_thumb100}); -moz-opacity:{$this->options['opacity_thumb']}; opacity:{$this->options['opacity_thumb']}; }\n";
		}
		if ( !$this->is_default_options('box_shadow_win') )
			$style .= "#TB_window { -moz-box-shadow:{$this->options['box_shadow_win']}; -webkit-box-shadow:{$this->options['box_shadow_win']}; -khtml-box-shadow:{$this->options['box_shadow_win']}; box-shadow:{$this->options['box_shadow_win']}; }\n";
		if ( !$this->is_default_options('txt_shadow_title') )
			$style .= "#TB_title { text-shadow:{$this->options['txt_shadow_title']}; }\n";
		if ( !$this->is_default_options('txt_shadow_cap') )
			$style .= "#TB_caption { text-shadow:{$this->options['txt_shadow_cap']}; }\n";

		if ( $this->options['img_prev'] == 'none' )
			$style .= "#TB_ImageClick a#TB_ImagePrev:hover { background-image: none; }\n";
		else if ( !$this->is_default_options('img_prev') )
			$style .= "#TB_ImageClick a#TB_ImagePrev:hover { background-image: url({$this->options['img_prev']}); }\n";
		if ( $this->options['img_next'] == 'none' )
			$style .= "#TB_ImageClick a#TB_ImageNext:hover { background-image: none; }\n";
		else if ( !$this->is_default_options('img_next') )
			$style .= "#TB_ImageClick a#TB_ImageNext:hover { background-image: url({$this->options['img_next']}); }\n";
		if ( $this->options['img_first'] == 'none' )
			$style .= "#TB_ImageClick a#TB_ImageFirst:hover { background-image: none; }\n";
		else if ( !$this->is_default_options('img_first') )
			$style .= "#TB_ImageClick a#TB_ImageFirst:hover { background-image: url({$this->options['img_first']}); }\n";
		if ( $this->options['img_last'] == 'none' )
			$style .= "#TB_ImageClick a#TB_ImageLast:hover { background-image: none; }\n";
		else if ( !$this->is_default_options('img_last') )
			$style .= "#TB_ImageClick a#TB_ImageLast:hover { background-image: url({$this->options['img_last']}); }\n";
		if ( $this->options['img_close'] == 'none' )
			$style .= "#TB_ImageClick a#TB_ImageClose:hover, #TB_ImageClick a#TB_ImageClose2:hover { background-image: none; }\n";
		else if ( !$this->is_default_options('img_close') )
			$style .= "#TB_ImageClick a#TB_ImageClose:hover, #TB_ImageClick a#TB_ImageClose2:hover { background-image: url({$this->options['img_close']}); }\n";
		if ( $this->options['img_close_btn'] == 'none' )
			$style .= "#TB_closeWindow { display: none; }\n";
		if ( $this->options['img_load'] == 'none' )
			$style .= "#TB_load { display: none !important; }\n";
		else if ( !$this->is_default_options('img_load') ) {
			$style .= "#TB_load { padding:15px; margin: 0; }\n";
			$style .= "#TB_load img { vertical-align:middle; }\n";
		}

		if ( !$this->is_default_options('hide_title') )
			$style .= "#TB_title.hover { visibility: hidden; }\n";
		if ( !$this->is_default_options('hide_cap') )
			$style .= "#TB_CaptionBar { visibility: hidden; }\n";

		if ($style)
			echo "<style type='text/css'>\n$style</style>\n";
	}

	function is_default_options($names) {
		if (!is_array($names))
			return $this->options[$names] == $this->options_def[$names];

		foreach ($names as $name) {
			if ($this->options[$name] != $this->options_def[$name])
				return false;
		}
		return true;
	}

	function add_auto_thickbox_action_links($links, $file) {
		if ( $file == plugin_basename(__FILE__) )
			$links[] = '<a href="options-general.php?page=' . $this->base_dir .'">' . __('Settings') . '</a>';
		return $links;
	}

	// Additional links on the Plugins page
	function add_auto_thickbox_links($links, $file) {
		if ( $file == plugin_basename(__FILE__) ) {
			$links[] = '<a href="plugin-install.php?tab=plugin-information&plugin=auto-thickbox-plus&TB_iframe" class="thickbox" title="' . AUTO_THICKBOX_PLUS . '">' . $this->texts['details'] . '</a>';
			$links[] = '<a href="http://wordpress.org/support/plugin/auto-thickbox-plus" target="_blank">' . __('Support', 'auto-thickbox') . '</a>';
			$links[] = '<a href="' . __('http://attosoft.info/en/', 'auto-thickbox') . 'contact/" target="_blank">' . $this->texts['contact'] . '</a>';
			$links[] = '<a href="' . __('https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=D2DLJNSUFBU4U', 'auto-thickbox') . '" target="_blank">' . __('Donate', 'auto-thickbox') . '</a>';
		}
		return $links;
	}

	var $base_dir;
	var $thickbox_js, $thickbox_css;
	var $options, $options_def;
	var $is_en_locale;
	var $texts;

	function auto_thickbox() {
		$this->__construct(); // for PHP4
	}

	function __construct() {
		$this->base_dir = basename(dirname(__FILE__));
		load_plugin_textdomain('auto-thickbox', false, $this->base_dir . '/languages');

		$this->thickbox_js = WP_DEBUG ? 'thickbox.js' :'thickbox.min.js';
		$this->thickbox_css = WP_DEBUG ? 'thickbox.css' : 'thickbox.min.css';

		if ( !is_admin() && isset($_SERVER['HTTP_USER_AGENT']) &&
			strpos($_SERVER['HTTP_USER_AGENT'], 'W3C_Validator') === false) {
			if ( !class_exists('anchor_utils') )
				include dirname(__FILE__) . '/anchor-utils/anchor-utils.php';

			add_action('wp_print_scripts', array(&$this, 'scripts'));
			add_action('wp_print_styles', array(&$this, 'styles'));

			add_action('wp_footer', array(&$this, 'footer_scripts'), 20);
			add_action('wp_footer', array(&$this, 'footer_styles'), 20);

			add_filter('filter_anchor', array(&$this, 'filter'));
		}

		if ( is_admin() ) {
			if (include_once dirname(__FILE__) . '/auto-thickbox-options.php')
				new auto_thickbox_options($this);
			add_filter('plugin_action_links', array(&$this, 'add_auto_thickbox_action_links'), 10, 2);
			add_filter('plugin_row_meta', array(&$this, 'add_auto_thickbox_links'), 10, 2);
		}

		$this->options_def = array(
			'thickbox_style' => 'single',
			'wp_gallery' => 'on',
			'thickbox_text' => 'on',
			'thickbox_target' => 'off',
			'no_thickbox' => 'nothickbox no_thickbox ',
			'auto_resize' => 'on',
			'builtin_res' => 'off',
			'script_place' => 'header',
			'effect_open' => 'none',
			'effect_close' => 'fade',
			'effect_trans' => 'none',
			'effect_title' => 'none',
			'effect_cap' => 'none',
			'hide_title' => 'off',
			'hide_cap' => 'off',
			'effect_speed' => 'fast',
			'click_img' => 'close',
			'click_end' => 'loop',
			'click_range' => '35',
			'click_bg' => 'close',
			'wheel_img' => 'prev_next',
			'drag_img_move' => 'off',
			'drag_img_resize' => 'off',
			'drag_content_move' => 'off',
			'drag_content_resize' => 'off',
			'key_close_esc' => 'on',
			'key_close_enter' => 'on',
			'key_prev_angle' => 'on',
			'key_prev_left' => 'on',
			'key_prev_tab' => 'off',
			'key_prev_space' => 'off',
			'key_prev_bs' => 'off',
			'key_next_angle' => 'on',
			'key_next_right' => 'on',
			'key_next_tab' => 'off',
			'key_next_space' => 'off',
			'key_end_home_end' => 'on',
			'position_title' => 'top',
			'position_cap' => 'bottom',
			'position_win' => 'fixed',
			'font_title' => '"Lucida Grande", Verdana, Arial, sans-serif',
			'font_cap' => '"Lucida Grande", Verdana, Arial, sans-serif',
			'font_weight_title' => 'normal',
			'font_weight_cap' => 'normal',
			'font_size_title' => '12',
			'font_size_cap' => '12',
			'font_size_nav' => '11',
			'color_title' => 'black',
			'color_cap' => 'black',
			'color_nav' => '#666',
			'bgcolor_title' => '#e8e8e8',
			'bgcolor_cap' => 'transparent',
			'bgcolor_img' => 'white',
			'bgcolor_content' => 'white',
			'bgcolor_bg' => 'black',
			'margin_img' => '15',
			'border_win' => '1px solid #555',
			'border_width_win' => '1',
			'border_style_win' => 'solid',
			'border_color_win' => '#555',
			'border_img_tl' => '1px solid #666',
			'border_width_img_tl' => '1',
			'border_style_img_tl' => 'solid',
			'border_color_img_tl' => '#666',
			'border_img_br' => '1px solid #ccc',
			'border_width_img_br' => '1',
			'border_style_img_br' => 'solid',
			'border_color_img_br' => '#ccc',
			'border_gallery' => '2px solid #cfcfcf',
			'border_width_gallery' => '2',
			'border_style_gallery' => 'solid',
			'border_color_gallery' => '#cfcfcf',
			'radius_win' => '0',
			'radius_img' => '0',
			'opacity_bg' => '0.75',
			'opacity_thumb' => '1',
			'box_shadow_win' => 'rgba(0,0,0,1) 0 4px 30px',
			'txt_shadow_title' => 'none',
			'txt_shadow_cap' => 'none',
			'ref_title' => "'link-title','link-name','blank','img-title','img-alt','img-desc','img-name'",
			'ref_cap' => "'link-title','link-name','blank','img-title','img-alt','img-desc','img-name','img-cap'",
			'post_id' => '0',
			'img_prev' => $this->plugins_url('images/tb-prev.png', __FILE__),
			'img_next' => $this->plugins_url('images/tb-next.png', __FILE__),
			'img_first' => $this->plugins_url('images/tb-first.png', __FILE__),
			'img_last' => $this->plugins_url('images/tb-last.png', __FILE__),
			'img_close' => $this->plugins_url('images/tb-close.png', __FILE__),
			'img_close_btn' => $this->plugins_url('images/tb-close.png', __FILE__),
			'img_load' => $this->plugins_url('images/loadingAnimation.gif', __FILE__)
		);
		$this->options = get_option('auto-thickbox-plus');
		$this->options = $this->options ? wp_parse_args($this->options, $this->options_def) : $this->options_def;

		// XXX: transition code for v0.5 or earlier
		$thickbox_style = get_option('thickbox_style');
		if ($thickbox_style) {
			$this->options['thickbox_style'] = $thickbox_style;
			delete_option('thickbox_style');
		}
		$thickbox_text = get_option('thickbox_text');
		if ($thickbox_text) {
			$this->options['thickbox_text'] = $thickbox_text;
			delete_option('thickbox_text');
		}
		$thickbox_res = get_option('thickbox_res');
		if ($thickbox_res) {
			$this->options['thickbox_res'] = $thickbox_res;
			delete_option('thickbox_res');
		}

		$updateOption = false;

		// XXX: transition code for v0.6 or earlier
		if (isset($this->options['thickbox_res'])) {
			$this->options['builtin_res'] = $this->options['thickbox_res'] == 'unload' ? 'on' : 'off';
			unset($this->options['thickbox_res']);
			$updateOption = true;
		}

		// XXX: transition code for between v1.1 and v1.2
		if (isset($this->options['cap_position'])) {
			$this->options['position_cap'] = $this->options['cap_position'];
			unset($this->options['cap_position']);
			$updateOption = true;
		}

		// XXX: transition code for v1.1 or earlier
		if (isset($this->options['bgcolor_win'])) {
			$this->options['bgcolor_img'] = $this->options['bgcolor_win'];
			unset($this->options['bgcolor_win']);
			$updateOption = true;
		}

		// XXX: transition code for v1.5 or earlier
		if ($this->options['thickbox_text'] == 'auto' || $this->options['thickbox_text'] == 'manual') {
			$this->options['thickbox_text'] = $this->options['thickbox_text'] == 'auto' ? 'on' : 'off';
			$updateOption = true;
		}
		if (!$this->is_default_options('border_win') && $this->options['border_win'] != 'none') {
			$values = explode(' ', trim($this->options['border_win']));
			$this->options['border_width_win'] = str_replace('px', '', $values[0]);
			$this->options['border_style_win'] = $values[1];
			$this->options['border_color_win'] = $values[2];
			$updateOption = true;
		}
		if (!$this->is_default_options('border_img_tl') && $this->options['border_img_tl'] != 'none') {
			$values = explode(' ', trim($this->options['border_img_tl']));
			$this->options['border_width_img_tl'] = str_replace('px', '', $values[0]);
			$this->options['border_style_img_tl'] = $values[1];
			$this->options['border_color_img_tl'] = $values[2];
			$updateOption = true;
		}
		if (!$this->is_default_options('border_img_br') && $this->options['border_img_br'] != 'none') {
			$values = explode(' ', trim($this->options['border_img_br']));
			$this->options['border_width_img_br'] = str_replace('px', '', $values[0]);
			$this->options['border_style_img_br'] = $values[1];
			$this->options['border_color_img_br'] = $values[2];
			$updateOption = true;
		}

		// XXX: transition code for v1.6 or earlier
		if (strpos($this->options['ref_cap'], 'gallery-cap') !== false) {
			$this->options['ref_cap'] = str_replace('gallery-cap', 'img-cap', $this->options['ref_cap']);
			$updateOption = true;
		}

		if ($this->is_default_options('post_id')) {
			$args = array(
				'post_status' => 'draft',
				'post_type' => 'auto-thickbox-plus'
			);
			$posts = get_posts($args);
			if (count($posts))
				$this->options['post_id'] = $posts[0]->ID;
			else {
				$args['post_title'] = AUTO_THICKBOX_PLUS;
				$this->options['post_id'] = wp_insert_post($args);
			}
			$updateOption = true;
		}

		if ($updateOption)
			update_option('auto-thickbox-plus', $this->options);

		$this->is_en_locale = strpos(get_locale(), 'en') !== false;
		$this->texts['next'] = $this->gettext('Next &gt;', array('Next &raquo;'));
		$this->texts['next2'] = trim(str_replace(array('&gt;', '&raquo;'), '', $this->texts['next']));
		$this->texts['prev'] = $this->gettext('&lt; Prev', array('&laquo; Previous'));
		$this->texts['prev2'] = trim(str_replace(array('&lt;', '&laquo;'), '', $this->texts['prev']));
		$this->texts['image'] = $this->gettext('Image', array('Images', 'File', 'Files'));
		$this->texts['of'] = $this->gettext('of');
		if (trim($this->texts['of']) == '' || ($this->texts['of'] == 'of' && !$this->is_en_locale))
			$this->texts['of'] = '/';
		$this->texts['close'] = ucfirst($this->gettext('Close', array('close')));
		$full_colon = html_entity_decode('&#xFF1A;', ENT_NOQUOTES, 'UTF-8');
		$this->texts['options'] = $this->gettext('Options:', array('Options', 'Settings'));
		$this->texts['options'] = AUTO_THICKBOX_PLUS . ' ' . str_replace(array(':', $full_colon), '', $this->texts['options']);
		$this->texts['action'] = $this->gettext('Action', array('Actions'));
		$this->texts['load'] = $this->gettext('Loading&#8230;', array('Loading...'));

		$this->texts['first2'] = ucfirst($this->gettext('first', array('First')));
		$this->texts['first'] = '&laquo; ' . $this->texts['first2'];
		$this->texts['last2'] = ucfirst($this->gettext('last', array('Last')));
		$this->texts['last'] = $this->texts['last2'] . ' &raquo;';

		$this->texts['view'] = ucfirst($this->gettext('View', array('view')));
		$this->texts['none'] = ucfirst($this->gettext('None', array('none')));
		$this->texts['loop'] = $this->gettext('Loop');
		$this->texts['shortcuts'] = $this->gettext('Keyboard Shortcuts', array('Keyboard shortcuts'));
		$this->texts['sel_color'] = $this->gettext('Select a Color', array('Select a color'));
		$this->texts['open'] = ucfirst($this->gettext('Open', array('open')));
		$this->texts['details'] = $this->gettext('Show Details', array('Details'));
		$this->texts['contact'] = ucfirst($this->gettext('Contact', array('contact')));
		$this->texts['link'] = ucfirst($this->gettext('Link', array('Links')));
		$this->texts['insert'] = $this->gettext('Insert Image', array('Insert an Image', 'Insert'));
		$this->texts['about'] = $this->gettext('About');
		$this->texts['visit'] = $this->gettext('Visit plugin site', array('Visit plugin homepage'));
	}

	/*
	 * search for translation in 'auto-thickbox' amd default (WordPress)
	 * 
	 * @param string $message msgid key
	 * @param array $alt_messages alternative msgid key
	 */
	function gettext($message, $alt_messages = array()) {
		$text = __($message, 'auto-thickbox');
		if ($text == $message) {
			$text = __($message);
			if ($text == $message && !$this->is_en_locale && $alt_messages) {
				foreach ($alt_messages as $alt_message) {
					$alt_text = __($alt_message);
					if ($alt_text != $alt_message)
						return $alt_text;
				}
			}
		}
		return $text;
	}

	function plugins_url( $path, $plugin ) {
		return version_compare('2.8', get_bloginfo('version')) > 0 ? plugins_url( 'auto-thickbox-plus/' . $path ) : plugins_url ( $path, $plugin );
	}

} # auto_thickbox

add_action('init', 'init_auto_thickbox');
function init_auto_thickbox() {
	new auto_thickbox();
}
?>