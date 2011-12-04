jQuery(document).ready( function($) {		
	$('input[name=groupblog-enable-blog]').click(function(){
		bpgb_toggle_options(this);
	});
 
	$('input[name=groupblog-silent-add]').click(function(){
	  if ($(this).is(':checked')) {
	    $('#groupblog-member-options input[type=radio]').removeAttr('disabled');
		} else {
	    $('#groupblog-member-options input[type=radio]').attr('disabled','true');
		}
	});
	
	var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	for(var i = 0; i < hashes.length; i++) {
		hash = hashes[i].split('=');
		if ( 'create_error' == hash[0] ) {
			var b = $('input[name=groupblog-enable-blog]');
			$(b).attr('checked','checked');
			bpgb_toggle_options(b);
		}
	}
	
	function bpgb_toggle_options(a) {
		if ($(a).is(':checked')) {
			$('input[name=groupblog-create-new]').removeAttr('disabled');
			$('select[name=groupblog-blogid]').removeAttr('disabled');
			$('input[name=groupblog-silent-add]').removeAttr('disabled');
			$('#groupblog-layout-options input[type=radio]').removeAttr('disabled');
			if ($('input[name=groupblog-silent-add]').is(':checked')) {
				$('#groupblog-member-options input[type=radio]').removeAttr('disabled');
			} else {
				$('#groupblog-member-options input[type=radio]').attr('disabled','true');
			}
		} else {
			$('input[name=groupblog-create-new]').attr('disabled','true');
			$('select[name=groupblog-blogid]').attr('disabled','true');
			$('input[name=groupblog-silent-add]').attr('disabled','true');
			$('#groupblog-member-options input[type=radio]').attr('disabled','true');
			$('#groupblog-layout-options input[type=radio]').attr('disabled','true');
		}
	}
	  
},(jQuery));