<?php
$themename = "Secondary";
$shortname = "Secondary";
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
      add_theme_page($themename." Options", "$themename Options", 'edit_themes', basename(__FILE__), 'mytheme_admin');
}
function mytheme_admin() {
    global $themename, $shortname, $options;
    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';     
?>
<div class="wrap">
<h2><b><?php echo $themename; ?> theme options</b></h2>
<form method="post">
<table class="optiontable">
<?php foreach ($options as $value) { 
if ($value['type'] == "text") { ?>     
<tr align="left"> 
    <th scope="row"><?php echo $value['name']; ?>:</th>
    <td>
        <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" size="40" />				
    </td>	
</tr>
<tr><td colspan=2> <small><?php echo $value['desc']; ?> </small> <hr /></td></tr>
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
		    <td colspan="2" style="text-align: left;"><h2><?php echo $value['name']; ?></h2></td>
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
<?php
}
add_action('admin_menu', 'mytheme_add_admin'); ?>