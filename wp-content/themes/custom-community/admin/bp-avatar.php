<?php
/*
* Description
*/

/**
* This function ...
*/
function cc_add_avatar_options($options, $tab_id){
    if($tab_id == 'buddypress'){
        $avatar_options = array(
            new TextOption(
                __("Default avatar",'cc'), 
                __("Type url for new default avatar.<br/><br/> For example: http://mysitename/wp-content/uploads/new_default_avatar.jpg<br/>",'cc'), 
                "bp_avatar",
                __("",'cc'))
        );
        $options = array_merge( $avatar_options, $options );
    }
    return $options;
}
add_filter('cc_cap_get_options', 'cc_add_avatar_options', 100, 2);

/**
* This function ...
*/
function cc_reset_default_bp_avatar(){
	if( !defined( 'BP_VERSION' ) ) return;

    global $cap, $bp;
    $new_thumb_url = $cap->bp_avatar;

    if( !empty($new_thumb_url) ){
        $bp->avatar->thumb->default = $new_thumb_url;
        $bp->avatar->full->default = $new_thumb_url;
    }
}
add_action('bp_core_set_avatar_globals' , 'cc_reset_default_bp_avatar');