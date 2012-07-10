/*
 * ###########################
 *   Sortable Sections
 * ###########################
 */
	function setSortable( selected_builder ){
		
		setEmpty(".selected_builder #sortable_template");
		setEmpty(".selected_builder #sortable_sections.sortcolumn");
	
		jQuery(".selected_builder #sortable_template").sortable({ 
				connectWith: '.connectedSortable',
				cancel: '.required-section',
				
				items: 'li:not(.bank_title)',
				receive: function(event, ui) {
					
					jQuery(this).find('.section-controls-toggle:hidden').fadeIn();
					
					var sectionID = jQuery(ui.item).attr("id");
					
					handlePlace(selected_builder, sectionID);
					
				},
				update: function(event, ui) {
				
					
					saveSectionOrder( selected_builder );				
		        }                                         
		    }
		);
		
		jQuery(".selected_builder #sortable_sections").sortable({ 
				connectWith: '.connectedSortable',
				cancel: '.required-section',
				
				items: 'li:not(.bank_title)',
				
				update: function() { 
					setEmpty(".selected_builder #sortable_sections"); 
				}                                         
		});
		
		jQuery(".selected_builder #sortable_template, .selected_builder #sortable_sections").disableSelection();	
	}
	
	

	/**
	*
	* @TODO document
	*
	*/
	function setEmpty( sortablelist ){
		
		if( !jQuery(sortablelist).has('.section-bar').length ){
			jQuery(sortablelist).addClass('nosections');
		} else {
			jQuery(sortablelist).removeClass('nosections');
		}
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function handlePlace( selected_builder, sectionID ){
		
		var prefix = '.selected_builder #';
		var newID;
		
		var bArea = selected_builder.split('-');
		var sArea = bArea[0];
		var sTemplate = bArea[1];
		
		var exp = sectionID.split('ID');
		var section = exp[0];

		if(sArea != 'main' && sArea != 'templates')
			return;
		
		var oArea = (sArea == 'main') ? 'templates' : 'main';
		
		var interface_prefix = '.the_template_builder .'+oArea+'-'+sTemplate+' .template_layout #';
		
		$unique_id = false;
		$set_new_id = false;
		
		$i = 2;
		
		while( !$unique_id ){
			
			if( !jQuery( interface_prefix + section ).exists() ){
				
				$unique_id = true;
				
			} else if( !jQuery( interface_prefix + section + 'ID' + $i ).exists() ){
				
				$set_new_id = true;
				
				$unique_id = true;

			} else {

				$i++;

			 }

		}
		
		if($set_new_id == true){
			
			newID = section+ 'ID' + $i;
			
			jQuery( prefix+sectionID ).attr( 'id', newID );

			jQuery( prefix+newID ).find( '.the_clone_id' ).html( '#'+$i );
			
		}
		
		
	
	}
	


	/**
	*
	* @TODO document
	*
	*/
	function cloneSection( sectionId ){
		
		var selected_builder = jQuery( '.selected_builder .template-slug' ).attr('id');
		var prefix = '.selected_builder #';
		var interface_prefix = '.the_template_builder #';
		
		var exp = sectionId.split('ID');
		var section = exp[0];
		
		$new_clone_id = false;
		$i = 2;
		
		while( !$new_clone_id ){
			
			if( !jQuery( interface_prefix + section + 'ID' + $i ).exists() ){
				
				$new_clone_id = true;
				
			} else {
				
				$i++;
			
			 }
			
		}

		var newID = section+ 'ID' + $i;
		
		jQuery( prefix+sectionId ).clone().hide().insertAfter( prefix+sectionId ).attr( 'id', newID );
		
		jQuery( prefix+newID ).find( '.the_clone_id' ).html( '#'+$i );
		
		jQuery( prefix+newID ).find( '.section-controls' ).hide();
		jQuery( prefix+newID ).find( '.clone_remove' ).show();
		
		jQuery( prefix+newID ).slideDown();
		
		saveSectionOrder( selected_builder );	
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function deleteSection( clicked ){
		
		var selected_builder = jQuery( '.selected_builder .template-slug' ).attr('id');
		var element = jQuery(clicked).parent().parent().parent().parent();
		
		element.slideUp('fast').attr('id', '');
		
		

		saveSectionOrder( selected_builder );	
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function saveSectionOrder( selected_builder ){
		var saveText = jQuery( '.selected_builder .confirm_save_pad' );
		
		setEmpty( ".selected_builder #sortable_template" );
		setEmpty(".selected_builder #sortable_sections");
		
        var order = jQuery('.selected_builder #sortable_template').sortable('serialize');
      
		var data = {
				action: 'pagelines_save_sortable',
				orderdata: order,
				template: selected_builder, 
				field: 'sections'
			};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.ajax({
			type: 'GET',
			url: ajaxurl,
			data: data,
			beforeSend: function(){ 
				TemplateSetupStartSave();
			},
			success: function(response) {
				TemplateSetupDoneSaving( 'Section Order Saved!' );
			}
		});
	}


/*
 * ###########################
 *   Template Builder Select
 * ###########################
 */
jQuery(document).ready(function(){

		
	// Set up Default State	
		var subTemplateCookie = jQuery.cookie('subTemplateCookie');
		var buildAreaCookie = jQuery.cookie('buildAreaCookie');
	
		var defaultTemplate = (subTemplateCookie == null) ? 'templates-default' : subTemplateCookie;
		var buildArea = (buildAreaCookie == null) ? 'ta-header' : buildAreaCookie;

		//alert(subTemplateCookie);
		jQuery('.sub-template-selector #'+defaultTemplate).addClass('sss-selected');
		
		if( buildArea == 'ta-templates' )
			doTemplatesSelect( defaultTemplate, 'sel-templates-sub');
		else if( buildArea == 'ta-content' )
			doTemplatesSelect( defaultTemplate, 'sel-content-sub');
		else
			doAreaSelect( buildArea );
		
		jQuery('#'+buildArea).addClass('builder_selected_area');
	
	
	
	// when a user clicks, highlight the area; slide up the sub selector panels (if they're open)
	jQuery('.tg-format').click(function() {
		
		if(!jQuery(this).hasClass('pro-area')) {
			
			// For select interface selection
			jQuery('.builder_selected_area').removeClass('builder_selected_area');
		
			jQuery(this).addClass('builder_selected_area');
		
			if(!jQuery(this).hasClass('tg-templates')) 
				jQuery('.sel-templates-sub.sub-template-selector').slideUp();
			
			if(!jQuery(this).hasClass('tg-content-templates')) 
				jQuery('.sel-content-sub.sub-template-selector').slideUp();
			
			var stemplate_id = jQuery(this).attr('id');
		
			jQuery.cookie('buildAreaCookie', stemplate_id);
		
		}
	});
	
	jQuery('.sss-button').click(function() {
		// For select interface selection
		jQuery('.sss-selected').removeClass('sss-selected');
		jQuery(this).addClass('sss-selected');
		
		var stemplate = jQuery(this).attr('id');
		
		jQuery.cookie('subTemplateCookie', stemplate);
		
		viewAndSort(stemplate);
	});
	
	// Load the ID of the element if it has a load build class on it
	jQuery('.load-build').click(function() {
		
		if(!jQuery(this).hasClass('pro-area')) {
			var stemplate_id = jQuery(this).attr('id');
			doAreaSelect(stemplate_id);
		}
	});
	
	jQuery('.tg-templates').click(function() {
		var selectedButton = jQuery('.sub-template-selector .sss-selected').attr('id');
		
		var exp = selectedButton.split('-');
		var stemplate = 'templates-'+exp[1];
		jQuery.cookie('subTemplateCookie', stemplate);
		doTemplatesSelect( stemplate, 'sel-templates-sub');
	});
	
	jQuery('.tg-content-templates').click(function() {
		var selectedButton = jQuery('.sub-template-selector .sss-selected').attr('id');
		
		var exp = selectedButton.split('-');
		var stemplate = 'main-'+exp[1];
		jQuery.cookie('subTemplateCookie', stemplate);
		doTemplatesSelect( stemplate, 'sel-content-sub');
		
	});
});

/**
*
* @TODO do
*
*/
function doAreaSelect( stemplate_id ){
	var stemplate = stemplate_id.replace('ta-', '');
	viewAndSort(stemplate);
}

/**
*
* @TODO do
*
*/
function doTemplatesSelect( stemplate, panel ){
	jQuery('.sss-selected').removeClass('sss-selected');
	jQuery('.sub-template-selector #'+stemplate).addClass('sss-selected');
	jQuery('.'+panel+'.sub-template-selector').slideDown();

	viewAndSort(stemplate);
}

/**
*
* @TODO do
*
*/
function viewAndSort( stemplate ){
	jQuery('.selected_builder').removeClass('selected_builder');
	jQuery('.'+stemplate).addClass('selected_builder');

	setSortable(stemplate);
}

/**
*
* @TODO do
*
*/
function toggleControls(button){
	jQuery(button).parent().parent().next('.section-controls').slideToggle('fast');
	
}

/*
 * ###########################
 *   Layout Control
 * ###########################
 */


		/**
		*
		* @TODO document
		*
		*/
		function LayoutSelectControl (ClickedLayout){
			jQuery(ClickedLayout).parent().parent().find('.layout-image-border').removeClass('selectedlayout');
			jQuery(ClickedLayout).addClass('selectedlayout');
			jQuery(ClickedLayout).parent().find('.layoutinput').attr("checked", "checked");
		}


		/**
		*
		* @TODO document
		*
		*/
		function deactivateCurrentBuilder() {
			// Deactivate old builder
			jQuery('.layout_controls').find('.layouteditor').removeClass('selectededitor');
			if ( window['OuterLayout'] ) window['OuterLayout'].destroy();
			if ( window['InnerLayout'] ) window['InnerLayout'].destroy();
		}


		/**
		*
		* @TODO document
		*
		*/
		function updateDimensions( LayoutMode, Source ) {
			var contentwidth = jQuery("."+LayoutMode+"  #contentwidth").width() * 2 - 24;
			var builderwidth = jQuery(".layout-main-content").width() * 2;
			var contentpercent = (contentwidth / builderwidth) * 100;
			var innereastwidth = jQuery("."+LayoutMode+"  .innereast").width() * 2;
			var innerwestwidth = jQuery("."+LayoutMode+"  .innerwest").width() * 2;
			var gutterwidth = (jQuery("."+LayoutMode+" #innerlayout .gutter").width()+2) * 2;
				
			
			// Don't trigger if content is 0px wide. This means the function was triggered in error or by a browser quirk. (e.g. dragging a tab in Firefox)
			if( contentwidth > 0 ){
				
				if(LayoutMode == 'one-sidebar-right' || LayoutMode == 'one-sidebar-left'){var ngutters = 1;}
				else if (LayoutMode == 'two-sidebar-right' || LayoutMode == 'two-sidebar-left' || LayoutMode == 'two-sidebar-center'){var ngutters = 2;}
				else if (LayoutMode == 'fullwidth'){var ngutters = 0;gutterwidth = 0}

				var innercenterwidth = contentwidth - innereastwidth - innerwestwidth;

				jQuery("."+LayoutMode+" #contentwidth .loelement-pad .width span").html(contentwidth);
				jQuery("."+LayoutMode+" .innercenter .loelement-pad .width span").html(innercenterwidth);
				jQuery("."+LayoutMode+" .innereast .loelement-pad .width span").html(innereastwidth);
				jQuery("."+LayoutMode+"  .innerwest .loelement-pad .width span").html(innerwestwidth);

				var primarysidebar = jQuery("."+LayoutMode+" #layout-sidebar-1 .loelement-pad .width span").html();
				var maincontent = jQuery("."+LayoutMode+" #layout-main-content .loelement-pad .width span").html();
				var wcontent = jQuery("."+LayoutMode+" #contentwidth .loelement-pad span").html();

				jQuery(".layout_controls").find("#input-content-width").val(wcontent);

				jQuery(".layout_controls").find("#input-responsive-width").val(contentpercent);

				jQuery("."+LayoutMode+" #input-primarysidebar-width").val(primarysidebar);

				jQuery("."+LayoutMode+" #input-maincolumn-width").val(maincontent);
				
				
				
				if(Source == 'margin-resize'){
					var theLayoutModes = new Array('fullwidth', 'one-sidebar-right', 'one-sidebar-left', 'two-sidebar-right', 'two-sidebar-left', 'two-sidebar-center');
				
					for( var i = 0; i < theLayoutModes.length; i++){
					
						if(theLayoutModes[i] != LayoutMode){
					
							if(theLayoutModes[i] == 'two-sidebar-right' || theLayoutModes[i] == 'two-sidebar-left' || theLayoutModes[i] == 'two-sidebar-center'){
								
								var modeContent = jQuery("."+theLayoutModes[i]+" #layout-main-content .loelement-pad .width span").html();
								var modeSB = jQuery("."+theLayoutModes[i]+" #layout-sidebar-1 .loelement-pad .width span").html();
								var modeSB2 = jQuery("."+theLayoutModes[i]+" #layout-sidebar-2 .loelement-pad .width span").html();
								
								jQuery("."+theLayoutModes[i]+" #input-maincolumn-width").val(wcontent - modeSB - modeSB2 );
							
							} else if(theLayoutModes[i] == 'one-sidebar-right' || theLayoutModes[i] == 'one-sidebar-left'){
							
								var modeContent = jQuery("."+theLayoutModes[i]+" #layout-main-content .loelement-pad .width span").html();
								var modeSB = jQuery("."+theLayoutModes[i]+" #layout-sidebar-1 .loelement-pad .width span").html();
								
								jQuery("."+theLayoutModes[i]+" #input-maincolumn-width").val(wcontent - modeSB);
								
							
							} else if (theLayoutModes[i] == 'fullwidth'){
								
								jQuery("."+theLayoutModes[i]+" #input-maincolumn-width").val(wcontent);
								
							}
					
						
						}

					}
					
				}
				
				
			} 
			


		}


	///// LAYOUT BUILDER //////
	function setLayoutBuilder(LayoutMode, margin, innereast, innerwest, gutter){

		
		var MainLayoutBuilder, InnerLayoutBuilder;
	
		window['OuterLayout'] = jQuery("."+LayoutMode+" .layout-main-content").layout({ 

				center__paneSelector:	".layout-inner-content"
			,	east__paneSelector:		".margin-east"
			,	west__paneSelector: 	".margin-west"
			,	closable:				false	// pane can open & close
			,	resizable:				true	// when open, pane can be resized 
			,	slidable:				false
			,	resizeWhileDragging:	true
			,	west__resizable:		true	// Set to TRUE to activate dynamic margin
			,	east__resizable:		true	// Set to TRUE to activate dynamic margin
			,	east__resizerClass: 	'pagelines-resizer-east'
			,	west__resizerClass: 	'pagelines-resizer-west'
			,	east__size:				margin
			,	west__size:				margin
			, 	east__maxSize:  		188
			, 	west__maxSize:  		188
			, 	west__onresize: function (pane, $Pane, paneState) {
			    var width  = paneState.innerWidth;
				var realwidth = width * 2;
				var currentElement = jQuery("."+LayoutMode+" .margin-east");
				
				// This will fire in Firefox in strange times, make sure it's visible before doing anything
				if(currentElement.is(':visible')){
					currentElement.width(width);
					var position = jQuery("."+LayoutMode+" .pagelines-resizer-west").position();
					jQuery("."+LayoutMode+" .pagelines-resizer-east").css('right', position.left);
					updateDimensions(LayoutMode, 'margin-resize');
					
				}
				
			} 
			, 	east__onresize: function (pane, $Pane, paneState) {
			    var width  = paneState.innerWidth;
				var realwidth = width * 2;
				var currentElement = jQuery("."+LayoutMode+" .margin-west");
				
				// This will fire in Firefox in strange times, make sure it's visible before doing anything
				if(currentElement.is(':visible')){
					currentElement.width(width);
					var position = jQuery("."+LayoutMode+" .pagelines-resizer-east").css('right');
					jQuery("."+LayoutMode+" .pagelines-resizer-west").css('left', position);
					updateDimensions(LayoutMode, 'margin-resize');
				}
			}
		});
		window['InnerLayout'] = jQuery("."+LayoutMode+" .layout-inner-content").layout({ 

				    	closable: 				true 
					,   togglerLength_open: 	0 
					,	resizable:				true
					,	slidable:				false	
					, 	north__resizable: 		false
					, 	south__resizable: 		false
					,	resizeWhileDragging:	true
					,	east__resizerClass: 	'gutter'
					,	west__resizerClass: 	'gutter'
					,	east__minSize: 			30
					,	west__minSize: 			30
					,	center__minWidth: 		20
					, 	east__closable:  		false
					, 	west__closable:  		false
					,   east__spacing_open:     gutter
					, 	west__spacing_open: 	gutter
					,	east__size: 			innereast
					,	west__size: 			innerwest
					, 	west__onresize: function (pane, $Pane, paneState) { updateDimensions(LayoutMode, 'Inner West'); } 
					, 	east__onresize: function (pane, $Pane, paneState) {	updateDimensions(LayoutMode, 'Inner East'); }
		});
		
		updateDimensions(LayoutMode, 'Layout Builder');
	}
