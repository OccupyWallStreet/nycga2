<?php

/*
 * Settup Fields
 * this function will create an array of field objects, including custom registered field types
 */
 
$this->setup_fields();
		
/*
 * Upgrade
 * test the current acf version against the version in the main file
 * If this is a newer version, show the upgrade database message
 */
 
$version = get_option('acf_version', false);
if($version)
{
	if(version_compare($version,$this->upgrade_version) < 0)
	{
		$this->admin_message('<p>Advanced Custom Fields v' . $this->version . ' requires a database upgrade. Please <a href="http://codex.wordpress.org/Backing_Up_Your_Database">backup your database</a> then click <a href="' . admin_url() . 'edit.php?post_type=acf&page=acf-upgrade" class="button">Upgrade Database</a></p>');
		
	}
}
else
{
	update_option('acf_version', $this->version );
}

/*
 * Deactivate add-on
 * called from page_settings, this code will deactivate an add-on
 */
 
if(isset($_POST['acf_field_deactivate']))
{
	// vars
	$message = "";
	$field = $_POST['acf_field_deactivate'];
	
	// delete field
	delete_option('acf_'.$field.'_ac');
	
	//set message
	if($field == "repeater")
	{
		$message = "<p>Repeater field deactivated</p>";
	}
	elseif($field == "options_page")
	{
		$message = "<p>Options page deactivated</p>";
	}
	elseif($field == "flexible_content")
	{
		$message = "<p>Flexible Content field deactivated</p>";
	}
	
	// show message on page
	$this->admin_message($message);
}

/*
 * Actviate add-on
 * called from page_settings, this code will activate an add-on
 */
 
 if(isset($_POST['acf_field_activate']) && isset($_POST['key']))
{
	// vars
	$message = "";
	$field = $_POST['acf_field_activate'];
	$key = trim($_POST['key']);

	// update option
	update_option('acf_'.$field.'_ac', $key);
	
	// did it unlock?
	if($this->is_field_unlocked($field))
	{
		//set message
		if($field == "repeater")
		{
			$message = "<p>Repeater field activated</p>";
		}
		elseif($field == "options_page")
		{
			$message = "<p>Options page activated</p>";
		}
		elseif($field == "flexible_content")
	{
		$message = "<p>Flexible Content field activated</p>";
	}
		
		$this->admin_message($message);
	}
	else
	{
		$this->admin_message('<p>License key unrecognised</p>', 'error');
	}	
}

/*
 * Create ACF Post Type
 */
 
$labels = array(
    'name' => __( 'Field&nbsp;Groups', 'acf' ),
	'singular_name' => __( 'Advanced Custom Fields', 'acf' ),
    'add_new' => __( 'Add New' , 'acf' ),
    'add_new_item' => __( 'Add New Field Group' , 'acf' ),
    'edit_item' =>  __( 'Edit Field Group' , 'acf' ),
    'new_item' => __( 'New Field Group' , 'acf' ),
    'view_item' => __('View Field Group'),
    'search_items' => __('Search Field Groups'),
    'not_found' =>  __('No Field Groups found'),
    'not_found_in_trash' => __('No Field Groups found in Trash'), 
);

register_post_type('acf', array(
	'labels' => $labels,
	'public' => false,
	'show_ui' => true,
	'_builtin' =>  false,
	'capability_type' => 'page',
	'hierarchical' => true,
	'rewrite' => false,
	'query_var' => "acf",
	'supports' => array(
		'title',
	),
	'show_in_menu'	=> false,
));

/*
 * Set Custom Columns
 * for the acf edit field groups page
 */

add_filter("manage_edit-acf_columns", "acf_columns_filter");
function acf_columns_filter($columns)
{
	$columns = array(
		'cb'	 	=> '<input type="checkbox" />',
		'title' 	=> 'Title',
	);
	return $columns;
}

?>