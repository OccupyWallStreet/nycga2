<?php
/**
 * 
 *
 *  Typography Control
 *
 *
 *  @package PageLines Framework
 *  @subpackage OptionsUI
 *  @since 2.0.b3
 *
 */

class PageLinesTypeUI {


	/**
	 * Construct
	 */
	function __construct() { 
		
		global $pl_foundry; 

		$this->foundry = $pl_foundry;
		$this->fonts = $pl_foundry->foundry;
	
	}

	/**
	 *
	 *  Main Layout Drag and Drop
	 *
	 */
	function build_typography_control($oid, $o){ 


		$preview_styles = '';

		$preview_styles = $this->foundry->get_type_css( ploption($oid) );
		
		echo OptEngine::input_label( get_pagelines_option_id($oid, 'font'), __('Select Font', 'pagelines'));
		
		$opts = $this->get_opts($oid, $o, pagelines_sub_option($oid, 'font'));
		
		$extra = 'onChange="PageLinesStyleFont(this, \'font-family\')" size="1"';
		
		echo OptEngine::input_select( get_pagelines_option_id($oid, 'font'), get_pagelines_option_name($oid, 'font'), $opts, 'fontselector', $extra);
		
		?>
		<div class="font_preview_wrap">
			<?php echo OptEngine::input_label( '', __('Preview', 'pagelines')); ?>
			<div class="font_preview" >
				<div class="font_preview_pad" style='<?php echo $preview_styles;?>' >
					The quick brown fox jumps over the lazy dog.
				</div>
			</div>
		</div>
		<span id="<?php pagelines_option_id($oid, '_set_styling_button'); ?>" class="button" onClick="PageLinesSimpleToggle('#<?php pagelines_option_id($oid, '_set_styling'); ?>', '#<?php pagelines_option_id($oid, '_set_advanced'); ?>')">Edit Font Styling</span>

		<span id="<?php pagelines_option_id($oid, '_set_advanced_button'); ?>" class="button" onClick="PageLinesSimpleToggle('#<?php pagelines_option_id($oid, '_set_advanced'); ?>', '#<?php pagelines_option_id($oid, '_set_styling'); ?>')">Advanced</span>

		<div id="<?php pagelines_option_id($oid, '_set_styling'); ?>" class="font_styling type_inputs">
			<?php $this->get_type_styles($oid, $o); ?>
			<div class="clear"></div>
		</div>

		<div id="<?php pagelines_option_id($oid, '_set_advanced'); ?>" class="advanced_type type_inputs">
			<?php $this->get_type_advanced($oid, $o); ?>
			<div class="clear"></div>
		</div>


	<?php
	
	
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function fonts_option($oid, $o){
	
	
		echo OptEngine::input_label($o['input_id'], $o['inputlabel']);
		echo OptEngine::input_select($o['input_id'], $o['input_name'], $this->get_opts($oid, $o, $o['val']), 'fontselector');
		
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function get_opts($oid, $o, $val){
		
		$opts = '';
		foreach($this->fonts as $fid => $f){
			$free = (isset($f['free']) && $f['free']) ? true : false;

			if(!VPRO && !$free){
			}else{
				$font_name = $f['name']; 

				if($f['web_safe']) $font_name .= ' *';
				if($f['google']) $font_name .= ' G';
			
				$title = sprintf('title="%s"', $this->foundry->gfont_key($fid));
			
				$opts .= OptEngine::input_option( $fid, selected( $fid, $val, false), $font_name, $f['family'], $title);
			}
			
		}
		
		return $opts;
		
	}


	/**
	*
	* @TODO document
	*
	*/
	function get_type_styles($oid, $o){

		// Set Letter Spacing (em)
		$this->_get_type_em_select($oid, array());

		// Convert to caps, small-caps?
		$this->_get_type_select($oid, array('id' => 'transform', 'inputlabel' => 'Text Transform', 'prop' => 'text-transform',  'selectvalues' => array('none' => 'None', 'uppercase' => 'Uppercase', 'capitalize' => 'Capitalize', 'lowercase' => 'lowercase'), 'default' => 'none'));

		// Small Caps?
		$this->_get_type_select($oid, array('id' => 'variant', 'inputlabel' => 'Variant', 'prop' => 'font-variant',  'selectvalues' => array('normal' => 'Normal', 'small-caps' => 'Small-Caps'), 'default' => 'normal'));

		// Bold? 
		$this->_get_type_select($oid, array('id' => 'weight', 'inputlabel' => 'Weight', 'prop' => 'font-weight', 'selectvalues' => array('normal' => 'Normal', 'bold' => 'Bold', 'lighter' => 'Light'), 'default' => 'normal'));

		// Italic?
		$this->_get_type_select($oid, array('id' => 'style', 'inputlabel' => 'Style', 'prop' => 'font-style',  'selectvalues' => array('normal' => 'Normal', 'italic' => 'Italic'), 'default' => 'normal'));
	}


	/**
	*
	* @TODO document
	*
	*/
	function get_type_advanced($oid, $o){ ?>
		<div class="type_advanced">
			<?php echo OptEngine::input_label( get_pagelines_option_id($oid, 'selectors'), __('Additional Selectors', 'pagelines')); ?>
			<textarea class=""  name="<?php pagelines_option_name($oid, 'selectors'); ?>" id="<?php pagelines_option_id($oid, 'selectors'); ?>" rows="3"><?php esc_attr_e( pagelines_sub_option($oid, 'selectors'), 'pagelines' ); ?></textarea>
		</div>
	<?php }


	/**
	*
	* @TODO document
	*
	*/
	function _get_type_em_select($oid, $o){ 

		$option_value = ( pagelines_sub_option($oid, 'kern') ) ? pagelines_sub_option($oid, 'kern') : '0.00em';
		?>
		<div class="type_select">
			<?php echo OptEngine::input_label( get_pagelines_option_id($oid, 'kern'), __('Letter Spacing', 'pagelines')); ?>
			<select id="<?php pagelines_option_id($oid, 'kern'); ?>" name="<?php pagelines_option_name($oid, 'kern'); ?>" onChange="PageLinesStyleFont(this, 'letter-spacing')">
				<option value="">&mdash;SELECT&mdash;</option>
				<?php 
					$count_start = -.3;
					for($i = $count_start; $i <= 1; $i += 0.05){
						
						$em = number_format(round($i, 2), 2).'em';
						
						printf('<option value="%1$s" %2$s>%1$s</option>', $em, selected($em, $option_value, false));
					}
				?>
			</select>
		</div>
	<?php }


	/**
	*
	* @TODO document
	*
	*/
	function _get_type_select($oid, $o){ 

		$option_value = ( pagelines_sub_option($oid, $o['id']) ) ? pagelines_sub_option($oid, $o['id']) : $o['default'];
		?>
		<div class="type_select">
			<?php echo OptEngine::input_label( get_pagelines_option_id($oid, $o['id']), $o['inputlabel']); ?>
			<select id="<?php pagelines_option_id($oid, $o['id']); ?>" name="<?php pagelines_option_name($oid, $o['id']); ?>" onChange="PageLinesStyleFont(this, '<?php echo $o['prop'];?>')">
				<option value="">&mdash;SELECT&mdash;</option>
				<?php foreach($o['selectvalues'] as $sid => $s):?>
						<option value="<?php echo $sid;?>" <?php selected($sid, $option_value); ?>><?php echo $s;?></option>
				<?php endforeach;?>
			</select>
		</div>
	<?php }
}
