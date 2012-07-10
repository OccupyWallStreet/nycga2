jQuery(function(){
	jQuery('.handle_div')
	.each(function(){
		jQuery(this).hover(function(){
			jQuery(this).find('h2').addClass('collapse');
		}, function(){
			jQuery(this).find('h2').removeClass('collapse');
		})
		.click(function(){
			jQuery(this).closest('.dragbox').find('.dragbox-content').toggle();
			bp_my_home_ajax_store_state();
		})
		.end()
	});
	
	jQuery('.dragbox')
	.each(function(){
		jQuery(this).find('h2').hover(function(){
			jQuery(this).find('.configure').css('visibility', 'visible');
		}, function(){
			jQuery(this).find('.configure').css('visibility', 'hidden');
		})
		.end()
		jQuery(this).find('.configure').css('visibility', 'hidden');
	});
	
	jQuery('.column').sortable({
		connectWith: '.column',
		handle: 'h2',
		cursor: 'move',
		placeholder: 'placeholder',
		forcePlaceholderSize: true,
		opacity: 0.4,
		start: function(event, ui){
					//Firefox, Safari/Chrome fire click event after drag is complete, fix for that
					if(jQuery.browser.mozilla || jQuery.browser.safari) jQuery(ui.item).find('.dragbox-content').toggle();
				},
		stop: function(event, ui){
					if(!jQuery.browser.mozilla && !jQuery.browser.safari) ui.item.css({'top':'0','left':'0'}); //Opera fix
					if(jQuery.browser.mozilla || jQuery.browser.safari) jQuery(ui.item).find('.dragbox-content').toggle();
					bp_my_home_ajax_store_state();
		}
	})
	.disableSelection();
});
/*
Ajax
*/
function bp_my_home_ajax_store_state() {
	
	var items="";
	jQuery('.column').each(function(){
		var columnId=jQuery(this).attr('id');
		jQuery('.dragbox', this).each(function(i){
			var collapsed=0;
			if(jQuery(this).find('.dragbox-content').css('display')=="none")
				collapsed=1;
			//Create Item object for current panel
			var item= 'id|'+jQuery(this).attr('id')+']'+ 'collapsed|'+collapsed+']'+'order|'+i+']'+'column|'+columnId+'[';
			//Push item object into items array
			items+=item;
			});
		});
		//Assign items array to sortorder JSON variable
		var sortorder=items;
	//Ajax call
	var data = {
		action: 'bp_my_home_save_state',
		state: sortorder
	};
	jQuery.post(ajaxurl, data, function(response) {
		//this message was pretty annoying !
		//if(response!="ok") alert(response);
	});
}