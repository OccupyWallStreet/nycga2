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
(function(a){a(document).ready(function(){a("body").bind("slidedeck:lens-change-update-choices",function(){if(a("#options-total_slides").length){a("#options-total_slides").attr("readonly",true);a("#options-total_slides").parent().append('<em><a class="upgrade-modal" href="'+slideDeck2AddonsURL+'" rel="slidecount">Upgrade</a> to get more slides per deck.</em>')}if(a("#slidedeck-covers").length){a("#slidedeck-covers").append('<span class="lite-disabled-mask"><em><a class="upgrade-modal" href="'+slideDeck2AddonsURL+'" rel="covers">Upgrade</a> to get access to covers.</em></span>')}});a("body").trigger("slidedeck:lens-change-update-choices");if(a(".upgrade-modal").length){var b="upsell";SlideDeckPlugin.UpgradeModal={addForClass:function(c){a("#slidedeck-"+b+"-simplemodal")[0].className=a("#slidedeck-"+b+"-simplemodal")[0].className.replace(/for\-[a-z]+\s?/,"");a("#slidedeck-"+b+"-simplemodal").addClass("for-"+c)},open:function(d){var c=this;if(!this.modal){this.modal=new SimpleModal({context:b})}this.modal.open(d)}};a("#slidedeck_form").delegate(".upgrade-modal","click",function(d){d.preventDefault();var c=a(this).attr("rel");a.get(ajaxurl+"?action=slidedeck_upsell_modal_content&feature="+c,function(e){SlideDeckPlugin.UpgradeModal.open(e);SlideDeckPlugin.UpgradeModal.addForClass(c);a("#slidedeck-upsell-simplemodal a.lens.placeholder").bind("click",function(f){f.preventDefault()})})})}})})(jQuery);