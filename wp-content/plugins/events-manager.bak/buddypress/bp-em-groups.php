<?php
/**
 * @param EM_Event $EM_Event
 */
function bp_em_group_event_save($EM_Event){
	if( is_object($EM_Event) && empty($EM_Event->group_id) && !empty($_REQUEST['group_id']) && is_numeric($_REQUEST['group_id']) ){
		//we have been requested an event creation tied to a group, so does this group exist, and does this person have admin rights to it?
		if( groups_is_user_admin(get_current_user_id(), $_REQUEST['group_id']) ){
			$EM_Event->group_id = $_REQUEST['group_id'];
		}				
	}	
	return $EM_Event;
}
add_action('em_event_save_pre','bp_em_group_event_save',1,1);

/**
 * @param boolean $result
 * @param EM_Event $EM_Event
 */
function bp_em_group_event_can_manage( $result, $EM_Event){
	if( !$result && !empty($EM_Event->group_id) ){ //only override if already false, incase it's true
		if( groups_is_user_admin(get_current_user_id(),$EM_Event->group_id) && current_user_can('edit_events') ){
			//This user is an admin of the owner's group, so they can edit this event.
			return true;
		}
	}
	return $result;
}
add_action('em_event_can_manage','bp_em_group_event_can_manage',1,2);


function bp_em_group_events_accepted_searches($searches){
	$searches[] = 'group';
	return $searches;
}
add_filter('em_accepted_searches','bp_em_group_events_accepted_searches',1,1);

function bp_em_group_events_get_default_search($searches, $array){
	if( !empty($array['group']) && (is_numeric($array['group']) || $array['group'] == 'my') ){
		$searches['group'] = $array['group'];
	}
	return $searches;
}
add_filter('em_events_get_default_search','bp_em_group_events_get_default_search',1,2);

function bp_em_group_events_build_sql_conditions( $conditions, $args ){
	if( !empty($args['group']) && is_numeric($args['group']) ){
		$conditions['group'] = "( `group_id`={$args['group']} )";
	}elseif( !empty($args['group']) && $args['group'] == 'my' ){
		$groups = groups_get_user_groups(get_current_user_id());
		if( count($groups) > 0 ){
			$conditions['group'] = "( `group_id` IN (".implode(',',$groups['groups']).") )";
		}
	}
	return $conditions;
}
add_filter('em_events_build_sql_conditions','bp_em_group_events_build_sql_conditions',1,2);