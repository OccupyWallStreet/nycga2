<?php

/**
* bp_my_home_setup_nav()
* Sets up the user profile navigation items
*/
function bp_my_home_setup_nav() {
	global $bp;
	
	// Add 'BP My Home' to the main user profile navigation
	bp_core_new_nav_item( array(
		'name' => __( 'My Home', 'bp-my-home' ),
		'slug' => BP_MYHOME_SLUG,
		'position' => 0,
		'show_for_displayed_user' => false,
		'screen_function' => 'bp_my_home_home',
		'default_subnav_slug' => 'my-widgets'
	) );

	$bpmh_link = $bp->loggedin_user->domain . BP_MYHOME_SLUG . '/';

	//Create two sub nav items for 'BP My Home'
	bp_core_new_subnav_item( array(
		'name' => __( 'My Widgets', 'bp-my-home' ),
		'slug' => 'my-widgets',
		'parent_slug' => BP_MYHOME_SLUG,
		'parent_url' => $bpmh_link,
		'screen_function' => 'bp_my_home_home',
		'position' => 10
	) );
	bp_core_new_subnav_item( array(
		'name' => __( 'My Settings', 'bp-my-home' ),
		'slug' => 'my-settings',
		'parent_slug' => BP_MYHOME_SLUG,
		'parent_url' => $bpmh_link,
		'screen_function' => 'bp_my_home_settings',
		'position' => 20,
		'user_has_access' => bp_is_my_profile() // double security :)
	) );
}
add_action( bp_my_home_is_still_bp_1_2() ? 'wp' : '', 'bp_my_home_setup_nav', 2 );
add_action( bp_my_home_is_still_bp_1_2() ? 'admin_menu' : '', 'bp_my_home_setup_nav', 2 );
add_action( bp_my_home_is_still_bp_1_2() ? 'network_admin_menu' : '', 'bp_my_home_setup_nav', 2 );


/**
* bp_my_home_load_template_filter
* loads template filter
*/
function bp_my_home_load_template_filter( $found_template, $templates ) {
	global $bp,$bp_deactivated;
	$querystring = "";

	//Only filter the template location when we're on the example component pages.
	if ( $bp->current_component != BP_MYHOME_SLUG )
		return $found_template;
	elseif($bp->loggedin_user->id!=$bp->displayed_user->id){
		if(strlen($_SERVER['QUERY_STRING'])>0){
			$querystring = '?'.$_SERVER['QUERY_STRING'];
		}
		if( isset($bp_deactivated['bp-activity.php']) ) bp_core_redirect( $bp->displayed_user->domain . 'profile' .'/'.$querystring);
		else bp_core_redirect( $bp->displayed_user->domain . BP_ACTIVITY_SLUG .'/'.$querystring);
	}

	foreach ( (array) $templates as $template ) {
		if ( file_exists( STYLESHEETPATH . '/' . $template ) )
			$filtered_templates[] = STYLESHEETPATH . '/' . $template;
		else
			$filtered_templates[] = dirname( __FILE__ ) . '/templates/' . $template;
	}

	$found_template = $filtered_templates[0];

	return apply_filters( 'bp_my_home_load_template_filter', $found_template );
}

add_filter( 'bp_located_template', 'bp_my_home_load_template_filter', 10, 2 );


/**
* bp_my_home_home
* where is the template ?
*/
function bp_my_home_home() {
	bp_core_load_template( apply_filters( 'bp_my_home_template_home', 'my-home-tpl/my-home' ) );
}


/**
* bp_my_home_settings
* where is the template ?
*/
function bp_my_home_settings() {
	bp_core_load_template( apply_filters( 'bp_my_home_template_home', 'my-home-tpl/my-settings' ) );
}


/**
* bp_my_home_multisort
* sort multidimensional array based on widget order
*/
function bp_my_home_multisort($array, $sort_by) {
    foreach ($array as $key => $value) {
        $evalstring = '';
        foreach ($sort_by as $sort_field) {
            $tmp[$sort_field][$key] = $value[$sort_field];
            $evalstring .= '$tmp[\'' . $sort_field . '\'], ';
        }
    }
    $evalstring .= '$array';
    $evalstring = 'array_multisort(' . $evalstring . ');';
    eval($evalstring);

    return $array;
}


/**
* bp_my_home_load_widgets
* Main function to load the activated widgets and apply user custom settings
*/
function bp_my_home_load_widgets(){
	global $bp;
	$user = wp_get_current_user();
	$user_state = get_user_meta($user->id, '_bpmh_user_saved_state',true);
	$user_widgets = get_user_meta($user->id, 'bpmh_user_activated_widgets',true);
	$active_widgets = "";
	
	$active_widgets_admin = get_option('_bpmh_activated_widgets');
	if($active_widgets_admin!=""){
		if($user_widgets=="") $active_widgets = $active_widgets_admin;
		//if user saved to none > no display
		elseif($user_widgets=="none") $active_widgets = "";
		//check if widget selected by user are still activated by Admin
		else $active_widgets = bp_my_home_compare_widgets($active_widgets_admin,$user_widgets);
	}
	
	$default_first_column = array();
	$default_second_column = array();
	$order_first_col = 0;
	$order_second_col = 0;
	
	if($active_widgets!="") {
		foreach($active_widgets as $widget_key=>$widget_values){
			$widget_parse = explode("/", $widget_key);
			$widget_name = $widget_parse[0];
			if($user_state==""){
				if($widget_values["default_column"]==1){
					$default_first_column[$widget_name] = array_merge($default_first_column, array('function_to_call'=>$widget_values["function_to_call"], 'order'=>$order_first_col, 'collapsed'=>0));
					$order_first_col +=1;
				}
				if($widget_values["default_column"]==2){
					$default_second_column[$widget_name] = array_merge($default_first_column, array('function_to_call'=>$widget_values["function_to_call"], 'order'=>$order_second_col, 'collapsed'=>0));
					$order_second_col+=1;
				}
			}
			elseif(!$user_state[$widget_name]){
				if($widget_values["default_column"]==1){
					$default_first_column[$widget_name] = array_merge($default_first_column, array('function_to_call'=>$widget_values["function_to_call"], 'order'=>$order_first_col, 'collapsed'=>0));
					$order_first_col +=1;
				}
				if($widget_values["default_column"]==2){
					$default_second_column[$widget_name] = array_merge($default_first_column, array('function_to_call'=>$widget_values["function_to_call"], 'order'=>$order_second_col, 'collapsed'=>0));
					$order_second_col+=1;
				}
			}
			else{
				if($widget_values["default_column"]==1 && $user_state[$widget_name]['col']=="column1"){
					$default_first_column[$widget_name] = array_merge($default_first_column, array('function_to_call'=>$widget_values["function_to_call"], 'order'=>$user_state[$widget_name]['order'], 'collapsed'=>$user_state[$widget_name]['collapsed']));
				}
				if($widget_values["default_column"]==1 && $user_state[$widget_name]['col']=="column2"){
					$default_second_column[$widget_name] = array_merge($default_first_column, array('function_to_call'=>$widget_values["function_to_call"], 'order'=>$user_state[$widget_name]['order'], 'collapsed'=>$user_state[$widget_name]['collapsed']));
				}
				if($widget_values["default_column"]==2 && $user_state[$widget_name]['col']=="column2"){
					$default_second_column[$widget_name] = array_merge($default_first_column, array('function_to_call'=>$widget_values["function_to_call"], 'order'=>$user_state[$widget_name]['order'], 'collapsed'=>$user_state[$widget_name]['collapsed']));
				}
				if($widget_values["default_column"]==2 && $user_state[$widget_name]['col']=="column1"){
					$default_first_column[$widget_name] = array_merge($default_first_column, array('function_to_call'=>$widget_values["function_to_call"], 'order'=>$user_state[$widget_name]['order'], 'collapsed'=>$user_state[$widget_name]['collapsed']));
				}
			}
		}
	}
	if(count($default_first_column)!=0) $default_first_column = bp_my_home_multisort($default_first_column, array('order'));
	if(count($default_second_column)!=0) $default_second_column = bp_my_home_multisort($default_second_column, array('order'));
	?>
	<?php if($active_widgets_admin=="" || bp_my_home_widget_dir_is_empty()==false):?>
		<div id="message" class="error fade">
			<p><?php _e('Admin forgot to activate at least 1 widget, if i were you i would contact him to make him move !','bp-my-home');?> </p>
		</div>
	<?php elseif($user_widgets=="none"):?>
		<div id="message" class="updated fade">
			<p><?php _e('You did choose to display no widget..','bp-my-home');?> 
			<a href="<?php echo $bp->loggedin_user->domain . BP_MYHOME_SLUG ;?>/my-settings/"><?php _e('What about adding some ?','bp-my-home');?></a></p>
		</div>
	<?php elseif($active_widgets==""):?>
		<div id="message" class="updated fade">
			<p><?php _e('The widgets you chose have been deactivated by Admin','bp-my-home');?> 
			<a href="<?php echo $bp->loggedin_user->domain . BP_MYHOME_SLUG ;?>/my-settings/"><?php _e('What about adding some ?','bp-my-home');?></a></p>
		</div>
	<?php endif;?>
	<div class="column" id="column1">
		<?php if(count($default_first_column)!=0):?>
			<?php
			foreach($default_first_column as $widget_ref=>$widget_data){
				$preficx = $widget_data['function_to_call'];
				if(file_exists(BP_MYHOME_WIDGETS_DIR.'/'.$widget_ref)){
					?>
					<div class="dragbox" id="item-<?php echo $widget_ref;?>" >
						<div class="handle_div"><h2>&nbsp;</h2></div>
						<h2>
							<?php if(function_exists($preficx.'_dragbox_config_link')):?>
							<span class="configure" >
								<?php call_user_func($preficx.'_dragbox_config_link');?>
							</span>
							<?php endif;?>
							<?php call_user_func($preficx.'_dragbox_title');?></h2>
						<div class="dragbox-content" <?php if($widget_data['collapsed']==1) echo 'style="display:none;"';?>>
							<?php call_user_func($preficx.'_widget_display');?> 
						</div>
					</div>
					<?php
				}
			}
		?>
		<?php endif;?>
	</div>
	<div class="column" id="column2" >
		<?php if(count($default_second_column)!=0):?>
			<?php
			foreach($default_second_column as $widget_ref=>$widget_data){
				$preficx = $widget_data['function_to_call'];
				if(file_exists(BP_MYHOME_WIDGETS_DIR.'/'.$widget_ref)){
					?>
					<div class="dragbox" id="item-<?php echo $widget_ref;?>" >
						<div class="handle_div"><h2>&nbsp;</h2></div>
						<h2>
							<?php if(function_exists($preficx.'_dragbox_config_link')):?>
							<span class="configure" >
								<?php call_user_func($preficx.'_dragbox_config_link');?>
							</span>
							<?php endif;?>
							<?php call_user_func($preficx.'_dragbox_title');?>
						</h2>
						<div class="dragbox-content" <?php if($widget_data['collapsed']==1) echo 'style="display:none;"';?>>
							<?php call_user_func($preficx.'_widget_display');?> 
						</div>
					</div>
					<?php
				}
			}
		?>
		<?php endif;?>
	</div>
	<hr style="clear:both;" />
	<?php
}


/********************************************************************************
* Ajax Functions
*
*/

/**
* bp_my_home_save_user_state
* Saves the widgets positionning
*/
function bp_my_home_save_user_state(){
	$user = wp_get_current_user();
	$array_parameters=array();
	$user_state=$_POST['state'];
	$widget_state = explode('[',$user_state);
	for($i=0;$i<count($widget_state)-1;$i++){
		$params = explode("]", $widget_state[$i]);
		foreach($params as $param){
			$key_val_params = explode("|", $param);
			if($key_val_params[0]=="id") $idw = substr($key_val_params[1], 5, strlen($key_val_params[1]));
			if($key_val_params[0]=="collapsed") $collapsed = $key_val_params[1];
			if($key_val_params[0]=="order") $order = $key_val_params[1];
			if($key_val_params[0]=="column") $column = $key_val_params[1];
		}
		$array_parameters[$idw]=array("col"=>$column, "order"=>$order, "collapsed"=>$collapsed);
	}
	if(update_user_meta( $user->id, '_bpmh_user_saved_state', $array_parameters )){
		echo "ok";
	}
	else _e('Oops, something went wrong !','bp-my-home');
	die();
}

add_action( 'wp_ajax_bp_my_home_save_state', 'bp_my_home_save_user_state' );


/**
* bp_my_home_load_settings
* user custom settings area
*/
function bp_my_home_load_settings(){
	$user = wp_get_current_user();
	$active_widgets = get_option('_bpmh_activated_widgets');
	if(isset($_POST['_bpmh_user_saved'])){
		$user_widgets_ok = get_user_meta($user->id, 'bpmh_user_activated_widgets',true);
		$user_widgets_activated = array();
		$mess_home_page = "ko";
		$mess_widgets = "ko";
		$message_id="koko";
		$message = array("okok"=>array("class"=>"updated","message"=>__('Custom settings saved.','bp-my-home')),
						 "okko"=>array("class"=>"error","message"=>__('Homepage settings saved but.. widgets settings failed.','bp-my-home')),
						 "kook"=>array("class"=>"error","message"=>__('Widgets settings saved but.. Homepage settings failed.','bp-my-home')),
						 "koko"=>array("class"=>"error","message"=>__('Homepage and Widgets settings failed.','bp-my-home'))
						);
		
		//1 handling user choice for homepage settings
		if(get_user_meta($user->id, 'bpmh_user_home_page',true)==$_POST['_user_home_page']){
			//if allready set, it's ok
			$mess_home_page = "ok";
		}
		else{
			if(update_user_meta( $user->id, 'bpmh_user_home_page', $_POST['_user_home_page'] )){
				$mess_home_page = "ok";
			}
			else $mess_home_page = "ko";
		}
		
		//2 handling no widget chosen !
		if(!isset($_POST['_widget_user_choice'])){
			
			if(update_user_meta( $user->id, 'bpmh_user_activated_widgets', "none" )){
				//no more widgets !
				$mess_widgets = "ok";
			}
			else{
				//allready set
				if($user_widgets_ok =="none") $mess_widgets = "ok";
				else $mess_widgets = "ko";
			}
		}
		else{
			foreach($active_widgets as $widget_ref=>$widget_data){
				if($_POST['_widget_user_choice'][$widget_ref]){
					$user_widgets_activated[$widget_ref] = array("function_to_call"=>$widget_data['function_to_call'],"default_column"=>$widget_data['default_column']);
				}
			}
			if(update_user_meta( $user->id, 'bpmh_user_activated_widgets', $user_widgets_activated )){
					//settings saved !
					$mess_widgets = "ok";
			}
			else{
				if($user_widgets_ok == $user_widgets_activated) $mess_widgets = "ok";
				else $mess_widgets = "ko";
			}
		}
		$message_id= $mess_home_page . $mess_widgets;
		//displaying result
		?>
		<div id="message" class="<?php echo $message[$message_id]["class"];?> fade"><p><?php echo $message[$message_id]["message"];?></p></div>
		<?php
	}
	$home_page = get_user_meta($user->id, 'bpmh_user_home_page',true);
	$user_widgets_ok = get_user_meta($user->id, 'bpmh_user_activated_widgets',true);
	
	$widget_status = array();
	if($user_widgets_ok=="" && $active_widgets!=""){
		foreach($active_widgets as $widget_ref=>$widget_data){
			$widget_status[] = array("widget_id"=>$widget_ref, "widget_ok"=>1, "function_to_call"=>$widget_data['function_to_call']);
		}
	}
	elseif($user_widgets_ok!="none" && $active_widgets!=""){
		foreach($active_widgets as $widget_ref=>$widget_data){
			$widgok = 0;
			if($user_widgets_ok[$widget_ref]) $widgok = 1;
			$widget_status[] = array("widget_id"=>$widget_ref, "widget_ok"=>$widgok, "function_to_call"=>$widget_data['function_to_call']);
		}
	}
	elseif($active_widgets!=""){
		foreach($active_widgets as $widget_ref=>$widget_data){
			$widget_status[] = array("widget_id"=>$widget_ref, "widget_ok"=>0, "function_to_call"=>$widget_data['function_to_call']);
		}
	}
	?>
	<div>
		<?php if($active_widgets!="" && bp_my_home_widget_dir_is_empty()!=false):?>
		<form action="" method="POST" class="standard-form">
		<table>
			<tr>
				<td>
					<input type="checkbox" name="_user_home_page" id="user_home_page" value="yes" <?php if($home_page=="yes") echo "CHECKED";?>/>&nbsp;
					<?php _e("Make 'My Home' as my homepage for this website", "bp-my-home");?>
				</td>
			</tr>
			<tr>
				<td>
					<h4><?php _e("'My Widgets' settings", "bp-my-home");?></h4>
					<?php if($active_widgets!=""):?>
					<?php
					foreach($widget_status as $widget){
						$check_widget_exists = explode("/", $widget["widget_id"]);
						if(file_exists(BP_MYHOME_WIDGETS_DIR.'/'.$check_widget_exists[0])){
							$widget_name = $widget["widget_id"];
							$preficx = $widget['function_to_call'];
							?>
							<div class="widget_settings" id="widget-<?php echo $widget_name;?>">
								<table>
									<tr>
										<td><input type="checkbox" name="_widget_user_choice[<?php echo $widget_name;?>][]" <?php if($widget['widget_ok']==1) echo "CHECKED";?>/>&nbsp;<b><?php if(function_exists($preficx.'_dragbox_title')) call_user_func($preficx.'_dragbox_title');?></b></td>
									</tr>
									<tr>
										<td class="bpmh-thumb">
											<?php 
											if(function_exists($preficx.'_user_settings')){
												call_user_func($preficx.'_user_settings');
											}
											?>
										</td>
									</tr>
								</table>
							</div>
							<?php
						}
						}
					?>
					<hr style="clear:both;" />
					<?php endif;?>
				</td>
			</tr>
			<tr><td><input type="submit" value="<?php _e('Save these settings','bp-my-home');?>" name="_bpmh_user_saved"/></td></tr>
		</table>
		</form>
		<?php else:?>
			<div id="message" class="error fade">
				<p><?php _e('Admin forgot to activate at least 1 widget, if i were you i would contact him to make him move !','bp-my-home');?> </p>
			</div>
		<?php endif;?>
	</div>
	<?php
}

/**
* bp_my_home_compare_widgets
* if user chose a widget that Admin deactivated, no more display of this widget
*/
function bp_my_home_compare_widgets($admin_activated, $user_activated){
	$in_admin_activated = "";
	foreach($user_activated as $user_wkey=>$user_wvalue){
		if($admin_activated[$user_wkey]){
			$in_admin_activated[$user_wkey] = array("function_to_call"=>$user_wvalue['function_to_call'],"default_column"=>$user_wvalue['default_column']);
		}
	}
	return $in_admin_activated;
}

?>