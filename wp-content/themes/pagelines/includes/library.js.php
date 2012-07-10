<?php
/**
 * Fix The WordPress Login Image URL
 */

/**
 * PageLines JavaScript Ready Start
 *
 * Create Character Data wrapper so JavaScript contained within will not be parsed incorrectly
 * @internal Should be used with pl_js_end
 * @internal NOTE the opening bracket is returned so a closing bracket should be included in the proceeding code.
 *
 * @return string - Opens CDATA wrapper and starts jQuery document ready script
 * @todo Review if opening bracket can be removed ... or added to pl_js_end
 */
function pl_js_ready_start(){
	return '<script type="text/javascript">/* <![CDATA[ */ jQuery(document).ready(function () {';
}


/**
 * PageLines JavaScript End
 *
 * Closes the Character Data wrapper
 * @internal Should be used with pl_js_ready_start
 *
 * @return string - closes CDATA wrapper
 */
function pl_js_end(){
	return '/* ]]> */ </script>';
}
