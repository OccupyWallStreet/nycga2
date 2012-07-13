<?php

/**
 * Single User Edit Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div id="bbpress-forums">
	
	<?php do_action( 'bbp_template_notices' );

	// Profile details
	bbp_get_template_part( 'user', 'details' );

	// User edit form
	bbp_get_template_part( 'form', 'user-edit' ); ?>

</div>
