<?php

/**
* bp_my_home_is_widget_active
* check if a widget is activated.
* 
*/
function bp_my_home_is_widget_active($widget_file){
	$allready_activated = get_option('_bpmh_activated_widgets');
	$is_active = false;
	if($allready_activated!=""){
		foreach($allready_activated as $widget_key=>$widget_values){
			if($widget_key==$widget_file) $is_active = true;
		}
	}
	return $is_active;
}

/**
* bp_my_home_manager_widget
* return the infos about the widget.
* 'bpmh widget function' is the most important one.
* 
*/
function bp_my_home_manager_widget($widget_boot){
	$file = BP_MYHOME_WIDGETS_DIR . '/'.$widget_boot;
	$fp = fopen( $file, 'r' );

	$file_data = fread( $fp, 8192 );
	fclose( $fp );
	
	//what i'm looking for in the widget header !
	$bpmh_headers = array("bpmh widget name", 
						  "bpmh widget function",
						  "bpmh widget column",
						  "bpmh widget URI", 
						  "bpmh widget Description", 
						  "bpmh widget Author",
						  "bpmh widget Author URI");
	$bpmh_values = array();

	foreach ( $bpmh_headers as $regex ) {
		preg_match( '/' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $matches);
		if($matches[1]){
			$bpmh_values[]=array($regex => trim($matches[1]));
		}
	}
	return $bpmh_values;
}


/**
* bp_my_home_manager_admin
* main function to display widgets list.
* 
*/
function bp_my_home_manager_admin(){
	if(isset($_FILES['widgetzip'])){
		$upload_ok = bpmh_upload_widget();
		if($upload_ok){
			$unzip_ok = bpmh_unzip_widget($_FILES['widgetzip']['name']);
			if($unzip_ok){
				unlink( BP_MYHOME_WIDGETS_DIR ."-temp/".$_FILES['widgetzip']['name'] );
				?>
				<div class="updated fade"><p><?php _e('Widget installed !', 'bp-my-home');?></p></div>
				<?php
			}
			else{
				?>
				<div class="error fade"><p><?php _e('Oops, error while unzipping!', 'bp-my-home');?></p></div>
				<?php
			}
		}
		else{
			?>
			<div class="error fade"><p><?php _e('Oops, error while uploading!', 'bp-my-home');?></p></div>
			<?php
		}
	}
	if(isset($_GET['action']) && $_GET['action']=="update"){
		$unzip_ok = bpmh_unzip_widget($_GET['zipwidget'],1);
		if($unzip_ok){
			unlink( BP_MYHOME_PLUGIN_DIR . '/zip-widgets/'.$_GET['zipwidget'] );
			?>
			<div class="updated fade"><p><?php _e('Widget installed !', 'bp-my-home');?></p></div>
			<?php
		}
		else{
			?>
			<div class="error fade"><p><?php _e('Oops, error while unzipping!', 'bp-my-home');?></p></div>
			<?php
		}
	}
	if(isset($_GET['action']) && $_GET['action']=="activate"){
		require( dirname( __FILE__ ) . '/bp-my-home-widget-activate.php' );
	}
	elseif(isset($_GET['action']) && $_GET['action']=="deactivate"){
		require( dirname( __FILE__ ) . '/bp-my-home-widget-deactivate.php' );
	}
	elseif(isset($_GET['action']) && $_GET['action']=="options"){
		if ( check_admin_referer('bpmh-options') ) {
			update_option( 'bp-my-home-auto-rss', $_POST['auto-rss'] );
			update_option( 'bp-my-home-auto-bkmk', $_POST['auto-bkmk'] );
			update_option( 'bp-my-home-auto-bkmk-use-tag', $_POST['auto-bkmk-tag'] );			
			?>
			<div class="updated fade"><p><?php _e('Options saved !', 'bp-my-home');?></p></div>
			<?php
		}
	}
	$widgets_infos = array();
	$widget_files = array();
	$open_dir_widgets = @opendir(BP_MYHOME_WIDGETS_DIR);
	$widget_dir="";
	if ( $open_dir_widgets ) {
		while (($file = readdir( $open_dir_widgets ) ) !== false ) {
			if ( substr($file, 0, 1) == '.' ){
				continue;
			}
			if ( is_dir( BP_MYHOME_WIDGETS_DIR.'/'.$file ) ) {
				$widget_dir=$file;
				$subdir_widgets = @ opendir( BP_MYHOME_WIDGETS_DIR.'/'.$file );
				if ( $subdir_widgets ) {
					while (($subfile = readdir( $subdir_widgets ) ) !== false ) {
						if ( substr($subfile, 0, 1) == '.' )
							continue;
						if ( substr($subfile, -4) == '.php' &&  substr($subfile, 0, -4)==$widget_dir)
							$widget_files[] = "$file/$subfile";
					}
				}
			} 
		}
	}else{
		?>
		<div class="error fade"><p><?php _e('Please make sure folders bpmh-widgets and bpmh-widgets-temp exist in folder /wp-content/uploads', 'bp-my-home');?></p></div>
		<?php
	}
	
	//looking for zip files of widget to upadate or install
	$dir_widgets_zip = BP_MYHOME_PLUGIN_DIR . '/zip-widgets';
	$open_dir_widgets_zip = @opendir($dir_widgets_zip);
	$zip_list = array();
	if ( $open_dir_widgets_zip ) {
		while (($file = readdir( $open_dir_widgets_zip ) ) !== false ) {
			if ( substr($file, 0, 1) == '.' ){
				continue;
			}
			if ( substr($file, -4) == '.zip' ) {
				$zip_list[substr($file, 0, -4)] = $file;
			}
		}
	}
	if(count($widget_files)>=1){
		foreach($widget_files as $widget){
			$widgets_infos[$widget] = bp_my_home_manager_widget($widget);
		}
	}
	$bpmh_auto_rss = get_option('bp-my-home-auto-rss');
	$bpmh_auto_bkmk = get_option('bp-my-home-auto-bkmk');
	$bpmh_auto_bkmk_tag = get_option('bp-my-home-auto-bkmk-use-tag');
	?>
	<div class="wrap">
		<h2><?php _e('BP My Home Widgets Administration', 'bp-my-home');?></h2>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e('Widget Name', 'bp-my-home');?></th>
					<th><?php _e('Widget Description', 'bp-my-home');?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th><?php _e('Widget Name', 'bp-my-home');?></th>
					<th><?php _e('Widget Description', 'bp-my-home');?></th>
				</tr>
			</tfoot>
			<tbody class="plugins">
				<?php if(count($widgets_infos)>=1):?>
				<?php foreach($widgets_infos as $key=>$info):?>
					<?php 
					$querylink = urlencode($key);
					$zipsrc = explode("/",$key);
					$zipkey = $zipsrc[0];
					if(bp_my_home_is_widget_active($key)){
					?>
					<tr class="active">
						<td class="plugin-title"><?php echo $info[0]['bpmh widget name']?></td>
						<td class="desc"><?php echo $info[4]['bpmh widget Description']?></td>
					</tr>
					<tr class="active second">
							<td class="plugin-title"><div class="row-actions-visible"><span class="network_deactivate"><a href="admin.php?page=bp-mh-admin&widgetfile=<?php echo $querylink;?>&action=deactivate" title="<?php _e('Deactivate this widget', 'bp-my-home');?>" class="delete"><?php _e('Deactivate this widget', 'bp-my-home');?></a></span><span></div></td>
							<td class="desc"><?php _e('By', 'bp-my-home');?> <a href="<?php echo $info[6]['bpmh widget Author URI']?>" title="<?php _e('Go to Author website', 'bp-my-home');?>"><?php echo $info[5]['bpmh widget Author']?></a> | <a href="<?php echo $info[3]['bpmh widget URI']?>" title="<?php _e('Go to Widget website', 'bp-my-home');?>"><?php _e('Go to Widget website', 'bp-my-home');?></a></td>
						</tr>
					<?php
					}
					else{
					?>
					<tr class="inactive">
						<td class="plugin-title"><?php echo $info[0]['bpmh widget name']?></td>
						<td class="desc"><?php echo $info[4]['bpmh widget Description']?></td>
					</tr>
					<tr class="inactive second">
							<td class="plugin-title"><div class="row-actions-visible"><span class="activate"><a href="admin.php?page=bp-mh-admin&widgetfile=<?php echo $querylink;?>&column=<?php echo $info[2]['bpmh widget column'];?>&function=<?php echo $info[1]['bpmh widget function'];?>&action=activate" title="<?php _e('Activate this widget', 'bp-my-home');?>" class="edit"><?php _e('Activate this widget', 'bp-my-home');?></a></span></div></td>
							<td class="desc"><?php _e('By', 'bp-my-home');?> <a href="<?php echo $info[6]['bpmh widget Author URI']?>" title="<?php _e('Go to Author website', 'bp-my-home');?>"><?php echo $info[5]['bpmh widget Author']?></a> | <a href="<?php echo $info[3]['bpmh widget URI']?>" title="<?php _e('Go to Widget website', 'bp-my-home');?>"><?php _e('Go to Widget website', 'bp-my-home');?></a></td>
					<?php
					}
					if($zip_list[$zipkey]){
					?>
					<tr class="plugin-update-tr"><td colspan="2" class="plugin-update"><div class="update-message"><?php _e('There is a new version of','bp-my-home');?> <?php echo $info[0]['bpmh widget name']?> <a href="admin.php?page=bp-mh-admin&action=update&amp;zipwidget=<?php echo $zip_list[$zipkey];?>"><?php _e('upgrade it','bp-my-home');?></a>.</div></td></tr>	
					<?php
					unset($zip_list[$zipkey]);
					}
					?>
				<?php endforeach;?>
				<?php endif;?>
				<?php if(count($zip_list)>=1):?>
					<?php foreach($zip_list as $kzip => $vzip):?>
						<?php if(bp_my_home_is_widget_active($kzip.'/'.$kzip.'.php')):?>
					<tr class="active">
						<td class="plugin-title"><?php echo $kzip;?></td>
						<td class="desc"><b><?php _e('This Widget was activated before the plugin upgrade, please make sure to install it so that users can still use it.','bp-my-home');?></b></td>
						<?php else:?>
					<tr class="inactive">
						<td class="plugin-title"><?php echo $kzip;?></td>
						<td class="desc"></td>
						<?php endif;?>
					</tr>
					<tr class="plugin-update-tr"><td colspan="2" class="plugin-update"><div class="update-message"><a href="admin.php?page=bp-mh-admin&action=update&amp;zipwidget=<?php echo $vzip;?>"><?php _e('Install this widget','bp-my-home');?></a>.</div></td></tr>
					<?php endforeach;?>
				<?php endif;?>
			</tbody>
		</table>
		<p>&nbsp;</p>
		<h3><?php _e('Upload a widget (zip archive)', 'bp-my-home');?></h3>
		<div id="new_unzip">
			<form method="post" enctype="multipart/form-data" action="admin.php?page=bp-mh-admin&action=upload">
					<label class="screen-reader-text" for="widgetzip"><?php _e('Zip Archive of the widget','bp-my-home');?></label>
					<input type="file" id="widgetzip" name="widgetzip">
					<input type="submit" class="button-primary" value="<?php _e('Install this widget','bp-my-home');?>">
			</form>
		</div>
		<p>&nbsp;</p>
		<h3><?php _e('Add to Rss and Bookmarks widget options', 'bp-my-home');?></h3>
		<div id="options_auto">
			<form method="post" action="admin.php?page=bp-mh-admin&action=options">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="auto-bkmk"><?php _e( 'Enable Add to My Bookmarks widget link', 'bp-my-home' ) ?></label></th>
						<td>
							<input type="radio" name="auto-bkmk" id="auto-bkmk-yes" value="yes" <?php if($bpmh_auto_bkmk=="yes") echo "checked";?>/><?php _e( 'Yes', 'bp-my-home' ) ?>&nbsp;
							<input type="radio" name="auto-bkmk" id="auto-bkmk-no" value="no" <?php if($bpmh_auto_bkmk=="no") echo "checked";?>/><?php _e( 'No', 'bp-my-home' ) ?>
						</td>
						<td><small><?php _e('In the single.php or the page.php template of your post or page, a link will automatically be added above your content','bp-my-home');?></small></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="auto-bkmk-tag"><?php _e( 'Do you want to use a tag in your single or page template instead ?', 'bp-my-home' ) ?></label></th>
						<td>
							<input type="radio" name="auto-bkmk-tag" id="auto-bkmk-tag-yes" value="yes" <?php if($bpmh_auto_bkmk_tag=="yes") echo "checked";?>/><?php _e( 'Yes', 'bp-my-home' ) ?>&nbsp;
							<input type="radio" name="auto-bkmk-tag" id="auto-bkmk-no" value="no" <?php if($bpmh_auto_bkmk_tag=="no") echo "checked";?>/><?php _e( 'No', 'bp-my-home' ) ?>
						</td>
						<td><small><?php _e('You will have to add this tag to your theme&rsquo;s single and page template files :', 'bp-my-home')?><br/><b>&lt;?php if(function_exists('the_bpmh_bkmks_tag')) the_bpmh_bkmks_tag() ; ?&gt;</b></small></small></td>
					</tr>
					<tr><td colspan="3"><hr/></td></tr>
					<tr valign="top">
						<th scope="row"><label for="auto-rss"><?php _e( 'Enable Add to My Feeds widget link', 'bp-my-home' ) ?></label></th>
						<td>
							<input type="radio" name="auto-rss" id="auto-rss-yes" value="yes" <?php if($bpmh_auto_rss=="yes") echo "checked";?>/><?php _e( 'Yes', 'bp-my-home' ) ?>&nbsp;
							<input type="radio" name="auto-rss" id="auto-rss-no" value="no" <?php if($bpmh_auto_rss=="no") echo "checked";?>/><?php _e( 'No', 'bp-my-home' ) ?>
						</td>
						<td><small><?php _e('You will have to add this tag<b>*</b> to your theme&rsquo;s template files :', 'bp-my-home')?><br/><b>&lt;?php if(function_exists('the_bpmh_rss_button')) the_bpmh_rss_button() ; ?&gt;</b></small></td>
					</tr>
					<tr><td colspan="3"><small><b>*</b> <?php _e('Using this tag in category.php, archive.php, search.php, single.php, index.php does not require more parameters. If you want to use it elsewhere or use a custom url feed (such as a feedburner one), you can add these parameters','bp-my-home');?> :<ol style="font-size:10px"><li><?php _e('the title of your feed','bp-my-home');?></li><li><?php _e('the url of your feed','bp-my-home');?></li></ol><u><?php _e('Example','bp-my-home');?></u> : <b>&lt;?php if(function_exists('the_bpmh_rss_button')) the_bpmh_rss_button('rss title', 'http://siteurl/feed') ; ?&gt;</b></small></td></tr>
					</table>
					<?php wp_nonce_field( 'bpmh-options' );?>
					<input type="submit" class="button-primary" value="<?php _e('Update these options','bp-my-home');?>">
			</form>
		</div>
		<p>&nbsp;</p>
	</div>
	<?php
}

/**
* bpmh_upload_widget
* upload zip archive to upgrade folder.
* 
*/
function bpmh_upload_widget(){
	$success = false;
	$file_temp = $_FILES['widgetzip']['tmp_name'];
	$file_name = $_FILES['widgetzip']['name'];
    $file_path = BP_MYHOME_WIDGETS_DIR ."-temp";
	//complete upload
	$filestatus = move_uploaded_file($file_temp,$file_path."/".$file_name);
	if(!$filestatus)
       $success = false;
    else 
       $success = true;

	return $success;
}

/**
* bpmh_unzip_widget
* unzip archive to widgets folder.
* 
*/
function bpmh_unzip_widget($archive, $type=0){
	global $wp_filesystem;
	if($type==0) $url = 'admin.php?page=bp-mh-admin&action=upload';
	else $url = 'admin.php?page=bp-mh-admin&action=update';

	$url = wp_nonce_url($url, 'add-widget');
	
	if ( false === ($credentials = request_filesystem_credentials($url, '', false, ABSPATH)) )
		return;

	if ( ! WP_Filesystem($credentials, ABSPATH) ) {
		request_filesystem_credentials($url, '', true, ABSPATH); //Failed to connect, Error and request again
		return;
	}
	
	if($type==0) $archive = BP_MYHOME_WIDGETS_DIR ."-temp/".$archive;
	else $archive = BP_MYHOME_PLUGIN_DIR . '/zip-widgets/'.$archive;
	
	return unzip_file($archive, BP_MYHOME_WIDGETS_DIR);
}
?>