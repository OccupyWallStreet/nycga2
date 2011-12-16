<?php
/**
* Shortcodes class for the WordPress plugin ProjectManager
* 
* @author 	Kolja Schleich
* @package	ProjectManager
* @copyright 	Copyright 2008-2009
*/

class ProjectManagerShortcodes
{
	/**
	 * initialize shortcodes
	 *
	 * @param none
	 * @return void
	 */
	function __construct()
	{
		$this->addShortcodes();
	}
	function LeagueManagerShortcodes()
	{
		$this->__construct();
	}
	
	
	/**
	 * Adds shortcodes
	 *
	 * @param none
	 * @return void
	 */
	function addShortcodes()
	{
		add_shortcode( 'dataset', array(&$this, 'displayDataset') );
		add_shortcode( 'project', array(&$this, 'displayProject') );
		add_shortcode( 'dataset_form', array(&$this, 'displayDatasetForm') );
		add_shortcode( 'project_search', array(&$this, 'displaySearchForm') );
		add_action( 'projectmanager_selections', array(&$this, 'displaySelections') );
		add_action( 'projectmanager_tablenav', array(&$this, 'displaySelections') );
		add_action( 'projectmanager_dataset', array(&$this, 'displayDataset') );
	}
	
	
	/**
	 * Load template for user display.
	 * 
	 * Checks firrst the current theme directory for a template
	 * before defaulting to the plugin
	 *
	 * @param string $template Name of the template file (without extension)
	 * @param array $vars Array of variables name=>value available to display code (optional)
	 * @return the content
	 */
	function loadTemplate( $template, $vars = array() )
	{
		global $projectmanager;
		extract($vars);
		
		ob_start();
		if ( file_exists( TEMPLATEPATH . "/projectmanager/$template.php")) {
			include(TEMPLATEPATH . "/projectmanager/$template.php");
		} elseif ( file_exists(PROJECTMANAGER_PATH . "/templates/$template.php") ) {
			include(PROJECTMANAGER_PATH . "/templates/$template.php");
		} else {
			$projectmanager->setMessage( sprintf(__('Could not load template %s.php', 'projectmanager'), $template), true );
			$projectmanager->printMessage();
		}
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
	
	
	/**
	 * Function to display search formular
	 *
	 * @param array $atts
	 * @return string
	 */
	function displaySearchForm( $atts )
	{
		global $projectmanager;
		
		extract(shortcode_atts(array(
			'project_id' => 0,
			'template' => 'extend'
		), $atts ));
		
		$projectmanager->initialize($project_id);

		$search_option = $projectmanager->getSearchOption();
		$search_string = $projectmanager->getSearchString();
		$form_fields = $projectmanager->getFormFields();
		
		$filename = 'searchform-'.$template;
		if ( !isset($_GET['show'])) {
			$out = $this->loadTemplate( $filename, array( 'form_fields' => $form_fields, 'search' => $search_string, 'search_option' => $search_option ) );
		}

		return $out;
	}
		
	
	/**
	 * display dataset form
	 *
	 * Include the dataset formular into the frontpage
	 *
	 * @param array $atts
	 * @return void
	 */
	function displayDatasetForm( $atts )
	{
		global $projectmanager;

		extract(shortcode_atts(array(
			'project_id' => 0,
		), $atts ));

		$projectmanager->initialize($project_id);
		$project = $projectmanager->getCurrentProject();
		
		$options = get_option('projectmanager');
		if ( isset($_GET['d_id']) ) {
			$edit = true;
			$form_title = __('Edit Dataset','projectmanager');
			$dataset_id = (int)$_GET['d_id'];
			$dataset = $projectmanager->getDataset( $dataset_id );
	
			$cat_ids = $projectmanager->getSelectedCategoryIDs($dataset);
			$dataset_meta = $projectmanager->getDatasetMeta( $dataset_id );
	
			$name = htmlspecialchars(stripslashes_deep($dataset->name), ENT_QUOTES);
	
			$img_filename = $dataset->image;
			$meta_data = array();
			foreach ( $dataset_meta AS $meta ) {
				if ( is_string($meta_data[$meta->form_field_id] ) )
					$meta_data[$meta->form_field_id] = htmlspecialchars(stripslashes_deep($meta->value), ENT_QUOTES);
				else
					$meta_data[$meta->form_field_id] = stripslashes_deep($meta->value);
			}
		}  else {
			$edit = false;
			$form_title = __('Add Dataset','projectmanager');
			$dataset_id = ''; $cat_ids = array(); $img_filename = ''; $name = ''; $meta_data = array();
		}

		$projectmanager->loadTinyMCE(); 

		$filename = 'dataset-form';
		$out = $this->loadTemplate( $filename, array('projectmanager' => $projectmanager, 'dataset' => $dataset, 'project' => $project, 'name' => $name, 'img_filename' => $img_filename, 'meta_data' => $meta_data, 'edit' => $edit, 'cat_ids' => $cat_ids, 'form_title' => $form_title) );

		return $out;
	}


	/**
	 * get dropdown selections
	 *
	 * This function is called via do_action('projectmanager_selections') and loads the template selections.php
	 *
	 * @param int $project_id
	 * @return void the dropdown selections
	 */
	function displaySelections( $project_id = false )
	{
		global $projectmanager;
		if ( $project_id )
			$project = $projectmanager->getProject($project_id);
		else
			$project = $projectmanager->getCurrentProject();
	
		$orderby = array( '' => __('Order By', 'projectmanager'), 'name' => __('Name','projectmanager'), 'id' => __('ID','projectmanager') );
		foreach ( $projectmanager->getFormFields() AS $form_field )
			$orderby['formfields_'.$form_field->id] = $form_field->label;

		$order = array( '' => __('Order','projectmanager'), 'asc' => __('Ascending','projectmanager'), 'desc' => __('Descending','projectmanager') );
		
		$category = ( -1 != $project->category ) ? $project->category : false;
		$selected_cat = $projectmanager->getCatID();
		
		$out = $this->loadTemplate( 'selections', array( 'category' => $category, 'selected_cat' => $selected_cat, 'orderby' => $orderby, 'order' => $order) );

		echo $out;
	}
	
	
	/**
	 * Function to display the project in a page or post as list.
	 *
	 *	[project id="x" template="table|gallery"]
	 *
	 * - id is the ID of the project to display
	 * - template is the template file without extension. Default values are "table" or "gallery".
	 *
	 * It follows a list of optional attributes
	 *
	 * - cat_id: specify a category to only display those datasets. all datasets will be displayed if missing
	 * - orderby: 'name', 'id' or 'formfield-X' where x is the formfield ID (default 'name')
	 * - order: 'asc' or 'desc' (default 'asc')
	 * - single: control if link to sigle dataset is displayed. Either 'true' or 'false' (default 'true')
	 * - selections: control wether or not selection panel is dislayed (default 'true')
	 *
	 * @param array $atts
	 * @return the content
	 */
	function displayProject( $atts )
	{
		global $wp, $projectmanager;
		
		extract(shortcode_atts(array(
			'id' => 0,
			'template' => 'table',
			'cat_id' => false,
			'orderby' => false,
			'order' => false,
			'single' => 'true',
			'selections' => 'true',
			'results' => true,
			'field_id' => false,
			'field_value' => false,
		), $atts ));
		$projectmanager->initialize($id);
		$project = $projectmanager->getCurrentProject();

		$single = ( $single == 'true' ) ? true : false;
		$random = ( $orderby == 'rand' ) ? true : false;

		if ( $cat_id ) $projectmanager->setCatID($cat_id);
	
		if ( isset($_GET['show']) ) {
			$datasets = $title = $pagination = false;
		} else {
			$formfield_id = false;
			
			if ( $projectmanager->isSearch() )
				$datasets = $projectmanager->getSearchResults();
			else
				$datasets = $projectmanager->getDatasets( array( 'limit' => $results, 'orderby' => $orderby, 'order' => $order, 'random' => $random, 'meta_key' => $field_id, 'meta_value' => $field_value) );
			
			$title = '';
			if ( $projectmanager->isSearch() ) {
				$num_datasets = $projectmanager->getNumDatasets($projectmanager->getProjectID(), true);
				$title = "<h3 style='clear:both;'>".sprintf(__('Search: %d of %d', 'projectmanager'),  $projectmanager->getNumDatasets($projectmanager->getProjectID()), $num_datasets)."</h3>";
			} elseif ( $projectmanager->isCategory() ) {
				$title = "<h3 style='clear:both;'>".$projectmanager->getCatTitle($projectmanager->getCatID())."</h3>";
			}
			
			$pagination = ( $projectmanager->isSearch() ) ? '' : $projectmanager->getPageLinks();
			
			$i = 0;
			foreach ( $datasets AS $dataset ) {
				$class = ( !isset($class) || "alternate" == $class ) ? '' : "alternate"; 
				
				$dataset->name = stripslashes($dataset->name);
				
				$url = get_permalink();
				$url = add_query_arg('show', $dataset->id, $url);
				$url = ($projectmanager->isCategory()) ? add_query_arg('cat_id', $projectmanager->getCatID(), $url) : $url;
				
				$project->num_datasets = $projectmanager->getNumDatasets($projectmanager->getProjectID(), true);
				$project->gallery_num_cols = ( $project->gallery_num_cols == 0 ) ? 4 : $project->gallery_num_cols;
				$project->dataset_width = floor(100/$project->gallery_num_cols);
				$project->single = ( $single == 'true' ) ? true : false;
				$project->selections = ( $selections == 'true' ) ? true : false;

				$datasets[$i]->class = $class;
				$datasets[$i]->URL = $url;
				$datasets[$i]->thumbURL = $projectmanager->getFileURL('/thumb.'.$dataset->image);
				$datasets[$i]->nameURL = ($projectmanager->hasDetails($single)) ? '<a href="'.$url.'">'.$dataset->name.'</a>' : $dataset->name;
				
				$i++;
			}
		}
		
		$out = $this->loadTemplate( $template, array('project' => $project, 'datasets' => $datasets, 'title' => $title, 'pagination' => $pagination) );
		
		return $out;
	}
	
	
	/**
	 * Function to display the single view of a dataset. Loaded by function list and gallery
	 *
	 *	[dataset id="1" template="" ]
	 *
	 * - id is the ID of the dataset to display
	 * - template is the name of a template (without extension). Will use default template dataset.php if missing or empty
	 *
	 * @param int $dataset_id
	 * @param boolean $callback - checks if function is called via action hook
	 * @return string
	 */
	function displayDataset( $atts, $action = false )
	{
		global $projectmanager;
		
		extract(shortcode_atts(array(
			'id' => 0,
			'template' => '',
			'echo' => 0
		), $atts ));
		
		if ( !$action ) {
			$url = get_permalink();
			$url = remove_query_arg('show', $url);
			$url = add_query_arg('paged', $projectmanager->getDatasetPage($id), $url);
			$url = ($projectmanager->isCategory()) ? add_query_arg('cat_id', $projectmanager->getCatID(), $url) : $url;
		} else {
			$url = false;
		}

		if ( $dataset = $projectmanager->getDataset( $id ) ) {
			$dataset->imgURL = $projectmanager->getFileURL($dataset->image);
			$dataset->name = stripslashes($dataset->name);
		}
				
		$filename = ( empty($template) ) ? 'dataset' : 'dataset-'.$template;
		$out = $this->loadTemplate( $filename, array('dataset' => $dataset, 'backurl' => $url) );

		if ( $echo )
			echo $out;
		
		return $out;
	}
}
?>
