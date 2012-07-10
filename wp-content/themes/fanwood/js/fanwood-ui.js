/**
 * Fanwood theme jQuery.
 */
$j = jQuery.noConflict();

$j(document).ready(
	function() {

		$j('.ui-accordion').accordion({
			clearStyle: true,
			navigation: true
		});
		
		$j('.ui-tabs').tabs();

	}
);