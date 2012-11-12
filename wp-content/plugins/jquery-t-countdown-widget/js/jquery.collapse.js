/*!
 * jQuery Collapse-O-Matic for T-Minus v1.1
 * http://www.twinpictures.de/
 *
 * Copyright 2011, Twinpictures
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, blend, trade,
 * bake, hack, scramble, difiburlate, digest and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

jQuery(document).ready(function() {
	jQuery('.collapseomatic:not(.colomat-close)').each(function(index) {
	    var thisid = jQuery(this).attr('id');
	    jQuery('#target-'+thisid).css('display', 'none');
    });
	
    jQuery('.collapseomatic').livequery('click', function(event) {
		//alert('phones ringin dude');
		jQuery(this).toggleClass('colomat-close');
		var id = jQuery(this).attr('id');
		jQuery('#target-'+id).slideToggle('fast', function() {
		    // Animation complete.
		});
	});
	
	jQuery('.rockstar').livequery('click', function(event) {
		//alert('phones ringin dude');
		var id = jQuery(this).attr('id');
		var key = jQuery(this).val();
		jQuery('.isrockstar').each(function(){
			jQuery(this).val('Rockstar Features:');
		});
	});
});