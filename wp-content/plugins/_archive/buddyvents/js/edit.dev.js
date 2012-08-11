var map;
var mark;
var markersArray = [];

function def_map_initialize() {
    var b = new google.maps.LatLng(editLat, editLng);
    var a = {
        zoom: 2,
        center: b,
        mapTypeId: google.maps.MapTypeId.HYBRID
    };
    map = new google.maps.Map(document.getElementById("loc-map"), a);
    mark = new google.maps.Marker({
        position: b,
        map: map
    });
    google.maps.event.addListener(map, "click", function (c) {
        var d = c.latLng;
        mark.setMap(null);
        placeMarker(d);
        d = String(d);
        d = d.split(", ");
        jQuery("#map_location_lat").val(d[0].substr(1, d[0].length));
        jQuery("#map_location_lng").val(d[1].substr(0, d[1].length - 1))
    })
}
function placeMarker(b) {
    deleteOverlays();
    var a = new google.maps.Marker({
        position: b,
        map: map
    });
    markersArray.push(a);
    map.setCenter(b)
}
function deleteOverlays() {
    if (markersArray) {
        for (i in markersArray) {
            markersArray[i].setMap(null)
        }
        markersArray.length = 0
    }
}
jQuery(document).ready(function () {
    if (jQuery("#no_coords").is(":checked")) {
        jQuery(".colorbox").hide()
    } else {
        jQuery(".colorbox").show()
    }
    jQuery("#no_coords").click(function () {
        if (jQuery(this).is(":checked")) {
            jQuery(".colorbox").hide()
        } else {
            jQuery(".colorbox").show()
        }
    });
    jQuery("#manual_coords").click(function () {
        if (jQuery(this).is(":checked")) {
            jQuery("#map_location_lng").val(editLng);
            jQuery("#map_location_lat").val(editLat)
        } else {
            jQuery("#map_location_lat,#map_location_lng").val("")
        }
    });
    var b = jQuery("#start_date").val();
    b = b.split("-");
    var a = jQuery("#end_date").val();
    a = a.split("-");
    var c = jQuery("#start_date,#end_date").datepicker({
    	firstDay: weekStart,
        minDate: "+1d",
        changeMonth: false,
        changeYear: false,
        dateFormat: "yy-mm-dd",
        onSelect: function (e) {
            var f = this.id == "start_date" ? "minDate" : "maxDate",
                d = jQuery(this).data("datepicker");
            date = jQuery.datepicker.parseDate(d.settings.dateFormat || jQuery.datepicker._defaults.dateFormat, e, d.settings);
            c.not(this).datepicker("option", f, date)
        }
    });
    jQuery('#start_time,#end_time').timepicker({
		ampm:clockType
	});
    jQuery("#change-coords").hide();
    jQuery("#coords-change").click(function () {
        jQuery("#change-coords").toggle("slow", function () {
            google.maps.event.trigger(map, "resize");
            var c = new google.maps.LatLng(editLat, editLng);
            map.setCenter(c)
        });
        return false
    });
    def_map_initialize()
});