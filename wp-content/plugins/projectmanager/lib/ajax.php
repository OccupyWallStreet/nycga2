<?php
/**
 * AJAX class for the WordPress plugin ProjectManager
 * 
 * @author 	Kolja Schleich
 * @package	ProjectManager
 * @copyright 	Copyright 2008-2009
*/
class ProjectManagerAJAX
{
	/**
	 * add ajax actions
	 *
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		// Admin AJAX
		if ( is_admin() ) {
			add_action( 'wp_ajax_projectmanager_save_name', array(&$this, 'saveName') );
			add_action( 'wp_ajax_projectmanager_save_categories', array(&$this, 'saveCategories') );
			add_action( 'wp_ajax_projectmanager_save_form_field_data', array(&$this, 'saveFormFieldData') );
			add_action( 'wp_ajax_projectmanager_save_form_field_options', array(&$this, 'saveFormFieldOptions') );
			add_action( 'wp_ajax_projectmanager_save_dataset_order', array(&$this, 'saveDatasetOrder') );
			add_action( 'wp_ajax_projectmanager_ajax_delete_file', array(&$this, 'deleteFile') ); 
			add_action( 'wp_ajax_projectmanager_insert_wp_user', array(&$this, 'insertWpUser') );

			add_action( 'wp_ajax_projectmanager_toggle_formfield_options', array(&$this, 'toggleFormfieldOptions') );
			add_action( 'wp_ajax_projectmanager_get_cat_dropdown', array(&$this, 'getCategoryDropdown') );
			add_action( 'wp_ajax_projectmanager_save_project_link', array(&$this, 'saveProjectLink') );
		}
	}
	function ProjectManagerAJAX()
	{
		$this->__construct();
	}


	/**
	 * SACK response to delete file
	 * 
	 * @since 1.4
	 */
	function deleteFile() {
		$file = $_POST['file'];
		@unlink($file);
		die();
	}


	/**
	 * SACK response for saving form field options
	 *
	 * @since 1.3
	 */
	function saveFormFieldOptions() {
		$options = get_option('projectmanager');
	
		$form_id = $_POST['form_id'];
		$form_options = substr($_POST['options'], 0, -1);
		$form_options = explode("|", $form_options);
		
		$options['form_field_options'][$form_id] = $form_options;
		update_option('projectmanager', $options);
	
		die("ProjectManager.reInit();");
	}

	
	/**
	 * save Project Link
	 *
	 * @since 2.7
	 */
	function saveProjectLink()
	{
		$project_id = (int)$_POST['project_id'];
		$formfield_id = (int)$_POST['formfield_id'];

		$options = get_option('projectmanager');
		$options['form_field_options'][$formfield_id] = $project_id;

		update_option('projectmanager', $options);
	
		die("ProjectManager.reInit();");
	}


	/**
	 * SACK response function for saving dataset name
	 *
	 * @since 1.2
	 */
	function saveName() {
		global $wpdb;
	
		$dataset_id = intval($_POST['dataset_id']);
		$new_name = $_POST['new_name'];
	
		/*if (get_magic_quotes_gpc())
			$new_name = stripslashes_deep($new_name);*/
		
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `name` = '%s' WHERE `id` = '%d'", $new_name, $dataset_id ) );

		die( "ProjectManager.reInit();jQuery('span#dataset_name_text" . $dataset_id . "').fadeOut('fast', function() {
			jQuery('a#thickboxlink_name" . $dataset_id . "').show();
			jQuery('span#dataset_name_text" . $dataset_id . "').html('" . addslashes_gpc( $new_name ) . "').fadeIn('fast');
			ProjectManager.doneLoading('loading_name_".$dataset_id."');
		});");
	}


	/**
	 * SACK response function for saving group
	 *
	 * @since 1.2
	 */
	function saveCategories() {
		global $wpdb, $projectmanager;
	
		$dataset_id = intval($_POST['dataset_id']);
		$new_cats = explode(",",substr($_POST['cat_ids'],0,-1));
	
		if ( count($new_cats) > 0 ) {
			$cat_name = $projectmanager->getSelectedCategoryTitles($new_cats);
		} else {
			$cat_name = __('None', 'projectmanager');
		}

		$wpdb->query( $wpdb->prepare ( "UPDATE {$wpdb->projectmanager_dataset} SET `cat_ids` = %s WHERE `id` = %d", maybe_serialize($new_cats), $dataset_id ) );

		die( "ProjectManager.reInit();jQuery('span#dataset_category_text" . $dataset_id . "').fadeOut('fast', function() {
			jQuery('a#thickboxlink_category" . $dataset_id . "').show();
			jQuery('span#dataset_category_text" . $dataset_id . "').html('" . $cat_name . "').fadeIn('fast');
			ProjectManager.doneLoading('loading_category_".$dataset_id."');
		});");
	}


	/**
	 * SACK response function to save any dynamic form field
	 *
	 * @since 1.2
	 */
	function saveFormFieldData() {
		global $wpdb, $projectmanager, $projectmanager_loader;
	
		$dataset_id = intval($_POST['dataset_id']);
		$formfield_type = $_POST['formfield_type'];
		$meta_id = intval($_POST['formfield_id']);
		$new_value = $_POST['new_value'];

		// Textarea
		if ( 'textfield' == $formfield_type )
			$new_value = str_replace('\n', "\n", $new_value);
		// Checkbox List
		if ( 'checkbox' == $formfield_type || 'project' == $formfield_type ) {
			$new_value = substr($new_value,0,-1);
			$new_value = explode(",",$new_value);
		}

		$count = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->projectmanager_datasetmeta} WHERE `dataset_id` = '".$dataset_id."' AND `form_id` = '".$meta_id."'" );
		if ( !empty($count) )
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_datasetmeta} SET `value` = '%s' WHERE `dataset_id` = '%d' AND `form_id` = '%d'", maybe_serialize($new_value), $dataset_id, $meta_id ) );
		else
			$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->projectmanager_datasetmeta} (form_id, dataset_id, value) VALUES ( '%d', '%d', '%s' )", $meta_id, $dataset_id, maybe_serialize($new_value) ) );
	
		// Textarea
		if ( 'textfield' == $formfield_type ) {
			$new_value = str_replace("\n", "", $new_value);
		if (strlen($new_value) > 150 )
			$new_value = substr($new_value, 0, 150)."...";
		}
			
		// Some special output formats
		if ( 'date' == $formfield_type )
			$new_value = mysql2date(get_option('date_format'), $_POST['new_value']);
		elseif ( 'time' == $formfield_type )
			$new_value = mysql2date(get_option('time_format'), $_POST['new_value']);
		elseif ( 'image' == $formfield_type )
			$new_value = '<img class="projectmanager_image" src="'.$new_value.'" alt="'.__("Image", "projectmanager").'" />';
		elseif ( 'uri' == $formfield_type )
			$new_value = '<a class="projectmanager_url" href="http://'.$projectmanager->extractURL($new_value, 'url').'" target="_blank" title="'.$projectmanager->extractURL($new_value, 'title').'">'.$projectmanager->extractURL($new_value, 'title').'</a>';
		elseif ( 'email' == $formfield_type )
			$new_value = '<a href="mailto:'.$projectmanager->extractURL($new_value, 'url').'" class="projectmanager_email">'.$projectmanager->extractURL($new_value, 'title').'</a>';	
		elseif ( 'numeric' == $formfield_type ) {
			$new_value = apply_filters( 'projectmanager_numeric', $new_value );
		} elseif ( 'currency' == $formfield_type ) {
			$new_value = money_format('%i', $new_value);
			$new_value = apply_filters( 'projectmanager_currency', $new_value );
		} elseif ( 'checkbox' == $formfield_type || 'project' == $formfield_type ) {
			$list = '<ul class="'.$formfield_type.'" id="form_field_'.$meta_id.'">';
			foreach ( (array)$new_value AS $item ) {
				if ( 'project' == $formfield_type && is_numeric($item) ) {
					$item = $projectmanager->getDataset($item);
					if ( $_GET['page'] == 'projectmanager' )
						$url_pattern = '<a href="admin.php?page=projectmanager&subpage=dataset&edit='.$item->id.'&project_id='.$item->project_id.'">%s</a>';
					else
						$url_pattern = '<a href="admin.php?page=project-dataset_'.$item->project_id.'&edit='.$item->id.'&project_id='.$item->project_id.'">%s</a>';
					
					$item = sprintf($url_pattern, $item->name);
				}
				$list .= '<li>'.$item.'</li>';
			}
			$list .= '</ul>';
			$new_value = $list;
		} elseif ( 'wp_user' == $formfield_type ) {
			$userdata = get_userdata($new_value);
			$new_value = $userdata->display_name;
		}


		die( "
			jQuery('span#datafield" . $meta_id . "_" . $dataset_id . "').fadeOut('fast', function() {
			jQuery('a#thickboxlink" . $meta_id . "_" . $dataset_id . "').show();
			jQuery('span#datafield" . $meta_id . "_" . $dataset_id . "').html('" . $new_value . "').fadeIn('fast');
			ProjectManager.doneLoading('loading_".$meta_id."_".$dataset_id."');
			ProjectManager.reInit();
		});");
	}


	/**
	 * SACK response to manually set order of datasets
	 *
	 * @since 2.0
	 */
	function saveDatasetOrder() {
		global $wpdb, $projectmanager_loader;
		$order = $_POST['order'];
		$order = $projectmanager_loader->adminPanel->getOrder($order);
		foreach ( $order AS $order => $dataset_id ) {
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->projectmanager_dataset} SET `order` = '%d' WHERE `id` = '%d'", $order, $dataset_id ) );
		}
	}


	/**
	 * SACK response to insert user data from database
	 *
	 * @since 2.5
	 */
	function insertWpUser() {
		$user_id = (int)$_POST['wp_user_id'];
		$user = new WP_User($user_id);
		$user = $user->data;

		die("
			document.getElementById('name').value = '".$user->display_name."';
			document.getElementById('user_id').value = '".$user_id."';
		");
	}


	/**
	 * get category dropdown
	 *
	 * @param none
	 * @since 2.7
	 */
	function getCategoryDropdown()
	{
		global $projectmanager;

		$project_id = (int)$_POST['project_id'];
		$formfield_id = (int)$_POST['formfield_id'];

		if ( !empty($project_id) ) {
			$p = $projectmanager->getProject($project_id);

			if ( !empty($p->category) && $p->category != -1 ) {
				$html = wp_dropdown_categories(array('hide_empty' => 0, 'name' => "form_field[".$formfield_id."][category]", 'orderby' => 'name', 'echo' => 0, 'show_option_none' => __('Select Categoy (Optional)', 'projectmanager'), 'child_of' => $p->category));
				$html = str_replace("\n", "", $html);
				die("jQuery('span#project_cat_".$formfield_id."').fadeOut('fast', function () {
					jQuery('span#project_cat_".$formfield_id."').html('".addslashes_gpc($html)."').fadeIn('fast');
				})");
			} else {
				die("jQuery('span#project_cat_".$formfield_id."').fadeOut('fast');");
			}
		} else {
			die("jQuery('span#project_cat_".$formfield_id."').fadeOut('fast');");
		}
	}


	/**
	 * toggle Formfield Options
	 *
	 * @since 2.7
	 */
	function toggleFormfieldOptions()
	{
		global $projectmanager_loader, $projectmanager;

		$project_id = (int)$_POST['project_id'];
		$formfield_id = (int)$_POST['formfield_id'];
		$formfield_type = (string)$_POST['formfield_type'];
		$options = (string)$_POST['options'];
		$options = explode("|", $options);

		$admin = $projectmanager_loader->getAdminPanel();

		$html = '';

		if ( 'project' == $formfield_type || 'checkbox' == $formfield_type || 'radio' == $formfield_type || 'select' == $formfield_type ) {
//			$html = '<div id="form_field_options_container'.$formfield_id.'" style="display: inline;">';
			$html = '<div id="form_field_options_div'.$formfield_id.'" style="overflow: auto; display: none;">';
//			$html .= '<form>';
			if ( 'project' == $formfield_type ) {
				$dims = array( 'width' => 300, 'height' => 100 );
				$thickbox_title = __( 'Choose Project to Link', 'projectmanager' );
				$icon = 'databases.png';
				
				$html .= '<div class="thickbox_content">';
				$html .= '<select size="1" id="form_field_project_'.$formfield_id.'">';
				$html .= '<option value="0">'.__( 'Choose Project', 'projectmanager' ).'</option>';
				foreach ( $projectmanager->getProjects() AS $p ) {
					if ( $p->id != $project_id )
						$html .= '<option value="'.$p->id.'">'.$p->title.'</option>';
				}
				$html .= '</select>';
				$html .= '<div class="buttonbar"><input type="button" value="'.__('Save').'" class="button-secondary" onclick="ProjectManager.saveProjectLink('.$formfield_id.');return false;" />&#160;<input type="button" value="'.__('Cancel').'" class="button" onclick="tb_remove();" /></div></div>';
			} elseif ( 'checkbox' == $formfield_type || 'radio' == $formfield_type || 'select' == $formfield_type ) {
				$dims = array( 'width' => 350, 'height' => 200 );
				$thickbox_title = __( 'Options', 'projectmanager' );
				$icon = 'application_list.png';

				$html .= '<div class="thickbox_content"><div class="">';
				$html .= '<ul id="form_field_options_'.$formfield_id.'">';
				foreach ( (array)$options AS $x => $item ) {
					$html .= '<li id="form_field_option_'.$formfield_id.'_'.$x.'"><input type="text" size="30" value="'.$item.'" name="form_field_option_'.$formfield_id.'" /><a class="image_link" href="#" onclick="return ProjectManager.removeFormFieldOption(\'form_field_option_'.$formfield_id.'_'.$x.'\', '.$formfield_id.');"><img src="../wp-content/plugins/projectmanager/admin/icons/trash.gif" alt="'.__( 'Delete', 'projectmanager' ).'" title="'.__( 'Delete Option', 'projectmanager' ).'" /></a></li>';
				}
				$html .= '</ul>';
				$html .= '</div>';

				$html .= '<p><a href="#" onClick="ProjectManager.addFormFieldOption('.$formfield_id.')">'.__( 'Add Option', 'projectmanager' ).'</a></p>';

				$html .= '<div class="buttonbar"><input type="button" value="'.__('Save').'" class="button-secondary" onclick="ProjectManager.ajaxSaveFormFieldOptions('.$formfield_id.');return false;" />&#160;<input type="button" value="'.__('Cancel').'" class="button" onclick="tb_remove();" /></div>';
				$html .= '</div>';
			}

//			$html .= '</form>';
			$html .= '</div>';
			$html .= '<span>&#160;<a href="#TB_inline&width='.$dims['width'].'&height='.$dims['height'].'&inlineId=form_field_options_div'.$formfield_id.'" style="display: inline;" id="options_link'.$formfield_id.'" class="thickbox" title="'.$thickbox_title.'"><img src="'.$admin->getIconURL($icon).'" alt="'.__('Options','projectmanager').'" class="middle" /></a></span>';
//			$html .= '</div>';
		}

		die("
			var type = '".$formfield_type."';
			new_element = document.createElement('div');
			new_element_id = 'form_field_options_container".$formfield_id."';
			new_element.id= new_element_id;
			new_element.style.display = 'inline';
			// Check if selected form type is selection, checkbox, or radio
			if ( type == 'project' || type == 'select' || type == 'checkbox' || type == 'radio' ) {
				if ( document.getElementById(new_element_id) ) {
					jQuery('div#form_field_options_container".$formfield_id."').fadeOut('fast');
				} else {
					if ( document.getElementById('form_field_optionos_box".$formfield_id."') ) {
						document.getElementById('form_field_options_box".$formfield_id."').appendChild(new_element);
					} else {
						alert('".__('An Error Occured. Please Save FormFields and set Options afterwards', 'projectmanager')."');
					}
				}
				jQuery('div#form_field_options_container".$formfield_id."').html(\"".addslashes_gpc($html)."\").fadeIn('fast');
				ProjectManager.reInit();
			} else {
				jQuery('div#form_field_options_container".$formfield_id."').fadeOut('fast');
				if (target_element = document.getElementById(new_element_id)) {;
					document.getElementById('form_field_options_box".$formfield_id."').removeChild(target_element);
				}
			}

		");
			//ProjectManager.doneLoading('loading_formfield_options_".$formfield_id."');
	}
}
