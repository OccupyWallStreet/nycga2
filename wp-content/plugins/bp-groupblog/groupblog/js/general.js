jQuery(document).ready( function() {
		
  jQuery('input[name=groupblog-enable-blog]').click(function(){
	  if (jQuery(this).is(':checked')) {
	    jQuery('input[name=groupblog-create-new]').removeAttr('disabled');
	    jQuery('select[name=groupblog-blogid]').removeAttr('disabled');
	    jQuery('input[name=groupblog-silent-add]').removeAttr('disabled');
	    jQuery('#groupblog-layout-options input[type=radio]').removeAttr('disabled');
	    if (jQuery('input[name=groupblog-silent-add]').is(':checked')) {
	      jQuery('#groupblog-member-options input[type=radio]').removeAttr('disabled');
	    } else {
	      jQuery('#groupblog-member-options input[type=radio]').attr('disabled','true');
	    }
		} else {
	    jQuery('input[name=groupblog-create-new]').attr('disabled','true');
	    jQuery('select[name=groupblog-blogid]').attr('disabled','true');
	    jQuery('input[name=groupblog-silent-add]').attr('disabled','true');
	    jQuery('#groupblog-member-options input[type=radio]').attr('disabled','true');
	    jQuery('#groupblog-layout-options input[type=radio]').attr('disabled','true');
		}
	});
 
  jQuery('input[name=groupblog-silent-add]').click(function(){
	  if (jQuery(this).is(':checked')) {
	    jQuery('#groupblog-member-options input[type=radio]').removeAttr('disabled');
		} else {
	    jQuery('#groupblog-member-options input[type=radio]').attr('disabled','true');
		}
	});
	  
});