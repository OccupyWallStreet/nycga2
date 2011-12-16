<?php
/**
* Admin class holding all adminstrative functions for the WordPress plugin ProjectManager
* 
* @author 	Kolja Schleich
* @package	ProjectManager
* @copyright 	Copyright 2009
*/

class ProjectManagerAdminPanel extends ProjectManager
{
	/**
	 * load admin area
	 *
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		require_once( ABSPATH . 'wp-admin/includes/template.php' );
		add_action( 'admin_menu', array(&$this, 'menu') );
		
		//add_action('admin_print_scripts', array(&$this, 'loadScripts') );
		add_action('admin_print_styles', array(&$this, 'loadStyles') );
	}
	function LeagueManagerAdmin()
	{
		$this->__construct();
	}
	

	/**
	 * get admin menu for subpage
	 *
	 * @param none
	 * @return array
	 */
	function getMenu()
	{
		$menu = array();
		$menu['settings'] = array( 'title' => __( 'Settings', 'projectmanager' ), 'cap' => 'edit_projects_settings', 'page' => 'project-settings_%d' );
		$menu['formfields'] = array( 'title' => __( 'Form Fields', 'projectmanager' ), 'cap' => 'edit_formfields', 'page' => 'project-formfields_%d' );
		$menu['dataset'] = array( 'title' => __( 'Add Dataset', 'projectmanager' ), 'cap' => 'edit_datasets', 'page' => 'project-dataset_%d' );
		$menu['import'] = array( 'title' => __( 'Import/Export', 'projectmanager' ), 'cap' => 'import_datasets', 'page' => 'project-import_%d' );

		return $menu;
	}


	/**
	 * adds menu to the admin interface
	 *
	 * @param none
	 */
	function menu()
	{
		$options = get_option('projectmanager');
		if( !isset($options['dbversion']) || $options['dbversion'] != PROJECTMANAGER_DBVERSION )
			$update = true;
		else
			$update = false;

		if ( !$update && $projects = parent::getProjects() ) {
			foreach( $projects AS $project ) {
				if ( isset($project->navi_link) && 1 == $project->navi_link ) {
					$icon = $project->menu_icon;
					if ( function_exists('add_object_page') )
						$page = add_object_page( $project->title, $project->title, 'view_projects', 'project_' . $project->id, array(&$this, 'display'), $this->getIconURL($icon) );
					else
						$page = add_menu_page( $project->title, $project->title, 'view_projects', 'project_' . $project->id, array(&$this, 'display'), $this->getIconURL($icon) );

					add_action("admin_print_scripts-$page", array(&$this, 'loadScripts') );
					
					$page = add_submenu_page('project_' . $project->id, __($project->title, 'projectmanager'), __('Overview','projectmanager'),'view_projects', 'project_' . $project->id, array(&$this, 'display'));
					add_action("admin_print_scripts-$page", array(&$this, 'loadScripts') );
					$page = add_submenu_page('project_' . $project->id, __( 'Add Dataset', 'projectmanager' ), __( 'Add Dataset', 'projectmanager' ), 'edit_datasets', 'project-dataset_' . $project->id, array(&$this, 'display'));
					add_action("admin_print_scripts-$page", array(&$this, 'loadScripts') );
					$page = add_submenu_page('project_' . $project->id, __( 'Form Fields', 'projectmanager' ), __( 'Form Fields', 'projectmanager' ), 'edit_formfields', 'project-formfields_' . $project->id, array(&$this, 'display'));
					add_action("admin_print_scripts-$page", array(&$this, 'loadScripts') );
					$page = add_submenu_page('project_' . $project->id, __( 'Settings', 'projectmanager' ), __( 'Settings', 'projectmanager' ), 'edit_projects_settings', 'project-settings_' . $project->id, array(&$this, 'display'));
					add_action("admin_print_scripts-$page", array(&$this, 'loadScripts') );
					add_submenu_page('project_' . $project->id, __('Categories'), __('Categories'), 'manage_projects', 'edit-tags.php?taxonomy=category');
					add_submenu_page('project_' . $project->id, __('Import/Export', 'projectmanager'), __('Import/Export', 'projectmanager'), 'import_datasets', 'project-import_' . $project->id, array(&$this, 'display'));			
				}
			}
			
		}
		
		
		// Add global Projects Menu
		$page = add_menu_page(__('Projects', 'projectmanager'), __('Projects', 'projectmanager'), 'view_projects', PROJECTMANAGER_PATH,array(&$this, 'display'), PROJECTMANAGER_URL.'/admin/icons/menu/databases.png');
		add_action("admin_print_scripts-$page", array(&$this, 'loadScripts') );
		
		add_submenu_page(PROJECTMANAGER_PATH, __('Projects', 'projectmanager'), __('Overview','projectmanager'),'view_projects', PROJECTMANAGER_PATH, array(&$this, 'display'));
		$page = add_submenu_page(PROJECTMANAGER_PATH, __( 'Settings'), __('Settings'), 'projectmanager_settings', 'projectmanager-settings', array( &$this, 'display') );
		add_action("admin_print_scripts-$page", array(&$this, 'loadColorpicker') );
		add_submenu_page(PROJECTMANAGER_PATH, __( 'Documentation', 'projectmanager'), __('Documentation', 'projectmanager'), 'view_projects', 'projectmanager-documentation', array( &$this, 'display') );
				
		$plugin = 'projectmanager/projectmanager.php';
		add_filter( 'plugin_action_links_' . $plugin, array( &$this, 'pluginActions' ) );
	}
	
	
	/**
	 * show admin menu
	 *
	 * @param none
	 */
	function display()
	{
		global $projectmanager;
		
		$options = get_option('projectmanager');

		// Update Plugin Version
		if ( $options['version'] != PROJECTMANAGER_VERSION ) {
			$options['version'] = PROJECTMANAGER_VERSION;
			update_option('projectmanager', $options);
		}

		if( !isset($options['dbversion']) || $options['dbversion'] != PROJECTMANAGER_DBVERSION ) {
			include_once ( dirname (__FILE__) . '/upgrade.php' );
			projectmanager_upgrade_page();
			return;
		}

		switch ($_GET['page']) {
			case 'projectmanager-settings':
				$this->displayOptionsPage();
				break;
			case 'projectmanager-documentation':
			  include_once( dirname(__FILE__) . '/documentation.php' );
			  break;
			case 'projectmanager':
				$page = isset($_GET['subpage']) ? $_GET['subpage'] : '';
				switch($page) {
					case 'show-project':
						include_once( dirname(__FILE__) . '/show-project.php' );
						break;
					case 'settings':
						include_once( dirname(__FILE__) . '/settings.php' );
						break;
					case 'dataset':
						include_once( dirname(__FILE__) . '/dataset.php' );
						break;
					case 'formfields':
						include_once( dirname(__FILE__) . '/formfields.php' );
						break;
					case 'import':
						include_once( dirname(__FILE__) . '/import.php' );
						break;
					default:
						include_once( dirname(__FILE__) . '/index.php' );
						break;
				}
				break;
			
			default:
				$page = explode("_", $_GET['page']);
				$projectmanager->initialize($page[1]);
							
				switch ($page[0]) {
					case 'project':
						include_once( dirname(__FILE__) . '/show-project.php' );
						break;
					case 'project-settings':
						include_once( dirname(__FILE__) . '/settings.php' );
						break;
					case 'project-dataset':
						include_once( dirname(__FILE__) . '/dataset.php' );
						break;
					case 'project-formfields':
						include_once( dirname(__FILE__) . '/formfields.php' );
						break;
					case 'project-import':
						include_once( dirname(__FILE__) . '/import.php' );
						break;
						
				}
		}
	}
	
	
	/**
	 * display link to settings page in plugin table
	 *
	 * @param array $links array of action links
	 * @return void
	 */
	function pluginActions( $links )
	{
		$settings_link = '<a href="admin.php?page=projectmanager-settings">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link );
	
		return $links;
	}
	
	
	/**
	 * load scripts
	 *
	 * @param none
	 * @return void
	 */
	function loadScripts()
	{
		wp_register_script( 'projectmanager', PROJECTMANAGER_URL.'/admin/js/functions.js', array( 'sack', 'scriptaculous', 'prototype' ), PROJECTMANAGER_VERSION );
		wp_register_script( 'projectmanager_formfields', PROJECTMANAGER_URL.'/admin/js/formfields.js', array( 'projectmanager', 'thickbox' ), PROJECTMANAGER_VERSION );
		wp_register_script ('projectmanager_ajax', PROJECTMANAGER_URL.'/admin/js/ajax.js', array( 'projectmanager' ), PROJECTMANAGER_VERSION );
		
		wp_enqueue_script( 'projectmanager_formfields' );
		wp_enqueue_script( 'projectmanager_ajax');
			
		echo "<script type='text/javascript'>\n";
		echo "var PRJCTMNGR_HTML_FORM_FIELD_TYPES = \"";
		foreach (parent::getFormFieldTypes() AS $form_type_id => $form_type) {
			$field_name = is_array($form_type) ? $form_type['name'] : $form_type;
			echo "<option value='".$form_type_id."'>".$field_name."</option>";
		}
		echo "\";\n";
			
		?>
		//<![CDATA[
		ProjectManagerAjaxL10n = {
			blogUrl: "<?php bloginfo( 'wpurl' ); ?>", pluginPath: "<?php echo PROJECTMANAGER_PATH; ?>", pluginUrl: "<?php echo PROJECTMANAGER_URL; ?>", requestUrl: "<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php", imgUrl: "<?php echo PROJECTMANAGER_URL; ?>/images", Edit: "<?php _e("Edit"); ?>", Post: "<?php _e("Post"); ?>", Save: "<?php _e("Save"); ?>", Cancel: "<?php _e("Cancel"); ?>", pleaseWait: "<?php _e("Please wait..."); ?>", Revisions: "<?php _e("Page Revisions"); ?>", Time: "<?php _e("Insert time"); ?>", Options: "<?php _e("Options", "projectmanager") ?>", Delete: "<?php _e('Delete', 'projectmanager') ?>", delFile: "<?php _e('Delete File', 'projectmanager')?>"
			   }
		//]]>
		<?php
		echo "</script>\n";
	}
	function loadColorpicker()
	{
		wp_register_script ('projectmanager_colorpicker', PROJECTMANAGER_URL.'/admin/js/colorpicker.js', array( 'colorpicker' ), PROJECTMANAGER_VERSION );
		wp_enqueue_script('projectmanager_colorpicker');
	}
	
	
	/**
	 * load styles
	 *
	 * @param none
	 * @return void
	 */
	function loadStyles()
	{
		wp_enqueue_style('thickbox');
		wp_enqueue_style('projectmanager', PROJECTMANAGER_URL . "/style.css", false, '1.0', 'screen');
		wp_enqueue_style('projectmanager_admin', PROJECTMANAGER_URL . "/admin/style.css", false, '1.0', 'screen');
	}
	
	
	/**
	 * set message by calling parent function
	 *
	 * @param string $message
	 * @param boolean $error (optional)
	 * @return void
	 */
	function setMessage( $message, $error = false )
	{
		parent::setMessage( $message, $error );
	}
	
	
	/**
	 * print message calls parent
	 *
	 * @param none
	 * @return string
	 */
	function printMessage()
	{
		parent::printMessage();
	}
	
	
	/**
	 * display global settings page (e.g. color scheme options)
	 *
	 * @param none
	 * @return void
	 */
	function displayOptionsPage($include=false)
	{
		$options = get_option('projectmanager');
		
		if ( current_user_can( 'projectmanager_settings' ) ) {
			if ( isset($_POST['updateProjectManager']) ) {
				check_admin_referer('projetmanager_manage-global-league-options');
				$options['colors']['headers'] = $_POST['color_headers'];
				$options['colors']['rows'] = array( $_POST['color_rows_alt'], $_POST['color_rows'] );
				
				update_option( 'projectmanager', $options );
				$this->setMessage(__( 'Settings saved', 'leaguemanager' ));
				$this->printMessage();
			}
			require_once (dirname (__FILE__) . '/settings-global.php');
		} else {
			echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
		}
	}
	
	
	/**
	 *  get icon URl
	 *  
	 *  First check if custom directory 'projectmanager/icons' exists in template directory
	 *  If not load default dir.
	 *  
	 *  @param none
	 *  @return directory
	 */
	function getIconURL( $icon, $dir = 'menu' )
	{
		if ( file_exists(TEMPLATEPATH . "/projectmanager/icons/".$icon))
			return get_template_directory_uri() . "/projectmanager/icons/".$icon;
		elseif ( file_exists(PROJECTMANAGER_PATH.'/admin/icons/'.$dir.'/'.$icon) )
			return PROJECTMANAGER_URL.'/admin/icons/'.$dir.'/'.$icon;
		else
			return PROJECTMANAGER_URL.'/admin/icons/'.$dir.'/databases.png';
	}
	
	
	/**
	 * check if there is only a single project
	 *
	 * @param none
	 * @return boolean
	 */
	function isSingle()
	{
		$this->single = false;
		$projects = parent::getProjects();
		foreach ( $projects AS $project ) {
			if ( 1 == $project->navi_link && parent::getNumProjects() == 1) {
				$this->single = true;
				break;
			}
		}
		return $this->single;
	}
	
	
	/**
	 * gets order of datasets
	 *
	 * @param string $input serialized string with order
	 * @param string $listname ID of list to sort
	 * @return sorted array of parameters
	 */
 	function getOrder( $input, $listname = 'the-list' )
	{
		parse_str( $input, $input_array );
		$input_array = $input_array[$listname];
		$order_array = array();
		for ( $i = 0; $i < count($input_array); $i++ ) {
			if ( $input_array[$i] != '' )
				$order_array[$i+1] = $input_array[$i];
		}
		return $order_array;	
	}
	
	
	/**
	 * gets checklist for groups. Adopted from wp-admin/includes/template.php
	 *
	 * @param int $child_of parent category
	 * @param array $selected cats array of selected category IDs
	 */
	function categoryChecklist( $child_of, $selected_cats )
	{
		$walker = new Walker_Category_Checklist;
		$child_of = (int) $child_of;
		
		$args = array();
		$args['selected_cats'] = $selected_cats;
		$args['popular_cats'] = array();
		$categories = get_categories( "child_of=$child_of&hierarchical=0&hide_empty=0" );
		
		$checked_categories = array();
		for ( $i = 0; isset($categories[$i]); $i++ ) {
			if ( in_array($categories[$i]->term_id, $args['selected_cats']) ) {
				$checked_categories[] = $categories[$i];
				unset($categories[$i]);
			}
		}

		// Put checked cats on top
		echo call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args));
		// Then the rest of them
		echo call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
	}
	



	/**
	 * get possible sorting options for datasets
	 *
	 * @param string $selected
	 * @return string
	 */
	function datasetOrderbyOptions( $selected )
	{
		$options = array( 'order' => __('Manual', 'projectmanager'), 'id' => __('ID', 'projectmanager'), 'name' => __('Name','projectmanager'), 'formfields' => __('Formfields', 'projectmanager') );
		
		foreach ( $options AS $option => $title ) {
			$select = ( $selected == $option ) ? ' selected="selected"' : '';
			echo '<option value="'.$option.'"'.$select.'>'.$title.'</option>';
		}
	}
	
	
	/**
	 * get possible order options
	 *
	 * @param string $selected
	 * @return string
	 */
	function datasetOrderOptions( $selected )
	{
		$options = array( 'ASC' => __('Ascending','projectmanager'), 'DESC' => __('Descending','projectmanager') );
		
		foreach ( $options AS $option => $title ) {
			$select = ( $selected == $option ) ? ' selected="selected"' : '';
			echo '<option value="'.$option.'"'.$select.'>'.$title.'</option>';
		}
	}
	
	
	/**
	 * add new project
	 *
	 * @param string $title
	 * @return string
	 */
	function addProject( $title )
	{
		global $wpdb;
	
		if ( !current_user_can('edit_projects') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}

		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_projects} (title) VALUES ('%s')", $title ) );
		$project_id = $wpdb->insert_id;
		
		$this->setMessage( __('Project added','projectmanager') );
		
		do_action('projectmanager_add_project', $project_id);
	}
	
	
	/**
	 * edit project
	 *
	 * @param string $title
	 * @param int $project_id
	 * @return string
	 */
	function editProject( $title, $project_id )
	{
		global $wpdb;

		if ( !current_user_can('edit_projects') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}
		
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_projects} SET `title` = '%s' WHERE `id` = '%d'", $title, $project_id ) );
		$this->setMessage( __('Project updated','projectmanager') );
		
		do_action('projectmanager_edit_project', $project_id);
	}
	
	
	/**
	 * delete project
	 *
	 * @param int  $project_id
	 * @return void
	 */
	function delProject( $project_id )
	{
		global $wpdb, $projectmanager;
		
		if ( !current_user_can('delete_projects') ) 
			return;

		$projectmanager->initialize($project_id);
		foreach ( $projectmanager->getDatasets() AS $dataset )
			$this->delDataset( $dataset->id );
		
		$wpdb->query( "DELETE FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = {$project_id}" );
		$wpdb->query( "DELETE FROM {$wpdb->projectmanager_projects} WHERE `id` = {$project_id}" );
		
		do_action('projectmanager_del_project', $project_id);
	}

	
	/**
	 * save Project Settings
	 *
	 * @param array $settings
	 * @param int $project_id
	 * @return void
	 */
	function saveSettings( $settings, $project_id )
	{
		global $wpdb;

		if ( !current_user_can('edit_projects_settings') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}

		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_projects} SET `settings` = '%s' WHERE `id` = '%d'", maybe_serialize($settings), $project_id ) );
		$this->setMessage(__('Settings saved', 'projectmanager'));
		
		do_action('projectmanager_save_settings', $project_id);
	}


	/**
	 * import datasets from CSV file
	 *
	 * @param int $project_id
	 * @param array $file CSV file
	 * @param string $delimiter
	 * @param array $cols column assignments
	 * @return string
	 */
	function importDatasets( $project_id, $file, $delimiter, $cols )
	{
		if ( !current_user_can('import_datasets') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}


		if ( $file['size'] > 0 ) {
			/*
			* Upload CSV file to image directory, temporarily
			*/
			$new_file =  parent::getFilePath().'/'.basename($file['name']);
			if ( move_uploaded_file($file['tmp_name'], $new_file) ) {
				$handle = @fopen($new_file, "r");
				if ($handle) {
					if ( "TAB" == $delimiter ) $delimiter = "\t"; // correct tabular delimiter
					
					$i = 0; $l=0; // initialize dataset & line counter
					while (!feof($handle)) {
						  $buffer = fgets($handle, 4096);
						  $line = explode($delimiter, $buffer);
						  
						  if ( $l > 0 && $line ) {
  						  $name = $line[0];
  						  $categories = empty($line[1]) ? '' : explode(",", $line[1]);
                /*
    						* get Category IDs from titles
    						*/						
    						$cat_ids = array();
    						if ( !empty($categories) ) {
    						  foreach ( $categories AS $category ) {
    						    $cat_ids[] = get_cat_ID($category);
                 				  }
                				}
                
    						// assign column values to form fields
    						foreach ( $cols AS $col => $form_field_id ) {
    							$meta[$form_field_id] = $line[$col];
    						}
    						
    						if ( $line && !empty($name) ) {
    							$this->addDataset($project_id, $name, $cat_ids, $meta);
    							$i++;
    						}
  					  }
  					  $l++;
					}
					fclose($handle);
					
					$this->setMessage(sprintf(__( '%d Datasets successfully imported', 'projectmanager' ), $i));
				} else {
					$this->setMessage( __('The file is not readable', 'projectmanager'), true );
				}
			} else {
				$this->setMessage(sprintf( __('The uploaded file could not be moved to %s.' ), parent::getFilePath()) );
			}
			@unlink($new_file); // remove file from server after import is done
		} else {
			$this->setMessage( __('The uploaded file seems to be empty', 'projectmanager'), true );
		}
	}
	
	
	/**
	 * check if dataset with given user ID exists
	 *
	 * @param int $project_id
	 * @param int $user_id
	 * @return boolean
	 */
	function datasetExists( $project_id, $user_id )
	{
		global $wpdb;

		$count= $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$project_id} AND `user_id` = '".$user_id."'" );

		if ( $count > 0 )
			return true;
		
		return false;
	}


	/**
	 * export datasets to CSV
	 *
	 * @param int $project_id
	 * @return file
	 */
	function exportDatasets( $project_id )
	{
		global $projectmanager;
		
		//if ( !current_user_can('import_datasets') ) {
		//	$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
		//	return;
		//}

		$this->project_id = $project_id;
		$projectmanager->initialize($project_id);
		$project = $projectmanager->getProject();
			
		$filename = $project->title."_".date("Y-m-d").".csv";
		/*
		* Generate Header
		*/
		$contents = __('Name','projectmanager')."\t".__('Categories','projectmanager');
		foreach ( $projectmanager->getFormFields() AS $form_field )
			$contents .= "\t".$form_field->label;
		
		foreach ( $projectmanager->getDatasets() AS $dataset ) {
			$contents .= "\n".$dataset->name."\t".$projectmanager->getSelectedCategoryTitles(maybe_unserialize($dataset->cat_ids));

			foreach ( $projectmanager->getDatasetMeta( $dataset->id ) AS $meta ) {
				// Remove line breaks
				$meta->value = str_replace("\r\n", "", stripslashes($meta->value));
				$contents .= "\t".strip_tags($meta->value);
			}
		}
		
		header('Content-Type: text/csv');
		header('Content-Disposition: inline; filename="'.$filename.'"');
		echo $contents;
		exit();
	}
	
	
	/**
	 * add new dataset
	 *
	 * @param int $project_id
	 * @param string $name
	 * @param array $cat_ids
	 * @param array $dataset_meta
	 * @param false|int $user_id
	 * @return string
	 */
	function addDataset( $project_id, $name, $cat_ids, $dataset_meta = false, $user_id = false )
	{
		global $wpdb, $current_user, $projectmanager;

		if ( $user_id && $this->datasetExists($project_id, $user_id) ) {
			$this->setMessage( __( 'You cannot add two datasets with same User ID.', 'projectmanager' ), true );
			return false;
		}

		$projectmanager->initialize($project_id);
		$this->project_id = $project_id;
		$project = $this->project = $projectmanager->getProject($project_id);
		if ( !$user_id ) $user_id = $current_user->ID;

		// Negative check on capability: user can't edit datasets
		if ( !current_user_can('edit_datasets') && !current_user_can('projectmanager_user') && !current_user_can('import_datasets') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}

		// user has only cap 'projectmanager_user' but not 'edit_other_datasets' and 'edit_datasets'
		if ( current_user_can('projectmanager_user') && !current_user_can('edit_other_datasets') && !current_user_can('edit_datasets') && !current_user_can('import_datasets') ) {
			// and dataset with this user ID already exists
			if ( $this->datasetExists($project_id, $user_id) ) {
				$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
				return;
			}
		}

		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_dataset} (name, cat_ids, project_id, user_id) VALUES ('%s', '%s', '%d', '%d')", $name, maybe_serialize($cat_ids), $project_id, $user_id ) );
		$dataset_id = $wpdb->insert_id;
				
		if ( $dataset_meta ) {
			foreach ( $dataset_meta AS $meta_id => $meta_value ) {
				$formfield = parent::getFormFields($meta_id);
					
				// Manage file upload
				if ( 'file' == $formfield->type || 'image' == $formfield->type || 'video' == $formfield->type ) {
					$file = array('name' => $_FILES['form_field']['name'][$meta_id], 'tmp_name' => $_FILES['form_field']['tmp_name'][$meta_id], 'size' => $_FILES['form_field']['size'][$meta_id], 'type' => $_FILES['form_field']['type'][$meta_id]);
					if ( !empty($file['name']) )
						$this->uploadFile($file);

					$meta_value = basename($file['name']);

					// Create Thumbails for Image
					if ( 'image' == $formfield->type && !empty($meta_value) ) {
						$new_file = parent::getFilePath().'/'.$meta_value;
						$image = new ProjectManagerImage($new_file);
						// Resize original file and create thumbnails
						$dims = array( 'width' => $project->medium_size['width'], 'height' => $project->medium_size['height'] );
						$image->createThumbnail( $dims, $new_file, $project->chmod );

						$dims = array( 'width' => $project->thumb_size['width'], 'height' => $project->thumb_size['height'] );
						$image->createThumbnail( $dims, parent::getFilePath().'/thumb.'.$meta_value, $project->chmod );
						
						$dims = array( 'width' => 80, 'height' => 50 );
						$image->createThumbnail( $dims, parent::getFilePath().'/tiny.'.$meta_value, $project->chmod );
					}		
				} elseif ( 'numeric' == $formfield->type || 'currency' == $formfiel->type ) {
					$meta_value += 0; // convert value to numeric type
				}
					
				if ( is_array($meta_value) ) {
					// form field value is a date
					if ( array_key_exists('day', $meta_value) && array_key_exists('month', $meta_value) && array_key_exists('year', $meta_value) ) {
						$meta_value = sprintf("%s-%s-%s", $meta_value['year'], $meta_value['month'], $meta_value['day']);
					} elseif ( array_key_exists('hour', $meta_value) && array_key_exists('minute', $meta_value) ) {
						$meta_value = sprintf("%s:%s", $meta_value['hour'], $meta_value['minute']);
					}
				}

				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ('%d', '%d', '%s')", $meta_id, $dataset_id, maybe_serialize($meta_value) ) );
			}
			
			// Check for unsubmitted form data, e.g. checkbox list
			if ($form_fields = parent::getFormFields()) {
				foreach ( $form_fields AS $form_field ) {
					if ( !array_key_exists($form_field->id, $dataset_meta) ) {
						$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ('%d', '%d', '')", $dataset_id, $form_field->id ) );
					}
				}
			}
		} else {
			// Populate empty meta value for new registered user
			foreach ( $projectmanager->getFormFields() AS $formfield ) {
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ('%d', '%d', '')", $formfield->id, $dataset_id ) );
			}
		}


		if ( isset($_FILES['projectmanager_image']) && $_FILES['projectmanager_image']['name'] != ''  )
			$this->uploadImage($dataset_id, $_FILES['projectmanager_image']);
				
		$this->setMessage( __( 'New dataset added to the database.', 'projectmanager' ) );
		
		do_action('projectmanager_add_dataset', $dataset_id);
	}
		
		
	/**
	 * edit dataset
	 *
	 * @param int $project_id
	 * @param string $name
	 * @param array $cat_ids
	 * @param int $dataset_id
	 * @param array $dataset_meta
	 * @param int $user_id
	 * @param boolean $del_image
	 * @param string $image_file
	 * @param int|false $owner
	 * @return string
	 */
	function editDataset( $project_id, $name, $cat_ids, $dataset_id, $dataset_meta = false, $user_id, $del_image = false, $image_file = '', $overwrite_image = false, $owner = false )
	{
		global $wpdb, $current_user, $projectmanager;
		$this->project_id = $project_id;
		$project = $this->project = $projectmanager->getProject($this->project_id);
		$dataset = $projectmanager->getDataset($dataset_id);

		// Check if user has either cap 'edit_datasets' or 'projectmanager_user'
		if ( !current_user_can('edit_datasets') && !current_user_can('projectmanager_user') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}

		// check if user has cap 'edit_other_datasets'
		if ( !current_user_can('edit_other_datasets') ) {
			if ( $dataset->user_id != $current_user->ID ) {
				$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
				return;
			}
		}

		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `name` = '%s', `cat_ids` = '%s' WHERE `id` = '%d'", $name, maybe_serialize($cat_ids), $dataset_id ) );
			
		// Change Dataset owner if supplied
		if ( $owner )
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `user_id` = '%d' WHERE `id` = '%d'", $owner, $dataset_id ) );
			
		if ( $dataset_meta ) {
			foreach ( $dataset_meta AS $meta_id => $meta_value ) {
				$formfield = parent::getFormFields($meta_id);
					
				// Manage file upload
				if ( 'file' == $formfield->type || 'image' == $formfield->type || 'video' == $formfield->type ) {
					$file = array('name' => $_FILES['form_field']['name'][$meta_id], 'tmp_name' => $_FILES['form_field']['tmp_name'][$meta_id], 'size' => $_FILES['form_field']['size'][$meta_id], 'type' => $_FILES['form_field']['type'][$meta_id], 'current' => $meta_value['current']);
					$delete = (1 == $meta_value['del']) ? true : false;
					$meta_value = $this->editFile($file, $meta_value['overwrite'], $delete);
					
					// Create Thumbnails for Image
					if ( 'image' == $formfield->type && !empty($meta_value) ) {
						$new_file = parent::getFilePath().'/'.$meta_value;
						$image = new ProjectManagerImage($new_file);
						// Resize original file and create thumbnails
						$dims = array( 'width' => $project->medium_size['width'], 'height' => $project->medium_size['height'] );
						$image->createThumbnail( $dims, $new_file, $project->chmod );

						$dims = array( 'width' => $project->thumb_size['width'], 'height' => $project->thumb_size['height'] );
						$image->createThumbnail( $dims, parent::getFilePath().'/thumb.'.$meta_value, $project->chmod );
						
						$dims = array( 'width' => 80, 'height' => 50 );
						$image->createThumbnail( $dims, parent::getFilePath().'/tiny.'.$meta_value, $project->chmod );
					}		
				} elseif ( 'numeric' == $formfield->type || 'currency' == $formfield->type ) {
					$meta_value += 0; // convert value to numeric type
				}
					
					
				if ( is_array($meta_value) ) {
					// form field value is a date
					if ( array_key_exists('day', $meta_value) && array_key_exists('month', $meta_value) && array_key_exists('year', $meta_value) ) {
						$meta_value = sprintf("%s-%s-%s", $meta_value['year'], $meta_value['month'], $meta_value['day']);
					} elseif ( array_key_exists('hour', $meta_value) && array_key_exists('minute', $meta_value) ) {
						$meta_value = sprintf("%s:%s", $meta_value['hour'], $meta_value['minute']);
					}
				}
					
				if ( 1 == $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_datasetmeta} WHERE `dataset_id` = '".$dataset_id."' AND `form_id` = '".$meta_id."'" ) )
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `value` = '%s' WHERE `dataset_id` = '%d' AND `form_id` = '%d'", maybe_serialize($meta_value), $dataset_id, $meta_id ) );
				else
					$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ( '%d', '%d', '%s' )", $meta_id, $dataset_id, maybe_serialize($meta_value) ) );
			}
		
			// Check for unsbumitted form data, e.g. checkbox lis
			if ($form_fields = parent::getFormFields()) {
				foreach ( $form_fields AS $form_field ) {
					if ( !array_key_exists($form_field->id, $dataset_meta) ) {
						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `value` = '' WHERE `dataset_id` = '%d' AND `form_id` = '%d'", $dataset_id, $form_field->id ) );
					}
				}
			}
		}
			
			
		// Delete Image if option is checked
		if ($del_image) {
			$wpdb->query("UPDATE {$wpdb->projectmanager_dataset} SET `image` = '' WHERE `id` = {$dataset_id}");
			$this->delImage( $image_file );
		}
				
		if ( isset($_FILES['projectmanager_image']) ) {
			if ( is_array($_FILES['projectmanager_image']['name']) ) {
				$file = array(
					'name' => $_FILES['projectmanager_image']['name'][$dataset_id],
					'tmp_name' => $_FILES['projectmanager_image']['tmp_name'][$dataset_id],
					'size' => $_FILES['projectmanager_image']['size'][$dataset_id],
					'type' => $_FILES['projectmanager_image']['type'][$dataset_id],
					);
			} else {
				$file = $_FILES['projectmanager_image'];
			}

			if ( !empty($file['name']) ) 
				$this->uploadImage($dataset_id, $file, $overwrite_image);
		}
			
		$this->setmessage( __('Dataset updated.', 'projectmanager') );
		
		do_action('projectmanager_edit_dataset', $dataset_id);
	}
		
	
  /**
   * duplicate dataset
   * 
   * @param int $dataset_id
   * @return boolean
   */
  function duplicateDataset( $dataset_id )
  {
    global $projectmanager, $wpdb;
    $dataset = $projectmanager->getDataset( $dataset_id );
    $meta = $projectmanager->getDatasetMeta( $dataset_id );
    
    $meta_data = array();
    foreach ( $meta AS $m ) {
      $meta_data[$m->form_field_id] = $m->value;
    }
    
    $this->addDataset($dataset->project_id, $dataset->name, maybe_unserialize($dataset->cat_ids), $meta_data);
    $id = $wpdb->insert_id;
    $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `image` = '%s' WHERE id = '%d'", $dataset->image, $id ) );
    
    return true;
  }
  
                	
	/**
	 * delete dataset
	 *
	 * @param int $dataset_id
	 * @return void;
	 */
	function delDataset( $dataset_id )
	{
		global $wpdb, $current_user, $projectmanager;
			
		$dataset = $projectmanager->getDataset($dataset_id); 

		if ( !current_user_can('delete_datasets') || ( !current_user_can('delete_other_datasets') && $dataset->user_id != $current_user->ID ) ) 
			return;
		
			
		$this->delImage( $dataset->image );
		foreach ( parent::getDatasetMeta($dataset_id) AS $dataset_meta ) {
			if ( 'file' == $dataset_meta->type || 'video' == $dataset_meta->type) {
				@unlink(parent::getFilePath($dataset_meta->value));
			} elseif ( 'image' == $dataset_meta->type ) {
				@unlink(parent::getFilePath($dataset_meta->value));
				@unlink(parent::getFilePath("thumb.".$dataset_meta->value));
				@unlink(parent::getFilePath("tiny.".$dataset_meta->value));
			}
		}
		$wpdb->query("DELETE FROM {$wpdb->projectmanager_datasetmeta} WHERE `dataset_id` = {$dataset_id}");
		$wpdb->query("DELETE FROM {$wpdb->projectmanager_dataset} WHERE `id` = {$dataset_id}");
		
		do_action('projectmanager_del_dataset', $dataset_id);
	}
	
	
	/**
	 * delete image along with thumbnails from server
	 *
	 * @param string $image
	 * @return void
	 *
	 */
	function delImage( $image )
	{
		@unlink( parent::getFilePath($image) );
		@unlink( parent::getFilePath('/thumb.'.$image) );
		@unlink( parent::getFilePath('/tiny.'.$image) );
	}
	
	
	/**
	 * set image path in database and upload image to server
	 *
	 * @param int $dataset_id
	 * @param array $file
	 * @param boolean $overwrite_image
	 * @return void | string
	 */
	function uploadImage( $dataset_id, $file, $overwrite = false )
	{
		global $wpdb;
		
		$project = $this->project;

		$new_file = parent::getFilePath().'/'.basename($file['name']);
		$image = new ProjectManagerImage($new_file);
		if ( $image->supported($file['name']) ) {
			if ( $file['size'] > 0 ) {
				if ( file_exists($new_file) && !$overwrite ) {
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `image` = '%s' WHERE id = '%d'", basename($file['name']), $dataset_id ) );
					$this->setMessage( __('File exists and is not uploaded. Set the overwrite option if you want to replace it.','projectmanager'), true );
				} else {
					if ( move_uploaded_file($file['tmp_name'], $new_file) ) {
						if ( $dataset = parent::getDataset($dataset_id) )
							if ( $dataset->image != '' ) $this->delImage($dataset->image);

						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `image` = '%s' WHERE id = '%d'", basename($file['name']), $dataset_id ) );
			
						// Resize original file and create thumbnails
						$dims = array( 'width' => $project->medium_size['width'], 'height' => $project->medium_size['height'] );
						$image->createThumbnail( $dims, $new_file, $project->chmod );

						$dims = array( 'width' => $project->thumb_size['width'], 'height' => $project->thumb_size['height'] );
						$image->createThumbnail( $dims, parent::getFilePath().'/thumb.'.basename($file['name']), $project->chmod );
						
						$dims = array( 'width' => 80, 'height' => 50 );
						$image->createThumbnail( $dims, parent::getFilePath().'/tiny.'.basename($file['name']), $project->chmod );
					} else {
						$this->setMessage( sprintf( __('The uploaded file could not be moved to %s.' ), parent::getFilePath() ), true );
					}
				}
			}
		} else {
			$this->setMessage( __('The file type is not supported.','projectmanager'), true );
		}
	}
	
	
	/**
	 * Upload file to webserver
	 * 
	 */
	function uploadFile( $file, $overwrite = false )
	{
		$new_file = parent::getFilePath().'/'.basename($file['name']);
		if ( file_exists($new_file) && !$overwrite ) {
			$this->setMessage( __('File exists and is not uploaded. Set the overwrite option if you want to replace it.','projectmanager'), true );
		} else {
			if ( !move_uploaded_file($file['tmp_name'], $new_file) ) {
				$this->setMessage( sprintf( __('The uploaded file could not be moved to %s.' ), parent::getFilePath() ), true );
			}
		}
	}
	
	
	/**
	 * Set File for editing datasets
	 * 
	 * @param array $file
	 * @param boolean $overwrite
	 * @param boolean $del_file
	 * @return string
	 */
	function editFile( $file, $overwrite, $del )
	{
		if ( $del )
			@unlink(parent::getFilePath(basename($file['current'])));
						
		if ( !empty($file['name']) ) {
			$overwrite = isset($overwrite) ? true : false;
			$this->uploadFile($file, $overwrite);
		}
		if ( $del )
			$meta_value = '';
		else
			$meta_value = !empty($file['name']) ? basename($file['name']) : $file['current'];
			
		return $meta_value;
	}
	
	
	/**
	 * save Form Fields
	 *
	 * @param int $project_id
	 * @param array $formfields
	 * @param array $new_formfields
	 *
	 * @return string
	 */
	function setFormFields( $project_id, $formfields, $new_formfields )
	{
		global $wpdb;
		
		if ( !current_user_can('edit_formfields') ) {
			$this->setMessage( __("You don't have permission to perform this task", 'projectmanager'), true );
			return;
		}

		$options = get_option('projectmanager');
		if ( !empty($formfields) ) {
			foreach ( $wpdb->get_results( "SELECT `id`, `project_id` FROM {$wpdb->projectmanager_projectmeta}" ) AS $form_field) {
				if ( !array_key_exists( $form_field->id, $formfields ) ) {
					$del = (bool) $wpdb->query( "DELETE FROM {$wpdb->projectmanager_projectmeta} WHERE `id` = {$form_field->id} AND `project_id` = {$project_id}"  );
					if ( $del ) unset($options['form_field_options'][$form_field->id]);
					if ( $project_id == $form_field->project_id )
						$wpdb->query( "DELETE FROM {$wpdb->projectmanager_datasetmeta} WHERE `form_id` = {$form_field->id}" );
				}
			}
				
			foreach ( $formfields AS $id => $formfield ) {
				$order_by = isset($formfield['orderby']) ? 1 : 0;
				$show_on_startpage = isset($formfield['show_on_startpage']) ? 1 : 0;
				$show_in_profile = isset($formfield['show_in_profile']) ? 1 : 0;
					
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_projectmeta} SET `label` = '%s', `type` = '%s', `show_on_startpage` = '%d', `show_in_profile` = '%d', `order` = '%d', `order_by` = '%d' WHERE `id` = '%d' LIMIT 1 ;", $formfield['name'], $formfield['type'], $show_on_startpage, $show_in_profile, $formfield['order'], $order_by, $id ) );
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `form_id` = '%d' WHERE `form_id` = '%d'", $id, $id ) );
			}
		} else {
			$wpdb->query( "DELETE FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = {$project_id}"  );
		}
			
		if ( !empty($new_formfields) ) {
			foreach ($new_formfields AS $tmp_id => $formfield) {
				$order_by = isset($formfield['orderby']) ? 1 : 0;
				$show_on_startpage = isset($formfield['show_on_startpage']) ? 1 : 0;
				$show_in_profile = isset($formfield['show_in_profile']) ? 1 : 0;
				
				$max_order_sql = "SELECT MAX(`order`) AS `order` FROM {$wpdb->projectmanager_projectmeta} WHERE `project_id` = {$project_id};";
				if ($formfield['order'] != '') {
					$order = $formfield['order'];
				} else {
					$max_order_sql = $wpdb->get_results($max_order_sql, ARRAY_A);
					$order = $max_order_sql[0]['order'] +1;
				}
				
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_projectmeta} (`label`, `type`, `show_on_startpage`, `show_in_profile`, `order`, `order_by`, `project_id`) VALUES ( '%s', '%s', '%d', '%d', '%d', '%d', '%d');", $formfield['name'], $formfield['type'], $show_on_startpage, $show_in_profile, $order, $order_by, $project_id ) );
				$id = $wpdb->insert_id;
					
				// Redirect form field options to correct $form_id if present
				if ( isset($options['form_field_options'][$tmp_id]) ) {
					$options['form_field_options'][$id] = $options['form_field_options'][$tmp_id];
					unset($options['form_field_options'][$tmp_id]);
				}
				
				/*
				* Populate default values for every dataset
				*/
				if ( $datasets = $wpdb->get_results( "SELECT `id` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$project_id}" ) ) {
					foreach ( $datasets AS $dataset ) {
						$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ( '%d', '%d', '' );", $id, $dataset->id ) );
					}
				}
			}
		}
		
		if ( isset($options['form_field_options'][$id]) )
		  sort($options['form_field_options'][$id]);
		  
		update_option('projectmanager', $options);
		$this->setMessage( __('Form Fields updated', 'projectmanager') );
		
		do_action('projectmanager_save_formfields', $project_id);
	}
	
	
	/**
	 * print breadcrumb navigation
	 *
	 * @param int $project_id
	 * @param string $page_title
	 * @param boolean $start
	 */
	function printBreadcrumb( $page_title, $start=false )
	{
		global $projectmanager;
		$project = $projectmanager->getProject($projectmanager->getProjectID());

		if ( 1 != $project->navi_link ) {
			echo '<p class="projectmanager_breadcrumb">';
			if ( !$this->single )
				echo '<a href="admin.php?page=projectmanager">'.__( 'Projectmanager', 'projectmanager' ).'</a> &raquo; ';
			
			if ( $page_title != $project->title )
				echo '<a href="admin.php?page=projectmanager&subpage=show-project&amp;project_id='.$project->id.'">'.$project->title.'</a> &raquo; ';
			
			if ( !$start || ($start && !$this->single) ) echo $page_title;
			
			echo '</p>';
		}
	}
	
	
	/**
	 * hook dataset input fields into profile
	 *
	 * @param none
	 */
	function profileHook()
	{
		global $current_user, $wpdb, $projectmanager;
		
		if ( !current_user_can('projectmanager_user') )
			return;

		$options = get_option('projectmanager');

		$projects = array();
		foreach ( $projectmanager->getProjects() AS $project ) {
			if ( isset($project->profile_hook) && 1 == $project->profile_hook ) 
				$projects[] = $project->id;
		}

		if ( !empty($projects) ) {
			foreach ( $projects AS $project_id ) {
				$this->project_id = $project_id;
				$projectmanager->initialize($this->project_id);
				$project = $projectmanager->getProject();
			
				$is_profile_page = true;
				$dataset = $wpdb->get_results( "SELECT `id`, `name`, `image`, `cat_ids`, `user_id` FROM {$wpdb->projectmanager_dataset} WHERE `project_id` = {$this->project_id} AND `user_id` = '".$current_user->ID."' LIMIT 0,1" );
				$dataset = $dataset[0];
					
				if ( $dataset ) {
					$dataset_id = $dataset->id;
					$cat_ids = $projectmanager->getSelectedCategoryIDs($dataset);
					$dataset_meta = $projectmanager->getDatasetMeta( $dataset_id );
		
					$img_filename = $dataset->image;
					$meta_data = array();
					foreach ( $dataset_meta AS $meta )
						if ( is_string($meta_data[$meta->form_field_id] ) )
							$meta_data[$meta->form_field_id] = htmlspecialchars(stripslashes_deep($meta->value), ENT_QUOTES);
						else
							$meta_data[$meta->form_field_id] = stripslashes_deep($meta->value);
			
					echo '<h3>'.$projectmanager->getProjectTitle().'</h3>';
					echo '<input type="hidden" name="project_id['.$dataset_id.']" value="'.$project_id.'" /><input type="hidden" name="dataset_id[]" value="'.$dataset_id.'" /><input type="hidden" name="dataset_user_id" value="'.$current_user->ID.'" />';
				
				  $projectmanager->loadTinyMCE();
					include( dirname(__FILE__). '/dataset-form-profile.php' );
				}
			}
		}
	}
	
	
	/**
	 * update Profile settings
	 *
	 * @param none
	 * @return none
	 */
	function updateProfile()
	{
		$user_id = $_POST['dataset_user_id'];

		foreach ( (array)$_POST['dataset_id'] AS $id ) {
			$del_image = isset( $_POST['del_old_image'][$id] ) ? true : false;
			$overwrite_image = ( isset($_POST['overwrite_image'][$id]) && 1 == $_POST['overwrite_image'][$id] ) ? true: false;
			$this->editDataset( $_POST['project_id'][$id], $_POST['display_name'], $_POST['post_category'][$id], $id, $_POST['form_field'][$id], $user_id, $del_image, $_POST['image_file'][$id], $overwrite_image );
		}
	}
}
