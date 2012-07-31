<?php
$themename = "DetoX";
$shortname = "Detox";
$options = array();
function my_options() {
    global $themename, $shortname, $options;
$sp_categories_obj = get_categories('hide_empty=0');
$sp_categories = array();
foreach ($sp_categories_obj as $sp_cat) {
	$sp_categories[$sp_cat->cat_ID] = $sp_cat->cat_name;
}
$categories_tmp = array_unshift($sp_categories, "Select a category:");	
$options = array (

array(  "name" => "Slider category",
            "type" => "heading",
			"desc" => "This section customizes the slider area.",
       ),

	array( 	"name" => "Slider category",
			"desc" => "Select the category that you would like to display as featured.",
			"id" => $shortname."_slide_category",
			"std" => "Select a category:",
			"type" => "select",
			"options" => $sp_categories),

array(  "name" => "Left Bar category",
            "type" => "heading",
			"desc" => "This section customizes the left bar posts featured.",
       ),
	array( 	"name" => "Left Bar category",
			"desc" => "Select the category that you would like to display as featured.",
			"id" => $shortname."_left_category",
			"std" => "Select a category:",
			"type" => "select",
			"options" => $sp_categories),
			
array(  "name" => "Middle Bar category",
            "type" => "heading",
			"desc" => "This section customizes the middle bar posts featured.",
       ),
	array( 	"name" => "Middle Bar category",
			"desc" => "Select the category that you would like to display as featured.",
			"id" => $shortname."_middle_category",
			"std" => "Select a category:",
			"type" => "select",
			"options" => $sp_categories),
			
			array(  "name" => "Right Bar category",
            "type" => "heading",
			"desc" => "This section customizes the Right bar posts featured.",
       ),
	array( 	"name" => "Right Bar category",
			"desc" => "Select the category that you would like to display as featured.",
			"id" => $shortname."_right_category",
			"std" => "Select a category:",
			"type" => "select",
			"options" => $sp_categories),

			array(  "name" => "Bottom slider category",
            "type" => "heading",
			"desc" => "This section customizes the Bottom slider  posts featured.",
       ),
	array( 	"name" => "Bottom slider category",
			"desc" => "Select the category that you would like to display as featured.",
			"id" => $shortname."_slicer_category",
			"std" => "Select a category:",
			"type" => "select",
			"options" => $sp_categories),
					
	array(  "name" => "Social Settings",
            "type" => "heading",
			"desc" => "Adjust the feedburner settings for your blog here .",
       ),
	               
array("name" => "Feedburner Url",
			"desc" => "Enter the feedburner url here.",
            "id" => $shortname."_url25",
            "std" => "feedburner url",
            "type" => "text"),
                        
   array("name" => "Twitter Url",
			"desc" => "Enter the twitter url here.",
            "id" => $shortname."_urltweet",
            "std" => "Twitter url",
            "type" => "text"),  
);
}
add_action('init', 'my_options');

function mytheme_add_admin() {
    global $themename, $shortname, $options;
    if ( $_GET['page'] == basename(__FILE__) ) {
        if ( 'save' == $_REQUEST['action'] ) {
                foreach ($options as $value) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
                foreach ($options as $value) {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
                header("Location: themes.php?page=controlpanel.php&saved=true");
                die;
        } else if( 'reset' == $_REQUEST['action'] ) {
            foreach ($options as $value) {
                delete_option( $value['id'] ); 
                update_option( $value['id'], $value['std'] );}
            header("Location: themes.php?page=controlpanel.php&reset=true");
            die;
        }
    }
      add_theme_page($themename." Post Options", "$themename Post Options", 'edit_themes', basename(__FILE__), 'mytheme_admin');
}
function mytheme_admin() {
    global $themename, $shortname, $options;
    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';     
?>
<div class="wrap" style="overflow:hidden;max-width:950px !important;">
<h2><b><?php echo $themename; ?> theme options</b></h2>

<div id="mainblock" style="fwidth:100%;overflow:hidden;"> 

<div id="sideblock" style="float:right;width:220px;margin-left:10px;"> 
     <h3>Information</h3>
     <div id="dbx-content" style="text-decoration:none;">       
 			 <img src="<?php bloginfo('stylesheet_directory'); ?>/images/milo.png" /><a style="text-decoration:none;" href="http://3oneseven.com/"> 3oneseven</a><br /><br />
			 <img src="<?php bloginfo('stylesheet_directory'); ?>/images/32.png" /><a style="text-decoration:none;" href="http://feeds2.feedburner.com/milo317"> Updates</a><br /><br />		 	 
			 <img src="<?php bloginfo('stylesheet_directory'); ?>/images/more.png" /><a style="text-decoration:none;" href="http://wp.milo317.com"> Cool themes by milo317</a><br /><br />
			 <img src="<?php bloginfo('stylesheet_directory'); ?>/images/twit.png" /><a style="text-decoration:none;" href="http://twitter.com/milo317"> Follow updates on Twitter</a><br /><br />			
			 <img src="<?php bloginfo('stylesheet_directory'); ?>/images/idea.png" /><a style="text-decoration:none;" href="http://3oneseven.com/contact/">Custom WordPress theme?</a>
     </div>
</div>

	<h2>New DetoX theme activated, congrats.</h2>
                
				<h4>Twitter auto posting:</h4>
				<p>
        Locate header.php at /wp-content/detox/,<br />
				locate line 94 and edit the twitter name (milo317) according to yours.<br />
        or use simply the wtitter widget.
        </p>
				
        <h4>Advertising areas</h4>
        <p>Go to the theme options at widgets,<br /> 
        <strong>use your ad widget code</strong></p>
        
        <h4>Category & Slider items</h4>
        <p>Go to the theme options at Appearance,<br />
        select your categories, please keep in mind that you need at least 5 posts for the slider categories to work.</p>
        
        <h4>Widgets</h4>
        <p>Front, all sidebars, ad sections and footer columns are fully widgetized.</p>
<p>Need more help? Contact milo317 via her <a href="http://forum.milo317.com">forum</a>.</p>
                
<form method="post">
<table style="width:80% !important;">
<?php foreach ($options as $value) { 
if ($value['type'] == "text") { ?>     
<tr align="left"> 
    <th scope="row"><?php echo $value['name']; ?>:</th>
    <td>
        <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" size="40" />				
    </td>	
</tr>
<tr>
<td colspan=2> <small><?php echo $value['desc']; ?> </small> <hr /></td></tr>
<?php } elseif ($value['type'] == "select") { ?>
    <tr align="left"> 
        <th scope="top"><?php echo $value['name']; ?>:</th>
	        <td>
            <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
                <?php foreach ($value['options'] as $option) { ?>
                <option<?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; }?>><?php echo $option; ?></option>
                <?php } ?>
            </select>			
        </td>	
</tr>
<tr><td colspan=2> <small><?php echo $value['desc']; ?> </small> <hr /></td></tr>
<?php } elseif ($value['type'] == "heading") { ?>
   <tr valign="top"> 
		    <td colspan="2" style="text-align: left;"><h2><?php echo $value['name']; ?></h2>
        </td>
		</tr>
<tr><td colspan=2> <small> <p> <?php echo $value['desc']; ?> </P> </small> <hr /></td></tr>
<?php } ?>
<?php 
}
?>
</table>
<p class="submit">
<input name="save" type="submit" value="Save changes" />    
<input type="hidden" name="action" value="save" />
</p>
</form>
<form method="post">
<p class="submit">
<input name="reset" type="submit" value="Reset" />
<input type="hidden" name="action" value="reset" />
</p>
</form>
</div>
</div>

<?php
}
add_action('admin_menu', 'mytheme_add_admin'); ?>