/**
 * LICENSE
 * This file is part of Flickr Gallery.
 *
 * Flickr Gallery is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package    flickr-gallery
 * @author     Dan Coulter <dan@dancoulter.com>
 * @copyright  Copyright 2009 Dan Coulter
 * @license    http://www.gnu.org/licenses/gpl.txt GPL 2.0
 * @version    1.0.2
 * @link       http://co.deme.me/projects/flickr-gallery/
 */
;(function($){
	$.flightboxIsVideo = false;
	$.flightboxShow = function($calling, options) {
		$calling = $($calling);
		$(".flightbox-current").removeClass("flightbox-current");
		$calling.addClass("flightbox-current");
		var $flightbox = $("#flightbox");
		$("#flightbox-image, #flightbox-meta").remove();
		
		$flightbox.prepend("<img id='flightbox-image' src='' />");
		$image = $("#flightbox-image");
		$("#flightbox-image").after("<div id='flightbox-meta'><div id='flightbox-close'></div><div id='flightbox-title'></div><div id='flightbox-description'></div><div id='flightbox-flickr-link'><a href='' target='_blank'>View this <span id='flightbox-type'>photo</span> on Flickr</a></div></div>");

		$image.unbind("load");
		$image.load(function(){
			$("#flightbox-title").html($calling.attr("title"));
			$("#flightbox-flickr-link a").attr("href", $calling.parent("a").attr("href"));
			$("#flightbox-meta").css({width:$image.innerWidth()});
			
			$("#flightbox-left, #flightbox-right").css({
				top: 15,
				width: Math.floor($image.innerWidth()/2),
				height: $image.innerHeight()
			});
			$("#flightbox-close").click(function(){
				$("#floverlay").remove();
				$("#flightbox").remove();
			});


			$("#flightbox-video").remove();
			if ( $calling.hasClass("video") ) {
				$.flightboxIsVideo = true;
				$("#flightbox-right,#flightbox-left").css({height: 33, top: $image.innerHeight()+15});
				$("#flightbox-prev,#flightbox-next").css({top:0, display:"block"});
				$("#flightbox-meta").css({paddingTop:33});
				
				var img_data = $image.attr("src").match(/[^\/]+_[^\/]+.jpg/g)[0].split(".")[0];
				var photo_id = img_data.split("_")[0];
				var photo_secret = img_data.split("_")[1];
				$image.before('<div id="flightbox-video" style="display: none;"><object type="application/x-shockwave-flash" width="' + $image.width() + '" height="' + $image.height() + '" data="http://www.flickr.com/apps/video/stewart.swf?v=67090&photo_id=' + photo_id + '&photo_secret=' + photo_secret + '" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"><param name="movie" value="http://www.flickr.com/apps/video/stewart.swf?v=67090&photo_id=' + photo_id + '&photo_secret=' + photo_secret + '"></param>								<param name="bgcolor" value="#000000"></param>								<param name="allowFullScreen" value="true"></param>								<embed type="application/x-shockwave-flash" src="http://www.flickr.com/apps/video/stewart.swf?v=67090&photo_id=' + photo_id + '&photo_secret=' + photo_secret + '" bgcolor="#000000" allowfullscreen="true" flashvars="intl_lang=en-us&amp;photo_secret=' + photo_secret + '&amp;photo_id=' + photo_id + '" width="' + $image.width() + '" height="' + $image.height() + '"></embed></object></div>').hide();
				$("#flightbox-type").html("video");
			} else {
				if ( $.flightboxIsVideo == true ) {
					$("#flightbox-next, #flightbox-prev").hide()
				}

				$.flightboxIsVideo = false;
				$("#flightbox-prev,#flightbox-next").css({top:100});
				$("#flightbox-meta").css({paddingTop:0});
				$("#flightbox-type").html("photo");
			}

			$("#flightbox").animate({
				left:	(($("body").width() - $image.innerWidth()-30)/2),
				height: $image.innerHeight() + $("#flightbox-meta").innerHeight(),
				width: $image.innerWidth()
			}, "normal", "swing", function(){
				$("#flightbox-meta").fadeIn("fast");
				if ( !$calling.hasClass("video") ) {
					$image.fadeIn("fast");
				} else {
					$("#flightbox-video").show();
				}
			});
			
			$("#flightbox-left").click(function(){
				$prev = $calling.parents(".flickr-thumb").prev(".flickr-thumb").find("img");
				if ( $prev.length ) {
					$.flightboxShow($prev, options);
				} else {
					$("#flightbox").remove();
					$("#floverlay").remove();
				}
			});
			$("#flightbox-right").click(function(){
				$next = $calling.parents(".flickr-thumb").next(".flickr-thumb").find("img");
				if ( $next.length ) {
					$.flightboxShow($next, options);
				} else {
					$("#flightbox").remove();
					$("#floverlay").remove();
				}
			});
			
		});
		
		if ( options.size_callback == null || $calling.hasClass("video") ) {
			$image.attr("src", $calling.attr("src").replace(/_[stm]\./g, "."));
		} else {
			info = options.size_callback($calling.attr("src").match(/[^\/]+_[^\/]+_[st].jpg/g)[0].split("_")[0]);
			$("#flightbox-description").html(info.description);
			max_width = $(window).width() - 30;
			max_height = $(window).height() - 50 - 30 - $("#flightbox-meta").height();
			//console.log($("#flightbox-description"));
			//console.log($("#flightbox-image"));
			var index = -1;
			$.each(info.sizes, function(key){
				if ( index == -1 && (this.width > max_width || this.height > max_height) ) {
					index = key - 1;
				}
			});
			if ( index == -1 ) index = info.sizes.length - 1;
			$image.attr("src", info.sizes[index].source);
		}

		
	}
	
	$.fn.flightbox = function(options) {
		var defaults = {
			size_callback: null
		}
		if ( options == undefined ) {
			options = defaults;
		} else {
			$.each(defaults, function(key){
				if ( options[key] == undefined ) {
					options[key] = defaults[key];
				}
			});
		}
		return this.each(function(){
			$(this).click(function(){
				var $calling = $(this);
				var $document = $(this.ie6 ? document.body : document);
				$("body").
					append('<div id="floverlay"></div>').
					append('<div id="flightbox"><div id="flightbox-right"><div id="flightbox-next"></div></div><div id="flightbox-left"><div id="flightbox-prev"></div></div></div>');
					
				var flightbox_offset;
				if (self.pageYOffset) {
					flightbox_offset = self.pageYOffset + 50;
				} else if (document.documentElement && document.documentElement.scrollTop) {
					flightbox_offset = document.documentElement.scrollTop + 50;
				} else if (document.body) {
					flightbox_offset = document.body.scrollTop + 50;
				}
					
				$("#floverlay").css({
					width:	$document.width(),
					height:	$document.height()
				}).click(function(){
					$(this).remove();
					$("#flightbox").remove();
				});
				
				$("#flightbox").css({
					top: flightbox_offset,
					left: (($("body").width() - 130)/2)
				});
				
				$("#flightbox-left, #flightbox-right").mouseover(function(e){
					if ( e.target.id == "flightbox-left" && $(".flightbox-current").parents(".flickr-thumb").prev(".flickr-thumb").find("img").length == 0) {
						$(e.target).css("cursor", "default");
						return;
					} else if ( e.target.id == "flightbox-right" && $(".flightbox-current").parents(".flickr-thumb").next(".flickr-thumb").find("img").length == 0) {
						$(e.target).css("cursor", "default");
						return;
					} else {
						$(e.target).css("cursor", "pointer");
					}
					if ( !$.flightboxIsVideo )
						$(this).children().show();
				});
				$("#flightbox-left, #flightbox-right").mouseout(function(){
					if ( !$.flightboxIsVideo )
						$(this).children().hide();
				});

				$.flightboxShow($calling, options);
				
				return false;
			});
			
		});
	}
})(jQuery);