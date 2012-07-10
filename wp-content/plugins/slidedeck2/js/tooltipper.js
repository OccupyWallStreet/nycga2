/**
 * jQuery ToolTipper Plugin
 * 
 * Quick tooltip plugin for jQuery
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
var ToolTipper;(function($){ToolTipper=function(b,c){var d=$(b),self=this;this.options={namespace:"",speed:"fast",delay:250,offsetX:0,offsetY:0,maxWidth:350};this.options=$.extend(this.options,c);this.prep(d);$('body').delegate('.tooltip, .tooltipper','mouseenter',function(a){self.show(this)}).delegate('.tooltip, .tooltipper','mouseleave',function(a){self.hide(this)})};ToolTipper.prototype.build=function(a){var b=$(a),message=$.data(a,'tooltipper-message');$('body').append('<span class="tooltipper '+this.options.namespace+'" style="display:none;">'+message+'</span>');$tooltipper=$('body').find('.tooltipper:last');$.data(a,'tooltipper-tip',$tooltipper);$.data(a,'tooltipper-elem',b);$.data($tooltipper[0],'tooltipper-tip',$tooltipper);$.data($tooltipper[0],'tooltipper-elem',b);return $tooltipper};ToolTipper.prototype.prep=function(c){var d=this;c.each(function(a){var b=c.eq(a);$.data(this,'tooltipper-message',this.title);b.removeAttr('title');$.data(this,'tooltipper-elem',b)})};ToolTipper.prototype.show=function(a){var b=$.data(a,'tooltipper-tip');if(!b)b=this.build(a);var c=$.data(a,'tooltipper-elem');clearTimeout(c[0].timer);var d=c.offset();b.css('max-width',this.options.maxWidth);b.css({top:d.top+this.options.offsetY-b.outerHeight(),left:d.left+this.options.offsetX,opacity:0,display:'block'}).stop().animate({top:d.top+this.options.offsetY-b.outerHeight(),opacity:1},this.options.speed)};ToolTipper.prototype.hide=function(b){var c=this,$elem=$.data(b,'tooltipper-elem'),tip=$.data(b,'tooltipper-tip');$elem[0].timer=setTimeout(function(){var a=tip.offset();tip.animate({top:a.top-5,opacity:0},c.options.speed,function(){tip.css({display:'none'})})},c.options.delay)};jQuery.fn.tooltipper=function(a){var b=$.data(this,'ToolTipper');if(!b)b=$.data(this,'ToolTipper',new ToolTipper(this,a));return this}})(jQuery);