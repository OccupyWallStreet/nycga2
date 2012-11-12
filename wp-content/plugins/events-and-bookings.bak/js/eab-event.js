jQuery(function() {
    jQuery(".hide-if-no-js").show();
    jQuery("#wpmudevevents-rsvps-response").hide();
    jQuery("#wpmudevevents-hide-rsvps").hide();
    
    jQuery("#wpmudevevents-load-rsvps").click(
        function () {
            jQuery("#wpmudevevents-rsvps-response").show();
            jQuery("#wpmudevevents-hide-rsvps").show();
            jQuery(this).hide();
            if (jQuery("#wpmudevevents-rsvps-response").text() == "") {
                jQuery("#wpmudevevents-rsvps-response").text("Loading...");
                jQuery("#wpmudevevents-rsvps-response").load(jQuery("#wpmudevevents-load-rsvps").attr("href"));
            }
            return false;
        }
    );
    
    jQuery("#wpmudevevents-hide-rsvps").click(
        function () {
            jQuery("#wpmudevevents-rsvps-response").hide();
            jQuery("#wpmudevevents-load-rsvps").show();
            jQuery(this).hide();
            if (jQuery("#wpmudevevents-rsvps-response").text() == "") {
                jQuery("#wpmudevevents-rsvps-response").text("Loading...");
                jQuery("#wpmudevevents-rsvps-response").load(jQuery("#wpmudevevents-load-rsvps").attr("href"));
            }
            return false;
        }
    );
    
    jQuery(".eab-buy_tickets-target").hide();
    jQuery(".eab-buy_tickets-trigger")
    	.show()
        //.on('click', function () {
    	.click(function () {
    		jQuery(this).hide().parent().find(".eab-buy_tickets-target").show();
    		return false;
    	})
    ;
});