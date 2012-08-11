jQuery(document).ready(function(){
    // Preload images in paginator initially
    jQuery('.wpv-pagination-preload-images').css('visibility', 'hidden');
    wpv_pagination_init_preload_images();
    jQuery('.wpv-pagination-preload-pages').each(function(){
        var view_number = jQuery(this).attr('id').substring(16);
        var max_pages = jQuery('#wpv_paged_max-'+view_number).val();
        wpv_pagination_preload_pages(view_number, 1, max_pages, false, true);
    });
});

function encodeToHex(str){
    var r="";
    var e=str.length;
    var c=0;
    var h;
    while(c<e){
        h=str.charCodeAt(c++).toString(16);
        while(h.length<2) h="0"+h;
        r+=h;
    }
    return r;
}

/**
 * Converts the given data structure to a JSON string.
 * Argument: arr - The data structure that must be converted to JSON
 * Example: var json_string = array2json(['e', {pluribus: 'unum'}]);
 * 			var json = array2json({"success":"Sweet","failure":false,"empty_array":[],"numbers":[1,2,3],"info":{"name":"Binny","site":"http:\/\/www.openjs.com\/"}});
 * http://www.openjs.com/scripts/data/json_encode.php
 */
function array2json(arr) {
    var parts = [];
    var is_list = (Object.prototype.toString.apply(arr) === '[object Array]');

    for(var key in arr) {
    	var value = arr[key];
        if(typeof value == "object") { //Custom handling for arrays
            if(is_list) parts.push(array2json(value)); /* :RECURSION: */
            else parts.push('"' + key + '":' + array2json(value)); /* :RECURSION: */
        } else {
            var str = "";
            if(!is_list) str = '"' + key + '":';

            //Custom handling for multiple data types
            if(typeof value == "number") str += value; //Numbers
            else if(value === false) str += 'false'; //The booleans
            else if(value === true) str += 'true';
            else str += '"' + value + '"'; //All other things
            // :TODO: Is there any more datatype we should be in the lookout for? (Functions?)

            parts.push(str);
        }
    }
    var json = parts.join(",");
    
    if(is_list) return '[' + json + ']';//Return numerical JSON
    return '{' + json + '}';//Return associative JSON
}


function wpv_serialize_array(data) {
	
    return encodeToHex(array2json(data));
}

function wpv_pagination_init_preload_images() {
    jQuery('.wpv-pagination-preload-images').each(function(){
        var preloadedImages = new Array();
        var element = jQuery(this);
        var images = element.find('img');
        if (images.length < 1) {
            element.css('visibility', 'visible');
        } else {
            images.one('load', function() {
                preloadedImages.push(jQuery(this).attr('src'));
                if (preloadedImages.length == images.length) {
                    element.css('visibility', 'visible');
                }
            }).each(function() {
                if(this.complete) {
                    jQuery(this).load();
                }
            });
        }
    });
}

function add_url_query_parameters(data) {
    var qs = (function(a) {
        if (a == "") return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p=a[i].split('=');
            if (p.length != 2) continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    })(window.location.search.substr(1).split('&'));
    
    data['get_params'] = {};
    for (var prop in qs) {
    	
        if (qs.hasOwnProperty(prop)) {
            if (!data.hasOwnProperty(prop)) {
                data['get_params'][prop] = qs[prop];
            }

        }
    }
    
    return data;
}

function wpv_add_url_controls_for_column_sort(form) {
	var data = Array();
	data = add_url_query_parameters(data);
	for (var param in data['get_params']) {
		if (data['get_params'].hasOwnProperty(param)) {
			
			if (form.find('input[name=' + param + ']').length == 0) {
				// we need to add an input element
				
				form.append('<input type="hidden" name="' + param + '" value="' + data['get_params'][param] + '" />');
			}
		}
	}
}

function add_view_parameters(data, page, view_number) {
    data['action'] = 'wpv_get_page';
    data['page'] = page;
    data['post_id'] = jQuery('input[name=wpv_post_id]').val();
    data['view_number'] = view_number;
    data['wpv_column_sort_id'] = jQuery('input[name=wpv_column_sort_id]').val();
    data['wpv_column_sort_dir'] = jQuery('input[name=wpv_column_sort_dir]').val();
    data['wpv_view_widget_id'] = jQuery('#wpv_widget_view-' + view_number).val();
    data['view_hash'] = jQuery('#wpv_view_hash-' + view_number).val();
    
    return data;
    };

function wpv_get_ajax_pagination_url(data) {
	var url;
	if (wpv_ajax_pagination_url.slice(-'.php'.length) == '.php') {
		url = wpv_ajax_pagination_url + '?wpv-ajax-pagination=' + wpv_serialize_array(data);
	} else {
		url = wpv_ajax_pagination_url + wpv_serialize_array(data);
	}
	return url;
}

function wpv_pagination_replace_view(view_number, page, ajax, effect, max_pages, cache_pages, preload_pages, spinner, spinner_image, callback_next, stop_rollover) {
    
    if (!(view_number in window.wpvPaginationAnimationFinished)) {
        window.wpvPaginationAnimationFinished[view_number] = false;
    } else if (window.wpvPaginationAnimationFinished[view_number] != true) {
        if (!(view_number in window.wpvPaginationQueue)) {
            window.wpvPaginationQueue[view_number] = new Array();
        }
        window.wpvPaginationQueue[view_number].push(arguments);
        return false;
    }
    window.wpvPaginationAnimationFinished[view_number] = false;
    
    if (stop_rollover) {
        wpv_stop_rollover[view_number] = true;
    }
    
    if (ajax != true) {
        // add elements for the current url parameters
        // So any views that filter by url parameters will still work.
        data = {};
        data = add_url_query_parameters(data);
        for (var prop in data['get_params']) {
            if (!(jQuery('#wpv-filter-' + view_number + ' > input[name=' + prop + ']').length > 0)) {
               
            	jQuery('<input>').attr({
                                    type: 'hidden',
                                    name: prop,
                                    value: data['get_params'][prop]
                                }).appendTo('#wpv-filter-' + view_number);

            }
        }
        
        jQuery('#wpv_paged-'+view_number).val(page);
        jQuery('#wpv-filter-'+view_number).submit();
        return false;
    }
                
    window.wpvPaginationAjaxLoaded[view_number] = false;
                
    if (typeof this.historyP == 'undefined' ) {
        this.historyP = new Array();
    }
    if (typeof window.wpvCachedPages == 'undefined' ) {
        window.wpvCachedPages = new Array();
    }
    if (typeof window.wpvCachedPages[view_number] == 'undefined' ) {
        window.wpvCachedPages[view_number] = new Array();
    }
                
    var wpvPaginatorLayout = jQuery('#wpv-view-layout-'+view_number);
    var wpvPaginatorFilter = jQuery('#wpv-filter-'+view_number);
    var wpvPaginatorPageSelector = jQuery('#wpv-page-selector-'+view_number);
    var responseCached = '';
                
    if (effect == 'fadeslow') {
        var speed = 1500; 
    } else if (effect == 'fadefast') {
        var speed = 1;
    } else {
        var speed = 500;
    }

    if (view_number in this.historyP) {
        var next = this.historyP[view_number] < page ? true : false;
    } else {
        var next = true;
    }
    if ((cache_pages || preload_pages) && page in window.wpvCachedPages[view_number]) {
        responseCached = window.wpvCachedPages[view_number][page];
        wpv_pagination_get_page(view_number, next, effect, speed, window.wpvCachedPages[view_number][page], wpvPaginatorLayout, wpvPaginatorFilter, callback_next);
        wpv_pagination_load_next_page(view_number, page, max_pages);
        wpv_pagination_load_previous_page(view_number, page, max_pages);
    } else {
        // Set loading class
        if (spinner != 'no') {
            var img = new Image();
            img.src = spinner_image;
            img.onload = function() {
                var wpvPaginatorLayoutOffset = wpvPaginatorLayout.position();
                wpvPaginatorLayout.before('<div style="width:32px;height:32px;border:1px solid #6D6D6D;background:#FFFFFF 50% 50% no-repeat url('+spinner_image+');position:absolute;z-index:99;top:'+(Math.round(wpvPaginatorLayoutOffset.top)+(Math.round(wpvPaginatorLayout.height()/2))-img.height)+'px; left:'+(Math.round(wpvPaginatorLayoutOffset.left)+(Math.round(wpvPaginatorLayout.width()/2))-img.width)+'px;" id="wpv_slide_loading_img_'+view_number+'" class="wpv_slide_loading_img"></div>').animate({opacity:0.5}, 300);
            };
        }
        var data = {};
        add_view_parameters(data, page, view_number);
        data = add_url_query_parameters(data);
        if (typeof(icl_lang) != 'undefined') {
            data['lang'] = icl_lang;
        }
        
        jQuery.get(wpv_get_ajax_pagination_url(data), function(response) {
            window.wpvCachedPages[view_number][page] = response;
            wpv_pagination_get_page(view_number, next, effect, speed, response, wpvPaginatorLayout, wpvPaginatorFilter, callback_next);
        });
  
        wpv_pagination_preload_pages(view_number, page, max_pages, cache_pages, preload_pages);
    }
    this.historyP[view_number] = page;
    return false;
}
                
function wpv_pagination_preload_pages(view_number, page, max_pages, cache_pages, preload_pages) {
    if (preload_pages) {
        wpv_pagination_load_next_page(view_number, page, max_pages);
        wpv_pagination_load_previous_page(view_number, page, max_pages);
    }
    if (cache_pages) {
        wpv_pagination_cache_current_page(view_number, page, max_pages);
    }
}
                
function wpv_pagination_load_next_page(view_number, page, max_pages) {
    if (typeof window.wpvCachedPages == 'undefined' ) {
        window.wpvCachedPages = new Array();
    }
    if (typeof window.wpvCachedPages[view_number] == 'undefined' ) {
        window.wpvCachedPages[view_number] = new Array();
    }
    var next_page = page+1;
    if (next_page in window.wpvCachedPages[view_number]) {
        return false;
    }
    // LOAD NEXT
    if (page < max_pages) {
        var dataNext = {};
        add_view_parameters(dataNext, next_page, view_number);
        dataNext = add_url_query_parameters(dataNext);
        if (typeof(icl_lang) != 'undefined') {
            dataNext['lang'] = icl_lang;
        }
        
        jQuery.get(wpv_get_ajax_pagination_url(dataNext), function(response) {
            window.wpvCachedPages[view_number][next_page] = response;
        });
    }
}
                
function wpv_pagination_load_previous_page(view_number, page, max_pages) {
    if (typeof window.wpvCachedPages == 'undefined' ) {
        window.wpvCachedPages = new Array();
    }
    if (typeof window.wpvCachedPages[view_number] == 'undefined' ) {
        window.wpvCachedPages[view_number] = new Array();
    }
    var previous_page = page-1;
    if (previous_page in window.wpvCachedPages[view_number]) {
        return false;
    }
    // LOAD PREVIOUS
    if (page > 1) {
        var dataPrevious = {};
        
        add_view_parameters(dataPrevious, previous_page, view_number);
        dataPrevious = add_url_query_parameters(dataPrevious);
        if (typeof(icl_lang) != 'undefined') {
            dataPrevious['lang'] = icl_lang;
        }
        jQuery.get(wpv_get_ajax_pagination_url(dataPrevious), function(response) {
            window.wpvCachedPages[view_number][previous_page] = response;
        });
    }
}
                
function wpv_pagination_cache_current_page(view_number, page, max_pages) {
    if (typeof window.wpvCachedPages == 'undefined' ) {
        window.wpvCachedPages = new Array();
    }
    if (typeof window.wpvCachedPages[view_number] == 'undefined' ) {
        window.wpvCachedPages[view_number] = new Array();
    }
    if (page in window.wpvCachedPages[view_number]) {
        return false;
    }
    // Cache current page
    if (page in window.wpvCachedPages[view_number] == false) {
        var dataCurrent = {};
        add_view_parameters(dataCurrent, page, view_number);
        dataCurrent = add_url_query_parameters(dataCurrent);
        if (typeof(icl_lang) != 'undefined') {
            dataCurrent['lang'] = icl_lang;
        }
        
        jQuery.get(wpv_get_ajax_pagination_url(dataCurrent), function(response) {
            window.wpvCachedPages[view_number][page] = response;
        });
    }
}
                
function wpv_pagination_get_page(view_number, next, effect, speed, response, wpvPaginatorLayout, wpvPaginatorFilter, callback_next) {
    var width = wpvPaginatorLayout.width();
    var height = wpvPaginatorLayout.height();
    wpvPaginatorLayout.attr('id', 'wpv-view-layout-'+view_number+'-response').wrap('<div class="wpv_slide_remove" style="width:'+width+'px;height:'+height+'px;overflow:hidden;" />').css('width', width);
                    
    var responseObj = jQuery('<div></div>').append(response);
    var responseView = responseObj.find('#wpv-view-layout-'+view_number);
    responseView.attr('id', 'wpv-view-layout-'+view_number).css('visibility', 'hidden').css('width', width);
    var responseFilter = responseObj.find('#wpv-filter-'+view_number).html();
//    wpvPaginatorFilter.html(responseFilter);
                    
    if (wpvPaginatorLayout.hasClass('wpv-pagination-preload-images')) {
        // Preload images
        var preloadImages = false;
        var preloadedImages = new Array();
        var images = responseView.find('img');
        if (images.length < 1) {
            wpv_pagination_slide(view_number, width, height, next, effect, speed, responseView, wpvPaginatorLayout, wpvPaginatorFilter, callback_next);
        } else {
            images.one('load', function() {
                preloadedImages.push(jQuery(this).attr('src'));
                if (preloadedImages.length == images.length) {
                    wpv_pagination_slide(view_number, width, height, next, effect, speed, responseView, wpvPaginatorLayout, wpvPaginatorFilter, callback_next);
                }
            }).each(function() {
                if(this.complete) {
                    jQuery(this).load();
                }
            });
        }
    } else {
        wpv_pagination_slide(view_number, width, height, next, effect, speed, responseView, wpvPaginatorLayout, wpvPaginatorFilter, callback_next);
    }
    wpvPaginatorFilter.html(responseFilter);
}
                
function wpv_pagination_slide(view_number, width, height, next, effect, speed, responseView, wpvPaginatorLayout, wpvPaginatorFilter, callback_next) {
                                       
    if (effect == 'slideh' || effect == 'slideleft' || effect == 'slideright') {
        if (effect == 'slideleft') {
            next = true;
        } else if (effect == 'slideright') {
            next = false;
        }
        if (next == true) {
            //                                responseView.css('position', 'relative').css('margin-left', width+'px').css('margin-top', '-'+height+'px').css('visibility', 'visible');
            wpvPaginatorLayout.css('float', 'left');
            responseView.css('float', 'left').css('visibility', 'visible');
            wpvPaginatorLayout.after(responseView).parent().children().wrapAll('<div style="width:5000px;" />');
            jQuery('#wpv_slide_loading_img_'+view_number).fadeOut(function(){jQuery(this).remove();});
            wpvPaginatorLayout.parent().delay(500).animate({marginLeft: '-'+width+'px'}, 1000, function(){
                wpvPaginatorLayout.parent().parent().animate({height: responseView.height()+'px'}, 500, function(){
                    responseView.css('position', 'static').css('float', 'none').css('height', wpvPaginatorLayout.parent().parent().height()+'px');
                    wpvPaginatorLayout.unwrap().unwrap().remove();
                    window.wpvPaginationAjaxLoaded[view_number] = true;
                    window.wpvPaginationAnimationFinished[view_number] = true;
                    if (callback_next != '') {
                        if (eval('typeof(' + callback_next + ') == \'function\'')) {
                            eval(callback_next+'();');
                        }
                    }
                    wpvPaginationQueueTrigger(view_number, next, wpvPaginatorFilter);
                });
            });
        } else {
            //                                responseView.css('position', 'relative').css('margin-left', '-'+width+'px').css('margin-top', '-'+height+'px').css('visibility', 'visible');
            wpvPaginatorLayout.css('float', 'right');
            responseView.css('float', 'right').css('visibility', 'visible');
            wpvPaginatorLayout.after(responseView).parent().children().wrapAll('<div style="width:' + (responseView.width()+wpvPaginatorLayout.width()) + 'px; margin-left:-' + (wpvPaginatorLayout.width()) + 'px;" />');
            jQuery('#wpv_slide_loading_img_'+view_number).fadeOut(function(){jQuery(this).remove();});
            wpvPaginatorLayout.parent().delay(500).animate({marginLeft: '0px'}, 1000, function(){
                wpvPaginatorLayout.parent().parent().animate({height: responseView.height()+'px'}, 500, function(){
                    responseView.css('position', 'static').css('margin', '0px').css('float', 'none').css('height', wpvPaginatorLayout.parent().parent().height()+'px');
                    wpvPaginatorLayout.unwrap().unwrap().remove();
                    window.wpvPaginationAjaxLoaded[view_number] = true;
                    window.wpvPaginationAnimationFinished[view_number] = true;
                    if (callback_next != '') {
                        if (eval('typeof(' + callback_next + ') == \'function\'')) {
                            eval(callback_next+'();');
                        }
                    }
                    wpvPaginationQueueTrigger(view_number, next, wpvPaginatorFilter);
                });
            });
        }
    } else if (effect == 'slidev' || effect == 'slideup' || effect == 'slidedown') {
        if (effect == 'slidedown') {
            next = false;
        } else if (effect == 'slideup') {
            next = true;
        }
        if (next == true) {
            responseView.css('visibility', 'visible');
            wpvPaginatorLayout.after(responseView).parent().children().wrapAll('<div />');
            jQuery('#wpv_slide_loading_img_'+view_number).fadeOut(function(){jQuery(this).remove();});
            wpvPaginatorLayout.parent().delay(500).animate({marginTop: '-'+height+'px'}, 1000, function(){
                wpvPaginatorLayout.parent().parent().animate({height: responseView.height()+'px'}, 500, function(){
                    responseView.css('position', 'static').css('margin', '0px');
                    wpvPaginatorLayout.unwrap().unwrap().remove();
                    window.wpvPaginationAjaxLoaded[view_number] = true;
                    window.wpvPaginationAnimationFinished[view_number] = true;
                    if (callback_next != '') {
                        if (eval('typeof(' + callback_next + ') == \'function\'')) {
                            eval(callback_next+'();');
                        }
                    }
                    wpvPaginationQueueTrigger(view_number, next, wpvPaginatorFilter);
                });
            });
        } else {
            responseView.css('visibility', 'visible');
            wpvPaginatorLayout.before(responseView).parent().children().wrapAll('<div />');
            wpvPaginatorLayout.parent().css('position', 'relative').css('margin-top', '-'+responseView.height()+'px');
            jQuery('#wpv_slide_loading_img_'+view_number).fadeOut(function(){jQuery(this).remove();});
            wpvPaginatorLayout.parent().delay(500).animate({marginTop: '0px'}, 1000, function(){
                wpvPaginatorLayout.parent().parent().animate({height: responseView.height()+'px'}, 500, function(){
                    responseView.css('position', 'static').css('margin', '0px');
                    wpvPaginatorLayout.unwrap().unwrap().remove();
                    window.wpvPaginationAjaxLoaded[view_number] = true;
                    window.wpvPaginationAnimationFinished[view_number] = true;
                    if (callback_next != '') {
                        if (eval('typeof(' + callback_next + ') == \'function\'')) {
                            eval(callback_next+'();');
                        }
                    }
                    wpvPaginationQueueTrigger(view_number, next, wpvPaginatorFilter);
                });
            });
        }
    } else { // Fade
        jQuery('#wpv_slide_loading_img_'+view_number).fadeOut(function(){jQuery(this).remove();});
        wpvPaginatorLayout.css('position', 'absolute').css('z-index', '5').after(responseView).next().css('position', 'static').prev().delay(500).fadeOut(speed+500, function(){
            wpvPaginatorLayout.unwrap().remove();
            window.wpvPaginationAjaxLoaded[view_number] = true;
            window.wpvPaginationAnimationFinished[view_number] = true;
            if (callback_next != '') {
                if (eval('typeof(' + callback_next + ') == \'function\'')) {
                    eval(callback_next+'();');
                }
            }
            wpvPaginationQueueTrigger(view_number, next, wpvPaginatorFilter);
        });
        responseView.hide().css('visibility', 'visible').delay(500).fadeIn(speed+500);
    }
}

////////////////////////////////////////////////////////////////
// links selector
////////////////////////////////////////////////////////////////

function wpv_pagination_replace_view_links(view_number, page, ajax, effect, max_pages, cache_pages, preload_pages, spinner, spinner_image, callback_next, stop_rollover) {
    for (var i = 1; i <= max_pages; i++) {
        if (i == page) {
            jQuery('#wpv-page-link-' + view_number + '-' + i).addClass('wpv_page_current');            
        } else {
            jQuery('#wpv-page-link-' + view_number + '-' + i).removeClass('wpv_page_current');            
        }
        
    }
    wpv_pagination_replace_view(view_number, page, ajax, effect, max_pages, cache_pages, preload_pages, spinner, spinner_image, callback_next, stop_rollover);
}

////////////////////////////////////////////////////////////////
// Rollover
////////////////////////////////////////////////////////////////

var wpv_stop_rollover = {};
window.wpvPaginationAjaxLoaded = {};
window.wpvPaginationAnimationFinished = {};
window.wpvPaginationQueue = {};

jQuery.fn.wpvRollover = function(){
    var args = arguments[0] || {id: 1, effect: "fade", speed: 5, page: 1, count: 1};
    var id = args.id;
    var effect = args.effect;
    var speed = args.speed*1000;
    var page = args.page;
    var count = args.count;
    var cache_pages = args.cache_pages;
    var preload_pages = args.preload_pages;
    var spinner = args.spinner;
    var spinner_image = args.spinner_image;
    var callback_next = args.callback_next;
    if (count > 1) {
        if ((id in window.wpvPaginationAjaxLoaded) && window.wpvPaginationAjaxLoaded[id] == false) {
            setTimeout('jQuery(this).wpvRollover({id:'+id+', effect:\''+effect+'\', speed:'+(speed/1000)+', page:'+page+', count:'+count+', cache_pages:'+cache_pages+', preload_pages:'+preload_pages+', spinner:\''+spinner+'\', spinner_image:\''+spinner_image+'\', callback_next:\''+callback_next+'\'})', 100);
            return false;
        }
        window.wpvPaginationAjaxLoaded[id] = false;
        var wpvInfiniteLoop = setTimeout(function(){
            if (effect == 'slideright' || effect == 'slidedown') {
                if (page <= 1) {
                    page = count;
                } else {
                    page--;
                }
            } else {
                if (page == count) {
                    page = 1;
                } else {
                    page++;
                }
            }
            
            if (!(id in wpv_stop_rollover)) {
                wpv_pagination_replace_view(id, page, true, effect, count, cache_pages, preload_pages, spinner, spinner_image, callback_next, false);
                jQuery(this).wpvRollover({id:id,
                                         effect:effect,
                                         speed:speed/1000, page:page,
                                         count:count,
                                         cache_pages:cache_pages,
                                         preload_pages:preload_pages,
                                         spinner:spinner,
                                         spinner_image:spinner_image,
                                         callback_next:callback_next});
            }
        }, speed);
    }
};

function wpvPaginationQueueTrigger(view_number, next, wpvPaginatorFilter) {
    if (view_number in window.wpvPaginationQueue && window.wpvPaginationQueue[view_number].length > 0) {
        var page = wpvPaginatorFilter.find('#wpv_paged-'+view_number).val();
        var max_pages = wpvPaginatorFilter.find('#wpv_paged_max-'+view_number).val();
        if (next) {
            page++;
        } else {
            page--;
        }
        if (page > max_pages) {
            page = 1;
        } else if (page < 1) {
            page = max_pages;
        }
        window.wpvPaginationQueue[view_number].sort();
        var args = window.wpvPaginationQueue[view_number][0];
        window.wpvPaginationQueue[view_number].splice(0, 1);
        wpv_pagination_replace_view(view_number, page, args[2], args[3], args[4], args[5], args[6], args[7], args[8], args[10]);
    }
}