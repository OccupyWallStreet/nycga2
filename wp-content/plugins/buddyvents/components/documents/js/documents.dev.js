jQuery(document).ready( function() {	
	jQuery('.del-document').live( 'click', function() {
		jQuery(this).parent('.MultiFile-label').remove();
		return false;
	});

	jQuery('#docs').MultiFile({
		STRING: {
			remove:'X',
			file:''
		},
		list: '#doc-wrapper',
		afterFileAppend: function(element, value, master_element){
			jQuery('.MultiFile-remove').addClass('button');
			jQuery('.MultiFile-label').last().append('<label for="name-'+ value +'">* '+ bpeDocs.fileName +'</label><input type="text" id="name-'+ value +'" name="doc['+ value +'][name]" value="'+ value +'" /><label for="desc-'+ value +'">'+ bpeDocs.fileDesc +'</label><textarea id="desc-'+ value +'" name="doc['+ value +'][desc]"></textarea>');
		}
	});
});