<?php

class acf_Image extends acf_Field
{
	
	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	*	@updated 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function __construct($parent)
	{
    	parent::__construct($parent);
    	
    	$this->name = 'image';
		$this->title = __('Image','acf');
		
		add_action('admin_head-media-upload-popup', array($this, 'popup_head'));
		add_filter('media_send_to_editor', array($this, 'media_send_to_editor'), 15, 2 );
		add_filter('get_media_item_args', array($this, 'allow_img_insertion'));
   	}
   	
   	
   	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_scripts / admin_print_styles
	*
	*	@author Elliot Condon
	*	@since 3.0.1
	* 
	*-------------------------------------------------------------------------------------*/
	
	function allow_img_insertion($vars)
	{
	    $vars['send'] = true;
	    return($vars);
	}
	
   	
   	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_scripts / admin_print_styles
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_print_scripts()
	{
		wp_enqueue_script(array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-tabs',

			'thickbox',
			'media-upload',			
		));
	}
	
	function admin_print_styles()
	{
  		wp_enqueue_style(array(
			'thickbox',		
		));
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_head
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_head()
	{
		?>
		<script type="text/javascript">
		
		(function($){
		
			$('#poststuff .acf_image_uploader .button').live('click', function(){
				
				// vars
				var div = $(this).closest('.acf_image_uploader');
				var post_id = $('input#post_ID').val();
				var preview_size = div.attr('data-preview_size');
				
				// set global var
				window.acf_div = div;
					
				// show the thickbox
				tb_show('Add Image to field', 'media-upload.php?post_id=' + post_id + '&type=image&acf_type=image&acf_preview_size=' + preview_size + 'TB_iframe=1');
			
				return false;
			});
				
			$('#poststuff .acf_image_uploader .remove_image').live('click', function(){
				
				// vars
				var div = $(this).closest('.acf_image_uploader');
				
				div.find('input.value').val('');
				div.removeClass('active');
				
				return false;
				
			});
				
		})(jQuery);
		</script>
		<?php
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*
	*	@author Elliot Condon
	*	@since 2.0.5
	*	@updated 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
	{
		// vars
		$class = "";
		$file_src = "";
		$preview_size = isset($field['preview_size']) ? $field['preview_size'] : 'medium';
		
		// get image url
		if($field['value'] != '' && is_numeric($field['value']))
		{
			$file_src = wp_get_attachment_image_src($field['value'], $preview_size);
			$file_src = $file_src[0];
			
			if($file_src) $class = "active";
		}
		
		// html
		echo '<div class="acf_image_uploader ' . $class . '" data-preview_size="' . $preview_size . '">';
			echo '<a href="#" class="remove_image"></a>';
			echo '<img src="' . $file_src . '" alt=""/>';	
			echo '<input class="value" type="hidden" name="' . $field['name'] . '" value="' . $field['value'] . '" />';
			echo '<p>'.__('No image selected','acf').'. <input type="button" class="button" value="'.__('Add Image','acf').'" /></p>';
		echo '</div>';
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_options
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	*	@updated 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_options($key, $field)
	{	
		// vars
		$field['save_format'] = isset($field['save_format']) ? $field['save_format'] : 'url';
		$field['preview_size'] = isset($field['preview_size']) ? $field['preview_size'] : 'thumbnail';
		
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Return Value",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][save_format]',
					'value'	=>	$field['save_format'],
					'layout'	=>	'horizontal',
					'choices' => array(
						'url'	=>	'Image URL',
						'id'	=>	'Attachment ID'
					)
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Preview Size",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][preview_size]',
					'value'	=>	$field['preview_size'],
					'layout'	=>	'horizontal',
					'choices' => array(
						'thumbnail'	=>	'Thumbnail',
						'medium'	=>	'Medium',
						'large'		=>	'Large',
						'full'		=>	'Full'
					)
				));
				?>
			</td>
		</tr>

		<?php
	}


	 
	/*---------------------------------------------------------------------------------------------
	 * popup_head - STYLES MEDIA THICKBOX
	 *
	 * @author Elliot Condon
	 * @since 1.1.4
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function popup_head()
	{
		if(isset($_GET["acf_type"]) && $_GET['acf_type'] == 'image')
		{
			$preview_size = isset($arr_postinfo['preview_size']) ? $arr_postinfo['preview_size'] : 'medium';
			
			?>
			<style type="text/css">
				#media-upload-header #sidemenu li#tab-type_url,
				#media-upload-header #sidemenu li#tab-gallery {
					display: none;
				}
				
				#media-items tr.url,
				#media-items tr.align,
				#media-items tr.image_alt,
				#media-items tr.image-size,
				#media-items tr.post_excerpt,
				#media-items tr.post_content,
				#media-items tr.image_alt p,
				#media-items table thead input.button,
				#media-items table thead img.imgedit-wait-spin,
				#media-items tr.submit a.wp-post-thumbnail {
					display: none;
				} 

				.media-item table thead img {
					border: #DFDFDF solid 1px; 
					margin-right: 10px;
				}

			</style>
			<script type="text/javascript">
			(function($){
			
				$(document).ready(function(){
				
					$('#media-items').bind('DOMNodeInserted',function(){
						$('input[value="Insert into Post"]').each(function(){
							$(this).attr('value','<?php _e("Select Image",'acf'); ?>');
						});
					}).trigger('DOMNodeInserted');
					
					$('form#filter').each(function(){
						
						$(this).append('<input type="hidden" name="acf_preview_size" value="<?php echo $preview_size; ?>" />');
						$(this).append('<input type="hidden" name="acf_type" value="image" />');
						
					});
				});
							
			})(jQuery);
			</script>
			<?php
		}
	}
	
	
	/*---------------------------------------------------------------------------------------------
	 * media_send_to_editor - SEND IMAGE TO ACF DIV
	 *
	 * @author Elliot Condon
	 * @since 1.1.4
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function media_send_to_editor($html, $id)
	{
		parse_str($_POST["_wp_http_referer"], $arr_postinfo);
		
		if(isset($arr_postinfo["acf_type"]) && $arr_postinfo["acf_type"] == "image")
		{
			
			$preview_size = isset($arr_postinfo['acf_preview_size']) ? $arr_postinfo['acf_preview_size'] : 'medium';
			
			$file_src = wp_get_attachment_image_src($id, $preview_size);
			$file_src = $file_src[0];
		
			?>
			<script type="text/javascript">
				
				self.parent.acf_div.find('input.value').val('<?php echo $id; ?>');
			 	self.parent.acf_div.find('img').attr('src','<?php echo $file_src; ?>');
			 	self.parent.acf_div.addClass('active');
			 	
			 	// reset acf_div and return false
			 	self.parent.acf_div = null;
			 	self.parent.tb_remove();
				
			</script>
			<?php
			exit;
		} 
		else 
		{
			return $html;
		}

	}
	

	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value_for_api($post_id, $field)
	{
		// vars
		$format = isset($field['save_format']) ? $field['save_format'] : 'url';
		
		$value = parent::get_value($post_id, $field);
		
		if($format == 'url')
		{
			$value = wp_get_attachment_url($value);
		}
		
		return $value;
	}
	
	
		
}

?>