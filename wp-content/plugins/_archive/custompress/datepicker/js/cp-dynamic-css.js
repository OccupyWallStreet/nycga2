/**
* Allows dynamic switching of the datepicker style sheets
*/
function update_stylesheet(url) {
	new_stylesheet=url;
	if(jQuery("#cp_dynamic_css").length==0){
		jQuery("head").append("<link/>");
		css=jQuery("head").children(":last");
		css.attr({id:"cp_dynamic_css",rel:"stylesheet",type:"text/css",href:new_stylesheet})
	}else{
		jQuery("#cp_dynamic_css").attr("href",new_stylesheet)
	}
}
