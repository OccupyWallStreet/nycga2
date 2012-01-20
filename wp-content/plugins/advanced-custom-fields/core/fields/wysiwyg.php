<?php

class acf_Wysiwyg extends acf_Field
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
    	
    	$this->name = 'wysiwyg';
		$this->title = __("Wysiwyg Editor",'acf');
		
		add_action('admin_head', array($this, 'add_tiny_mce'));
		
   	}
   	
   	
   	/*--------------------------------------------------------------------------------------
	*
	*	add_tiny_mce
	*
	*	@author Elliot Condon
	*	@since 3.0.3
	*	@updated 3.0.3
	* 
	*-------------------------------------------------------------------------------------*/
   	
   	function add_tiny_mce()
   	{
   		global $post;
   		
   		if($post && post_type_supports($post->post_type, 'editor'))
   		{
   			// do nothing, wysiwyg will render correctly!
   		}
   		else
   		{
   			wp_tiny_mce();
   		}
		
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

			// wysiwyg
			'editor',
			'thickbox',
			'media-upload',
			'word-count',
			'post',
			'editor-functions',
			'tiny_mce',
						
		));
	}
	
	function admin_print_styles()
	{
  		wp_enqueue_style(array(
  			'editor-buttons',
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
			
			// store wysiwyg buttons
			$.acf_wysiwyg_buttons = {};
			

			$.fn.acf_deactivate_wysiwyg = function(){

				$(this).find('.acf_wysiwyg textarea').each(function(){

					tinyMCE.execCommand("mceRemoveControl", false, $(this).attr('id'));
					
				});
				
			};
			
			
			$.fn.acf_activate_wysiwyg = function(){
				
				// tinymce must exist
				if(!typeof(tinyMCE) == "object")
				{
					return false;
				}
	
				
				
					
				// add tinymce to all wysiwyg fields
				$(this).find('.acf_wysiwyg textarea').each(function(){
					
					// reset buttons
					tinyMCE.settings.theme_advanced_buttons1 = $.acf_wysiwyg_buttons.theme_advanced_buttons1;
					tinyMCE.settings.theme_advanced_buttons2 = $.acf_wysiwyg_buttons.theme_advanced_buttons2;
				
					var toolbar = $(this).closest('.acf_wysiwyg').attr('data-toolbar');
					
					if(toolbar == 'basic')
					{
						tinyMCE.settings.theme_advanced_buttons1 = "bold,italic,formatselect,|,link,unlink,|,bullist,numlist,|,undo,redo";
						tinyMCE.settings.theme_advanced_buttons2 = "";
					}
					else
					{
						// add images + code buttons
						tinyMCE.settings.theme_advanced_buttons2 += ",code";
					}
					

					//console.log( $(this).attr('id') + ': before: ' + tinyMCE.settings.theme_advanced_buttons1);
					//tinyMCE.execCommand("mceRemoveControl", false, $(this).attr('id'));
					tinyMCE.execCommand('mceAddControl', false, $(this).attr('id'));


				});

				
			};
			
			
			$(window).load(function(){
				
				// store variables
				$.acf_wysiwyg_buttons.theme_advanced_buttons1 = tinyMCE.settings.theme_advanced_buttons1;
				$.acf_wysiwyg_buttons.theme_advanced_buttons2 = tinyMCE.settings.theme_advanced_buttons2;
				
				$('#poststuff').acf_activate_wysiwyg();
				
			});
			
			// Sortable: Start
			$('#poststuff .repeater > table > tbody, #poststuff .acf_flexible_content > .values').live( "sortstart", function(event, ui) {
				
				$(ui.item).find('.acf_wysiwyg textarea').each(function(){
					tinyMCE.execCommand("mceRemoveControl", false, $(this).attr('id'));
				});
				
			});
			
			// Sortable: End
			$('#poststuff .repeater > table > tbody, #poststuff .acf_flexible_content > .values').live( "sortstop", function(event, ui) {
				
				$(ui.item).find('.acf_wysiwyg textarea').each(function(){
					tinyMCE.execCommand("mceAddControl", false, $(this).attr('id'));
				});
				
			});
			
			// Delete
			$('#poststuff .repeater a.remove_field').live('click', function(event){
				
				var tr = $(event.target).closest('tr').find('.acf_wysiwyg textarea').each(function(){
					tinyMCE.execCommand("mceRemoveControl", false, $(this).attr('id'));
				});				
			});
			
			
		})(jQuery);
		</script>
		<style type="text/css">
			.acf_wysiwyg iframe{ 
				min-height: 250px;
			}
			
			#post-body .acf_wysiwyg .wp_themeSkin .mceStatusbar a.mceResize {
				top: -2px !important;
			}
			
			.acf_wysiwyg .editor-toolbar {
				display: none;
			}
			
			.acf_wysiwyg #editorcontainer {
				background: #fff;
			}
		</style>
		<?php
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
		$field['toolbar'] = isset($field['toolbar']) ? $field['toolbar'] : 'full';
		$field['media_upload'] = isset($field['media_upload']) ? $field['media_upload'] : 'yes';
		
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Toolbar",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][toolbar]',
					'value'	=>	$field['toolbar'],
					'layout'	=>	'horizontal',
					'choices' => array(
						'full'	=>	'Full',
						'basic'	=>	'Basic'
					)
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Show Media Upload Buttons?",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][media_upload]',
					'value'	=>	$field['media_upload'],
					'layout'	=>	'horizontal',
					'choices' => array(
						'yes'	=>	'Yes',
						'no'	=>	'No',
					)
				));
				?>
			</td>
		</tr>
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
		$field['toolbar'] = isset($field['toolbar']) ? $field['toolbar'] : 'full';
		$field['media_upload'] = isset($field['media_upload']) ? $field['media_upload'] : 'yes';
		
		$id = 'wysiwyg_' . uniqid();
		
		$version = get_bloginfo('version');
		
		?>
		<?php if(version_compare($version,'3.2.1') > 0): ?>
			
		<?php else: ?>
			
		<?php endif; ?>
		
		<div class="acf_wysiwyg wp-editor-wrap" data-toolbar="<?php echo $field['toolbar']; ?>">
			<?php if($field['media_upload'] == 'yes'): ?>
				<?php if(version_compare($version,'3.2.1') > 0): ?>
					<div id="wp-content-editor-tools" class="wp-editor-tools">
						<div class="hide-if-no-js wp-media-buttons">
							<?php do_action( 'media_buttons' ); ?>
						</div>
					</div>
				<?php else: ?>
					<div id="editor-toolbar">
						<div id="media-buttons" class="hide-if-no-js">
							<?php do_action( 'media_buttons' ); ?>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<div id="editorcontainer" class="wp-editor-container">
				<textarea id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" ><?php echo wp_richedit_pre($field['value']); ?></textarea>
			</div>
		</div>
		<?php

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
		$value = parent::get_value($post_id, $field);
		
		$value = apply_filters('the_content',$value); 
		
		return $value;
	}
	

}

?>