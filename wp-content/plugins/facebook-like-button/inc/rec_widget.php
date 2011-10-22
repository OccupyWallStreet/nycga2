<?php

/*Recommendations Widget Code*/

include_once(ABSPATH . "wp-content/plugins/facebook-like-button/inc/rec_wid_layout.php");

class Rec_Widget extends Widget_Layout
{

		
	public function Widget_Struc($args)
	{
		//Widget Title
		$wid_title = (get_option('fpp_rec_wid_title') == '') ? 'Facebook Recommendations' : get_option('fpp_rec_wid_title');
		echo $args['before_widget'];
        echo $args['before_title'] . $wid_title . $args['after_title'];
        echo parent::Layout();
        echo $args['after_widget'];
				
		}
		
	public function Register()
	{
		$wid_title = (get_option('fpp_rec_wid_title') == '') ? 'Facebook Recommendations' : get_option('fpp_rec_wid_title');
		wp_register_sidebar_widget( 1, $wid_title, array('Rec_Widget', 'Widget_Struc'),  array('Rec_Widget', 'Controls'));
	
		}
	
	}
	
add_action("plugins_loaded", array('Rec_Widget', 'Register'));

?>