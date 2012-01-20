<?php

class acf_File extends acf_Field
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
    	
    	$this->name = 'file';
		$this->title = __('File','acf');
		
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
			
			$('#poststuff .acf_file_uploader .no_file .button').live('click', function(){
				
				// vars
				var div = $(this).closest('.acf_file_uploader');
				var post_id = $('input#post_ID').val();
				
				// set global var
				window.acf_div = div;
					
				// show the thickbox
				tb_show('Add Image to field', 'media-upload.php?post_id=' + post_id + '&type=file&acf_type=file&TB_iframe=1');
			
				return false;
			});
				
			$('#poststuff .acf_file_uploader .file .button').live('click', function(){
				
				// vars
				var div = $(this).closest('.acf_file_uploader');
				
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
		
		// get file url
		if($field['value'] != '' && is_numeric($field['value']))
		{
			$file_src = wp_get_attachment_url($field['value']);
			if($file_src) $class = "active";
		}
				
		// html
		echo '<div class="acf_file_uploader ' . $class . '">';
			echo '<p class="file"><span class="file_url">'.$file_src.'</span> <input type="button" class="button" value="'.__('Remove File','acf').'" /></p>';
			echo '<input class="value" type="hidden" name="' . $field['name'] . '" value="' . $field['value'] . '" />';
			echo '<p class="no_file">'.__('No File selected','acf').'. <input type="button" class="button" value="'.__('Add File','acf').'" /></p>';
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
						'url'	=>	'File URL',
						'id'	=>	'Attachment ID'
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
		if(isset($_GET["acf_type"]) && $_GET['acf_type'] == 'file')
		{
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
							$(this).attr('value','<?php _e("Select File",'acf'); ?>');
						});
					}).trigger('DOMNodeInserted');
					
					$('form#filter').each(function(){
						
						$(this).append('<input type="hidden" name="acf_type" value="file" />');
						
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
		
		if(isset($arr_postinfo["acf_type"]) && $arr_postinfo["acf_type"] == "file")
		{

			$file_src = wp_get_attachment_url($id);
		
			?>
			<script type="text/javascript">
				
				self.parent.acf_div.find('input.value').val('<?php echo $id; ?>');
			 	self.parent.acf_div.find('span.file_url').text('<?php echo $file_src; ?>');
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