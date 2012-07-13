<?php
/*
Simple:Press
Admin Support Routines
$LastChangedDate: 2011-01-28 22:15:16 -0700 (Fri, 28 Jan 2011) $
$Rev: 5371 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sfa_get_forums_in_group($groupid)
{
	global $wpdb;
	return $wpdb->get_results("SELECT * FROM ".SFFORUMS." WHERE group_id=".$groupid." ORDER BY forum_seq");
}

function sfa_get_group_forums_by_parent($groupid, $parentid)
{
	global $wpdb;
	return $wpdb->get_results("SELECT * FROM ".SFFORUMS." WHERE group_id=".$groupid." AND parent=".$parentid." ORDER BY forum_seq");
}

function sfa_get_forums_all()
{
	global $wpdb;

	return $wpdb->get_results(
		"SELECT forum_id, forum_name, ".SFGROUPS.".group_id, group_name
		 FROM ".SFFORUMS."
		 JOIN ".SFGROUPS." ON ".SFFORUMS.".group_id = ".SFGROUPS.".group_id
		 ORDER BY group_seq, forum_seq");
}

function sfa_create_group_select($groupid = 0)
{
	$groups = sf_get_groups_all();
	$out='';
	$default='';

	$out.= '<option value="">'.__("Select Forum Group:", "sforum").'</option>';

	if($groups)
	{
		foreach($groups as $group)
		{
			if($group->group_id == $groupid)
			{
				$default = 'selected="selected" ';
			} else {
				$default - null;
			}
			$out.='<option '.$default.'value="'.$group->group_id.'">'.sf_filter_title_display($group->group_name).'</option>'."\n";
			$default='';
		}
	}
	return $out;
}

# Creates a select list of forums in a group.
# If forum id is specified it is NOT included.
function sfa_create_group_forum_select($groupid, $forumid = 0, $parent)
{
	$forums = sfa_get_forums_in_group($groupid);
	$out='';
	if($forums)
	{
		foreach($forums as $forum)
		{
			if($forum->forum_id != $forumid)
			{
				$selected = '';
				if ($forum->forum_id == $parent) $selected = ' selected="selected"';
				$out.='<option'.$selected.' value="'.$forum->forum_id.'">'.sf_filter_title_display($forum->forum_name).'</option>'."\n";
			}
		}
	}
	return $out;
}

function sfa_create_topic_status_select($current = '')
{
	global $wpdb;

	$sets = $wpdb->get_results("SELECT meta_id, meta_key FROM ".SFMETA." WHERE meta_type='topic-status'");
	$out = '<select class="sfquicklinks sfacontrol" name="forum_topic_status">'."\n";
	if($sets)
	{
		$out.= '<option value="">'.__("None", "sforum").'</option>';
		$default='';
		foreach($sets as $set)
		{
			if($set->meta_id == $current)
			{
				$default = 'selected="selected" ';
			} else {
				$default - null;
			}
			$out.='<option '.$default.'value="'.$set->meta_id.'">'.esc_html($set->meta_key).'</option>'."\n";
			$default='';
		}
	}

	$out.='</select>';
	return $out;
}

function sfa_update_check_option($key)
{
	if(isset($_POST[$key]))
	{
		sf_update_option($key, true);
	} else {
		sf_update_option($key, false);
	}
	return;
}

global $sfactions;

$sfactions = array(
    "action" => array(
	'Can view forum',
	'Can view forum lists only',
	'Can view forum and topic lists only',
	'Can view admin posts',
	'Can start new topics',
	'Can reply to topics',
	'Can break linked topics',
	'Can edit own topic titles',
	'Can edit any topic titles',
	'Can pin topics',
	'Can move topics',
	'Can move posts',
	'Can lock topics',
	'Can delete topics',
	'Can edit own posts forever',
	'Can edit own posts until reply',
	'Can edit any posts',
	'Can delete own posts',
	'Can delete any posts',
	'Can pin posts',
	'Can reassign posts',
	'Can view users email addresses',
	'Can view members profiles',
	'Can view members lists',
	'Can report posts',
	'Can bypass spam control',
	'Can bypass post moderation',
	'Can bypass post moderation once',
	'Can moderate pending posts',
	'Can create linked topics',
	'Can use spoilers',
	'Can view links',
	'Can upload images',
	'Can upload media',
	'Can upload files',
	'Can use signatures',
	'Can upload signatures',
	'Can upload avatars',
	'Can use private messaging',
	'Can subscribe',
	'Can watch topics',
	'Can change topic status',
	'Can rate posts'
	),
	"members" => array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,2,2,1,1,1,1)
);

function sfa_get_usergroups_all($usergroupid=Null)
{
	global $wpdb;
	$where='';
	if(!is_null($usergroupid)) $where=" WHERE usergroup_id=".$usergroupid;
	return $wpdb->get_results("SELECT * FROM ".SFUSERGROUPS.$where);
}

function sfa_get_usergroups_row($usergroup_id)
{
	global $wpdb;

	return $wpdb->get_row("SELECT * FROM ".SFUSERGROUPS." WHERE usergroup_id=".$usergroup_id);
}

function sfa_create_usergroup_row($usergroupname, $usergroupdesc, $usergroupismod, $report_failure=false)
{
	global $wpdb;

	# first check to see if user group name exists
	$exists = $wpdb->get_var("SELECT usergroup_id FROM ".SFUSERGROUPS." WHERE usergroup_name='".$usergroupname."'");
	if($exists)
	{
		if($report_failure == true)
		{
			return false;
		} else {
			return $exists;
		}
	}

	# go on and create the new user group
	$sql ="INSERT INTO ".SFUSERGROUPS." (usergroup_name, usergroup_desc, usergroup_is_moderator) ";
	$sql.="VALUES ('".$usergroupname."', '".$usergroupdesc."', '".$usergroupismod."');";

	if($wpdb->query($sql))
	{
		return $wpdb->insert_id;
	} else {
		return false;
	}
}

function sfa_get_forum_permissions($forum_id)
{
	global $wpdb;

	return $wpdb->get_results("SELECT * FROM ".SFPERMISSIONS." WHERE forum_id=".$forum_id." ORDER BY permission_role");
}

function sfa_remove_permission_data($permission_id)
{
	global $wpdb;

	return $wpdb->query("DELETE FROM ".SFPERMISSIONS." WHERE permission_id=".$permission_id);
}

function sfa_get_all_roles()
{
	global $wpdb;

	return $wpdb->get_results("SELECT * FROM ".SFROLES." ORDER BY role_id");
}

function sfa_create_role_row($role_name, $role_desc, $actions, $report_failure=false)
{
	global $wpdb;

	# first check to see if rolename exists
	$exists = $wpdb->get_var("SELECT role_id FROM ".SFROLES." WHERE role_name='".$role_name."'");
	if($exists)
	{
		if($report_failure == true)
		{
			return false;
		} else {
			return $exists;
		}
	}

	# go on and create the new role
	$sql ="INSERT INTO ".SFROLES." (role_name, role_desc, role_actions) ";
	$sql.="VALUES ('".$role_name."', '".$role_desc."', '".$actions."');";

	if($wpdb->query($sql))
	{
		return $wpdb->insert_id;
	} else {
		return false;
	}
}

function sfa_get_role_row($role_id)
{
	global $wpdb;

	return $wpdb->get_row("SELECT * FROM ".SFROLES." WHERE role_id=".$role_id);
}

function sfa_get_defpermissions($group_id)
{
	global $wpdb;

	return $wpdb->get_results("
		SELECT permission_id, ".SFUSERGROUPS.".usergroup_id, permission_role, usergroup_name
		FROM ".SFDEFPERMISSIONS."
		JOIN ".SFUSERGROUPS." ON ".SFDEFPERMISSIONS.".usergroup_id = ".SFUSERGROUPS.".usergroup_id
		WHERE group_id=".$group_id);
}

function sfa_get_defpermissions_role($group_id, $usergroup_id)
{
	global $wpdb;

	return $wpdb->get_var("
		SELECT permission_role
		FROM ".SFDEFPERMISSIONS."
		WHERE group_id=".$group_id." AND usergroup_id=".$usergroup_id);
}

function sfa_display_usergroup_select($filter = false, $forum_id = 0)
{ ?>
										<?php $usergroups = sfa_get_usergroups_all(); ?>
										<p><?php _e("Select User Group", "sforum") ?>:&nbsp;&nbsp;
										<select style="width:145px" class='sfacontrol' name='usergroup_id'>
<?php
											$out = '<option value="-1">'.__("Select User Group", "sforum").'</option>';
											if ($filter) $perms = sfa_get_forum_permissions($forum_id);
											foreach ($usergroups as $usergroup)
											{
												$disabled = '';
												if ($filter ==1 and $perms) {
													foreach ($perms as $perm) {
														if ($perm->usergroup_id == $usergroup->usergroup_id) {
															$disabled = 'disabled="disabled" ';
															continue;
														}
													}
												}
												$out.='<option '.$disabled.'value="'.$usergroup->usergroup_id.'">'.sf_filter_title_display($usergroup->usergroup_name).'</option>'."\n";
												$default='';
											}
											echo $out;
?>
										</select></p>
<?php
}

function sfa_display_permission_select($cur_perm = 0)
{ ?>
	          												<?php $roles = sfa_get_all_roles(); ?>
															<p><?php _e("Select Permission Set", "sforum") ?>:&nbsp;&nbsp;
															<select style="width:165px" class='sfacontrol' name='role'>
<?php
																$out = '';
																if ($cur_perm == 0) $out='<option value="-1">'.__("Select Permission Set", "sforum").'</option>';
																foreach($roles as $role)
																{
																	$selected = '';
																	if ($cur_perm == $role->role_id) $selected = 'selected = "selected" ';
																	$out.='<option '.$selected.'value="'.$role->role_id.'">'.sf_filter_title_display($role->role_name).'</option>'."\n";
																}
																echo $out;
?>
															</select></p>
<?php
}

function sfa_select_icon_dropdown($name, $label, $path, $cur)
{
	# Open folder and get cntents for matching
	$dlist = @opendir($path);
	if (!$dlist)
	{
		return;
	}

	echo '<select name="'.$name.'" class="sfcontrol" style="vertical-align:middle;">';
	if ($cur != '') $label = __('Remove', 'sforum');
	echo '<option value="">'.$label.'</option>';
	while (false !== ($file = readdir($dlist)))
	{
		if ($file != "." && $file != "..")
		{
			$selected = '';
			if ($file == $cur) $selected = ' selected="selected"';
			echo '<option'.$selected.' value="'.esc_attr($file).'">'.esc_html($file).'</option>';
		}
	}
	echo '</select>';
	closedir($dlist);

	return;
}

/**
 * User Search class.
 *
 * @since unknown
 */
class SP_User_Search {
	var $results;
	var $search_term;
	var $page;
	var $role;
	var $raw_page;
	var $users_per_page = 50;
	var $first_user;
	var $last_user;
	var $query_limit;
	var $query_orderby;
	var $query_from;
	var $query_where;
	var $total_users_for_query = 0;
	var $too_many_total_users = false;
	var $search_errors;

	function SP_User_Search ($search_term = '', $page = '', $role = '') {
		$this->search_term = $search_term;
		$this->raw_page = ( '' == $page ) ? false : (int) $page;
		$this->page = (int) ( '' == $page ) ? 1 : $page;
		$this->role = $role;

		$this->prepare_query();
		$this->query();
		$this->prepare_vars_for_template_usage();
		$this->do_paging();
	}

	function prepare_query() {
		global $wpdb;
		$this->first_user = ($this->page - 1) * $this->users_per_page;

		$this->query_limit = $wpdb->prepare(" LIMIT %d, %d", $this->first_user, $this->users_per_page);
		$this->query_orderby = ' ORDER BY user_login';

		$search_sql = '';
		if ( $this->search_term ) {
			$searches = array();
			$search_sql = 'AND (';
			foreach ( array('user_login', 'display_name') as $col )
				$searches[] = $col." LIKE '%$this->search_term%'";
			$search_sql .= implode(' OR ', $searches);
			$search_sql .= ')';
		}

		$this->query_from = " FROM $wpdb->users";
		$this->query_where = " WHERE 1=1 $search_sql";

		if ( $this->role ) {
			$this->query_from .= " INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id";
			$this->query_where .= $wpdb->prepare(" AND $wpdb->usermeta.meta_key = '{$wpdb->prefix}capabilities' AND $wpdb->usermeta.meta_value LIKE %s", '%'.$this->role.'%');
		} elseif ( is_multisite() ) {
			$level_key = $wpdb->prefix.'capabilities'; # wpmu site admins don't have user_levels
			$this->query_from .= ", $wpdb->usermeta";
			$this->query_where .= " AND $wpdb->users.ID = $wpdb->usermeta.user_id AND meta_key = '{$level_key}'";
		}

		do_action_ref_array( 'pre_user_search', array( &$this ) );
	}

	function query() {
		global $wpdb;

		$this->results = $wpdb->get_col("SELECT DISTINCT($wpdb->users.ID)".$this->query_from.$this->query_where.$this->query_orderby.$this->query_limit);

		if ( $this->results )
			$this->total_users_for_query = $wpdb->get_var("SELECT COUNT(DISTINCT($wpdb->users.ID))".$this->query_from.$this->query_where); # no limit
		else
			$this->search_errors = new WP_Error('no_matching_users_found', __('No matching users were found!'));
	}

	function prepare_vars_for_template_usage() {
		$this->search_term = stripslashes($this->search_term); # done with DB, from now on we want slashes gone
	}

	function do_paging() {
		if ( $this->total_users_for_query > $this->users_per_page ) { # have to page the results
			$args = array();
			if( ! empty($this->search_term) )
				$args['usersearch'] = urlencode($this->search_term);
			if( ! empty($this->role) )
				$args['role'] = urlencode($this->role);

			$this->paging_text = paginate_links( array(
				'total' => ceil($this->total_users_for_query / $this->users_per_page),
				'current' => $this->page,
				'base' => 'users.php?%_%',
				'format' => 'userspage=%#%',
				'add_args' => $args
			) );
		}
	}

	function get_results() {
		return (array) $this->results;
	}

	function page_links() {
		echo $this->paging_text;
	}

	function results_are_paged() {
		if ( $this->paging_text )
			return true;
		return false;
	}

	function is_search() {
		if ( $this->search_term )
			return true;
		return false;
	}
}

?>