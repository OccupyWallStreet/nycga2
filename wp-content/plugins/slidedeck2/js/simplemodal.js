/**
 * jQuery Simple Modal plugin
 * 
 * A simple modal library
 * 
 * @author dtelepathy
 * @version 1.0.1
 */

/*
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
var SimpleModal=function(a){this.options={namespace:"slidedeck",context:"",hideOnOverlayClick:true,hideOnEscape:true,speedIn:500,speedOut:500,onComplete:null,onCleanup:null,onClosed:null};this.elems={};this.initialize(a);return this};(function($){SimpleModal.prototype._maskId=function(){var a=[];if(this.options.namespace!=="")a.push(this.options.namespace);if(this.options.context!=="")a.push(this.options.context);a.push('simplemodal-mask');return a.join('-')};SimpleModal.prototype._modalId=function(){var a=[];if(this.options.namespace!=="")a.push(this.options.namespace);if(this.options.context!=="")a.push(this.options.context);a.push('simplemodal');return a.join('-')};SimpleModal.prototype.build=function(){var b=this,modalId=this._modalId(),maskId=this._maskId();this.elems.modal=$('#'+modalId);this.elems.mask=$('#'+maskId);if(this.elems.modal.length<1){$('body').append('<div id="'+modalId+'" class="simplemodal" style="display:none;" />');this.elems.modal=jQuery('#'+modalId)}if(this.elems.mask.length<1){$('body').append('<div id="'+maskId+'" class="simplemodal-mask" style="display:none;"><div id="'+maskId+'-inner" class="simplemodal-mask-inner"></div></div>');this.elems.mask=$('#'+maskId);this.elems.mask.bind('click',function(){if(b.options.hideOnOverlayClick===true)b.close()})}this.position=this.elems.modal.css('position');$(document).bind('keyup',function(a){if(a.keyCode==27)if(b.options.hideOnEscape===true)b.close()})};SimpleModal.prototype.close=function(){var a=this;if(typeof(this.options.onCleanup)=='function')this.options.onCleanup(this);this.elems.mask.fadeOut(this.options.speedOut);this.elems.modal.fadeOut(this.options.speedOut,function(){a.elems.modal.css({'-webkit-transition':'','-moz-transition':'','-o-transition':'','transition':''});if(typeof(a.options.onClosed)=='function')a.options.onClosed(a)});this.elems.modal.removeClass('open')};SimpleModal.prototype.initialize=function(a){var b=this;this.options=$.extend(this.options,a);this.elems.$window=$(window);this.build();this.elems.$window.resize(function(){b.reposition()})};SimpleModal.prototype.open=function(a){var b=this;this.elems.modal.html(a);this.elems.mask.fadeIn(this.options.speedIn);this.elems.modal.fadeIn(this.options.speedIn,function(){b.elems.modal.css({'-webkit-transition':'top 0.5s ease-in-out','-moz-transition':'top 0.5s ease-in-out','-o-transition':'top 0.5s ease-in-out','transition':'top 0.5s ease-in-out'})});this.reposition();this.elems.modal.addClass('open');if(typeof(this.options.onComplete)=='function')this.options.onComplete(this)};SimpleModal.prototype.reposition=function(){var a=this.elems.modal.outerHeight();var b=this.elems.$window.height();var c=window.scrollTop||window.scrollY;var d=this.elems.modal.offset().top;var e=$(document).height();switch(this.position){default:case"fixed":if(d+a>b){if(a>b){this.elems.modal.css({top:20,marginTop:0})}else{this.elems.modal.css({top:'50%',marginTop:0-(a/2)})}}else{this.elems.modal.css({top:'50%',marginTop:0-(a/2)})}break;case"absolute":var f=e-a-40;this.elems.modal.css({top:Math.min(c,f)+20,marginTop:0});break}}})(jQuery);