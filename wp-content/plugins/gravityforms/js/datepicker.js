jQuery(document).ready(
    function() {
        jQuery('.datepicker').each(
            function (){
                var element = jQuery(this);
                var format = element.hasClass("mdy") ? "mm/dd/yy" : "dd/mm/yy";

                var image = "";
                var showOn = "focus";
                if(element.hasClass("datepicker_with_icon")){
                    showOn = "both";
                    image = jQuery('#gforms_calendar_icon_' + this.id).val();
                }

                element.datepicker({ yearRange: '-100:+10', showOn: showOn, buttonImage: image, buttonImageOnly: true, dateFormat: format });
            }
        );
    }
);

