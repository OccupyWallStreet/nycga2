<?php
/**
 * Theme Junkie Contact Widget
 */

class TJ_Contact extends WP_Widget {

	function TJ_Contact() {
		$widget_ops = array('description' => 'Add contact info as a widget.' );
		parent::WP_Widget(false, __('ThemeJunkie - Contact', 'themejunkie'),$widget_ops);
	}

	function widget($args, $instance) {
        $title = $instance['title'];
		$address = $instance['address'];
		$phone = $instance['phone'];
		$fax = $instance['fax'];
		$email = $instance['email'];
		$facebook_id = $instance['facebook_id'];
        $twitter_id = $instance['twitter_id'];
        $linkedin_id = $instance['linkedin_id'];
        $flickr_id = $instance['flickr_id'];
        $feedburner_id = $instance['feedburner_id'];

        echo '<div class="tj_widget_contact">';

        if($address != ''){
            echo'<h3 class="widget-title">'.$title.'</h3>
                 <ul class="contact-list">';
        }else{
            echo'<h3 class="widget-title">Get in Touch</h3>
                 <ul class="contact-list">';
        }

		if($address != '')
			echo '<li><p>Address:</p><span>'.$address.'</span></li>';

		if($phone != '')
            echo '<li><p>Phone:</p><span>'.$phone.'</span></li>';

        if($fax != '')
            echo '<li><p>Fax:</p><span>'.$fax.'</span></li>';

        if($email != '')
            echo '<li><p>Email:</p><span>'.$email.'</span></li>';

		echo '</ul>';

        echo '<ul class="social-list">';

        if($facebook_id != '')
			echo '<li class="twitter-icon"><a href="http://www.twitter.com/'.$facebook_id.'">Twitter</a></li>';

        if($twitter_id != '')
			echo '<li class="facebook-icon"><a href="http://www.facebook.com/'.$twitter_id.'">Facebook</a></li>';

        if($linkedin_id != '')
			echo '<li class="linkedin-icon"><a href="http://www.linkedin.com/'.$linkedin_id.'">Linkedin</a></li>';

        if($flickr_id != '')
			echo '<li class="flickr-icon"><a href="http://www.flickr.com/photos/'.$flickr_id.'">Flickr</a></li>';

        if($feedburner_id != '')
			echo '<li class="rss-icon"><a href="http://feeds.feedburner.com/'.$feedburner_id.'">RSS Feed</a></li>';

        echo '</ul>';

        echo '</div>';

	}

	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	function form($instance) {
        $title = esc_attr($instance['title']);
        $address = esc_attr($instance['address']);
		$phone = esc_attr($instance['phone']);
		$fax = esc_attr($instance['fax']);
		$email = esc_attr($instance['email']);
		$facebook_id = esc_attr($instance['facebook_id']);
        $twitter_id = esc_attr($instance['twitter_id']);
        $linkedin_id = esc_attr($instance['linkedin_id']);
        $flickr_id = esc_attr($instance['flickr_id']);
        $feedburner_id = esc_attr($instance['feedburner_id']);
		?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','themejunkie'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('address'); ?>"><?php _e('Address:','themejunkie'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('address'); ?>" value="<?php echo $address; ?>" class="widefat" id="<?php echo $this->get_field_id('address'); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('phone'); ?>"><?php _e('Phone:','themejunkie'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('phone'); ?>" value="<?php echo $phone; ?>" class="widefat" id="<?php echo $this->get_field_id('phone'); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('fax'); ?>"><?php _e('Fax:','themejunkie'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('fax'); ?>" value="<?php echo $fax; ?>" class="widefat" id="<?php echo $this->get_field_id('fax'); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('email'); ?>"><?php _e('Email:','themejunkie'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('email'); ?>" value="<?php echo $email; ?>" class="widefat" id="<?php echo $this->get_field_id('email'); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('facebook_id'); ?>"><?php _e('Facebook ID:','themejunkie'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('facebook_id'); ?>" value="<?php echo $facebook_id; ?>" class="widefat" id="<?php echo $this->get_field_id('facebook_id'); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('twitter_id'); ?>"><?php _e('Twitter ID:','themejunkie'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('twitter_id'); ?>" value="<?php echo $twitter_id; ?>" class="widefat" id="<?php echo $this->get_field_id('twitter_id'); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('linkedin_id'); ?>"><?php _e('Linkedin ID:','themejunkie'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('linkedin_id'); ?>" value="<?php echo $linkedin_id; ?>" class="widefat" id="<?php echo $this->get_field_id('linkedin_id'); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('flickr_id'); ?>"><?php _e('Flickr ID:','themejunkie'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('flickr_id'); ?>" value="<?php echo $flickr_id; ?>" class="widefat" id="<?php echo $this->get_field_id('flickr_id'); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('feedburner_id'); ?>"><?php _e('FeedBurner ID:','themejunkie'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('feedburner_id'); ?>" value="<?php echo $feedburner_id; ?>" class="widefat" id="<?php echo $this->get_field_id('feedburner_id'); ?>" />
        </p>
        <?php
	}
}

register_widget('TJ_Contact');
?>
