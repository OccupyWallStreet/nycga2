function ridwpa_add_linkcat_ids( row ) {
	var reg = "cat-([0-9]+)";
	var Ausdruck = new RegExp(reg, "i");
	var linkcat_id = Ausdruck.exec(row.id)[1];
	jQuery("a[href^='link-category.php?action=edit&cat_ID=']:first", row).each(function() {
		jQuery(this).after(' (ID ' + linkcat_id + ')');
	});
}

function ridwpa_roll_through_linkcat_rows() {
	jQuery("tr[id^='link-cat-']").each(function() {
    	ridwpa_add_linkcat_ids( this );
  	});
}

jQuery(document).ready(function() {
   ridwpa_roll_through_linkcat_rows();
});