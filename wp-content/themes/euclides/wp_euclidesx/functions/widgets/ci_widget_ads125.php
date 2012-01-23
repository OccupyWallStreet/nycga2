<?php class CI_Ads125 extends WP_Widget {

	function CI_Ads125(){
		$widget_ops = array('description' => 'Display 125x125 Banners');
		$control_ops = array('width' => 300, 'height' => 400);
		parent::WP_Widget(false, $name='-= CI 125x125 Ads =-', $widget_ops, $control_ops);
	}


	function widget($args, $instance) {
 		extract($args);
 		$ci_title = $instance['ci_title'];
 		$ci_random = $instance['ci_random'];

		$b = array();
		for($i=1; $i<=8; $i++)
		{
	 		$b[$i]['url'] = $instance['ci_b'.$i.'url'];
	 		$b[$i]['lin'] = $instance['ci_b'.$i.'lin'];
	 		$b[$i]['tit'] = $instance['ci_b'.$i.'tit'];
		}
 		
 		echo $before_widget;
 	
 		if ($ci_title) 
 			echo $before_title . $ci_title . $after_title;

 		echo '<ul id="ads125" class="group">';

		if($ci_random=="random")
			shuffle($b);

		$i=1;
		foreach($b as $key=>$value)
		{
			if (!empty($value['url']))
			{
				if ($i % 2==0)
					echo '<li class="last"><a href="'. $value['lin'] .'"><img src="' . $value['url'] . '" alt="' . $value['tit'] . '" /></a></li>';
				else			
					echo '<li><a href="'. $value['lin'] .'"><img src="' . $value['url'] . '" alt="' . $value['tit'] . '" /></a></li>';
			$i++;
			}
		}

 		echo "</ul>";
 	
 		echo $after_widget;
	} // widget

 	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['ci_title'] = stripslashes($new_instance['ci_title']);
		$instance['ci_random'] = $new_instance['ci_random'];
		
		for($i=1; $i<=8; $i++)
		{
			$instance['ci_b'.$i.'url'] = stripslashes($new_instance['ci_b'.$i.'url']);
			$instance['ci_b'.$i.'lin'] = stripslashes($new_instance['ci_b'.$i.'lin']);
			$instance['ci_b'.$i.'tit'] = stripslashes($new_instance['ci_b'.$i.'tit']);
		}
		
		return $instance;
 	} // save
 
	function form($instance){
		$instance = wp_parse_args( (array) $instance, array('ci_title'=>'', 'ci_random' => '', 'ci_b1url'=>'', 'ci_b1lin'=>'', 'ci_b1tit'=>'', 'ci_b2url'=>'', 'ci_b2lin'=>'', 'ci_b2tit'=>'' , 'ci_b3url'=>'', 'ci_b3lin'=>'', 'ci_b3tit'=>'' , 'ci_b4url'=>'', 'ci_b4lin'=>'', 'ci_b4tit'=>'' , 'ci_b5url'=>'', 'ci_b5lin'=>'', 'ci_b5tit'=>'' , 'ci_b6url'=>'', 'ci_b6lin'=>'', 'ci_b6tit'=>'' , 'ci_b7url'=>'', 'ci_b7lin'=>'', 'ci_b7tit'=>'' , 'ci_b8url'=>'', 'ci_b8lin'=>'', 'ci_b8tit'=>''));
 		
 		$ci_title = htmlspecialchars($instance['ci_title']);
 		$ci_random = $instance['ci_random'];
 
 		$b = array();
		for($i=1; $i<=8; $i++)
		{
			$b[$i]['url'] = htmlspecialchars($instance['ci_b'.$i.'url']);
			$b[$i]['lin'] = htmlspecialchars($instance['ci_b'.$i.'lin']);
			$b[$i]['tit'] = htmlspecialchars($instance['ci_b'.$i.'tit']);
		}
		
 		
 		echo '<p><label>' . 'Title'	. '</label><input id="' . $this->get_field_id('ci_title') . '" name="' . $this->get_field_name('ci_title') . '" type="text" value="' . $ci_title . '" class="widefat" /></p>';
 		echo '<p><input id="' . $this->get_field_id('ci_random') . '" name="' . $this->get_field_name('ci_random') . '" type="checkbox"'. checked($instance['ci_random'], "random") .' value="random"  /> <label><strong>' . 'Display ads in random order?'	. '</strong></label></p>';

		for($i=1; $i<=8; $i++)
		{
			$b[$i]['url'] = htmlspecialchars($instance['ci_b'.$i.'url']);
			$b[$i]['lin'] = htmlspecialchars($instance['ci_b'.$i.'lin']);
			$b[$i]['tit'] = htmlspecialchars($instance['ci_b'.$i.'tit']);
	 		echo '<p><label>' . 'Banner #'.$i.' URL'	. '</label><input id="' . $this->get_field_id('ci_b'.$i.'url') . '" name="' . $this->get_field_name('ci_b'.$i.'url') . '" type="text" value="' . $b[$i]['url'] . '" class="widefat" /></p>';
	 		echo '<p><label>' . 'Banner #'.$i.' Link' 	. '</label><input id="' . $this->get_field_id('ci_b'.$i.'lin') . '" name="' . $this->get_field_name('ci_b'.$i.'lin') . '" type="text" value="' . $b[$i]['lin'] . '" class="widefat" /></p>';
	 		echo '<p><label>' . 'Banner #'.$i.' Title' 	. '</label><input id="' . $this->get_field_id('ci_b'.$i.'tit') . '" name="' . $this->get_field_name('ci_b'.$i.'tit') . '" type="text" value="' . $b[$i]['tit'] . '" class="widefat" /></p>';
		}


	} // form

} // class

	function CI_Ads125Widget() {
		register_widget('CI_Ads125');
	}

	add_action('widgets_init', 'CI_Ads125Widget');
?>