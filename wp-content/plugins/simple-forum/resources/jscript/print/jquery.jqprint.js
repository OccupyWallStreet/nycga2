// -----------------------------------------------------------------------
// eros@recoding.it
// jqprint 0.3
//
// - 19/06/2009 - some new implementations, added Opera support
// - 11/05/2009 - first sketch
//
// Printing plug-in for jQuery, evolution of jPrintArea: http://plugins.jquery.com/project/jPrintArea
// requires jQuery 1.3.x
//------------------------------------------------------------------------

(function(jQuery) {
    var opt;

    jQuery.fn.jqprint = function (options) {
        opt = jQuery.extend({}, jQuery.fn.jqprint.defaults, options);

        var jQueryelement = (this instanceof jQuery) ? this : jQuery(this);

        if (opt.operaSupport && jQuery.browser.opera)
        {
            var tab = window.open("","jqPrint-preview");
            tab.document.open();

            var doc = tab.document;
        }
        else
        {
            var jQueryiframe = jQuery("<iframe  />");

            if (!opt.debug) { jQueryiframe.css({ position: "absolute", width: "0px", height: "0px", left: "-600px", top: "-600px" }); }

            jQueryiframe.appendTo("body");
            var doc = jQueryiframe[0].contentWindow.document;
        }

        if (opt.importCSS)
        {
            if (jQuery("link[media=print]").length > 0)
            {
                jQuery("link[media=print]").each( function() {
                    doc.write("<link type='text/css' rel='stylesheet' href='" + jQuery(this).attr("href") + "' media='print' />");
                });
            }
            else
            {
                jQuery("link").each( function() {
                    doc.write("<link type='text/css' rel='stylesheet' href='" + jQuery(this).attr("href") + "' />");
                });
            }
        }

        if (opt.printContainer) { doc.write(jQueryelement.outer()); }
        else { jQueryelement.each( function() { doc.write(jQuery(this).html()); }); }

        doc.close();

        (opt.operaSupport && jQuery.browser.opera ? tab : jQueryiframe[0].contentWindow).focus();
        setTimeout( function() { (opt.operaSupport && jQuery.browser.opera ? tab : jQueryiframe[0].contentWindow).print(); if (tab) { tab.close(); } }, 1000);
    }

    jQuery.fn.jqprint.defaults = {
		debug: false,
		importCSS: true,
		printContainer: true,
		operaSupport: true
	};

    // Thanks to 9__, found at http://users.livejournal.com/9__/380664.html
    jQuery.fn.outer = function() {
      return jQuery(jQuery('<div></div>').html(this.clone())).html();
    }
})(jQuery);