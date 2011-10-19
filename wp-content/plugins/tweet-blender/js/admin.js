/**
 * @author kirill
 * 
 * v3.3.10
 */
var ajaxURLs = new Array(),
screenNamesCount = 0;
  
// make tabs
jQuery(document).ready(function(){
	
	// don't do anything if we are not showing full admin page
	if (jQuery('#icon-tweetblender').length <= 0) {
		return;
	}

	// Bind event listener to save button
	jQuery('#btn_save_settings').click(function() {
		jQuery('#settings_form').submit();
	});
		
	// initialize tabs
    var tabsElement = jQuery("#tabs").tabs({
	    show:function(event, ui) {
			
			// find out index
			var tabsEl = jQuery('#tabs').tabs();
			var selectedTabIndex = tabsEl.tabs('option', 'selected');

	        jQuery('#tb_tab_index').val(selectedTabIndex);
	        return true;
	    }
	});
	
	// reopen last used tab
	if (typeof(lastUsedTabId) != 'undefined') {
		tabsElement.tabs('select', lastUsedTabId);
	}

	// bind event handler to disable archive checkbox
	jQuery('#archive_is_disabled').click(function() {
		if (jQuery('#archive_is_disabled').is(':checked')) {
			jQuery('#archivesettings tr').slice(1).hide();
		}
		else {
			jQuery('#archivesettings tr').slice(1).show();
		}
	});
	
	// bind event handler to mutually exclusive filters
	jQuery('#filter_hide_replies').click(function() {
		if (jQuery(this).is(':checked')) {
			jQuery('#filter_hide_not_replies').removeAttr('checked');
		}
	});
	jQuery('#filter_hide_not_replies').click(function() {
		if (jQuery(this).is(':checked')) {
			jQuery('#filter_hide_replies').removeAttr('checked');
		}
	});

	// check limit for admin's PC
	jQuery.ajax({
		url: 'http://twitter.com/account/rate_limit_status.json',
		dataType: 'jsonp',
		success: function(json){
			var hitsLeftHtml = '';
			if (json.remaining_hits > 0) {
				hitsLeftHtml = 	'<span class="pass">' + json.remaining_hits + '</span>';
			}
			else {
				hitsLeftHtml = '<span class="fail">0</span>';
			}
			jQuery('#locallimit').html(
				TB_labels.limit_num.format(json.hourly_limit) + ' &middot; ' + 
				TB_labels.limit_left.format(hitsLeftHtml) + ' &middot; ' + 
				TB_labels.limit_reset + TB_verbalTime(TB_str2date(json.reset_time))
			);
		},
		error: function(){
			jQuery('#locallimit').html('<span class="fail">' + TB_labels.check_fail + '</span>');
		}
	});	
	
	// if there were any problems, highlight the Status tab
	if(jQuery('span.fail').length > 0) {
		jQuery('#statustab a').children('span').addClass('fail');
	}

	// Cache Manager add-on
	if (typeof(TB_cacheManagerAvailable) == 'undefined' || !TB_cacheManagerAvailable) {
		jQuery('#cache-manager-tab a').css('text-decoration','line-through');
		jQuery('img.tb-addon-screenshot').parent().fancybox();
	}

	// nStyle add-on
	if (typeof(TB_nStyleAvailable) == 'undefined' || !TB_nStyleAvailable) {
		jQuery('#nstyle-tab a').css('text-decoration','line-through');
		jQuery('img.tb-addon-screenshot').parent().fancybox();
	}
});

function TB_showPopup(dialogName) {
	$('#' + dialogName).dialog('open');
}


// Twitter oAuth window
function tAuth(url) {
	var tWin = window.open(url,'tWin','width=800,height=410,toolbar=0,location=1,status=0,menubar=0,resizable=1');
}