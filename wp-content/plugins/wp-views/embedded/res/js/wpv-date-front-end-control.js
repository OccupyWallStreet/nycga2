jQuery(document).ready(function() {
    jQuery(".wpv-date-front-end").datepicker({
        onSelect: function(dateText, inst) {
            data = 'date=' + dateText;
            data += '&date-format=' + jQuery(this).prev().val();
            data += '&action=wpv_format_date';

            // show the spinner            
            //jQuery(this).next().show();

            var control = this;
            jQuery.post(front_ajaxurl, data, function(response) {
                response = jQuery.parseJSON(response);

                jQuery(control).prev().prev().val(response['timestamp']);

                jQuery(control).prev().prev().prev().html(response['display']);
                
            });
            
        },
        dateFormat : 'ddmmyy',
        showOn: "button",
        buttonImage: wpv_calendar_image,
        buttonText: wpv_calendar_text,
        buttonImageOnly: true
        
        
    });
    
    jQuery("div.ui-datepicker").css('font-size', '12px');
    
});


