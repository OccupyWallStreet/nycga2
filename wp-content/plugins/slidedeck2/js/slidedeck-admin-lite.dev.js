/*!
 * SlideDeck 2 Lite for WordPress Lite Admin JavaScript
 * 
 * More information on this project:
 * http://www.slidedeck.com/
 * 
 * Full Usage Documentation: http://www.slidedeck.com/usage-documentation 
 * 
 * @package SlideDeck
 * @subpackage SlideDeck 2 Lite for WordPress
 * 
 * @author dtelepathy
 */

/*!
Copyright 2012 digital-telepathy  (email : support@digital-telepathy.com)

This file is part of SlideDeck.

SlideDeck is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

SlideDeck is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with SlideDeck.  If not, see <http://www.gnu.org/licenses/>.
*/
(function($){
	$(document).ready(function(){
		
		// Bind to the custom lens change event
		$('body').bind( 'slidedeck:lens-change-update-choices', function(){
			// Lite slide UI limits
			if( $('#options-total_slides').length ){
				$('#options-total_slides').attr('readonly', true);
				$('#options-total_slides').parent().append('<em><a class="upgrade-modal" href="' + slideDeck2AddonsURL + '" rel="slidecount">Upgrade</a> to get more slides per deck.</em>');
			}
			
			// Covers UI Limit
			if( $('#slidedeck-covers').length ){
				$('#slidedeck-covers').append('<span class="lite-disabled-mask"><em><a class="upgrade-modal" href="' + slideDeck2AddonsURL + '" rel="covers">Upgrade</a> to get access to covers.</em></span>');
			}
		});
		
		$('body').trigger( 'slidedeck:lens-change-update-choices' );
		
		// Modals for the upsells
		if( $('.upgrade-modal').length ){
			var context = 'upsell';
			
			
			// Generic Upgrade modal.
			SlideDeckPlugin.UpgradeModal = {
				addForClass: function( theClass ){
					// Remove the previous pattern
					$('#slidedeck-' + context + '-simplemodal')[0].className = $('#slidedeck-' + context + '-simplemodal')[0].className.replace(/for\-[a-z]+\s?/, '');
					// Add the new class
					$('#slidedeck-' + context + '-simplemodal').addClass( 'for-' + theClass );
				},
				
		        open: function(data){
		            var self = this;
		            
		            if(!this.modal){
		                this.modal = new SimpleModal({
		                    context: context
		                });
		            }
					this.modal.open(data);
				}
			};
			
			$('#slidedeck_form').delegate( '.upgrade-modal', 'click', function(event){
				event.preventDefault();
				var slug = $(this).attr('rel');
				 
	            $.get(ajaxurl + "?action=slidedeck_upsell_modal_content&feature=" + slug , function(data){
					SlideDeckPlugin.UpgradeModal.open(data);
					SlideDeckPlugin.UpgradeModal.addForClass( slug );
					
					// Make sure the <a> tags do nothing in the lenses upgrade modal
					 $('#slidedeck-upsell-simplemodal a.lens.placeholder').bind( 'click', function(event){
					 	event.preventDefault();
					 });
	            });
			});
		}
	});
})(jQuery);
