function ridwpa_add_cat_ids( row ) {
	var reg = "cat-([0-9]+)";
	var Ausdruck = new RegExp(reg, "i");
	var cat_id = Ausdruck.exec(row.id)[1];
	jQuery("a[href^='categories.php?action=edit&cat_ID=']:first", row).each(function() {
		jQuery(this).append(' (ID ' + cat_id + ')');
	});
}

function ridwpa_roll_through_cat_rows() {
	jQuery("tr[id^='cat-']").each(function() {
    	ridwpa_add_cat_ids( this );
  	});
}

jQuery(document).ready(function() {
   ridwpa_roll_through_cat_rows();
});