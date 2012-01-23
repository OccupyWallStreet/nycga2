<?php

require( dirname(__FILE__) . '/../../../wp-load.php' );

$current_user = wp_get_current_user();

$group_id = BP_Groups_Group::group_exists($_GET['groupname']);
if (!$group_id) {
  echo 'sorry, couldnt find a group with slug ' . $_GET['groupname'];
  return;
}

if (!is_super_admin() && !groups_is_user_admin( $current_user->ID, $group_id )) {
  echo 'sorry, you dont have the required privileges';
  return;
}

$username = $_GET['username'];
$user = get_userdatabylogin($username);
if($user) {
  $user_id = $user->ID;
  groups_join_group($group_id, $user_id);
  echo $user_id;
} else {
  echo 'sorry, couldnt find a user with username ' . $username;
  return;
}

?>

