<?php class CI_Bsa extends WP_Widget {

 function CI_Bsa(){
  $widget_ops = array('description' => 'BuySellAds.com Integration');
  $control_ops = array('width' => 400, 'height' => 200);
  parent::WP_Widget(false, $name='-= CI BuySellAds.com =-', $widget_ops, $control_ops);
 }

 function widget($args, $instance) {
 		extract($args);
 		$code = $instance['code'];
 		echo $before_widget;
 		echo '<div class="group">' . $code . '</div>'; 
 		echo $after_widget;
 } // widget

 function update($new_instance, $old_instance){
 		$instance = $old_instance;
 		$instance['code'] = stripslashes($new_instance['code']);
 		return $instance;
 	} // save
 
 function form($instance){
  	$instance = wp_parse_args( (array) $instance, array('code'=>'') );
 		$code = htmlspecialchars($instance['code']);
 		echo '<p><label>' . 'Code:' . '</label><textarea cols="30" rows="10" id="' . $this->get_field_id('code') . '" name="' . $this->get_field_name('code') . '" class="widefat" >'. $code .'</textarea></p>';
 } // form

} // class

function CI_BSAWidget() {
  register_widget('CI_Bsa');
}

add_action('widgets_init', 'CI_BSAWidget');
?>