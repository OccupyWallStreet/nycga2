jQuery(document).ready(function($) {
    function getClientWidth() { return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;   }
    function getClientHeight() { return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight; }

    jQuery(function() {
        var hideDelay = 0;
        var hideTimer = null;
        var x; // hack: variable for ajax object
        var showDelay = 1000 * ajax_delay; // delay before the pop-up shows itself in ms

        var container = jQuery('<div id="popupContainer"><table width="" border="0" cellspacing="0" cellpadding="0" align="center" class="popupBubble"><tr><td class="corner topLeft"></td><td class="top"></td><td class="corner topRight"></td></tr><tr><td class="left">&nbsp;</td><td><div id="popupContent"></div></td><td class="right">&nbsp;</td></tr><tr><td class="corner bottomLeft">&nbsp;</td><td class="bottom">&nbsp;</td><td class="corner bottomRight"></td></tr></table></div>');

		jQuery('body').append(container);

        jQuery('.avatar').live('mouseover', function() {

			// get user or group ID and the type itself
            var elem_var = String(jQuery(this).attr('rel')).split('_');
            var elemType = elem_var[0];
            var elemID = elem_var[1];
            if ( !elemID ) return;

            if (hideTimer)
                clearTimeout(hideTimer);

            var pos = jQuery(this).offset();
            var width = jQuery(this).width();

            $.data(this, 'timer', setTimeout(function() {


                // hack
                // display the container before the ajax request
				var right = getClientWidth() - pos.left - width;
				var boxWidth = jQuery('div#popupContainer').width();
				if ( boxWidth < right ) {
					container.css({
						left: (pos.left + width) + 'px',
						top: pos.top - 5 + 'px'
					});
				}else{
					container.css({
						left: (pos.left - boxWidth) + 'px',
						top: pos.top - 5 + 'px'
					})
				}
				container.css('display', 'block');

                // populate the popup with a loader.gif
                var loading = '<img src="'+ajax_image+'/ajax-loader.gif" alt="Loading" />';
                jQuery('div#popupContent').html(loading);

                // check for the current ajax request and abort it if needed
                if(x) {x.abort(); x = null; }
                // end hack

                x = $.ajax({
                    type: 'GET',
                    url: ajax_url,
                    data: {
						ID: elemID,
						type: elemType,
						action: 'cd_ab_the_avatardata'
                    },
                    success: function(data) {

                        // Verify requested person is this person since we could have multiple ajax requests out if the server is taking a while.
                        if (data.indexOf(elemID) > 0) {
                            var text = jQuery(data).html();
                            jQuery('div#popupContent').html(text);
                                var right = getClientWidth() - pos.left - width;
                                var boxWidth = jQuery('div#popupContainer').width();
                                if ( boxWidth < right ) {
                                    container.css({
                                        left: (pos.left + width) + 'px',
                                        top: pos.top - 5 + 'px'
                                    });
                                }else{
                                    container.css({
                                        left: (pos.left - boxWidth) + 'px',
                                        top: pos.top - 5 + 'px'
                                    })
                                }
                            container.css('display', 'block');
                        }
                    }
                });


             }, showDelay ));


        }).live('mouseout', function() {
            clearTimeout($.data(this, 'timer'));
        });

        jQuery('.avatar').live('mouseout', function() {
            if (hideTimer)
                clearTimeout(hideTimer);
            hideTimer = setTimeout(function() {
                // hack: abort the ajax request
                if(x) {x.abort(); x = null; }
                container.css('display', 'none');
            }, hideDelay);
        });

        // Allow mouseover of details without hiding details
        jQuery('#popupContainer').mouseover(function() {
            if (hideTimer)
                clearTimeout(hideTimer);
        });

        // Hide after mouseout
        jQuery('#popupContainer').mouseout(function() {
            if (hideTimer)
                clearTimeout(hideTimer);
            hideTimer = setTimeout(function() {
                // hack: abort the ajax request;
                if(x) {x.abort(); x = null; }
                container.css('display', 'none');
            }, hideDelay);
        });
    });

});