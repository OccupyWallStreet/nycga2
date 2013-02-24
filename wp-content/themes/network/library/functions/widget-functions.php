<?php

/* This widget outputs a simple text title and block of text in the footer sidebar */

class FooterText_Widget extends WP_Widget {
	function FooterText_Widget() {
		parent::WP_Widget(false, 'Network Footer Text');
	}

function form($instance) {
		// outputs the options form on admin
		$text = esc_attr($instance['text']);
		$title = esc_attr($instance['title']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

		<p><label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text:'); ?> <textarea class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" ><?php echo $text; ?></textarea></label></p>
		
<?php
	}
	
function update($new_instance, $old_instance) {
		// processes widget options to be saved
		return $new_instance;
	}
function widget($args, $instance) {
		// outputs the content of the widget
		$args['title'] = $instance['title'];
		$args['text'] = $instance['text'];
        db_footerText($args);
	}
}
register_widget('FooterText_Widget');

function db_footerText($args = array()) {
	global $wpdb;
	echo '<div class="footer-column">
		<h4>'.$args['title'].'</h4>
		<p>'.$args['text'].'</p>
	</div>';
}


/* This widget outputs a list of links (up to 10) and title in the footer sidebar */


class FooterLinks_Widget extends WP_Widget {
	function FooterLinks_Widget() {
		parent::WP_Widget(false, 'Network Footer Links');
	}
	
function form($instance) {
		// outputs the options form on admin
		$bigtitle = esc_attr($instance['bigtitle']);
		$title[1] = esc_attr($instance['title1']);
		$link[1] = esc_attr($instance['link1']);
		$title[2] = esc_attr($instance['title2']);
		$link[2] = esc_attr($instance['link2']);
		$title[3] = esc_attr($instance['title3']);
		$link[3] = esc_attr($instance['link3']);
		$title[4] = esc_attr($instance['title4']);
		$link[4] = esc_attr($instance['link4']);
		$title[5] = esc_attr($instance['title5']);
		$link[5] = esc_attr($instance['link5']);
		$title[6] = esc_attr($instance['title6']);
		$link[6] = esc_attr($instance['link6']);
		$title[7] = esc_attr($instance['title7']);
		$link[7] = esc_attr($instance['link7']);
		$title[8] = esc_attr($instance['title8']);
		$link[8] = esc_attr($instance['link8']);
		$title[9] = esc_attr($instance['title9']);
		$link[9] = esc_attr($instance['link9']);
		$title[10] = esc_attr($instance['title10']);
		$link[10] = esc_attr($instance['link10']);

?>

		<p><label for="<?php echo $this->get_field_id('bigtitle'); ?>"><?php _e('Title:', 'network'); ?> <input class="widefat" id="<?php echo $this->get_field_id('bigtitle'); ?>" name="<?php echo $this->get_field_name('bigtitle'); ?>" type="text" value="<?php echo $bigtitle; ?>" /></label></p>
		
		<?php for ($x = 1; $x <= 10; $x++) { ?>
		
		<p><label for="<?php echo $this->get_field_id('title'.$x); ?>"><?php _e('Link #'.$x.' Name:', 'network'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'.$x); ?>" name="<?php echo $this->get_field_name('title'.$x); ?>" type="text" value="<?php echo $title[$x]; ?>" /></label></p>

		<p><label for="<?php echo $this->get_field_id('link'.$x); ?>"><?php _e('Link #'.$x.' URL:', 'network'); ?> <input class="widefat" id="<?php echo $this->get_field_id('link'.$x); ?>" name="<?php echo $this->get_field_name('link'.$x); ?>" type="text" value="<?php echo $link[$x]; ?>" /></label></p>

		
		<?php } ?>

		
<?php
	}
	
function update($new_instance, $old_instance) {
		// processes widget options to be saved
		return $new_instance;
	}
function widget($args, $instance) {
		// outputs the content of the widget
	
		$args['bigtitle'] = $instance['bigtitle'];		
		$args['title1'] = $instance['title1'];
		$args['link1'] = $instance['link1'];
		$args['title2'] = $instance['title2'];
		$args['link2'] = $instance['link2'];
		$args['title3'] = $instance['title3'];
		$args['link3'] = $instance['link3'];
		$args['title4'] = $instance['title4'];
		$args['link4'] = $instance['link4'];
		$args['title5'] = $instance['title5'];
		$args['link5'] = $instance['link5'];
		$args['title6'] = $instance['title6'];
		$args['link6'] = $instance['link6'];
		$args['title7'] = $instance['title7'];
		$args['link7'] = $instance['link7'];
		$args['title8'] = $instance['title8'];
		$args['link8'] = $instance['link8'];
		$args['title9'] = $instance['title9'];
		$args['link9'] = $instance['link9'];
		$args['title10'] = $instance['title10'];
		$args['link10'] = $instance['link10'];

        db_footerLinks($args);
	}
}
register_widget('FooterLinks_Widget');

function db_footerLinks($args = array()) {
	global $wpdb;
	echo '<div class="footer-column">
		<h4>'.$args['bigtitle'].'</h4>
		<ul>';
	$counter = 1;
	for ($x = 1; $x <= 10; $x++) { 	
		if ($args['title'.$x] != '' && $args['link'.$x] != '') {
			echo '<li><a title="'.$args['title'.$x].'" href="http://'.str_replace('http://','',$args['link'.$x]).'">'.$args['title'.$x].'</a></li>';
		}
	}
	echo '</ul>
	</div>';
}


/* This widget outputs a block of text in the right sidebar */

class SidebarText_Widget extends WP_Widget {
	function SidebarText_Widget() {
		parent::WP_Widget(false, 'Network Siderbar Text');
	}
	
function form($instance) {
		// outputs the options form on admin
		$text = esc_attr($instance['text']);
		$title = esc_attr($instance['title']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'network'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

		<p><label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text:', 'network'); ?> <textarea class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" ><?php echo $text; ?></textarea></label></p>
		
<?php
	}
	
function update($new_instance, $old_instance) {
		// processes widget options to be saved
		return $new_instance;
	}
function widget($args, $instance) {
		// outputs the content of the widget
		$args['title'] = $instance['title'];
		$args['text'] = $instance['text'];
        db_footerText($args);
	}
}
register_widget('SidebarText_Widget');

function db_sidebarText($args = array()) {
	global $wpdb;
	echo '<div class="sidebar-text-interior">
		<h4>'.$args['title'].'</h4>
		<p>'.$args['text'].'</p>
	</div>';
}
?>