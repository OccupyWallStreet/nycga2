<?php
/*
Plugin Name: T(-) Countdown
Plugin URI: http://plugins.twinpictures.de/plugins/t-minus-countdown/
Description: Display and configure multiple T(-) Countdown timers using a shortcode or sidebar widget.
Version: 2.2.3
Author: twinpictures, baden03
Author URI: http://www.twinpictures.de/
License: GPL2
*/

/*  Copyright 2012 Twinpictures (www.twinpictures.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//widget scripts
function countdown_scripts(){
		$current_version  = get_option('t-minus_version');
		if(!$current_version){
			//delete the old style system
			delete_option( 't-minus_styles' );
			//add version check
			add_option('t-minus_version', '2.2.2');
		}
		
		$styles_arr = array("TIE-fighter","c-3po","c-3po-mini","carbonite","carbonlite","darth","jedi");
		add_option('t-minus_styles', $styles_arr);
		$plugin_url = plugins_url() .'/'. dirname( plugin_basename(__FILE__) );
		wp_enqueue_script('jquery');
        if (is_admin()){
                //jquery admin stuff
                wp_register_script('tminus-admin-script', $plugin_url.'/js/jquery.collapse.js', array ('jquery'), '1.0' );
                wp_enqueue_script('tminus-admin-script');
				
				wp_register_script('livequery-script', $plugin_url.'/js/jquery.livequery.min.js', array ('jquery'), '1.0' );
                wp_enqueue_script('livequery-script');
				
				wp_register_style('colapse-admin-css', $plugin_url.'/admin/collapse-style.css', array (), '1.0' );    
                wp_enqueue_style('colapse-admin-css');
        }
		else{
				//lwtCountdown script
                wp_register_script('countdown-script', $plugin_url.'/js/jquery.t-countdown-1.0.js', array ('jquery'), '1.0' );
                wp_enqueue_script('countdown-script');
				
				//register all countdown styles for enqueue-as-needed
				$styles_arr = get_option('t-minus_styles');
				foreach($styles_arr as $style_name){
					wp_register_style( 'countdown-'.$style_name.'-css', $plugin_url.'/css/'.$style_name.'/style.css', array(), '1.2' );
				}
		}
}
add_action( 'init', 'countdown_scripts' );

//style folders array
function folder_array($path, $exclude = ".|..") {
	if(is_dir($path)){
		$dh = opendir($path);
		$exclude_array = explode("|", $exclude);
		$result = array();
		while(false !== ( $file = readdir($dh) ) ) { 
			if( !in_array( strtolower( $file ), $exclude_array) ){
				$result[] = $file;
			}
		}
		closedir($dh);
		return $result;
	}
}

add_option('rockstar', '');

/**
 * CountDownTimer Class
 */
class CountDownTimer extends WP_Widget {
    /** constructor */
    function CountDownTimer() {
        //parent::WP_Widget(false, $name = 'CountDownTimer');
		$widget_ops = array('classname' => 'CountDownTimer', 'description' => __('A highly customizable jQuery countdown timer by Twinpictures') );
		$this->WP_Widget('CountDownTimer', 'T(-) Countdown', $widget_ops);
    }
	
    /** Widget */
    function widget($args, $instance) {
		global $add_my_script;
        extract( $args );
		//insert some style into your life
		$style = empty($instance['style']) ? 'jedi' : apply_filters('widget_style', $instance['style']);
		wp_enqueue_style( 'countdown-'.$style.'-css' );
		
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		$tophtml = empty($instance['tophtml']) ? ' ' : apply_filters('widget_tophtml', $instance['tophtml']);
        $bothtml = empty($instance['bothtml']) ? ' ' : apply_filters('widget_bothtml', $instance['bothtml']);
        $launchhtml = empty($instance['launchhtml']) ? ' ' : apply_filters('widget_launchhtml', $instance['launchhtml']);
        $launchtarget = empty($instance['launchtarget']) ? 'After Countdown' : apply_filters('widget_launchtarget', $instance['launchtarget']);
		
		$day = empty($instance['day']) ? 20 : apply_filters('widget_day', $instance['day']);
		$month = empty($instance['month']) ? 12 : apply_filters('widget_month', $instance['month']);
		$year = empty($instance['year']) ? 2012 : apply_filters('widget_year', $instance['year']);
		$hour = empty($instance['hour']) ? 20 : apply_filters('widget_hour', $instance['hour']);
		$min = empty($instance['min']) ? 12 : apply_filters('widget_min', $instance['min']);
		$sec = empty($instance['sec']) ? 20 : apply_filters('widget_sec', $instance['sec']);
		
		$weektitle = empty($instance['weektitle']) ? 'weeks' : apply_filters('widget_weektitle', $instance['weektitle']);
		$daytitle = empty($instance['daytitle']) ? 'days' : apply_filters('widget_daytitle', $instance['daytitle']);
		$hourtitle = empty($instance['hourtitle']) ? 'hours' : apply_filters('widget_hourtitle', $instance['hourtitle']);
		$mintitle = empty($instance['mintitle']) ? 'minutes' : apply_filters('widget_mintitle', $instance['mintitle']);
		$sectitle = empty($instance['sectitle']) ? 'seconds' : apply_filters('widget_sectitle', $instance['sectitle']);
		$omitweeks = empty($instance['omitweeks']) ? 'false' : apply_filters('widget_omitweeks', $instance['omitweeks']);
		$jsplacement = empty($instance['jsplacement']) ? 'footer' : apply_filters('widget_jsplacement', $instance['jsplacement']);
		
		//now
		$now = time() + ( get_option( 'gmt_offset' ) * 3600);
		
		//target
		$target = mktime(
			$hour, 
			$min, 
			$sec, 
			$month, 
			$day, 
			$year
		);
		
		//difference in seconds
		$diffSecs = $target - $now;
		
		//countdown digits
		$date = array();
		$date['secs'] = $diffSecs % 60;
		$date['mins'] = floor($diffSecs/60)%60;
		$date['hours'] = floor($diffSecs/60/60)%24;
		if($omitweeks == 'false'){
		    $date['days'] = floor($diffSecs/60/60/24)%7;
		}
		else{
		    $date['days'] = floor($diffSecs/60/60/24); 
		}
		$date['weeks']	= floor($diffSecs/60/60/24/7);
	
		foreach ($date as $i => $d) {
			$d1 = $d%10;
			//53 = 3
			//153 = 3
	
			if($d < 100){
				$d2 = ($d-$d1) / 10;
				//53 = 50 / 10 = 5
				$d3 = 0;
			}
			else{
				$dr = $d%100;
				//153 = 53
				//345 = 45
				$dm = $d-$dr;
				//153 = 100
				//345 = 300
				$d2 = ($d-$dm-$d1) / 10;
				//153 = 50 / 10 = 5
				//345 = 40 / 10 = 4
				$d3 = $dm / 100;
			}
			/* here is where the 1000's support will go... someday. */
			
			//now assign all the digits to the array
			$date[$i] = array(
				(int)$d3,
				(int)$d2,
				(int)$d1,
				(int)$d
			);
		}
		
		
        echo $before_widget;
        if ( $title ){
            echo $before_title . $title . $after_title;
        }
		echo '<div id="'.$args['widget_id'].'-widget">';
		echo '<div id="'.$args['widget_id'].'-tophtml" class="'.$style.'-tophtml" >';
        if($tophtml){
            echo stripslashes($tophtml); 
        }
		echo '</div>';
		
		//drop in the dashboard
		echo '<div id="'.$args['widget_id'].'-dashboard" class="'.$style.'-dashboard">';
		
			if($omitweeks == 'false'){
				//set up correct style class for double or triple digit love
				$wclass = $style.'-dash '.$style.'-weeks_dash';
				if($date['weeks'][0] > 0){
					$wclass = $style.'-tripdash '.$style.'-weeks_trip_dash';
				}
			
				echo '<div class="'.$wclass.'">
						<span class="'.$style.'-dash_title">'.$weektitle.'</span>';
						//show third week digit if the number of weeks is greater than 99
				if($date['weeks'][0] > 0){
					echo '<div class="'.$style.'-digit">'.$date['weeks'][0].'</div>';
				}
				echo '<div class="'.$style.'-digit">'.$date['weeks'][1].'</div>
						<div class="'.$style.'-digit">'.$date['weeks'][2].'</div>
					</div>'; 
			}
					
			//set up correct style class for double or triple digit love
			$dclass = $style.'-dash '.$style.'-days_dash';
			if($omitweeks == 'true' && $date['days'][3] > 99){
				$dclass = $style.'-tripdash '.$style.'-days_trip_dash';
			}
			
			echo '<div class="'.$dclass.'">
					<span class="'.$style.'-dash_title">'.$daytitle.'</span>';
			//show third day digit if there are NO weeks and the number of days is greater that 99
			if($omitweeks == 'true' && $date['days'][3] > 99){
				echo '<div class="'.$style.'-digit">'.$date['days'][0].'</div>';
			}
			echo '<div class="'.$style.'-digit">'.$date['days'][1].'</div>
				<div class="'.$style.'-digit">'.$date['days'][2].'</div>
			</div>
	
			<div class="'.$style.'-dash '.$style.'-hours_dash">
				<span class="'.$style.'-dash_title">'.$hourtitle.'</span>
				<div class="'.$style.'-digit">'.$date['hours'][1].'</div>
				<div class="'.$style.'-digit">'.$date['hours'][2].'</div>
			</div>
	
			<div class="'.$style.'-dash '.$style.'-minutes_dash">
				<span class="'.$style.'-dash_title">'.$mintitle.'</span>
				<div class="'.$style.'-digit">'.$date['mins'][1].'</div>
				<div class="'.$style.'-digit">'.$date['mins'][2].'</div>
			</div>
	
			<div class="'.$style.'-dash '.$style.'-seconds_dash">
				<span class="'.$style.'-dash_title">'.$sectitle.'</span>
				<div class="'.$style.'-digit">'.$date['secs'][1].'</div>
				<div class="'.$style.'-digit">'.$date['secs'][2].'</div>
			</div>
        </div>'; //close the dashboard
		
        echo '<div id="'.$args['widget_id'].'-bothtml" class="'.$style.'-bothtml">';
        if($bothtml){
            echo  stripslashes($bothtml);    
        }
		echo '</div>';
		echo '</div>';
		echo $after_widget;
		$t = date( 'n/j/Y H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600));
		
		//launch div
		$launchdiv = "";
		if($launchtarget == "Above Countdown"){
			$launchdiv = "tophtml";
		}
		else if($launchtarget == "Below Countdown"){
			$launchdiv = "bothtml";
		}
		else if($launchtarget == "Entire Widget"){
			$launchdiv = "widget";
		}

		if($jsplacement == "footer"){
			$add_my_script[$args['widget_id']] = array(
				'id' => $args['widget_id'],
				'day' => $day,
				'month' => $month,
				'year' => $year,
				'hour' => $hour,
				'min' => $min,
				'sec' => $sec,
				'localtime' => $t,
				'style' => $style,
				'omitweeks' => $omitweeks,
				'content' => trim($launchhtml),
				'launchtarget' => $launchdiv,
				'launchwidth' => 'auto',
				'launchheight' => 'auto'
			);
		}
		else{
			?>            
			<script language="javascript" type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('#<?php echo $args['widget_id']; ?>-dashboard').countDown({	
						targetDate: {
							'day': 	<?php echo $day; ?>,
							'month': 	<?php echo $month; ?>,
							'year': 	<?php echo $year; ?>,
							'hour': 	<?php echo $hour; ?>,
							'min': 	<?php echo $min; ?>,
							'sec': 	<?php echo $sec; ?>,
							'localtime':	'<?php echo $t; ?>'
						},
						style: '<?php echo $style; ?>',
						omitWeeks: <?php echo $omitweeks;
										if($launchhtml){
											echo ", onComplete: function() { jQuery('#".$args['widget_id']."-".$launchdiv."').html('".do_shortcode($launchhtml)."'); }";
										}
									?>
					});
				});
			</script>
			<?php
		}
    }

    /** Update */
    function update($new_instance, $old_instance) {
		$instance = array_merge($old_instance, $new_instance);
		//return array_map('strip_tags', $instance);
		if(isset($instance['isrockstar']) && $instance['isrockstar']){
			update_option('rockstar', $instance['isrockstar']);
		}
		
		//update the styles
		//$style_arr = get_option('t-minus_styles');
		//$style_arr[$instance['style']] = $instance['style'];
		//update_option('t-minus_styles', $style_arr);
		
		return array_map('mysql_real_escape_string', $instance);
    }

    /** Form */
    function form($instance) {
        $title = stripslashes($instance['title']);
		$day = esc_attr($instance['day']);
		if(!$day){
			$day = 20;
		}
		else if($day > 31){
			$day = 31;
		}
		//apply_filters('widget_day', $day);
		
		$month = esc_attr($instance['month']);
		if(!$month){
			$month = 12;
		}
		else if($month > 12){
			$month = 12;
		}
		
		$year = esc_attr($instance['year']);
		if(!$year){
			$year = 2012;
		}
		
		$hour = esc_attr($instance['hour']);
		if(!$hour){
			$hour = 20;
		}
		else if($hour > 23){
			$hour = 23;
		}
		
		$min = esc_attr($instance['min']);
		if(!$min){
			$min = 12;
		}
		else if($min > 59){
			$min = 59;
		}
		
		$sec = esc_attr($instance['sec']);
		if(!$sec){
			$sec = 20;
		}
		else if($sec > 59){
			$sec = 59;
		}
		$omitweeks = esc_attr($instance['omitweeks']);
		if(!$omitweeks){
			$omitweeks = 'false';
		}
		$style = esc_attr($instance['style']);
		if(!$style){
			$style = 'jedi';
		}
		$jsplacement = esc_attr($instance['jsplacement']);
		if(!$jsplacement){
			$jsplacement = 'footer';
		}

		$weektitle = empty($instance['weektitle']) ? 'weeks' : apply_filters('widget_weektitle', stripslashes($instance['weektitle']));
		$daytitle = empty($instance['daytitle']) ? 'days' : apply_filters('widget_daytitle', stripslashes($instance['daytitle']));
		$hourtitle = empty($instance['hourtitle']) ? 'hours' : apply_filters('widget_hourtitle', stripslashes($instance['hourtitle']));
		$mintitle = empty($instance['mintitle']) ? 'minutes' : apply_filters('widget_mintitle', stripslashes($instance['mintitle']));
		$sectitle = empty($instance['sectitle']) ? 'seconds' : apply_filters('widget_sectitle', stripslashes($instance['sectitle']));
			
		$isrockstar = get_option('rockstar');
		
		if($isrockstar){
			//rockstar features
			$tophtml = empty($instance['tophtml']) ? ' ' : apply_filters('widget_tophtml', stripslashes($instance['tophtml']));
			$bothtml = empty($instance['bothtml']) ? ' ' : apply_filters('widget_bothtml', stripslashes($instance['bothtml']));
			$launchhtml = empty($instance['launchhtml']) ? ' ' : apply_filters('widget_launchhtml', stripslashes($instance['launchhtml']));
			$launchtarget = empty($instance['launchtarget']) ? 'After Counter' : apply_filters('widget_launchtarget', $instance['launchtarget']);
		}
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('day'); ?>"><?php _e('Target Date (DD-MM-YYYY):'); ?></label><br/><input style="width: 30px;" id="<?php echo $this->get_field_id('day'); ?>" name="<?php echo $this->get_field_name('day'); ?>" type="text" value="<?php echo $day; ?>" />-<input style="width: 30px;" id="<?php echo $this->get_field_id('month'); ?>" name="<?php echo $this->get_field_name('month'); ?>" type="text" value="<?php echo $month; ?>" />-<input style="width: 40px;" id="<?php echo $this->get_field_id('year'); ?>" name="<?php echo $this->get_field_name('year'); ?>" type="text" value="<?php echo $year; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('hour'); ?>"><?php _e('Target Time (HH:MM:SS):'); ?></label><br/><input style="width: 30px;" id="<?php echo $this->get_field_id('hour'); ?>" name="<?php echo $this->get_field_name('hour'); ?>" type="text" value="<?php echo $hour; ?>" />:<input style="width: 30px;" id="<?php echo $this->get_field_id('min'); ?>" name="<?php echo $this->get_field_name('min'); ?>" type="text" value="<?php echo $min; ?>" />:<input style="width: 30px;" id="<?php echo $this->get_field_id('sec'); ?>" name="<?php echo $this->get_field_name('sec'); ?>" type="text" value="<?php echo $sec; ?>" /></p>
		<?php
			//Omit Week Slector
            $negative = '';
            $positive = '';
            if($omitweeks == 'false'){
                $negative = 'CHECKED';
            }else{
                $positive = 'CHECKED'; 
            }
			
			//JS Placement Slector
            $foot = '';
            $inline = '';
            if($jsplacement == 'footer'){
                $foot = 'CHECKED';
            }else{
                $inline = 'CHECKED'; 
            }
		?>
		<p><?php _e('Omit Weeks:'); ?> <input id="<?php echo $this->get_field_id('omitweeks'); ?>-no" name="<?php echo $this->get_field_name('omitweeks'); ?>" type="radio" <?php echo $negative; ?> value="false" /><label for="<?php echo $this->get_field_id('omitweeks'); ?>-no"> <?php _e('No'); ?> </label> <input id="<?php echo $this->get_field_id('omitweeks'); ?>-yes" name="<?php echo $this->get_field_name('omitweeks'); ?>" type="radio" <?php echo $positive; ?> value="true" /> <label for="<?php echo $this->get_field_id('omitweeks'); ?>-yes"> <?php _e('Yes'); ?></label></p>
		<p><?php _e('Style:'); ?> <select name="<?php echo $this->get_field_name('style'); ?>" id="<?php echo $this->get_field_name('style'); ?>">
		<?php	

		    $styles_arr = folder_array(WP_PLUGIN_DIR.'/'. dirname( plugin_basename(__FILE__) ).'/css');
			update_option('t-minus_styles', $styles_arr);
			foreach($styles_arr as $style_name){
				$selected = "";
				if($style == $style_name){
					$selected = 'SELECTED';
				}
				echo '<option value="'.$style_name.'" '.$selected.'>'.$style_name.'</option>';
			}
		?>
	    </select></p>
		<p><?php _e('Inject Script:'); ?> <input id="<?php echo $this->get_field_id('jsplacement'); ?>-foot" name="<?php echo $this->get_field_name('jsplacement'); ?>" type="radio" <?php echo $foot; ?> value="footer" /><label for="<?php echo $this->get_field_id('jsplacement'); ?>-foot"> <?php _e('Footer'); ?> </label> <input id="<?php echo $this->get_field_id('jsplacement'); ?>-inline" name="<?php echo $this->get_field_name('jsplacement'); ?>" type="radio" <?php echo $inline; ?> value="inline" /> <label for="<?php echo $this->get_field_id('jsplacement'); ?>-inline"> <?php _e('Inline'); ?></label></p>
		
		<input class="isrockstar" id="<?php echo $this->get_field_id('isrockstar'); ?>" name="<?php echo $this->get_field_name('isrockstar'); ?>" type="hidden" value="<?php echo $isrockstar; ?>" />
		<?php
		if($isrockstar){
			echo __($isrockstar).'<br/>';
		}
		else{
			?>
			<p id="header-<?php echo $this->get_field_id('unlock'); ?>"><input class="rockstar" id="<?php echo $this->get_field_id('unlock'); ?>" name="<?php echo $this->get_field_name('unlock'); ?>" type="checkbox" value="" /> <label for="<?php echo $this->get_field_id('unlock'); ?>"><?php _e('This is totally worth 3 bucks.'); ?></label></p>
			<div id="target-<?php echo $this->get_field_id('unlock'); ?>" class="collapseomatic_content">
			<?php
		}
		
		if($isrockstar){
		?>
		<a class="collapseomatic" id="tophtml<?php echo $this->get_field_id('tophtml'); ?>"><?php _e('Above Countdown'); ?></a>
		<div id="target-tophtml<?php echo $this->get_field_id('tophtml'); ?>" class="collapseomatic_content">
				<p><label for="<?php echo $this->get_field_id('tophtml'); ?>"><?php _e('Top HTML:'); ?></label> <textarea id="<?php echo $this->get_field_id('tophtml'); ?>" name="<?php echo $this->get_field_name('tophtml'); ?>"><?php echo $tophtml; ?></textarea></p>
		</div>
		<br/>
		<a class="collapseomatic" id="bothtml<?php echo $this->get_field_id('bothtml'); ?>"><?php _e('Below Countdown'); ?></a>
		<div id="target-bothtml<?php echo $this->get_field_id('bothtml'); ?>" class="collapseomatic_content">
				<p><label for="<?php echo $this->get_field_id('bothtml'); ?>"><?php _e('Bottom HTML:'); ?></label> <textarea id="<?php echo $this->get_field_id('bothtml'); ?>" name="<?php echo $this->get_field_name('bothtml'); ?>"><?php echo $bothtml; ?></textarea></p>
		</div>
		<br/>
		<a class="collapseomatic" id="launchhtml<?php echo $this->get_field_id('launchhtml'); ?>"><?php _e('When Countdown Reaches Zero'); ?></a>
		<div id="target-launchhtml<?php echo $this->get_field_id('launchhtml'); ?>" class="collapseomatic_content">
				<p><label for="<?php echo $this->get_field_id('launchhtml'); ?>"><?php _e('Launch Event HTML:'); ?></label> <textarea id="<?php echo $this->get_field_id('launchhtml'); ?>" name="<?php echo $this->get_field_name('launchhtml'); ?>"><?php echo $launchhtml; ?></textarea></p>
				<p><?php _e('Launch Target:'); ?> <select name="<?php echo $this->get_field_name('launchtarget'); ?>" id="<?php echo $this->get_field_name('launchtarget'); ?>">
				<?php
					$target_arr = array('Above Countdown', 'Below Countdown', 'Entire Widget');
					foreach($target_arr as $target_name){
						$selected = "";
						if($launchtarget == $target_name){
							$selected = 'SELECTED';
						}
						echo '<option value="'.$target_name.'" '.$selected.'>'.__($target_name).'</option>';
					}
				?>
				</select></p>
		</div>
		<br/>
		<a class="collapseomatic" id="titles<?php echo $this->get_field_id('weektitle'); ?>"><?php _e('Digit Titles'); ?></a>
		<div id="target-titles<?php echo $this->get_field_id('weektitle'); ?>" class="collapseomatic_content">
				<p><label for="<?php echo $this->get_field_id('weektitle'); ?>"><?php _e('How do you spell "weeks"?:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('weektitle'); ?>" name="<?php echo $this->get_field_name('weektitle'); ?>" type="text" value="<?php echo $weektitle; ?>" /></label></p>
				<p><label for="<?php echo $this->get_field_id('daytitle'); ?>"><?php _e('How do you spell "days"?:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('daytitle'); ?>" name="<?php echo $this->get_field_name('daytitle'); ?>" type="text" value="<?php echo $daytitle; ?>" /></label></p>
				<p><label for="<?php echo $this->get_field_id('hourtitle'); ?>"><?php _e('How do you spell "hours"?:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('hourtitle'); ?>" name="<?php echo $this->get_field_name('hourtitle'); ?>" type="text" value="<?php echo $hourtitle; ?>" /></label></p>
				<p><label for="<?php echo $this->get_field_id('mintitle'); ?>"><?php _e('How do you spell "minutes"?:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('mintitle'); ?>" name="<?php echo $this->get_field_name('mintitle'); ?>" type="text" value="<?php echo $mintitle; ?>" /></label></p>
				<p><label for="<?php echo $this->get_field_id('sectitle'); ?>"><?php _e('And "seconds" are spelled:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('sectitle'); ?>" name="<?php echo $this->get_field_name('sectitle'); ?>" type="text" value="<?php echo $sectitle; ?>" /></label></p>
		</div>
	
		<?php
		}
		else{
			echo '</div>';
		}
		
		?>
		<br/>
		<a class="collapseomatic" id="tccc<?php echo $this->get_field_id('isrockstar'); ?>"><?php _e('Schedule Recurring Countdown'); ?></a>
		<div id="target-tccc<?php echo $this->get_field_id('isrockstar'); ?>" class="collapseomatic_content">
				<p><a href="http://plugins.twinpictures.de/premium-plugins/t-minus-countdown-control/" target="_blank" title="T(-) Countdown Control">T(-) Countdown Control</a> is a premium countdown plugin that includes the ability to schedule and manage mulitple recurring T(-) Countdowns... the Jedi way.</p>
		</div>
		<?php
    }
} // class CountDownTimer

// register CountDownTimer widget
add_action('widgets_init', create_function('', 'return register_widget("CountDownTimer");'));


//code for the footer
add_action('wp_footer', 'print_my_script');
 
function print_my_script() {
	global $add_my_script;
 
	if ( ! $add_my_script ){
		return;
	}
	
	?>
		<script language="javascript" type="text/javascript">
			jQuery(document).ready(function() {
	<?php			
	foreach((array) $add_my_script as $script){
	?>
		jQuery('#<?php echo $script['id']; ?>-dashboard').countDown({	
			targetDate: {
				'day': 	<?php echo $script['day']; ?>,
				'month': <?php echo $script['month']; ?>,
				'year': <?php echo $script['year']; ?>,
				'hour': <?php echo $script['hour']; ?>,
				'min': 	<?php echo $script['min']; ?>,
				'sec': 	<?php echo $script['sec']; ?>,
				'localtime': '<?php echo $script['localtime']; ?>'
			},
			style: '<?php echo $script['style']; ?>',
			omitWeeks: <?php echo $script['omitweeks'];
				if($script['content']){
					echo ", onComplete: function() {
						jQuery('#".$script['id']."-".$script['launchtarget']."').css({'width' : '".$script['launchwidth']."', 'height' : '".$script['launchheight']."'});
						jQuery('#".$script['id']."-".$script['launchtarget']."').html('".do_shortcode($script['content'])."');
					}";
				}?>
		});
	<?php
	}
	?>
			});
		</script>
	<?php
}

//the short code
function tminuscountdown($atts, $content=null) {
	global $add_my_script;
	//find a random number, if no id was assigned
	$ran = rand(1, 10000);
	
    extract(shortcode_atts(array(
		'id' => $ran,
		't' => '20-12-2012 20:12:20',
        'weeks' => 'weeks',
		'days' => 'days',
		'hours' => 'hours',
		'minutes' => 'minutes',
		'seconds' => 'seconds',
		'omitweeks' => 'false',
		'style' => 'jedi',
		'before' => '',
		'after' => '',
		'width' => 'auto',
		'height' => 'auto',
		'launchwidth' => 'auto',
		'launchheight' => 'auto',
		'launchtarget' => 'countdown',
		'jsplacement' => 'footer',
	), $atts));
 
	
	//update the styles
	//$style_arr = get_option('t-minus_styles');
	//$style_arr[$style] = $style;
	//update_option('t-minus_styles', $style_arr);
	
	//enqueue style that was already registerd
	wp_enqueue_style( 'countdown-'.$style.'-css' );
		
	$now = time() + ( get_option( 'gmt_offset' ) * 3600);
	$target = strtotime($t, $now);
	
	//difference in seconds
	$diffSecs = $target - $now;

	$day = date ( 'd', $target );
	$month = date ( 'm', $target );
	$year = date ( 'Y', $target );
	$hour = date ( 'H', $target );
	$min = date ( 'i', $target );
	$sec = date ( 's', $target );
	
	//countdown digits
	$date_arr = array();
	$date_arr['secs'] = $diffSecs % 60;
	$date_arr['mins'] = floor($diffSecs/60)%60;
	$date_arr['hours'] = floor($diffSecs/60/60)%24;
	
	if($omitweeks == 'false'){
		$date_arr['days'] = floor($diffSecs/60/60/24)%7;
	}
	else{
		$date_arr['days'] = floor($diffSecs/60/60/24); 
	}
	$date_arr['weeks']	= floor($diffSecs/60/60/24/7);
	
	foreach ($date_arr as $i => $d) {
		$d1 = $d%10;
		if($d < 100){
			$d2 = ($d-$d1) / 10;
			$d3 = 0;
		}
		else{
			$dr = $d%100;
			$dm = $d-$dr;
			$d2 = ($d-$dm-$d1) / 10;
			$d3 = $dm / 100;
		}
		/* here is where the 1000's support will go... someday. */
		
		//now assign all the digits to the array
		$date_arr[$i] = array(
			(int)$d3,
			(int)$d2,
			(int)$d1,
			(int)$d
		);
	}
	
	if(is_numeric($width)){
		$width .= 'px';
	}
	if(is_numeric($height)){
		$height .= 'px';
	}
	$tminus = '<div id="'.$id.'-countdown" style="width:'.$width.'; height:'.$height.';">';
	$tminus .= '<div id="'.$id.'-above" class="'.$style.'-tophtml">';
    if($before){
        $tminus .=  $before; 
    }
	$tminus .=  '</div>';
		
	//drop in the dashboard
	$tminus .=  '<div id="'.$id.'-dashboard" class="'.$style.'-dashboard">';
	if($omitweeks == 'false'){
		//set up correct style class for double or triple digit love
		$wclass = $style.'-dash '.$style.'-weeks_dash';
		if($date_arr['weeks'][0] > 0){
			$wclass = $style.'-tripdash '.$style.'-weeks_trip_dash';
		}
			
		$tminus .=  '<div class="'.$wclass.'"><span class="'.$style.'-dash_title">'.$weeks.'</span>';
		if($date_arr['weeks'][0] > 0){
			$tminus .=  '<div class="'.$style.'-digit">'.$date_arr['weeks'][0].'</div>';
		}
		$tminus .=  '<div class="'.$style.'-digit">'.$date_arr['weeks'][1].'</div><div class="'.$style.'-digit">'.$date_arr['weeks'][2].'</div></div>'; 
	}
					
	//set up correct style class for double or triple digit love
	$dclass = $style.'-dash '.$style.'-days_dash';
	if($omitweeks == 'true' && $date_arr['days'][3] > 99){
		$dclass = $style.'-tripdash '.$style.'-days_trip_dash';
	}
			
	$tminus .= '<div class="'.$dclass.'"><span class="'.$style.'-dash_title">'.$days.'</span>';
	//show thrid day digit if there are NO weeks and the number of days is greater that 99
	if($omitweeks == 'true' && $date_arr['days'][3] > 99){
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['days'][0].'</div>';
	}
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['days'][1].'</div><div class="'.$style.'-digit">'.$date_arr['days'][2].'</div>';
	$tminus .= '</div>';
	$tminus .= '<div class="'.$style.'-dash '.$style.'-hours_dash">';
		$tminus .= '<span class="'.$style.'-dash_title">'.$hours.'</span>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['hours'][1].'</div>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['hours'][2].'</div>';
	$tminus .= '</div>';
		$tminus .= '<div class="'.$style.'-dash '.$style.'-minutes_dash">';
		$tminus .= '<span class="'.$style.'-dash_title">'.$minutes.'</span>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['mins'][1].'</div>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['mins'][2].'</div>';
	$tminus .= '</div>';
		$tminus .= '<div class="'.$style.'-dash '.$style.'-seconds_dash">';
		$tminus .= '<span class="'.$style.'-dash_title">'.$seconds.'</span>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['secs'][1].'</div>';
		$tminus .= '<div class="'.$style.'-digit">'.$date_arr['secs'][2].'</div>';
	$tminus .= '</div>';
	$tminus .= '</div>'; //close the dashboard

	$tminus .= '<div id="'.$id.'-below" class="'.$style.'-bothtml">';
	if($after){
		$tminus .= $after;    
	}
	$tminus .= '</div></div>';

	$t = date( 'n/j/Y H:i:s', gmmktime() + ( get_option( 'gmt_offset' ) * 3600));
	
	if(is_numeric($launchwidth)){
		$launchwidth .= 'px';
	}
	if(is_numeric($launchheight)){
		$launchheight .= 'px';
	}
	$content = mysql_real_escape_string( $content);
	$content = str_replace(array('\r\n', '\r', '\n<p>', '\n'), '', $content);
	$content = stripslashes($content);
	if($jsplacement == "footer"){
		$add_my_script[$id] = array(
			'id' => $id,
			'day' => $day,
			'month' => $month,
			'year' => $year,
			'hour' => $hour,
			'min' => $min,
			'sec' => $sec,
			'localtime' => $t,
			'style' => $style,
			'omitweeks' => $omitweeks,
			'content' => $content,
			'launchtarget' => $launchtarget,
			'launchwidth' => $launchwidth,
			'launchheight' => $launchheight
		);
	}
	else{
		?>
		<script language="javascript" type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#<?php echo $id; ?>-dashboard').countDown({	
					targetDate: {
						'day': 	<?php echo $day; ?>,
						'month': <?php echo $month; ?>,
						'year': <?php echo $year; ?>,
						'hour': <?php echo $hour; ?>,
						'min': 	<?php echo $min; ?>,
						'sec': 	<?php echo $sec; ?>,
						'localtime': '<?php echo $t; ?>'
					},
					style: '<?php echo $style; ?>',
					omitWeeks: <?php echo $omitweeks;
						if($content){
							echo ", onComplete: function() {
								jQuery('#".$id."-".$launchtarget."').css({'width' : '".$launchwidth."', 'height' : '".$launchheight."'});
								jQuery('#".$id."-".$launchtarget."').html('".do_shortcode($content)."');	
							}";
						}?>
				});
			});
		</script>
		<?php		
	}
	return $tminus;
}
add_shortcode('tminus', 'tminuscountdown');

?>