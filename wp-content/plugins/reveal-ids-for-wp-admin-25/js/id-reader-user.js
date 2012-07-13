function ridwpa_add_user_ids( row ) {
	var reg = "user-([0-9]+)";
	var Ausdruck = new RegExp(reg, "i");
	var user_id = Ausdruck.exec(row.id)[1];
	jQuery("a[href^='user-edit.php?user_id=']:first", row).each(function() {
		jQuery(this).after(' <span style="font-weight:200;">(ID ' + user_id + ')</span>');
	});
}

function ridwpa_roll_through_user_rows() {
	jQuery("tr[id^='user-']").each(function() {
    	ridwpa_add_user_ids( this );
  	});
}

jQuery(document).ready(function() {
   ridwpa_roll_through_user_rows();
});