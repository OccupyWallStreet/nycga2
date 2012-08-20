<?php
$root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
if (file_exists($root.'/wp-load.php')) {
// WP 2.6
require_once($root.'/wp-load.php');
} else {
// Before 2.6
require_once($root.'/wp-config.php');
}$old_options=get_option('SOCIALTOOLBARICONS');asort($_GET['listItem']);$new_array=array_flip($_GET['listItem']);for($i=0;$i<count($old_options);$i++)
{
$old_options[$i]['order']=$new_array[$i];
}
update_option('SOCIALTOOLBARICONS',$old_options);
echo '<div class="updated fade"><p>';
echo _e('List Updated Save your Settings');
echo '</p></div>';
?>