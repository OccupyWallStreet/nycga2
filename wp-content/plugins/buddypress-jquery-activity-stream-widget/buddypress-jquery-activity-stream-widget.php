<?php
/*
Plugin Name: Buddypress Jquery Activity Stream Widget
Plugin URI: http://clubkoncepto.com
Description: Let your site viewers/users easily read the activity streams by adding a simple yet customizable widget that displays streams in an animated manner.  <br />Note: This is a <a href="http://buddypress.org/">buddypress</a> plugin so you must install one.
Author: dunhakdis
Author URI: http://clubkoncepto.com
Version: 0.0.1
*/

// $show_blog_list_widget = true;  // Uncomment this if you want this widget available for all users

function BPjQueryActivityStream_init() {
		
        if (!function_exists('register_sidebar_widget') )
            return;
				function BPjQueryActivityStream($args) {
				$options = get_option("BPjQueryActivityStream");
					extract($args);
					//Override controller
					$activityURI = 'activity/activity-loop.php';
					  echo $before_widget;
						echo $before_title;
							echo $options['BPjQueryActivityStream_title'];
						echo $after_title;
					include $activityURI;	echo '<div style="clear:both"><br /></div>';
					echo $after_widget;
					
				}
		if (function_exists('wp_register_sidebar_widget') ) // fix for wordpress 2.2.1
			wp_register_sidebar_widget(sanitize_title('BP jQuery Activity Streams' ), 'BP jQuery Activity Streams', 'BPjQueryActivityStream', array(), 1);
		else
			register_sidebar_widget('BP jQuery Activity Streams', 'BPjQueryActivityStream', 1);
			register_widget_control('BP jQuery Activity Streams', 'BPjQueryActivityStream_control', 100, 200 );
}


function display_headers(){
		$static_url = "/wp-content/plugins/buddypress-jquery-activity-stream-widget/css/jq_fade.css";
		$options = get_option("BPjQueryActivityStream");
		
		?>
			<script type="text/javascript" src="<?php bloginfo('url') ?>/wp-content/plugins/buddypress-jquery-activity-stream-widget/js/jquery.innerfade.js"></script>
			<style type="text/css" media="screen, projection">
			@import url(<?php bloginfo('url')?><?php echo $static_url ?>);
			</style>
			<script type="text/javascript">
				var noConfict = jQuery.noConflict();
					noConfict(document).ready(
						function(){
							noConfict('#news').innerfade({
							animationtype: '<?php echo $options["BPjQueryActivityStream_effect"]; ?>',
							speed:   <?php echo $options["BPjQueryActivityStream_delay"]; ?>,
							timeout: <?php echo $options["BPjQueryActivityStream_timeout"]; ?>,
							containerheight: '<?php echo $options["BPjQueryActivityStream_height"].'px'; ?>'
						});
				});
			</script>
<?php
}
//User Options//
function BPjQueryActivityStream_control(){
	$BPjQueryActivityStream_effectCollection = array('slide','fade');
	add_option("BPjQueryActivityStream_title");
	$options = get_option("BPjQueryActivityStream");
	//Set-up Default Value
	if(!is_array($options)):
		$options = array(
			"BPjQueryActivityStream_title"=>"Activity",
			"BPjQueryActivityStream_delay"=>1000,
			"BPjQueryActivityStream_timeout"=>3000,
			"BPjQueryActivityStream_effect"=>"slide",
			"BPjQueryActivityStream_height"=>30,
		);
	endif;
	//End Setting-up Defaults
	if($_POST['BPjQueryActivityStream-submit']):
		$options['BPjQueryActivityStream_title'] = htmlspecialchars($_POST["BPjQueryActivityStream_title"]);
		$options['BPjQueryActivityStream_delay'] = htmlspecialchars($_POST["BPjQueryActivityStream_delay"]);
		$options['BPjQueryActivityStream_timeout'] = htmlspecialchars($_POST["BPjQueryActivityStream_timeout"]);
		$options['BPjQueryActivityStream_effect'] = htmlspecialchars($_POST["BPjQueryActivityStream_effect"]);
		$options['BPjQueryActivityStream_height'] = htmlspecialchars($_POST["BPjQueryActivityStream_height"]);
		update_option("BPjQueryActivityStream",$options);
	endif;
	?>
  <p>
    <label for="BPjQueryActivityStream_title"><?php echo 'Title:'; ?> </label><br />
	<input type="text" id="BPjQueryActivityStream_title" name="BPjQueryActivityStream_title" value="<?php echo $options['BPjQueryActivityStream_title'];?>" />
  </p>
  <p>
	<label for="BPjQueryActivityStream_delay"><?php echo 'Speed:'; ?></label><br />
	<input type="text" id="BPjQueryActivityStream_delay" name="BPjQueryActivityStream_delay" value="<?php echo $options['BPjQueryActivityStream_delay'];?>"/>
  </p>
  <p>
	<label for="BPjQueryActivityStream_timeout"><?php echo 'Time Out:'; ?></label><br />
	<input type="text" id="BPjQueryActivityStream_timeout" name="BPjQueryActivityStream_timeout" value="<?php echo $options['BPjQueryActivityStream_timeout'];?>" />
  </p>
  <p>
	<label for="BPjQueryActivityStream_effect"><?php echo 'Animation Type:'; ?> </label><br />
	<select id="BPjQueryActivityStream_effect" name="BPjQueryActivityStream_effect">
		<?php foreach($BPjQueryActivityStream_effectCollection as $BPjQueryActivityStream_effectCollection): ?>
			<option <?php 
			if($BPjQueryActivityStream_effectCollection == $options['BPjQueryActivityStream_effect']): 
				echo 'selected';
			endif;
			?> value="<?php echo $BPjQueryActivityStream_effectCollection; ?>"><?php echo $BPjQueryActivityStream_effectCollection; ?></option>
		<?php endforeach; ?>
	</select>
  </p>
  <p>
	<label for="BPjQueryActivityStream_height"><?php echo 'Container Height:'; ?></label><br />
	<input type="text" id="BPjQueryActivityStream_height" name="BPjQueryActivityStream_height" value="<?php echo $options['BPjQueryActivityStream_height'];?>" />px
  </p>
    <input type="hidden" id="BPjQueryActivityStream-submit" name="BPjQueryActivityStream-submit" value="1" />
  
	<?php
}
//End User Options//
add_action('wp_head','display_headers');
        $show_blog_list_widget = true;
if ($show_blog_list_widget)
        add_action('plugins_loaded', 'BPjQueryActivityStream_init');

?>
