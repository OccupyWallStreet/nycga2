<?php

if( ! function_exists( 'bp_group_avatar_micro' ) ) {
	
	function bp_group_avatar_micro() {
		echo bp_get_group_avatar_micro();
	}
	
	function bp_get_group_avatar_micro() {
		return bp_get_group_avatar( 'type=thumb&width=15&height=15' );
	}
	
}

?>
