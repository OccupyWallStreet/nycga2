jQuery(document).ready(function($) {
	//handle changelog slidedowns
	$('.wdv-view-link').click(function() {
		$(this).next('.wdv-changelog-drop').slideDown();
		$(this).hide();
		return false;
	});
	$('.wdv-close-link').click(function() {
		$(this).parent('.wdv-changelog-drop').hide();
		$(this).parent().prev('.wdv-view-link').show();
		return false;
	});
	$('a.upgrade-all').click(function() {
		$(this).parents("form").submit();
		return false;
	});
});