function re_init() {
   tb_init('a.thickbox, area.thickbox, input.thickbox');
}

function ame_ajax_form_mediadesc( postid, mediadesc ) {
	var ame_e = jQuery('#ame_mediadesc' + postid);
	var ame_text = jQuery('#ame_mediadesc_text' + postid).html();
	var revert_ame_e = ame_e.html();
	ame_e.html('<input type="text" id="ame-new-mediadesc' + postid + '" value="" style="font-size:0.8em;" /> <a href="javascript:void(0);" id="ame_mediadesc_save' + postid + '"><img src="' + ameAjaxL10n.imgUrl + 'save_small.gif" border="0" alt="' + ameAjaxL10n.Save + '" title="' + ameAjaxL10n.Save + '" /></a> <a href="javascript:void(0);" id="ame_mediadesc_cancel' + postid + '"><img src="' + ameAjaxL10n.imgUrl + 'cancel_small.gif" border="0" alt="' + ameAjaxL10n.Cancel + '" title="' + ameAjaxL10n.Cancel + '" /></a>');
	jQuery('input#ame-new-mediadesc' + postid).val( ame_text );
	jQuery('#ame_mediadesc' + postid + ' #ame_mediadesc_cancel' + postid).click(function() {
		ame_e.html( revert_ame_e );
	});
	jQuery('#ame_mediadesc' + postid + ' #ame_mediadesc_save' + postid).click(function() {
		var new_mediadesc = jQuery('input#ame-new-mediadesc' + postid).val();
		mediadescSpanFadeOut( postid, new_mediadesc );
	});
}

function mediadescSpanFadeOut( postid, ame_mediadesc ) {
	jQuery("span#ame_mediadesc" + postid).fadeOut('fast', function() {
		var loading = '<img border="0" alt="" src="' + ameAjaxL10n.imgUrl + 'loader.gif" align="absbottom" /> ' + ameAjaxL10n.pleaseWait;
		jQuery("span#ame_mediadesc" + postid).fadeIn('fast', function() {
			var ame_sack = new sack( ajaxurl );
			ame_sack.execute = 1;
			ame_sack.method = 'POST';
			ame_sack.setVar( "action", "ame_ajax_save_mediadesc" );
			ame_sack.setVar( "postid", postid );
			ame_sack.setVar( "new_mediadesc", ame_mediadesc );
			ame_sack.onError = function() { alert('Ajax error on saving media description'); };
			ame_sack.runAJAX();
		});
		jQuery("span#ame_mediadesc" + postid).html( loading );
	});
}

function ame_ajax_form_tags( postid, posttags ) {
	var ame_e = jQuery('#ame_tags' + postid);
	var revert_ame_e = ame_e.html();
	ame_e.html('<input type="text" id="ame-new-tags' + postid + '" value="' + posttags + '" style="font-size:0.8em;width:75%;" /> <a href="javascript:void(0);" id="ame_tag_save' + postid + '"><img src="' + ameAjaxL10n.imgUrl + 'save_small.gif" border="0" alt="' + ameAjaxL10n.Save + '" title="' + ameAjaxL10n.Save + '" /></a> <a href="javascript:void(0);" id="ame_tag_cancel' + postid + '"><img src="' + ameAjaxL10n.imgUrl + 'cancel_small.gif" border="0" alt="' + ameAjaxL10n.Cancel + '" title="' + ameAjaxL10n.Cancel + '" /></a>');
	jQuery('#ame_tags' + postid + ' #ame_tag_cancel' + postid).click(function() {
		ame_e.html( revert_ame_e );
	});
	jQuery('#ame_tags' + postid + ' #ame_tag_save' + postid).click(function() {
		var new_tags = jQuery('input#ame-new-tags' + postid).val();
		tagSpanFadeOut( postid, new_tags );
	});
	ame_setupSuggest( postid );
}

function tagSpanFadeOut( postid, ame_tags ) {
	jQuery("span#ame_tags" + postid).fadeOut('fast', function() {
		var loading = '<img border="0" alt="" src="' + ameAjaxL10n.imgUrl + 'loader.gif" align="absbottom" /> ' + ameAjaxL10n.pleaseWait;
		jQuery("span#ame_tags" + postid).fadeIn('fast', function() {
			var ame_sack = new sack( ajaxurl );
			ame_sack.execute = 1;
			ame_sack.method = 'POST';
			ame_sack.setVar( "action", "ame_ajax_save_tags" );
			ame_sack.setVar( "postid", postid );
			ame_sack.setVar( "new_tags", ame_tags );
			ame_sack.onError = function() { alert('Ajax error on saving tags'); };
			ame_sack.runAJAX();
		});
		jQuery("span#ame_tags" + postid).html( loading );
	});
}

function catSpanFadeOut( postid, ame_cats ) {
	//jQuery("span#ame_category" + postid + ", a#thickboxlink" + postid).fadeOut('fast', function() {
		var loading = '<img border="0" alt="" src="' + ameAjaxL10n.imgUrl + 'loader.gif" align="absbottom" /> ' + ameAjaxL10n.pleaseWait;
		jQuery("span#ame_category" + postid).fadeIn('fast', function() {
			var ame_sack = new sack( ajaxurl );
			ame_sack.execute = 1;
			ame_sack.method = 'POST';
			ame_sack.setVar( "action", "ame_ajax_save_categories" );
			ame_sack.setVar( "postid", postid );
			ame_sack.setVar( "ame_cats", ame_cats );
			ame_sack.onError = function() { alert('Ajax error on saving categories'); };
			ame_sack.onSuccess = function() { re_init(); };
			ame_sack.runAJAX();
			jQuery(".categorychoosewrap").html('');
		});
		jQuery("span#ame_category" + postid + "").html( loading );
	//});
}

function ame_ajax_get_categories( postid ) {
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 0;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_ajax_get_categories" );
	ame_sack.setVar( "postid", postid );
	ame_sack.onError = function() { alert('Ajax error on getting categories') };
	ame_sack.onCompletion = function() {
		jQuery('#categorychoosewrap' + postid).html( ame_sack.response );
		jQuery('#thickboxlink' + postid).delay(500).click();
	};
	ame_sack.runAJAX();
	
	return false;
}

function ame_ajax_save_categories( postid ) {
	tb_remove();
	var n = jQuery("#categorychoose" + postid + " #categorychecklist input:checked").length;
	var ame_cats = '';
	for(var a=0;a<n;a++){
		ame_cats += jQuery("#categorychoose" + postid + " #categorychecklist input:checked")[a].value + ',';
	}
	window.setTimeout("catSpanFadeOut(" + postid + ", '" + ame_cats + "')", 500);
}

function linkcatSpanFadeOut( linkid, ame_linkcats ) {
	//jQuery("span#ame_linkcategory" + linkid + ", a#thickboxlink" + linkid).fadeOut('fast', function() {
		var loading = '<img border="0" alt="" src="' + ameAjaxL10n.imgUrl + 'loader.gif" align="absbottom" /> ' + ameAjaxL10n.pleaseWait;
		jQuery("span#ame_linkcategory" + linkid).fadeIn('fast', function() {
			var ame_sack = new sack( ajaxurl );
			ame_sack.execute = 1;
			ame_sack.method = 'POST';
			ame_sack.setVar( "action", "ame_ajax_save_linkcategories" );
			ame_sack.setVar( "linkid", linkid );
			ame_sack.setVar( "ame_linkcats", ame_linkcats );
			ame_sack.onError = function() { alert('Ajax error on saving link categories'); };
			ame_sack.onSuccess = function() { re_init(); };
			ame_sack.runAJAX();
		});
		jQuery( "span#ame_linkcategory" + linkid ).html( loading );
	//});
}

function ame_ajax_save_linkcategories( linkid ) {
	var n = jQuery("#linkcategorychoose" + linkid + " #categorychecklist input:checked").length;
	var ame_linkcats = '';
	for(var a=0;a<n;a++){
		ame_linkcats += jQuery("#linkcategorychoose" + linkid + " #categorychecklist input:checked")[a].value + ',';
	}
	tb_remove();
	window.setTimeout("linkcatSpanFadeOut(" + linkid + ", '" + ame_linkcats + "')", 500);
}

function ame_ajax_set_commentstatus( postid, status, posttype ) {
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_ajax_set_commentstatus" );
	ame_sack.setVar( "postid", postid );
	ame_sack.setVar( "comment_status", status );
	ame_sack.setVar( "posttype", posttype );
	ame_sack.onError = function() { alert('Ajax error on toggling comment status') };
	ame_sack.runAJAX();
}

function ame_ajax_get_pageorder( pageordertable ) {
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_get_pageorder" );
	ame_sack.setVar( "pageordertable2", pageordertable );
	ame_sack.onError = function() { alert('Ajax error on getting page order') };
	ame_sack.runAJAX();
}

function ame_ajax_toggle_imageset( setid ) {
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_ajax_toggle_imageset" );
	ame_sack.setVar( "setid", setid );
	ame_sack.onError = function() { alert('Ajax error on toggling image set') };
	ame_sack.runAJAX();
}

function ame_ajax_toggle_showinvisposts( status ) {
	jQuery("#ame_toggle_showinvisposts").prop("value", ameAjaxL10n.pleaseWait);
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_toggle_showinvisposts" );
	ame_sack.setVar( "status", status );
	ame_sack.onError = function() { alert('Ajax error on toggling post visibility') };
	ame_sack.runAJAX();
}

function ame_ajax_toggle_orderoptions( status ) {
	jQuery("#ame_order2_loader").html(ameAjaxL10n.pleaseWait);
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_toggle_orderoptions" );
	ame_sack.setVar( "status", status );
	ame_sack.onError = function() { alert('Ajax error on toggling page order column') };
	ame_sack.runAJAX();
}

function ame_ajax_order_save( cat_id, posttype ) {
	var neworderid = jQuery("input#ame_" + posttype + "order" + cat_id).prop('value');
	jQuery("span#ame_order_loader" + cat_id).show();
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_save_order" );
	ame_sack.setVar( "category_id", cat_id );
	ame_sack.setVar( "new_orderid", neworderid );
	ame_sack.setVar( "posttype", posttype );
	ame_sack.onError = function() { alert('Ajax error on saving page order') };
	ame_sack.runAJAX();
}

function ame_slug_edit( cat_id, posttype ) {
	var newslug = jQuery("input#ame_slug" + cat_id).prop('value');
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_slug_edit" );
	ame_sack.setVar( "col_no", jQuery('.widefat thead tr th').length );
	ame_sack.setVar( "category_id", cat_id );
	ame_sack.setVar( "posttype", posttype );
	ame_sack.onError = function() { alert('Ajax error on editing post slug') };
	ame_sack.runAJAX();
}

function ame_ajax_set_visibility( post_id, status, posttype ) {
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_toggle_visibility" );
	ame_sack.setVar( "category_id", post_id );
	ame_sack.setVar( "vis_status", status );
	ame_sack.setVar( "posttype", posttype );
	ame_sack.onError = function() { alert('Ajax error on toggling visibility') };
	ame_sack.runAJAX();
}

function ame_ajax_set_sticky( post_id, posttype ) {
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_toggle_sticky" );
	ame_sack.setVar( "post_id", post_id );
	ame_sack.onError = function() { alert('Ajax error on toggling sticky') };
	ame_sack.runAJAX();
}

function ame_ajax_set_postdate( post_id, pickedDate, posttype ) {
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_set_date" );
	ame_sack.setVar( "category_id", post_id );
	ame_sack.setVar( "pickedDate", pickedDate );
	ame_sack.setVar( "posttype", posttype );
	ame_sack.onError = function() { alert('Ajax error on chosing new date') };
	ame_sack.runAJAX();
}

function ame_title_edit( cat_id, title_text, posttype ) {
	var anz_cols = jQuery('.widefat:first thead th:visible').length;
	var addHTML = '<tr id="alter' + posttype + '-' + cat_id + '" class="author-other status-publish" valign="middle" style="display:none;"><td colspan="' + anz_cols + '" align="center"><input type="text" value="' + unescape(title_text) + '" size="50" id="ame_title' + cat_id + '" /> <div class="button-group"><input value="' + ameAjaxL10n.Save + '" class="button button-primary primary" type="button" onclick="ame_ajax_title_save(\'' + cat_id + '\', \'' + posttype + '\');" /><input value="' + ameAjaxL10n.Cancel + '" class="button button-secondary secondary" type="button" onclick="ame_edit_cancel(\'' + cat_id + '\');" /></div></td></tr>';
	//jQuery("#" + posttype + "-" + cat_id).after( addHTML );
	jQuery("#" + posttype + "-" + cat_id).fadeOut( function() {
		jQuery("#" + posttype + "-" + cat_id).after( addHTML );
		jQuery("#alter" + posttype + "-" + cat_id).fadeIn();
	});
	//jQuery("#" + posttype + "-" + cat_id).hide();
}

function ame_ajax_title_save( cat_id, posttype ) {
	var newtitle = jQuery("input#ame_title" + cat_id).prop('value');
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_save_title" );
	ame_sack.setVar( "category_id", cat_id );
	ame_sack.setVar( "new_title", newtitle );
	ame_sack.setVar( "posttype", posttype );
	ame_sack.onError = function() { alert('Ajax error on saving post title') };
	ame_sack.runAJAX();
}

function ame_ajax_slug_save( cat_id, typenumber ) {
	var newslug = jQuery("input#ame_slug" + cat_id).prop('value');
	if ( newslug == '' || newslug == ' ' || newslug == '  ' ) {
		alert( ameAjaxL10n.slugEmpty );
		return;
	}
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_save_slug" );
	ame_sack.setVar( "category_id", cat_id );
	ame_sack.setVar( "new_slug", newslug );
	ame_sack.setVar( "typenumber", typenumber );
	ame_sack.onError = function() { alert('Ajax error on saving post slug') };
	ame_sack.runAJAX();
}

function ame_ajax_author_save( cat_id, typenumber ) {
	var newauthor = jQuery("select#author-" + cat_id).prop('value');
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_save_author" );
	ame_sack.setVar( "category_id", cat_id );
	ame_sack.setVar( "newauthor", newauthor );
	ame_sack.setVar( "typenumber", typenumber );
	ame_sack.onError = function() { alert('Ajax error on saving post authot') };
	ame_sack.runAJAX();
}

function ame_author_edit( post_id, posttype ) {
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_author_edit" );
	ame_sack.setVar( "post_id", post_id );
	ame_sack.setVar( "col_no", jQuery('.widefat thead tr th').length );
	ame_sack.setVar( "posttype", posttype );
	ame_sack.onError = function() { alert('Ajax error on editing author') };
	ame_sack.runAJAX();
}

function ame_ajax_set_linkvisibility( link_id ) {
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_toggle_linkvisibility" );
	ame_sack.setVar( "link_id", link_id );
	ame_sack.onError = function() { alert('Ajax error on toggling link visibility') };
	ame_sack.runAJAX();
}

function ame_edit_cancel( cat_id ) {
	jQuery("#alter" + ameAjaxL10n.postType + "-" + cat_id).hide();
	jQuery("#" + ameAjaxL10n.postType + "-" + cat_id).show();
}

function ame_ajax_set_excludestatus( page_id, status_id ) {
	var ame_sack = new sack( ajaxurl );
	ame_sack.execute = 1;
	ame_sack.method = 'POST';
	ame_sack.setVar( "action", "ame_toggle_excludestatus" );
	ame_sack.setVar( "pageid", page_id );
	ame_sack.setVar( "statusid", status_id );
	ame_sack.onError = function() { alert('Ajax error on toggling image set') };
	ame_sack.runAJAX();
}

function ame_check_all( id, status ) {
	jQuery('div#categorychoose' + id).find('ul li input:checkbox').prop('checked', status);
}