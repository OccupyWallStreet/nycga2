/* ------------------------------------------------------------------------
	prettyCheckboxes
	
	Developped By: Stephane Caron (http://www.no-margin-for-errors.com)
	Version: 1.1
------------------------------------------------------------------------- */
	
	jQuery.fn.prettyCheckboxes = function(settings) {
		settings = jQuery.extend({
					checkboxWidth: 17,
					checkboxHeight: 17,
					className : 'prettyCheckbox',
					display: 'list'
				}, settings);

		jQuery(this).each(function(){
			// Find the label
			$label = jQuery('label[for='+jQuery(this).attr('id')+']');

			// check if not starting 'sf'
			if(jQuery(this).attr('id').substr(0, 2) != 'sf')
			{
				return true;
			}

			// check if an excluded id
			for(var x=0; x<pcbExclusions.length; x++)
			{
				if(jQuery(this).attr('id') == pcbExclusions[x])
				{
					return true;
				}
			}

			// Add the checkbox holder to the label
			$label.prepend("<span class='holderWrap'><span class='holder'></span></span>");

			// If the checkbox is checked, display it as checked
			if(jQuery(this).is(':checked')) { $label.addClass('checked'); };

			// Assign the class on the label
			$label.addClass(settings.className).addClass(jQuery(this).attr('type')).addClass(settings.display);

			// Assign the dimensions to the checkbox display
			$label.find('span.holderWrap').width(settings.checkboxWidth).height(settings.checkboxHeight);
			$label.find('span.holder').width(settings.checkboxWidth);

			// Hide the checkbox
			jQuery(this).addClass('hiddenCheckbox');

			// Associate the click event
			$label.bind('click',function(){
				jQuery('input#' + jQuery(this).attr('for')).triggerHandler('click');
				
				if(jQuery('input#' + jQuery(this).attr('for')).is(':checkbox')){
					jQuery(this).toggleClass('checked');
					jQuery('input#' + jQuery(this).attr('for')).checked = true;
				}else{
					$toCheck = jQuery('input#' + jQuery(this).attr('for'));

					// Uncheck all radio
					jQuery('input[name='+$toCheck.attr('name')+']').each(function(){
						jQuery('label[for=' + jQuery(this).attr('id')+']').removeClass('checked');	
					});

					jQuery(this).addClass('checked');
					$toCheck.checked = true;
				};
			});
			
			jQuery('input#' + $label.attr('for')).bind('keypress',function(e){
				if(e.keyCode == 32){
					if(jQuery.browser.msie){
						jQuery('label[for='+jQuery(this).attr('id')+']').toggleClass("checked");
					}else{
						jQuery(this).trigger('click');
					}
					return false;
				};
			});
		});
	};
