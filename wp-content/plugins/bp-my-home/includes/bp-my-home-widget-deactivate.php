<?php
/*script to deactivate widget*/
$widget_file = $_GET['widgetfile'];
$allready_activated = get_option('_bpmh_activated_widgets');
$remain_activated = array();

//building a new array
foreach($allready_activated as $widget_key=>$widget_values){
	if($widget_key!=$widget_file){
		$remain_activated[$widget_key]=array('function_to_call'=>$widget_values["function_to_call"], 'default_column'=>$widget_values["default_column"]);
	}
}

//Updating the available widget(s)
if(count($remain_activated)==0){
	if(update_option('_bpmh_activated_widgets', "")){
		?>
		<div class="updated fade"><p><?php _e('Widget deactivated !', 'bp-my-home');?></p></div>
		<?php
	}
	else{
		?>
		<div class="error fade"><p><?php _e('Oops, something went wrong !', 'bp-my-home');?></p></div>
		<?php
	}
}
else{
	if(update_option('_bpmh_activated_widgets', $remain_activated)){
		?>
		<div class="updated fade"><p><?php _e('Widget deactivated !', 'bp-my-home');?></p></div>
		<?php
	}
	else{
		?>
		<div class="error fade"><p><?php _e('Oops, something went wrong !', 'bp-my-home');?></p></div>
		<?php
	}
}
?>