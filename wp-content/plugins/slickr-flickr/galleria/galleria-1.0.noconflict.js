/**
 * Galleria (http://monc.se/kitchen)
 *
 * Galleria is a javascript image gallery written in jQuery.
 * It loads the images one by one from an unordered list and displays thumbnails when each image is loaded. 
 * It will create thumbnails for you if you choose so, scaled or unscaled, 
 * centered and cropped inside a fixed thumbnail box defined by CSS.
 * 
 * The core of Galleria lies in it's smart preloading behaviour, snappiness and the fresh absence 
 * of obtrusive design elements. Use it as a foundation for your custom styled image gallery.
 *
 * MAJOR CHANGES v.FROM 0.9
 * Galleria now features a useful history extension, enabling back button and bookmarking for each image.
 * The main image is no longer stored inside each list item, instead it is placed inside a container
 * onImage and onThumb functions lets you customize the behaviours of the images on the site
 *
 * Tested in Safari 3, Firefox 2, MSIE 6, MSIE 7, Opera 9
 * 
 * Version 1.0
 * Februari 21, 2008
 *
 * Copyright (c) 2008 David Hellsing (http://monc.se)
 * Licensed under the GPL licenses.
 * http://www.gnu.org/licenses/gpl.txt
 **/

/*********************************************************************
MODIFIED TO WORK WITH WORDPRESS

What was changed?
* The addition of the first line of code '$j=jQuery.noConflict();' and
* I manually went through and replaced all '$' jQuery references with
* '$j'. Also, removed '(jQuery)' which was tacked on to the end of
* the main (currently line 400, there is also a note there).
* Nothing else was changed.

* Note that the jQuery history plugin (at the end of this file)
* is already using 'jQuery' rather than '$'. I made no changes to the
* plugin, nor did I test the plugin.

Why was it changed?
* Addition of noConflict() @jQuery to prevent problems using Galleria
* in Wordpress (when using jQuery in Wordpress' core) and also to
* prevent potential problems when other JS libraries are being used,
* such as Prototype.

Changes made on July 30, 2009
* by Matthew Pavkov - http://www.matthewpavkov.com

References and thanks to
* @http://docs.jquery.com/Using_jQuery_with_Other_Libraries
* @http://wordpress.org/support/topic/141394
*********************************************************************/

$j=jQuery.noConflict();


$j(function(){

/**
 * 
 * @desc Convert images from a simple html <ul> into a thumbnail gallery
 * @author David Hellsing
 * @version 1.0
 *
 * @name Galleria
 * @type jQuery
 *
 * @cat plugins/Media
 * 
 * @example $('ul.gallery').galleria({options});
 * @desc Create a a gallery from an unordered list of images with thumbnails
 * @options
 *   insert:   (selector string) by default, Galleria will create a container div before your ul that holds the image.
 *             You can, however, specify a selector where the image will be placed instead (f.ex '#main_img')
*   onImage:  (function) a function that gets fired when the image is displayed and brings the jQuery image object.
 *             You can use it to add click functionality and effects.
 *             f.ex onImage(image) { image.css('display','none').fadeIn(); } will fadeIn each image that is displayed
 *   onThumb:  (function) a function that gets fired when the thumbnail is displayed and brings the jQuery thumb object.
 *             Works the same as onImage except it targets the thumbnail after it's loaded.
 *
**/

var $$;

$$ = $j.fn.galleria = function(options) {

	// check for basic CSS support
	if (!_hasCSS()) { return false; }


	// set default options
	var $defaults = {
		history : false,
		clickNext : true,
		onImage : function(image,caption,thumb) {},
		onThumb : function(thumb) {},
		imageArray:	[],
		activeImage: 0,
        autoPlay : false,
        timer : false,
        slideDelay: 5000,
        captions : false,
        descriptions : false
        };


	// extend the options
	var $settings = $j.extend($defaults, options);

	var jQueryMatchedObj = $j(this); // This, in this context, refer to jQuery object

	// bring the options to the galleria object
	for (var i in $settings) {
	    if (i) { $j(this).galleria[i] = $settings[i]; }
	}

	// add the Galleria class
    var $ul = $j(this).children('ul').addClass('galleria');

     //add gallery container for holding large photo, caption and description
	var _insert = $j(document.createElement('div')).insertBefore($ul);

	// create a wrapping div for the image
	var _div = $j(document.createElement('div')).addClass('galleria_wrapper');

	// create a caption span
	var _span = $j(document.createElement('span')).addClass('caption');

	// create a description paragraph
	var _description = $j(document.createElement('div')).addClass('description');

	// inject the wrapper in in the insert selector
	_insert.addClass('galleria_container').append(_div).append(_span).append(_description);

	// loop through list
	$ul.children('li').each(function(i) {

			// bring the scope
			var _container = $j(this);

			// build element specific options
			var _o = $j.meta ? $j.extend({}, $settings, _container.data()) : $settings;

			// remove the clickNext if image is only child
			_o.clickNext = $j(this).is(':only-child') ? false : _o.clickNext;

			// try to fetch an anchor
			var _a = $j(this).find('a').is('a') ? $j(this).find('a') : false;

			// reference the original image as a variable and hide it
			var _img = $j(this).children('img').css('display','none');

			// extract the original source
			var _src = _a ? _a.attr('href') : _img.attr('src');
            $settings.imageArray.push(_src);

			// find a title
			var _title = _a ? _a.attr('title') : _img.attr('title');

			// find a description
			var _description = _img.attr('alt');
			if (_description == _title) _description = "";

			// create loader image
			var _loader = new Image();

			// check url and activate container if match
			if (_o.history && (window.location.hash && window.location.hash.replace(/\#/,'') == _src)) {
				_container.siblings('.active').removeClass('active');
				_container.addClass('active');
			}

			// begin loader
			$j(_loader).load(function () {

				// try to bring the alt
				$j(this).attr('alt',_img.attr('alt'));

				//-----------------------------------------------------------------
				// the image is loaded, let's create the thumbnail

				var _thumb = _a ?
					_a.find('img').addClass('thumb noscale').css('display','none') :
					_img.clone(true).addClass('thumb').css('display','none');

				if (_a) { _a.replaceWith(_thumb); }

				if (!_thumb.hasClass('noscale')) { // scaled tumbnails!
					var w = Math.ceil( _img.width() / _img.height() * _container.height() );
					var h = Math.ceil( _img.height() / _img.width() * _container.width() );
					if (w < h) {
						_thumb.css({ height: 'auto', width: _container.width(), marginTop: -(h-_container.height())/2 });
					} else {
						_thumb.css({ width: 'auto', height: _container.height(), marginLeft: -(w-_container.width())/2 });
					}
				}

				// add the rel attribute
				_thumb.attr('rel',_src);

				// add the title attribute
				_thumb.attr('title',_title);

				// add the alt attribute
				if (_description) _thumb.attr('alt',_description);

				// add the click functionality to the _thumb
				_thumb.click(
                    function () { _galleria_find_image (_thumb.attr('rel'));
                });

				// hover classes for IE6
				_thumb.hover(
					function() { $j(this).addClass('hover'); },
					function() { $j(this).removeClass('hover'); }
				);
				_container.hover(
					function() { _container.addClass('hover'); },
					function() { _container.removeClass('hover'); }
				);

				// prepend the thumbnail in the container
				_container.prepend(_thumb);

				// show the thumbnail
				_thumb.css('display','block');

				// call the onThumb function
				_o.onThumb(jQuery(_thumb));

				// check active class and activate image if match
				if (_container.hasClass('active')) {
					_select_galleria_image(_src);
					//_span.text(_title);
				}

				// finally delete the original image
				_img.remove();

			}).error(function () {

				// Error handling
			    _container.html('<span class="error" style="color:red">Error loading image: '+_src+'</span>');

			}).attr('src', _src);
    	});
    	
    	$j(this).css('visibility','visible'); 

        $j(this).find('.nav .nextSlide').click(function() {
		      _stop_galleria_show();
			  _next_galleria_image();
              return false;
              });
        $j(this).find('.nav .prevSlide').click(function() {
		      _stop_galleria_show();
			  _prev_galleria_image();
              return false;
              });
        $j(this).find('.nav .stopSlide').click(function() {
		      _stop_galleria_show();
              return false;
              });
        $j(this).find('.nav .startSlide').click(function() {
		      _galleria_slideshow();
              return false;
              });

        if($settings.autoPlay){
	    	_start_galleria_show();
	    }


   function _start_galleria_show() {
  	    if ( ! $settings.timer) {
  	        var tmFunc = function(){ _galleria_slideshow(); };
            $settings.timer = setTimeout(tmFunc, $settings.slideDelay);
            }
   }

   function _stop_galleria_show() {
        if ($settings.timer) clearTimeout($settings.timer);
        $settings.timer = false;
    }

    function _galleria_find_image(_src) {
        activeImage = 0;
        l = $settings.imageArray.length;
        for (i=0; i<l; i++) {
             if (_src == $settings.imageArray[i]) {
                 activeImage = i;
                 break;
             }
      	  }
        _galleria_activate(activeImage);
    }

    function _next_galleria_image() {
       $settings.activeImage = $settings.activeImage < ($settings.imageArray.length -1) ?($settings.activeImage + 1):0;
	   _select_galleria_image();
	}

    function _prev_galleria_image() {
        $settings.activeImage = $settings.activeImage > 0 ? ($settings.activeImage - 1):($settings.imageArray.length - 1);
		_select_galleria_image();
    }

    function _galleria_activate(imageIndex) {
	    _stop_galleria_show();
        $settings.activeImage = imageIndex;
        _select_galleria_image();
    }


    function _galleria_slideshow(){
	    _next_galleria_image();
        _stop_galleria_show();
    	if($j('.slickr-flickr-galleria').length > 0) _start_galleria_show();
    }

    function _hasCSS()  {
	    $j('body').append(
	    	$j(document.createElement('div')).attr('id','css_test').css({ width:'1px', height:'1px', display:'none' })
	    );
	    var _v = ($j('#css_test').width() != 1) ? false : true;
	    $j('#css_test').remove();
	    return _v;
    };

    function _select_galleria_image() {

        // get the wrapper
        var _wrapper = _div;

        var _src = $settings.imageArray[$settings.activeImage];

  	    if (_src) {

    	    // get the thumb
        	var _thumb = $ul.find('img[rel="'+_src+'"]');

    		// alter the active classes
    		_thumb.parents('li').siblings('.active').removeClass('active');
    		_thumb.parents('li').addClass('active');

    		// define a new image
    		var _img  = $j(new Image()).attr('src',_src).addClass('replaced');

    		// empty the wrapper and insert the new image
    		_wrapper.empty().append(_img);

            if ($settings.captions) {
                // insert the caption
        		_wrapper.siblings('.caption').html(_thumb.attr('title'));

                if ($settings.descriptions) {
                    _alt = _thumb.attr('alt') != _thumb.attr('title') ? _thumb.attr('alt') : "";
        		    _wrapper.siblings('.description').html(_alt);
                    }
            }
    		// fire the onImage function to customize the loaded image's features
    		$settings.onImage(_img,_wrapper.siblings('.caption'),_thumb);

    		// add clickable image helper
    		if($settings.clickNext) {
    			_img.css('cursor','pointer').click(function() {
    		      if($settings.timer) {clearTimeout($settings.timer); $settings.timer = 0; }
    			  _next_galleria_image();
                  });
    		}
    	} else {

    		// clean up the container if none are active
    		_wrapper.siblings().andSelf().empty();

    		// remove active classes
    		$ul.children('li.active').removeClass('active');
    	}
    };
   return this;

};

});