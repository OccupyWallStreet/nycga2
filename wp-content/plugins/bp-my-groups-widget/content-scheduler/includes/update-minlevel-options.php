<?php
// 4/26/2011 4:05:56 PM
// Check for and update the following postmeta values:
// min-level from numbers to level_NUMBERS

// Get the min-level option
$options = get_option('ContentScheduler_Options');
if( isset( $options['min-level'] ) )
{
	// get the current min_level
	$min_level = $options['min-level'];
	$new_level = '';
	// I'm going ahead with the switch right now instead of a simple level_ + current value because if there is something unexpected I can set default.
	// change based on numeric value
	switch($min_level)
	{
		case 0:
			$new_level = 'level_0';
			break;
		case 1:
			$new_level = 'level_1';
			break;
		case 2:
			$new_level = 'level_2';
			break;
		case 3:
			$new_level = 'level_3';
			break;
		case 4:
			$new_level = 'level_4';
			break;
		case 5:
			$new_level = 'level_5';
			break;
		case 6:
			$new_level = 'level_6';
			break;
		case 7:
			$new_level = 'level_7';
			break;
		case 8:
			$new_level = 'level_8';
			break;
		case 9:
			$new_level = 'level_9';
			break;
		case 10:
			$new_level = 'level_10';
			break;
		default:
			$new_level = 'level_1';
	} // end switch
	// now update the option in the database
	$options['min-level'] = $new_level;
	update_option( 'ContentScheduler_Options', $options );
} // end if checking for existence of min-level option
// else do nothing
?>
