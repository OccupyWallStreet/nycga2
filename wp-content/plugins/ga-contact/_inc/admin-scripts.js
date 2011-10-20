jQuery(document).ready(function(){

	var status = jQuery('input:checkbox.bpge_allgroups').attr('checked');

	if ( status && status == 'checked'){
		jQuery('input:checkbox.bpge_allgroups').change( function(){
			jQuery('input:checkbox.bpge_groups').attr('checked', '');
			jQuery('input:checkbox.bpge_allgroups').attr('checked', '');
		});
	}

	if ( !status || status == ''){
		jQuery('input:checkbox.bpge_allgroups').change( function(){
			jQuery('input:checkbox.bpge_groups').attr('checked', 'checked');
			jQuery('input:checkbox.bpge_allgroups').attr('checked', 'checked');
		});
	}

	jQuery('input:checkbox.bpge_groups').change( function(){
		jQuery('input:checkbox.bpge_allgroups').attr('checked', '');
  });



});