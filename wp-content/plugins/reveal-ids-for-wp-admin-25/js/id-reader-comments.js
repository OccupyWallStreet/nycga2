function ridwpa_add_comment_ids( row ) {
	var reg = "comment-([0-9]+)";
	var Ausdruck = new RegExp(reg, "i");
	var cat_id = Ausdruck.exec(row.id)[1];
	jQuery("td.author strong", row).each(function() {
		jQuery(this).append(' (ID ' + cat_id + ')');
	});
}

function ridwpa_roll_through_comment_rows() {
	jQuery("tr[id^='comment-']").each(function() {
    	ridwpa_add_comment_ids( this );
  	});
}

jQuery(document).ready(function() {
   ridwpa_roll_through_comment_rows();
});