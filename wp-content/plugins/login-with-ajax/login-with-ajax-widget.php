<?php
class LoginWithAjaxWidget extends WP_Widget {
    /** constructor */
    function LoginWithAjaxWidget() {
    	$widget_ops = array('description' => __( "Login widget with AJAX capabilities.", 'login-with-ajax') );
        parent::WP_Widget(false, $name = 'Login With Ajax', $widget_ops);	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
    	global $LoginWithAjax;
    	$LoginWithAjax->widget($args, $instance);  
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
        if($new_instance['template'] == ''){
        	unset($new_instance['template']);
        }
    	return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
    	global $LoginWithAjax;
        ?>
            <p>
            	<label for="<?php echo $this->get_field_id('profile_link'); ?>"><?php _e('Show profile link?', 'login-with-ajax'); ?> </label>
                <input id="<?php echo $this->get_field_id('profile_link'); ?>" name="<?php echo $this->get_field_name('profile_link'); ?>" type="checkbox" value="1" <?php echo ($instance['profile_link']) ? 'checked="checked"':""; ?> />
			</p>
            <p>
            	<label for="<?php echo $this->get_field_id('registration'); ?>"><?php _e('AJAX Registration?', 'login-with-ajax'); ?> </label>
                <input id="<?php echo $this->get_field_id('registration'); ?>" name="<?php echo $this->get_field_name('registration'); ?>" type="checkbox" value="1" <?php echo ($instance['registration']) ? 'checked="checked"':""; ?> />
			</p>
			<?php if( count($LoginWithAjax->templates) > 1 ): ?>
			<p>
            	<label for="<?php echo $this->get_field_id('template'); ?>"><?php _e('Template', 'login-with-ajax'); ?> </label>
            	<select id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>" >
            		<?php foreach( array_keys($LoginWithAjax->templates) as $template ): ?>
            		<option <?php echo ($instance['template'] == $template) ? 'selected="selected"':""; ?>><?php echo $template ?></option>
            		<?php endforeach; ?>
            	</select>
			</p>
			<?php endif; ?>
        <?php 
    }

}
?>