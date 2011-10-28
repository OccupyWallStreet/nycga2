/* Register onclick event */
function registerTagClick() {
	jQuery('#taglist ul li span').bind("click", function(){
		addTagToInput(this.innerHTML, "renametag_old");
		addTagToInput(this.innerHTML, "deletetag_name");
		addTagToInput(this.innerHTML, "addtag_match");
		addTagToInput(this.innerHTML, "tagname_match");
	});
}

/* Register ajax nav and reload event once ajax data loaded */
function registerAjaxNav() {
	jQuery(".navigation a").click(function() {
		jQuery("#tagslist").load(this.href, function(){
  			registerTagClick();
  			registerAjaxNav();
		});
		return false;
	});
}

/* Add tag into input */
function addTagToInput( tag, name_element ) {
	var input_element = document.getElementById( name_element );

	if ( input_element.value.length > 0 && !input_element.value.match(/,\s*$/) )
		input_element.value += ", ";

	var comma = new RegExp(tag + ",");
	if ( !input_element.value.match(comma) )
		input_element.value += tag + ", ";

	return true;
}