(function($){
	
	/*----------------------------------------------------------------------
	*
	*	vars
	*
	*---------------------------------------------------------------------*/
	var shift_is_down = false;
	var data = {
		action 			:	'get_input_metabox_ids',
		post_id			:	false,
		page_template	:	false,
		page_parent		:	false,
		page_type		:	false,
		page			:	false,
		post			:	false,
		post_category	:	false,
		post_format		:	false
	};
	
	
	/*----------------------------------------------------------------------
	*
	*	Exists
	*
	*---------------------------------------------------------------------*/
	
	$.fn.exists = function()
	{
		return $(this).length>0;
	};
	
		
	/*----------------------------------------------------------------------
	*
	*	Document Ready
	*
	*---------------------------------------------------------------------*/
	
	$(document).ready(function(){
		
		// vars
		var post_id = $('input#post_ID').val();
	
		// show metaboxes for this post
		data = {
			action 			:	'get_input_metabox_ids',
			post_id			:	post_id,
			page_template	:	false,
			page_parent		:	false,
			page_type		:	false,
			page			:	post_id,
			post			:	post_id,
			post_category	:	false,
			post_format		:	false,
			taxonomy		:	false
		};
		
		// update fields from ajax response
		function update_fields()
		{		
			$.ajax({
				url: ajaxurl,
				data: data,
				type: 'post',
				dataType: 'json',
				success: function(result){
					
					// hide all metaboxes
					$('#poststuff .acf_postbox').hide();
					$('#adv-settings .acf_hide_label').hide();
					
					// show the new postboxes
					$.each(result, function(k, v) {
						$('#poststuff #acf_' + v).show();
						$('#adv-settings .acf_hide_label[for="acf_' + v + '-hide"]').show();
					});
					
					// load style
					$.ajax({
						url: ajaxurl,
						data: {
							action : 'get_input_style',
							acf_id : result[0]
						},
						type: 'post',
						dataType: 'html',
						success: function(result){
						
							$('#acf_style').html(result);
							
						}
					});
					
				}
			});
		}
		//update_fields();
		
		// hide acf stuff
		/*$('#poststuff .acf_postbox').hide();
		$('#adv-settings .acf_hide_label').hide();
		
		// show acf?
		$('#poststuff .acf_postbox').each(function(){
			
			// vars
			var show = $(this).children('.inside').children('.options').attr('data-show');
			var id = $(this).attr('id').replace('acf_', '');
			
			if(show == 'true')
			{
				$(this).show();
				$('#adv-settings .acf_hide_label[for="acf_' + id + '-hide"]').show();
			}
			
		});*/
		
		
		// on save, delete all unused metaboxes
		$('input#publish').click(function(){
			
			// do validation?
			$('#post-body .acf_postbox:hidden').remove();
			
			return true;
		});
		
		/*--------------------------------------------------------------------------------------
		*
		*	Change
		*
		*-------------------------------------------------------------------------------------*/
	
		$('#page_template').change(function(){
			
			data.page_template = $(this).val();
			update_fields();
		    
		});
		
		$('#parent_id').change(function(){
			
			var page_parent = $(this).val();
			
			if($(this).val() != "")
			{
				data.page_type = 'child';
			}
			else
			{
				data.page_type = 'parent';
			}
			
			update_fields();
		    
		});
		
		$('#categorychecklist input[type="checkbox"]').change(function(){
			
			data.post_category = ['0'];
			
			$('#categorychecklist :checked').each(function(){
				data.post_category.push($(this).val())
			});
			
			//console.log(data.post_category);
					
			update_fields();
			
		});	
		
		
		$('#post-formats-select input[type="radio"]').change(function(){
			
			data.post_format = $(this).val();
			update_fields();
			
		});	
		
		// taxonomy
		$('div[id*="taxonomy-"] input[type="checkbox"]').change(function(){
			
			data.taxonomy = ['0'];
			
			$(this).closest('ul').find('input[type="checkbox"]:checked').each(function(){
				data.taxonomy.push($(this).val())
			});

			update_fields();
			
		});	
		
	});
	
	
	
})(jQuery);