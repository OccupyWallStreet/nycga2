<?php

/**
To install Admin Menu Editor as a global plugin in WPMU :
	1) Place the "admin-menu-editor" directory into your "mu-plugins" directory.
	2) Move this file, admin-menu-editor-mu.php, from the "admin-menu-editor" directory
	   to your "mu-plugins" directory.
	   
The resulting directory structure should look like this : 

mu-plugins/
	admin-menu-editor-mu.php
	admin-menu-editor/ 
		menu-editor.php
		menu-editor-core.php
		...and other Admin Menu Editor files

**/

//Load the plugin
$ws_menu_editor_filename = dirname(__FILE__) . '/admin-menu-editor/menu-editor.php';
$ws_menu_editor_pro_filename = dirname(__FILE__) . '/admin-menu-editor-pro/menu-editor.php';
if ( file_exists($ws_menu_editor_filename) ) {
	require $ws_menu_editor_filename;
} elseif ( file_exists($ws_menu_editor_pro_filename) ) {
	require $ws_menu_editor_pro_filename;
} else {
	add_action('admin_notices', 'ws_ame_installation_error');
}

function ws_ame_installation_error(){
	if ( !is_site_admin() ) return;
?>
<div class="error fade"><p>
		<strong>Admin Menu Editor is installed incorrectly!</strong>
		</p>
		<p>
		Please copy the entire <code>admin-menu-directory</code> directory to your <code>mu-plugins</code> 
		directory, then move only the admin-menu-editor-mu.php file from
		<code>admin-menu-editor/includes</code> to <code>mu-plugins</code>.
		</p> 
</div>
<?php
}

?>