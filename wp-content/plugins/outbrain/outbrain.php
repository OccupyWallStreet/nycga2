<?php
/*
Plugin Name: outbrain
Plugin URI: http://wordpress.org/extend/plugins/outbrain/
Description: A WordPress plugin to deal with the <a href="http://www.outbrain.com">Outbrain</a> blog posting rating system.
Author: outbrain
Version: 7.0.0.0
Author URI: http://www.outbrain.com
*/

$ob_pi_directory = '';
if( basename( dirname( __FILE__) ) == 'mu-plugins' )
	$ob_pi_directory = 'outbrain/';

include $ob_pi_directory.'ob_versionControl.php';


//Control of parts related Partners
if ($userType == "Partners"){
	$itemRecommendationsPerPage = true;
	$itemSelfRecommendations	= true;
	$itemExport					= true;
} else {
	$itemRecommendationsPerPage = false;
	$itemSelfRecommendations	= false;
	$itemExport					= false;
}


$outbrain_plugin_version = "7.0.0.0_". $userType;


// consts
$outbrain_start_comment = "//OBSTART:do_NOT_remove_this_comment";
$outbrain_end_comment = "//OBEND:do_NOT_remove_this_comment";


// add admin options page
function outbrain_add_options_page(){
	add_options_page('Outbrain options', 'Outbrain Options', 8, basename(__FILE__), 'outbrain_options_form');
}

function getAdminPage(){
 if (function_exists('admin_url')){
    $url = admin_url("options-general.php") . "?page=outbrain.php";
  }else{
    $url = $_SERVER['REQUEST_URI'];
  }
  return $url;
}

function outbrain_globals_init(){
	if ( ! defined( 'WP_CONTENT_URL' ) )
		define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
	if ( ! defined( 'WP_CONTENT_DIR' ) )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	if ( ! defined( 'WP_PLUGIN_URL' ) )
		define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
	if ( ! defined( 'WP_PLUGIN_DIR' ) )
		define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR. '/plugins' );
}

// Add settings link on plugin page
function outbrain_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=outbrain.php">Settings</a>';
  array_unshift($links, $settings_link); 
  return $links; 
}
 
function outbrain_options_form() {

	global $itemRecommendationsPerPage,$itemSelfRecommendations, $itemExport, $ob_pi_directory;

	$maxPages = 6;

	/*
	option: outbrain_pages_list
	pages list
	0: is_home (home page)
	1: is_single (single post)
	2: is_page (page)
	3: is_archive (some archive. Category, Author, Date based and Tag pages are all types of Archives)
	*/

	$PIpath 	= outbrain_get_plugin_place();
	$PIurlPath 	= outbrain_get_plugin_admin_path();


	$selected_pages 		= (isset($_POST['select_pages']))? $_POST['select_pages']: get_option("outbrain_pages_list");
	$selected_pages_recs 	= (isset($_POST['select_pages_recs']))? $_POST['select_pages_recs']: get_option("outbrain_pages_recs");

	if (isset($_POST['claim'])){
		$key	=	isset($_POST['key'])? $_POST['key']:'';
		if ($key != ''){
			update_option("outbrain_claim_key",$key);
		}
		die; // end of file
	} else if (isset($_POST['saveClaimStatus'])){
		update_option("outbrain_claim_status_num",$_POST['status']);
		update_option("outbrain_claim_status_string",$_POST['statusString']);
		die; // end of file
	} else if (isset($_POST['export'] ) && ($_POST['export']== "true") ){
		include( $ob_pi_directory.'ob_export.php');
  } else if (isset($_POST['reset'] ) && ($_POST['reset']== "true") ){
    update_option("outbrain_claim_key","");
  } else if (isset($_POST['keySave'] ) && ($_POST['keySave']== "true") ){
    $key	=	isset($_POST['key'])? $_POST['key']:'';
		if ($key != ''){
			update_option("outbrain_claim_key",$key);
      echo "<div id='message' class='updated fade'><p><strong>Key saved!</strong></p></div>";
		}
    
	} else if (isset($_POST['outbrain_send'])){
		// form sent
		$value = (isset($_POST['lang_path'])? $_POST['lang_path'] : (isset($_POST['your_translation_path'])? $_POST['your_translation_path'] : ''));
		if ($value != ''){
			update_option("outbrain_lang",$value);
		}

		$recommendations_value = 	(isset($_POST['outbrain_rater_show_recommendations']) && $_POST['outbrain_rater_show_recommendations'] == true);
		update_option("outbrain_rater_show_recommendations",$recommendations_value);

		$self_recommendations_value = 	(isset($_POST['outbrain_rater_self_recommendations']) && $_POST['outbrain_rater_self_recommendations'] == true);
		update_option("outbrain_rater_self_recommendations",$self_recommendations_value);

		$selected_pages = (isset($_POST['select_pages']))? $_POST['select_pages']: array();
		update_option("outbrain_pages_list",$selected_pages);

		$selected_pages_recs = (isset($_POST['select_pages_recs']))? $_POST['select_pages_recs']: array();
		update_option("outbrain_pages_recs",$selected_pages_recs);

		?>
		<div id="message" class="updated fade">
			<p>
				<strong><?php _e('Options saved.'); ?></strong>
			</p>
		</div>
	<?php
	}
	?>

	<div class="wrap" style="text-align:left;direction:ltr;">

		<table border="0" style="width:100%;">
			<tr>
				<td width="1%" nowrap="nowrap"><h2><?php _e('Outbrain options', 'outbrain') ?></h2></td>
				<td align="right"><a href="http://getsatisfaction.com/outbrain" target="_blank" style="font-size:13px;">Outbrain Support</a></td>
				<td style="width:20px">&nbsp;</td>
			</tr>
		</table>

		<form method="post" id="outbrain_form" name="outbrain_form" action="<?php echo getAdminPage(); ?>">
		<input type="hidden" name="export" 			id="export" 		value="false">
    <input type="hidden" name="reset" 			id="reset" 	   	value="false">
    <input type="hidden" name="keySave" 		id="keySave" 	   	value="false">
		<input type="hidden" name="obVersion" 	id="obVersion" 		value="<?php echo getVersion(); ?>">
		<input type="hidden" name="obCurrentKey" id="obCurrentKey" 	value="<?php outbrain_returnClaimCode() ?>">

			<?php
			if (function_exists('wp_nonce_field')){
				wp_nonce_field('update-options');
			}

			//get the path to plug ins
			$pathOfAdmin = outbrain_get_plugin_admin_path();
			?>
			<input type="hidden" name="outbrain_send" value="send" />
			<ul style="position: relative;">
				<div id="block_claim" class="option_board_right" style="display:none;">
					<a href="javascript:void(0)" onclick="toggleStateValidate(this)" class="blockTitle">Verify Blog ownership to Outbrain <span id="claim_title" style="font-weight:bold"> (This blog is already claimed)</span></a>
					<div id="block_claim_inner" class="block_inner" style="display:none;">
						<div>
							Outbrain key is used to verify your blog ownership.<br/>
							It will allow you to receive interesting statistics on your blog ratings and customize additional features. <br />
							<a href="http://www.outbrain.com/ln/AddBlogPage" target="_blank">For further information.</a><a href="#" onclick="javascript:failedMsg()" style="color:#ffffff">.</a>
						</div>
						<div id="outbrain_key_insertion">
							Outbrain Key
							 <?php 
                $key = get_option('outbrain_claim_key');
                if ( isset($key) && strlen($key) > 0 ){
                  $readonly = " readonly='readonly' ";  
                }
                
                echo "<input type='text' $readonly size='35' name='key' value='$key' onkeyup='' />";
                if ( isset($key) && strlen($key) > 0 ){
                  echo  "<button type='button' id='claim_key' class='button' name='claim_key' onclick='doClaim(\"$key\")'>Claim this blog</button>"; 
                  echo "<button type='button' id='claim_reset' class='button' name='claim_reset' onclick='outbrainReset()'>Reset</button>";
                }else{
                  echo "<button type='submit' id='claim_save' class='button' name='claim_save' onclick='outbrainKeySave()'>Claim key</button>";
                }  
               
               ?> 
               
							
               
							<span id="claimLoadingImage">&nbsp;</span>
						</div>
						<div id="after_claiming">
						</div>
					</div>
				</div>

				<div  id="block_language" class="option_board_right" style="display:none;">
					<a href="javascript:void(0)" onclick="toggleStateValidate(this)" class="blockTitle">Language file </a>
					<div id="block_language_inner" style="display:none" class="block_inner">

						Select a language:
						<span style="margin-left:10px;">&nbsp;</span>
						<select name="lang_path" id="langs_list" onchange="outbrain_changeLang(language_list[this.selectedIndex])" onkeyup="outbrain_changeLang(language_list[this.selectedIndex])">
							<?php //JS print here the options ?>
						</select>
						<span style="margin-left:40px;">&nbsp;</span>
						<div id='translator_div'></div>
						<div style="clear:both;">
							<a href='http://www.outbrain.com/addtranslation'>Can't find your language here?</a>
						</div>
					</div>
				</div>


				<div id="block_settings" class="option_board_right" style="display:none">
					<a href="javascript:void(0)" onclick="toggleStateValidate(this)" class="blockTitle"> Settings</a>
					<div id="block_settings_inner" style="display:none" class="block_inner">

						<div id="block_pages" class="option_board_down" style="">
							<a href="javascript:void(0)" onclick="toggleStateValidate(this)" class="blockTitle"> Pages</a>
							<div id="block_pages_inner" style="" class="block_inner">
								<?php
									$select_page_texts = array('Home page','Single post','Page','Archive (category page, author page, date page and also tag page in WP 2.3+)','Attachment','Excerpt');
									
									for ($i=0;$i<$maxPages;$i++){
										$checked = '';
										$checked_recs = '';
										if (in_array($i,$selected_pages)){
											$checked = " checked='checked' ";
										}
										if (in_array($i,$selected_pages_recs)){
											$checked_recs = " checked='checked' ";
										}
									?>
										<div class="block_inner"><label><input type="checkbox" name="select_pages[]" <?php echo $checked; ?> value="<?php echo $i; ?>"> <?php echo $select_page_texts[$i]; ?> </label></div>
										<?php if ($itemRecommendationsPerPage){?>
										<div class="block_inner" style="margin-left:40px;margin-bottom:10px"><label><input type="checkbox" name="select_pages_recs[]" <?php echo $checked_recs; ?> value="<?php echo $i; ?>"> Show recommendations </label></div>
										<?php } ?>
									<?php
									}
								?>
							</div>
						</div>


						<?php
						if ($itemSelfRecommendations){
						$checked	=	(get_option('outbrain_rater_self_recommendations')	==	true)? 'checked="checked"':''; ?>
						<div id="block_recommendation" class="option_board_down" style="">
							<a href="javascript:void(0)" onclick="toggleStateValidate(this)" class="blockTitle"> Recommendations</a>
								<div id="block_recommendation_inner" style="" class="block_inner">
								<label>
									<input type="checkbox" name="outbrain_rater_self_recommendations" <?php echo $checked; ?> /> Only recommend my blog posts
								</label>
							</div>
						</div>
						<?php }?>
					</div>
				</div>

				<div id="block_additonal_setting" class="option_board_down" style="display:none">
					<a href="javascript:void(0)" onclick="toggleStateValidate(this)" class="blockTitle">Additional Features</a>

					<div id="block_additonal_setting_inner" style="display:block" class="block_inner">
						<div id="block_additonal_instruction" style="display:none" >
						Blog ownership verification is required to enable additional customization features.
						</div>
						<ul>
							<div id="block_custom_settings" class="additional_settings" style="display:none;">
								<a href='http://www.outbrain.com/ln/BlogSettings?key=<?php outbrain_returnEncodeClaimCode()  ?>'> Configure outbrain settings </a>
							</div>
							<div id="block_MP" class="additional_settings" style="">
								<?php
									$mostPopularBlockContent	=	'';
									if (function_exists('register_sidebar_widget')){ //	only for installations with widgets support
										$mostPopularBlockContent	=	'<a href="widgets.php"> Add Most Popular widget</a>&nbsp;';
									} else {
										$mostPopularBlockContent	=	'<a href="http://getsatisfaction.com/outbrain/topics/install_most_popular_widget_with_no_wordpress_widgets_support" target="_blank">Your blog does not support widgets, See our tech support forum for installation instructions of the Most Popular widget.</a>';
									}
									echo $mostPopularBlockContent;
								?>
							</div>
							<?php if ($itemExport){ ?>
								<div id="block_export" class="additional_settings" style="">
									Export Rates from WP PostRatings <input type="button" value="Export" id="export" onclick="javascript:callExportPage()" class="key_button_active">
								<div style="font-size:0.9em;">	This action might take a while...<br>
										Please send the result file to <a href="mailto:support@outbrain.com">Outbrain Support</a> and we will import your blog rates shortly..
								</div>
							</div>
							<?php } ?>
						</ul>
					</div>
				</div>

				<div id="block_logger" class="option_board_down" style="display:none">
					<a href="javascript:void(0)" onclick="toggleStateValidate(this)" class="blockTitle">Log </a>

					<div id="block_logger_inner" style="display:block" class="block_inner">
						<div id="block_logger_display" style="display:block" >
							Please reffer to <a href="mailto:support@outbrain.com">Outbrain Support</a> and attach the logger contant.<br/> We will assist you shortly....<br/>
						</div>
						<p></p>
						<div id="block_logger_textArea_display" style="display:block" >
							<textarea rows="4" id="outbrainLogger" readonly="readonly" style="width:700px;"></textarea>
						</div>
					</div>
				</div>



				<!--
				<div id="getWidget" style="text-align:center;width:500px;margin:auto;border:1px solid red;padding:10px;display:none" >
					<?php
						$mostPopularBlockContent	=	'';
						if (function_exists('register_sidebar_widget')){ //	only for installations with widgets support
							$mostPopularBlockContent	=	'<a href="widgets.php">get outbrain Most Popular widget - click here and add the widget</a>';
						} else {
							$mostPopularBlockContent	=	'<a href="http://getsatisfaction.com/outbrain/topics/install_most_popular_widget_with_no_wordpress_widgets_support" target="_blank">Your blog does not support widgets, See our tech support forum for installation instructions of the Most Popular widget.</a>';
						}
						echo $mostPopularBlockContent;
					?>
				</div>
				-->
			</ul>
			<div id="block_loader" style="text-align:center;width:500px;margin:auto;padding:10px;display:block" class="">
				<img src="<?php echo $pathOfAdmin,$ob_pi_directory ?>ob_spinner.gif"></img>
				<b>Loading...</b>
			</div>

			<p id="block_submit" class="submit options" style="text-align:left;display:none">
				<input type="submit" name="Submit" value="<?php _e('Update Options') ?>"/>
			</p>


		</form>
	</div>
	<script language="javascript">
		var pathOfPlug  ='<?php echo $pathOfAdmin ?>';
	//check if claim already

		var key = "<?php outbrain_returnClaimCode()  ?>";

		if (key.length > 0){
			outbrain_isUserClaim(key);
		}else {
			outbrain_noClaimMode();//no key - show other options
		}
	</script>
	<?php
}

function getVersion(){
	 global $outbrain_plugin_version;
	 return $outbrain_plugin_version;
}

// display the plugin
function outbrain_display ($content)
{
	global $post_ID, $outbrain_start_comment, $outbrain_end_comment, $outbrain_plugin_version, $itemRecommendationsPerPage, $itemSelfRecommendations ;

	$where = array();
	$fromDB = get_option("outbrain_pages_list");
	if ((isset($fromDB)) && (is_array($fromDB))){
			$where = $fromDB;
	}
	//now get recommendations array
	$where_recs = array();
	$fromDB_recs = get_option("outbrain_pages_recs");
	if ((isset($fromDB_recs)) && (is_array($fromDB_recs))){
			$where_recs = $fromDB_recs;
	}

	if
	(
		(!(is_feed()) &&  !(is_preview())) &&
		(
			((is_home()) && (in_array(0,$where))) 	||
			((is_single()) && (!is_attachment()) && (in_array(1,$where)) )	||
			((is_page()) && (in_array(2,$where))) 	||
			((is_archive()) && (in_array(3,$where)))||
			((is_attachment()) && (in_array(4,$where)))
		)
	)
	{
		$recommendations_string				=	'';
		$self_recommendations_string		=	'';

	if ($itemRecommendationsPerPage){
		if (
			((is_home()) && (in_array(0,$where_recs))) 	||
			((is_single()) && (!is_attachment()) && (in_array(1,$where_recs)) )	||
			((is_page()) && (in_array(2,$where_recs))) 	||
			((is_archive()) && (in_array(3,$where_recs)))||
			((is_attachment()) && (in_array(4,$where_recs)))
		)
		{
			$recommendations_string 		= "var OB_showRec			=	true;";
		}else{
			$recommendations_string 		= "var OB_showRec			=	false;";
		}
	}

	if ($itemSelfRecommendations){
		if (get_option('outbrain_rater_self_recommendations')	==	true){
			$self_recommendations_string 	= "var OB_self_posts		=	true;";
		} else{
			$self_recommendations_string 	= "var OB_self_posts		=	false;";
		}
	}

	$installation_time_string			=	get_option('installation_time');
	$raterMode							      =	get_option('outbrain_raterMode');
	$recMode							        =	get_option('outbrain_recMode');


	if (! isset($installation_time_string) || (isset($installation_time_string) &&  empty($installation_time_string))){
		$installation_time_string =   time();
		update_option("installation_time",$installation_time_string);
	}
	if (! isset($raterMode) || (isset($raterMode) &&  empty($raterMode))){
  	$raterMode =   "stars";
		update_option("outbrain_raterMode",$raterMode);
	}
  if (! isset($recMode) || (isset($recMode) &&  empty($recMode))){
  	$recMode =   "rec";
		update_option("outbrain_recMode",$recMode);
	}

		$content .= '<script type=\'text/javascript\'>
		<!--
		' . $outbrain_start_comment . '
		var OutbrainPermaLink="' . get_permalink( $post_ID ) . '";
		if(typeof(OB_Script)!=\'undefined\'){OutbrainStart();} else {
		var OB_PlugInVer="'.$outbrain_plugin_version.'";'.$recommendations_string . $self_recommendations_string.';var OB_raterMode="'.$raterMode.'";var OB_recMode="'.$recMode.'";var OBITm="'.$installation_time_string.'";var OB_Script=true;var OB_langJS="' . get_option("outbrain_lang").'";document.write(unescape("%3Cscript src=\'http://widgets.outbrain.com/OutbrainRater.js\' type=\'text/javascript\'%3E%3C/script%3E"));}
		' . $outbrain_end_comment . '
		//-->
		</script>
		';
	}
	return $content;
}

// change the plugin on the_excerpt call
function outbrain_display_excerpt($content){
	global $outbrain_start_comment,$outbrain_end_comment;
  
	$where = array();
	$fromDB = get_option("outbrain_pages_list");

	if ((isset($fromDB)) && (is_array($fromDB))){
     $where = $fromDB;
     if (! in_array(5,$where)){
      return $content;
    }	
  }
  
  $pos = strpos($content,$outbrain_start_comment);
	$posEnd = strpos($content,$outbrain_end_comment);
	if ($pos){
		if ($posEnd == false){
			$content = str_replace(substr($content,$pos,strlen($content)),'',$content);
		} else {
			$content = str_replace(substr($content,$pos,$posEnd-$pos+strlen($outbrain_end_comment)),'',$content);
		}
	}
	$content = $content . outbrain_display('');
	return $content;
}

// print the css / js functions of the options page

function outbrain_get_plugin_admin_path(){
	$site_url = get_option("siteurl");
	// make sure the url ends with /
	$last = substr($site_url, strlen( $site_url ) - 1 );
	if ($last != "/") $site_url .= "/";
	// calculate base url based on current directory.
	$base_len = strlen(ABSPATH);
	$suffix = substr(dirname(__FILE__),$base_len)."/";
	// fix windows path sperator to url path seperator.
	$suffix = str_replace("\\","/",$suffix);
	$base_url = $site_url . $suffix;

	return $base_url;
}

function outbrain_get_plugin_place(){
	$ref = dirname(__FILE__);
	return $ref;
}

function outbrain_admin_script(){
	global $ob_pi_directory;
	if ((strpos($_SERVER['QUERY_STRING'],'outbrain.php') == false) ){
		// no outbrain's options page.
		return;
	}

	$lang_list = 'http://widgets.outbrain.com/language_list.js';
	$base_url	=	outbrain_get_plugin_admin_path();
?>
	<link rel="stylesheet" href="<?php echo $base_url,$ob_pi_directory ?>ob_style.css" type="text/css" />
	<script type="text/javascript" src="<?php echo $lang_list; ?>"></script>
	<script type="text/javascript" src="<?php echo $base_url,$ob_pi_directory ?>jquery-1.2.6.min.js"></script>
	<script type="text/javascript" src="<?php echo $base_url,$ob_pi_directory?>ob_script.js"></script>

	<script type="text/javascript">
		onload = function(){
			var current;
			<?php if (isset($_POST['lang_path'])){ ?>
				current = "<?php echo $_POST['lang_path']?>";
			<?php } else { ?>
				current = "<?php echo get_option('outbrain_lang')?>";
			<?php } ?>
			outbrain_admin_onload(current);
		}
	</script>

<?php
}

//--------------------------------------------------------------------------------------------------------
//	most popular widget
//--------------------------------------------------------------------------------------------------------
$outbrain_widget_dbdatafieldname = 'outbrain_mostPopular_data';

function outbrain_mostPopular_widget_control(){

	if (!function_exists('register_sidebar_widget')){ // no widgets in this wordpress installation!
		return;
	}

	global $outbrain_widget_dbdatafieldname;
	$curr_options = $new_options = get_option($outbrain_widget_dbdatafieldname);

	if ( $_POST["outbrain_widget_sent"] ) {
		$new_options['title'] = trim(strip_tags(stripslashes($_POST["outbrain_widget_title"])));
		$new_options['postsCount'] = $_POST["outbrain_widget_postsCount"];
		$new_options['dontShowVotersCount'] = $_POST["outbrain_widget_VotersCount"];
		
		if (!is_numeric($new_options['postsCount'])){
			$new_options['postsCount'] = $curr_options['postsCount'];
		}

		if (!is_numeric($new_options['dontShowVotersCount'])){
			$new_options['dontShowVotersCount'] = $curr_options['dontShowVotersCount'];
		}
	}

	if ( $curr_options != $new_options ) {
		$curr_options = $new_options;
		update_option($outbrain_widget_dbdatafieldname, $curr_options);
	}
	?>
		<input type="hidden" name="outbrain_widget_sent" value="1" />
		<div style="width:100%;margin-bottom:15px;">
			<label name="outbrain_widget_title">title</label>
			<div style="margin-left:15px;">
				<input type="text" name="outbrain_widget_title"	value="<?php	echo $curr_options['title'];	?>" />
			</div>
		</div>
		<div style="width:100%;margin-bottom:15px;">
			<label name="outbrain_widget_postsCount">how many posts to display</label>
			<div style="margin-left:15px;">
				<select name="outbrain_widget_postsCount">
					<?php
						for ($i=1;$i<=10;$i++){
							if ($curr_options['postsCount'] == $i){
								echo "<option value='$i' selected='selected'>$i</option>";
							} else {
								echo "<option value='$i'>$i</option>";
							}
						}
					?>
				</select>

				<!-- input type="text" name="outbrain_widget_postsCount" value="<?php	echo $curr_options['postsCount'];	?>" / -->
			</div>
		</div>
		<div style="width:100%;margin-bottom:15px;">
			<div style="margin-left:15px;">
				<input type="radio" name="outbrain_widget_VotersCount" 	value="0" <?php if($curr_options['dontShowVotersCount'] != 1) echo "checked='checked'" ?>>&nbsp; <label>Show number of raters</label><br/>
				<input type="radio" name="outbrain_widget_VotersCount" 	value="1" <?php if($curr_options['dontShowVotersCount'] == 1) echo "checked='checked'" ?>>&nbsp; <label>Don't show number of raters</label>
			</div>
		</div>

	<?php
}

function outbrain_mostPopular_widget($args) {

	global $outbrain_widget_dbdatafieldname;

	$options 		= get_option($outbrain_widget_dbdatafieldname);
	$title 			= $options['title'];
	$count 			= $options['postsCount'];
	$dontShowCountRec 	= $options['dontShowVotersCount'];

    extract($args);
	$cssUpdate  = '';

	$text .= '';

	$text	.=	$before_widget
			.	$before_title
			.	$title
			.	$after_title
			.	'<script type="text/javascript">'
			.	"\r\n"
			.	'var OB_MP_hideTitle = true; // hide the widget\'s title from js. we have it from wordpress'
			.	"\r\n";

	if (is_numeric($count)){
	$text	.=	''
			.	'var OB_MP_itemsCount =' . $count . ';'
			.	"\r\n";
	}

	if (is_numeric($dontShowCountRec) && $dontShowCountRec == 1 ){//show
	$cssUpdate	.=	''
				.'.outbrain_MP_widget .item_rating {display:none;}';
	}

	$text	.=	''
			.	'var OB_langJS ="' . get_option("outbrain_lang") . '";'
			.	"\r\n"
			.	'</script>'
			.	'<script type="text/javascript" src="http://widgets.outbrain.com/outbrainMP.js"></script>'
			.	'<style type="text/css">'. $cssUpdate .'</style>'
			.	$after_widget;

	echo $text;

}

function outbrain_mostPopular_widget_init(){

	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') ){
		return;
	}

	global $outbrain_widget_dbdatafieldname;
	$defaults = array();
	$defaults['title'] = 'Most Popular Posts';
	$defaults['postsCount'] = 3;

	add_option($outbrain_widget_dbdatafieldname, $defaults);

	register_sidebar_widget('Most Popular','outbrain_mostPopular_widget');
	register_widget_control('Most Popular', 'outbrain_mostPopular_widget_control', 250, 170);
}

function outbrain_addClaimCode(){
	$key	=	get_option('outbrain_claim_key');
	if ($key == ''){
		return;
	}
	echo "<meta name='OBKey' content='$key' />\r\n";
}

function outbrain_returnClaimCode(){
	$key	=	get_option('outbrain_claim_key');
	if ($key == ''){
		return;
	}
	echo "$key";
}

function outbrain_returnEncodeClaimCode(){
	$key	=	get_option('outbrain_claim_key');
	if ($key == ''){
		return;
	}
	$encodeKey = urlencode($key);
	echo "$encodeKey";
}

if( $installType == 'inline' )
	outbrain_mostPopular_widget_init();
else
	add_action('plugins_loaded', 'outbrain_mostPopular_widget_init');
//
outbrain_globals_init();
// add filters

$outbrain_plugin = plugin_basename(__FILE__); 

add_filter("plugin_action_links_$outbrain_plugin", 'outbrain_settings_link' );
add_filter('the_content'	, 'outbrain_display');
add_filter('the_excerpt'	, 'outbrain_display_excerpt');
add_filter('wp_head'		, 'outbrain_addClaimCode', 1);
add_action('admin_menu'		, 'outbrain_add_options_page');
add_action('admin_head'		, 'outbrain_admin_script');

add_option('outbrain_pages_list',array(0,1,2,3,4,5));
add_option('outbrain_pages_recs',array(0,1,2,3,4,5));
add_option('outbrain_claim_key','');
add_option('outbrain_claim_status_num','');
add_option('outbrain_claim_status_string','');
add_option('outbrain_raterMode',$raterMode);
add_option('outbrain_recMode',$recMode);

add_option('outbrain_rater_show_recommendations',false);
add_option('outbrain_rater_self_recommendations',false);

?>
