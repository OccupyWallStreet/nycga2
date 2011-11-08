<?php
//check_admin_referer();

$themename = "Voidy";
$shortname = "voidy";

load_theme_textdomain($shortname);

$option_values = "";

$options = array (

	array(	"name" => "Header",
			"type" => "title",
			"id" => $shortname."_temp"),
			
			
	array(	"type" => "open",
			"id" => $shortname."_temp"),
	

	array(	"name" => "Logo",
			"desc" => "URL to the logo image to be used instead of the site name. Give the complete path. (Giving a value in this field will hide your site name).",
			"id" => $shortname."_logo",
			"std" => "",
			"type" => "text"),
			
	array(	"name" => "Logo CSS Style",
			"desc" => "CSS styles that need to be applied to the logo img tag can be given here.",
			"id" => $shortname."_logo_style",
			"std" => "",
			"type" => "text"),
			
	array(	"name" => "Favicon",
			"desc" => "URL to the favicon (shortcut icon) image to be used. Give the complete path.",
			"id" => $shortname."_favicon",
			"std" => "",
			"type" => "text"),			
	
	array(	"name" => "Twitter",
			"desc" => "Your twitter username which will be linked to from the header.",
			"id" => $shortname."_twitter",
			"std" => "",
			"type" => "text"),
			
	array(  "name" => "Hide Twitter?",
			"desc" => "Check this box if you would like to HIDE the Twitter link in the header.",
            "id" => $shortname."_hide_twitter",
            "type" => "checkbox",
            "std" => "false"),
	
	array(	"name" => "RSS Feed",
			"desc" => "The URL of your RSS Feed which will be linked to from the header.",
			"id" => $shortname."_rss",
			"std" => "".get_bloginfo('rss_url')."",
			"type" => "text"),
	
	array(  "name" => "Hide RSS?",
			"desc" => "Check this box if you would like to HIDE the RSS link in the header.",
            "id" => $shortname."_hide_rss",
            "type" => "checkbox",
            "std" => "false"),
	
	array(  "name" => "Disable submenus?",
			"desc" => "Check this box if you would like to disable sub menus for child pages in the main navigation menu at the top.",
            "id" => $shortname."_disable_submenus",
            "type" => "checkbox",
            "std" => "false"),
			
	array(  "name" => "Show Email Subscription?",
			"desc" => "Check this box if you would like to show the email subscription form for Feedburner. (For this to work you should fill out the FeedBurber ID setting given below too.)",
            "id" => $shortname."_show_email",
            "type" => "checkbox",
            "std" => "false"),

	array(	"name" => "FeedBurner ID",
			"desc" => "Eg: If your FeedBurner RSS URL is <b>http://feeds2.feedburner.com/Diovo</b> give <b>Diovo</b> in the above field",
			"id" => $shortname."_feedburner",
			"std" => "",
			"type" => "text"),

	array(  "name" => "Hide sidebar text?",
			"desc" => "Check this box if you would like to HIDE the below text from the sidebar.",
            "id" => $shortname."_hide_sidebar_text",
            "type" => "checkbox",
            "std" => "false"),
			
	array(	"name" => "Sidebar Text",
			"desc" => "Text to display in the sidebar.",
            "id" => $shortname."_sidebar_text",
			"std" => "What I say is immensely important than who I am. Let the search be for the meaning and substance in my words rather than the intricacies of my existence.<br/><br/>Go to <a href='wp-admin/themes.php?page=functions.php'>the theme admin page</a> to edit/remove this text.",
            "type" => "textarea"),
	
	array(  "name" => "Hide Tags?",
			"desc" => "Check this box if you would like to HIDE the Tags section from under posts.",
            "id" => $shortname."_hide_tags",
            "type" => "checkbox",
            "std" => "false"),

	array(  "name" => "Hide Author Name & Categories?",
			"desc" => "Check this box if you would like to HIDE the Author Name & Categories section from the single posts.",
            "id" => $shortname."_hide_categories",
            "type" => "checkbox",
            "std" => "false"),
	
	array(	"type" => "close",
			"id" => $shortname."_temp")
	
);

get_theme_option();

function mytheme_add_admin() {
    global $themename, $shortname, $options;
	$optionvar = array();
    if ( isset($_GET['page']) && $_GET['page'] == basename(__FILE__) )  {
        if ( isset($_REQUEST['action']) && 'save' == $_REQUEST['action'] ) {
				check_admin_referer( 'voidy-nonce');
                foreach ($options as $value) {
	                if($value['id']!="voidy_temp")
	                    if( isset( $_REQUEST[ $value['id'] ] ) ) {
							$optionvar[$value['id']] = $_REQUEST[ $value['id']];
						} else {
							$optionvar[$value['id']] = $value['std'];
						} 
					}
				update_option( $shortname."_options", $optionvar  );
				header("Location: themes.php?page=functions.php&saved=true");
                die;
        } else if( isset($_REQUEST['action']) && 'reset' == $_REQUEST['action'] ) {
			check_admin_referer( 'voidy-nonce');
			delete_option( $shortname."_options" ); 
            header("Location: themes.php?page=functions.php&reset=true");
            die;
        }
    }
    add_theme_page($themename." Options", "".$themename." Options", 'edit_themes', basename(__FILE__), 'mytheme_admin');
}

function get_theme_option(){
	global $themename, $shortname, $options, $option_values;
	$optionvar = get_option( $shortname."_options");
	foreach ($options as $value) {
		if($value['id']!="voidy_temp")
			if(isset($optionvar[$value['id']])){
				$option_values[$value['id']] = $optionvar[$value['id']];
			}else{
				$option_values[$value['id']] = $value['std'];
			}
	}
}

function mytheme_admin() {
    global $themename, $shortname, $options, $option_values;
    if ( isset($_REQUEST['saved']) &&  $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
    if ( isset($_REQUEST['reset']) &&  $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
?>
<div class="wrap">
<h2><?php echo $themename; ?> settings</h2>
<?php 

foreach ($options as $value) { 
    switch ( $value['type'] ) {
		case "open":
		?>
		<br/>
		<form method="post">
		<table width="100%" border="0" style="background-color:#eef5fb; padding:10px;">
  
        
        
		<?php break;
		
		case "close":
		?>
		
        </table><br />
        
        
		<?php break;
			case "title":
		?>
				<div>
				<?php _e('Donate to support the development of this theme:', "voidy" ); ?> <form action="https://www.paypal.com/cgi-bin/webscr" method="post"> <input name="cmd" type="hidden" value="_s-xclick" /> <input name="hosted_button_id" type="hidden" value="10883505" /> <input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" type="image" /> <img src="https://www.paypal.com/en_US/i/scr/pixel.gif" border="0" alt="" width="1" height="1" /></form>
				</div>
                
        
		<?php break;

		case 'text':
		?>
        
        <tr>
            <td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
            <td width="80%"><input style="width:400px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( $option_values[ $value['id'] ] != "") { echo $option_values[ $value['id'] ]; } else { echo $value['std']; } ?>" /></td>
        </tr>

        <tr>
            <td><small><?php echo $value['desc']; ?></small></td>
        </tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px dotted #000000;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>

		<?php 
		break;
		
		case 'textarea':
		?>
        
        <tr>
            <td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
            <td width="80%"><textarea name="<?php echo $value['id']; ?>" style="width:400px; height:200px;" cols="" rows=""><?php if ( $option_values[ $value['id'] ] != "") { echo stripslashes($option_values[ $value['id'] ]); } else { echo $value['std']; } ?></textarea></td>
            
        </tr>

        <tr>
            <td><small><?php echo $value['desc']; ?></small></td>
        </tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px dotted #000000;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>

		<?php 
		break;
		
		case 'select':
		?>
        <tr>
            <td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
            <td width="80%"><select style="width:240px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><?php foreach ($value['options'] as $option) { ?><option<?php if ( $option_values[ $value['id'] ] == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?></select></td>
       </tr>
                
       <tr>
            <td><small><?php echo $value['desc']; ?></small></td>
       </tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px dotted #000000;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>

		<?php
        break;
            
		case "checkbox":
		?>
            <tr>
            <td width="20%" rowspan="2" valign="middle"><strong><?php echo $value['name']; ?></strong></td>
                <td width="80%"><?php if($option_values[$value['id']]=="true"){ $checked = "checked=\"checked\""; }else{ $checked = ""; } ?>
                        <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
                        </td>
            </tr>
                        
            <tr>
                <td><small><?php echo $value['desc']; ?></small></td>
           </tr><tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px dotted #000000;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>
            
        <?php 		break;
} 
}
?>

</table>

<p class="submit">
<input name="save" type="submit" value="<?php _e('Save changes', "voidy" ); ?>" />    
<input type="hidden" name="action" value="save" />
</p>
<?php wp_nonce_field('voidy-nonce'); ?>
</form>
<form method="post">
	<?php wp_nonce_field('voidy-nonce'); ?>
	<p class="submit">
	<input name="reset" type="submit" value="Reset" />
	<input type="hidden" name="action" value="reset" />
	</p>
</form>

<?php
}

add_action('admin_menu', 'mytheme_add_admin');
add_theme_support('menus');
add_action( 'init', 'register_my_menu' );

function register_my_menu() {
	register_nav_menu( 'primary-menu', __( 'Primary Menu' ) );
}

function default_nav_menu() {

	global $options, $option_values;

	foreach ($options as $value) {
		if($value['id'] != "voidy_temp"){
			if (empty($option_values[ $value['id']])) {
				$$value['id'] = $value['std'];
			} else {
				$$value['id'] = $option_values[ $value['id'] ]; 
			}
		}
	}
	
	$menu_content = "<ul><li ";
	if(is_home()){
		$menu_content .= ' class="current_page_item"';
	}
	$menu_content .= "><a href='".get_bloginfo('url')."' title='Home'>". __("Home", "voidy" )."</a></li>";
    $menu_content .= wp_list_pages('title_li=&depth='.($voidy_disable_submenus == "true" ? 1 : 3).'&echo=0');
	$menu_content .= "</ul>";
	echo $menu_content;
	return false;
}

if ( function_exists('register_sidebar') ) {register_sidebar();} 


// add&nbsp; [youtube=]
function youtube_embed($atts, $content = null){
extract(shortcode_atts(array(
'size' => 'm'
), $atts));
$content = substr($atts[0] ,1);
if($size=="s" || $size=="S"){$width=320; $height=265;}
elseif ($size=="m" || $size=="M"){$width=425; $height=344;}
elseif ($size=="l" || $size=="L"){$width=480; $height=385;}
elseif ($size=="xl" || $size=="XL"){$width=640; $height=505;}
$content = str_replace("watch?v=", "v/", $content);
$output='<object type="application/x-shockwave-flash" data="' . $content . '" width="' . $width . '" height="' . $height . '"><param name="movie" value="' . $content . '" /><param name="FlashVars" value="playerMode=embedded" /><param name="wmode" value="transparent" /></object>';
return $output;
}
add_shortcode('youtube', 'youtube_embed');
// add&nbsp; [googlevideo=]
function googlevideo_embed($atts, $content = null){
extract(shortcode_atts(array(
'size' => 'm'
), $atts));
$content = substr($atts[0] ,1);
if($size=="s" || $size=="S"){$width=320; $height=265;}
elseif ($size=="m" || $size=="M"){$width=425; $height=344;}
elseif ($size=="l" || $size=="L"){$width=480; $height=385;}
elseif ($size=="xl" || $size=="XL"){$width=640; $height=505;}
$content = str_replace("http://video.google.com/videoplay?docid=-", "", $content);
$content = 'http://video.google.com/googleplayer.swf?docId=-' . $content;
$output='<object type="application/x-shockwave-flash" data="' . $content . '" width="' . $width . '" height="' . $height . '"><param name="movie" value="' . $content . '" /><param name="FlashVars" value="playerMode=embedded" /><param name="wmode" value="transparent" /></object>';
return $output;
}
add_shortcode('googlevideo', 'googlevideo_embed');

function get_comment_fields($fields){
	
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );

	$commenter = wp_get_current_commenter();
	$fields['author'] = '<p class="comment-form-author">' .
					'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' />'.
					'<label for="author">' . __( 'Name' ) . ( $req ? __(" (required)", "voidy" )  : '' ) . '</label></p>';
					
	$fields['email']  = '<p class="comment-form-email">'.
					'<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' />'.
					'<label for="email">' . __( 'Email' ) . ( $req ? __(" (required)", "voidy" ) : '' ) . '</label></p>';
					
	$fields['url']    = '<p class="comment-form-url">'.
					'<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />'.
					'<label for="url">' . __( 'Website' ) . '</label></p>';
	
	return $fields;

}
add_filter('comment_form_default_fields','get_comment_fields');