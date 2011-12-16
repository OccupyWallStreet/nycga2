<?php

/**
 * display widget
 *
 * @param int $number
 * @param array $instance
 */
function projectmanager_display_widget( $number, $instance ) {
	echo "<ul id='projectmanager-widget-".$instance['project']."' class='projectmanager_widget'>";
	$widget = new ProjectManagerWidget(true);
	$widget->widget( array('number' => $number), $intance );
	echo "</ul>";
}


/**
 * display searchform manually
 *
 * @param int $project_id Project ID
 * @param array $args assoziative array of parameters, see default values (optional)
 * @return void
 */
function projectmanager_searchform( $project_id, $args = array() ) {
	global $pmShortcodes;
	$defaults = array( 'template' => 'extend' );
	$args = array_merge($defaults, $args);
	extract($args, EXTR_SKIP);
	echo $pmShortcodes->displaySearchForm( array('project_id' => $project_id, 'template' => $template) );
}


/**
 * display selections for categories and dataset ordering options
 *
 * @param int $project_id ProjectID
 * @return void
 */
function projectmanager_selections( $project_id ) {
	global $pmShortcodes;
	$pmShortcodes->displaySelections($project_id);
}


/**
 * display dataset form
 *
 * @param int $project_id
 */
function projectmanager_datasetform( $project_id ) {
	global $pmShortcodes;
	echo $pmShortcodes->displayDatasetForm( array('project_id' => $project_id) );	
}


/**
 * display project manually
 *
 * @param int $id Project ID
 * @param array $args assoziative array of parameters, see default values (optional)
 * @return void
 */
function project( $id, $args = array() ) {
	global $pmShortcodes;
	$defaults = array( 'template' => 'table', 'cat_id' => false, 'orderby' => false, 'order' => false, 'single' => true, 'selections' => true, 'results' => true, 'field_id' => false, 'field_value' => false );
	$args = array_merge($defaults, $args);
	extract($args, EXTR_SKIP);
	echo $pmShortcodes->displayProject( array('id' => $id, 'template' => $template, 'cat_id' => $cat_id, 'orderby' => $orderby, 'order' => $order, 'single' => $single, 'selections' => $selections, 'results' => $results, 'field_id' => $field_id, 'field_vaLue' => $field_value) );
}


/**
 * display dataset manually
 *
 * @param int $id Dataset ID
 * @param array $args assoziative array of parameters, see default values (optional)
 * @return void
 */
function dataset( $id, $args = array() ) {
	global $pmShortcodes;
	$defaults = array( 'template' => '' );
	$args = array_merge($defaults, $args);
	extract($args, EXTR_SKIP);
	$pmShortcodes->displayDataset( array('id' => $id, 'template' => $template, 'echo' => 1) );
}

?>
