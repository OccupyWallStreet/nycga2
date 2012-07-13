<?php



function create_bps_menu() {

	add_submenu_page('bp-general-settings','BP Sidebar','BP Sidebar','administrator','bps-settings','bps_admin_page');

	add_action('admin_init', 'register_bps_settings');

}

	add_action(is_multisite() ? 'network_admin_menu' : 'admin_menu','create_bps_menu');

	

function register_bps_settings() {	

	$input_field_names = array(

	'bps_sidebar_list',

	'bps_sidebar_position',

	'bps_display_tool'

	);

	foreach($input_field_names as $field_name){ register_setting('bps_settings',$field_name); }

}



function bps_admin_page(){
	if(isset($_POST['bps_submit'])){
		if(!isset($_POST['bps_option']['bps_sidebar_position'])) $_POST['bps_option']['bps_sidebar_position'] = 'false';
		if(!isset($_POST['bps_option']['bps_display_tool'])) $_POST['bps_option']['bps_display_tool'] = 'false';
		foreach((array)$_POST['bps_option'] as $key => $value){
			update_option($key,stripcslashes($value));
		}
	}

	$bps_default_sidebar_list = "Home,Activity,Members,Groups,Blog";

	$bps_default_sidebar_position = 'before';

	

	if(get_option('bps_sidebar_list') == ''){ update_option('bps_sidebar_list',$bps_default_sidebar_list); }

	if(get_option('bps_sidebar_position') == ''){ update_option('bps_sidebar_position',$bps_default_sidebar_position); }

	

	//Variables

	$bps_sidebar_list = get_option('bps_sidebar_list');

	$bps_sidebar_position = get_option('bps_sidebar_position');

	$bps_display_tool = get_option('bps_display_tool');

	

	$sidebars = explode(',',$bps_sidebar_list);

	$widget_no = 1;

	?>

	<div class="side_list">

		<h3 class="underline_it">Current Sidebars:</h3>

    	<?php

		foreach($sidebars as $list_item){ 

			echo "<li>Sidebar $widget_no = <strong>" . $list_item . '</strong></li>';

			$widget_no ++;

		}

		?>

		<p><a href="<?php echo get_bloginfo('url') . '/wp-admin/widgets.php' ?>" title="WP Widgets page">Edit Widgets</a></p>

        <h3 class="underline_it">Tips:</h3>

		<p class="tips">~ When sidebars are unregistered (Ex: Going from 5 to 4 sidebars) the child widgets will move to the "Inactive Widgets" area in Wordpress.</p>

		<p class="tips">~ Use the Component & Activity Indicator to plan sidebar names.</p>

	</div>

	<style type="text/css">

	h3{padding-bottom:3px;}

	.bps_form {width:383px;}

	.side_list {float:right; width:250px; margin-top:20px; }

	.underline_it {border-bottom:1px solid #333;}

	.side_list li {list-style-type: none;}

	p.tips {font-size:11px;}

	form[name='BPSform'], input[type='submit'] {margin-top:30px;}

	</style>

<div class="wrap">

<div class="bps_form">

    		<h2>BP Sidebar Settings</h2>
			<?php if(isset($_POST['bps_submit'])) : ?>
				<div id="message" class="updated fade">
					<p>
						<?php _e( 'Settings Saved', 'bps' ) ?>
					</p>
				</div>
			<?php endif; ?>
    		<form name="BPSform" method="post" action="">

      			<?php settings_fields('bps_settings'); ?>

      			<!-- Sidebar List -->

                <label class="input_label">Register Sidebars:</label><br />

                <textarea name="bps_option[bps_sidebar_list]" style="height:60px; overflow:auto;" class="input_field"  wrap="off" type="text" cols="48" rows="1" /><?php echo $bps_sidebar_list ?></textarea><br />

                <span class="help">Enter a comma separated list of sidebars to register.<br />

                <em>Note: A blank entry restores default sidebars.</em></span><br />

                <!-- Show BP Worksheet -->

                 <h3 class="underline_it">Options:</h3>

                <p>

                    <input name="bps_option[bps_sidebar_position]" type="checkbox" <?php if($bps_sidebar_position == 'true') echo 'checked'; ?> value="true" />

                    <span class="checkbox_text_last">Display sidebars below the default BP sidebar widget</span><br />

                </p>

                <p>

                    <input name="bps_option[bps_display_tool]" type="checkbox" <?php if($bps_display_tool == 'true') echo 'checked'; ?> value="true" />

                    <span class="checkbox_text">Show Component & Action Indicator</span><br />

                </p>

                <p>

                    <input name="bps_submit" type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />

                </p>

			</form>

  		</div><!-- /.bps_form -->

  <hr style="clear:right;"/>

<div style="width:600px; margin:30px 0;">

      <h3>Using BP Sidebar</h3>

        <ol>

            <li>The default set of registered sidebars allows you to put individual sidebars on the home page, Buddypress component pages and blog related pages.</li>

            <li>There are two pre-defined sidebars provided, Home and Blog. All other sidebar names will use the Buddypress component and action system for targeting pages.</li>

            <li>Turn on the Component & Action Indicator. When you are logged in the indicator will display the current component and action variable for the page you are on. </li>

            <li>Browse your site and make a note of the results displayed in the Component &amp; Action Indicator. The component variable also holds the blog category or even an individual page name. This information will allow you to add a sidebar to individual or groups of pages. Turn off the tool when done.</li>

                <li>Name your  sidebars based on component name, or the component plus action name. Be sure to use a single space between the component and action when naming a sidebar. <em>Note: Capitalization is irrelevant.</em></li>	

        </ol>

        

        <h3>Options</h3>

        <ol>

            <li>Buddypress by default has a sidebar named "Sidebar" that displays on all pages, use this option to show your custom sidebars above or below this widget.</li>

            <li>Turns on the debugging and planning tool that is displayed on the front end of your blog, shows the current component and action.</li>

        </ol>

        

<h3>Example sidebar names</h3>

        <ol>

            <li><strong>Activity</strong> - Displayed on all activity component pages.</li>

            <li><strong>Activity Just-Me</strong> - Displayed on your profile page where the <em>Component=activity</em> and  <em>Action=just-me.</em></li>

            <li><strong>Category Myposts</strong> - Displayed  on the archive page for the blog category named &quot;Myposts&quot;.</li>

            <li><strong>Author Adam</strong> - Displayed on the archive page for the blog author named &quot;Adam&quot;.</li>

            <li><strong>Contact</strong> - Displayed on an individual page named &quot;contact&quot;.</li>

        </ol>

        

  </div>

</div><!-- /.wrap -->

<?php

}



//Register the Sidebars

$sidebars = explode(',', get_option('bps_sidebar_list'));

foreach($sidebars as $sidebar) {

	register_sidebar(array('name'=> $sidebar,

		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-inner">',

		'after_widget' => '</div></div>',

		'before_title' => '<h3 class="widgettitle">',

		'after_title' => '</h3>',

	));

}



//Get Sidebar

function bps_get_sidebar(){

	 global $bp;

	 $page_info = get_page(get_the_ID());
 
	 $the_sidebar_name = $bp->current_component;

	 

	 if(is_single() || is_archive()) bps_show_blog_sidebar();

	 if(is_home() || is_front_page()) $the_sidebar_name = 'Home'; 

	 elseif($the_sidebar_name == '') $the_sidebar_name = $page_info->post_title;

	 else{

		 $the_sidebar_name = $bp->current_component;

		 $action_sidebar_name = $bp->current_component . ' ' . $bp->current_action;

	 }

     bps_show_sidebar($the_sidebar_name,$action_sidebar_name);

	 return;

}



//Show Sidebar

function bps_show_sidebar($the_sidebar_name,$action_sidebar_name){

	if(dynamic_sidebar($action_sidebar_name)) :

	else: dynamic_sidebar($the_sidebar_name);

	endif;

}

function bps_show_blog_sidebar(){

	dynamic_sidebar('Blog'); 

}



//Page Trace Addon

function bps_trace_page_data(){

	global $bp;

	$the_component_name = $bp->current_component;

	$the_action_name = $bp->current_action;

	$plugin_url = get_bloginfo('wpurl') . '/wp-content/plugins/buddypress-sidebar/';

	//Only display if logged in

	if(!is_user_logged_in() || get_option('bps_display_tool') != 'true') return;

	?>

	<style type="text/css">

	#bp_pagetrace,#bp_pagetrace p{background-image:url(<?php echo $plugin_url ?>60pc_black.png); background-repeat:repeat;}

	#bp_pagetrace{position:fixed; z-index:999; padding:6px; top:25px;}

	#bp_pagetrace p{display:inline; padding:2px 8px; font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;}

	#bp_pagetrace em{color:#CEFFFF;}

	#bp_pagetrace em.first{margin-right:26px;}

	</style>

	<div id="bp_pagetrace">

    <p>

    Component = <em class="first"><?php if($the_component_name != '') echo $the_component_name; else echo 'Null';	?></em>

    Action = <em><?php if($the_action_name != '') echo $the_action_name; else echo 'Null'; ?></em>

    </p>

    </div>

    <?php

}

add_action('bp_header','bps_trace_page_data');



//Action Conditional

if(get_option('bps_sidebar_position') == 'true'){

	add_action('bp_inside_after_sidebar','bps_get_sidebar');

}

else{

	add_action('bp_after_sidebar_me','bps_get_sidebar');

	add_action('bp_after_sidebar_login_form','bps_get_sidebar');

}

?>