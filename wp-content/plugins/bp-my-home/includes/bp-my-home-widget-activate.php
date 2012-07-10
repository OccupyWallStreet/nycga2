<?php
/*script to activate widget*/
$widget_file = $_GET['widgetfile'];
$widget_function = $_GET['function'];
$widget_column = $_GET['column'];
$activated_widget[$widget_file] = array('function_to_call'=>$widget_function, 'default_column'=>$widget_column);
$allready_activated = get_option('_bpmh_activated_widgets');

//Updating available widget(s)
if($allready_activated==""){
	if(update_option('_bpmh_activated_widgets', $activated_widget)){
		?>
		<div class="updated fade"><p><?php _e('Widget activated !', 'bp-my-home');?></p></div>
		<?php
	}
	else{
		?>
		<div class="error fade"><p><?php _e('Oops, something went wrong !', 'bp-my-home');?></p></div>
		<?php
	}
}
else{
	$now_activated = array_merge($allready_activated, $activated_widget);
	if(update_option('_bpmh_activated_widgets', $now_activated)){
		?>
		<div class="updated fade"><p><?php _e('Widget activated !', 'bp-my-home');?></p></div>
		<?php
	}
	else{
		?>
		<div class="error fade"><p><?php _e('Oops, something went wrong !', 'bp-my-home');?></p></div>
		<?php
	}
}
?>
