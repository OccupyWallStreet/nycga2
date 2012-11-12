<?php
/*  
	Copyright 2012  Laurent (KwarK) Bertrand  (email : kwark@allwebtuts.net)
	
	Please consider a small donation for my work. Behind each code, there is a geek who has to eat.
	Thank you for my futur bundle...pizza-cola. Bundle vs bundle, it's a good deal, no ? 
	Small pizza donation @ http://kwark.allwebtuts.net
	
	You can not remove this comments such as my informations.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
// disallow direct access to file
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	wp_die(__('Sorry, but you cannot access this page directly.', 'livetv'));
}


// only necessary when the plugin part "livestream_page" is active
function livetv_general_options_livestream_page()
{
	add_submenu_page('plugin-livetv-fork.php', 'General lives page', __('General livetream', 'livetv'), 'manage_options', 'page-admin/page-admin-livetreams.php', 'livetv_do_admin_page_level_livestream_page');
}
add_action( 'livetv_add_submenu_page', 'livetv_general_options_livestream_page'); //plugin api


// Do admin options page General livestream for livestream page (plugin level 2)
function livetv_do_admin_page_level_livestream_page()
{
		
    if(isset($_POST['submitted']) && $_POST['submitted'] == "yes")
	{
		$livetv_activate_creation_role = stripslashes($_POST['livetv_activate_creation_role']);
		update_option("livetv_activate_creation_role", $livetv_activate_creation_role);
		
		$livetv_defaut_role_wordpress = stripslashes($_POST['livetv_defaut_role_wordpress']);
		update_option("livetv_defaut_role_wordpress", $livetv_defaut_role_wordpress);

		$livetv_users_role_selected = stripslashes($_POST['livetv_users_role_selected']);
		update_option("livetv_users_role_selected", $livetv_users_role_selected);
		
		$livetv_h3 = stripslashes($_POST['livetv_h3']);
		update_option("livetv_h3", $livetv_h3);
		
		$livetv_view_offline = stripslashes($_POST['livetv_view_offline']);
		update_option("livetv_view_offline", $livetv_view_offline);
		
		$livetv_effect = stripslashes($_POST['livetv_effect']);
		update_option("livetv_effect", $livetv_effect);
		
		$livetv_cache = stripslashes($_POST['livetv_cache']);
		update_option("livetv_cache", $livetv_cache);
		
		$livetv_irc = stripslashes($_POST['livetv_irc']);
		update_option("livetv_irc", $livetv_irc);
		
		$livetv_twitter = stripslashes($_POST['livetv_twitter']);
		update_option("livetv_twitter", $livetv_twitter);
		
		$livetv_facebook = stripslashes($_POST['livetv_facebook']);
		update_option("livetv_facebook", $livetv_facebook);
		
		$livetv_qtip = stripslashes($_POST['livetv_qtip']);
		update_option("livetv_qtip", $livetv_qtip);
		
		$livetv_types_order = stripslashes($_POST['livetv_types_order']);
		update_option("livetv_types_order", $livetv_types_order);
		
		$livetv_disable_normal = stripslashes($_POST['livetv_disable_normal']);
		update_option("livetv_disable_normal", $livetv_disable_normal);
		
		$livetv_span_color = stripslashes($_POST['livetv_span_color']);
		update_option("livetv_span_color", $livetv_span_color);
		
		$livetv_irc_own3d = stripslashes($_POST['livetv_irc_own3d']);
		update_option("livetv_irc_own3d", $livetv_irc_own3d);
		
		$livetv_irc_twitch = stripslashes($_POST['livetv_irc_twitch']);
		update_option("livetv_irc_twitch", $livetv_irc_twitch);
		
		$livetv_irc_justin = stripslashes($_POST['livetv_irc_justin']);
		update_option("livetv_irc_justin", $livetv_irc_justin);
		
		$livetv_limit = stripslashes($_POST['livetv_limit']);
		update_option("livetv_limit", $livetv_limit);
		
		$livetv_pagination_limit = stripslashes($_POST['livetv_pagination_limit']);
		update_option("livetv_pagination_limit", $livetv_pagination_limit);
		
		$livetv_width_thumbnails = stripslashes($_POST['livetv_width_thumbnails']);
		update_option("livetv_width_thumbnails", $livetv_width_thumbnails);
		
		$livetv_height_thumbnails = stripslashes($_POST['livetv_height_thumbnails']);
		update_option("livetv_height_thumbnails", $livetv_height_thumbnails);
		
		$livetv_list_display = stripslashes($_POST['livetv_list_display']);
		update_option("livetv_list_display", $livetv_list_display);
		
		if(!empty($_POST['livetv_new_role_to_add']))
		{
			$livetv_new_role_to_add = stripslashes($_POST['livetv_new_role_to_add']);
			$livetv_new_role_to_add_data = preg_replace('# #', '_', strtolower(stripslashes($_POST['livetv_new_role_to_add'])));
			$livetv_new_role_based_on = stripslashes($_POST['livetv_new_role_based_on']);
		
			if($livetv_new_role_based_on == "subscriber"){
				$live_tv_capabilities = array(
					'read' => true, // True allows that capability
					'level_0' => true
				);
			}
			
			if($livetv_new_role_based_on == "contributor"){
				$live_tv_capabilities = array(
					'read' => true,
					'delete_posts' => true,
					'edit_posts' => true,
					'level_1' => true,
					'level_0' => true
				);
			}
			
			if($livetv_new_role_based_on == "author"){
				$live_tv_capabilities = array(
					'read' => true,
					'delete_posts' => true,
					'edit_posts' => true,
					'edit_published_posts' => true,	
					'upload_files' => true,
					'publish_posts' => true,	
					'delete_published_posts' => true,
					'level_2' => true,
					'level_1' => true,
					'level_0' => true
				);
			}
			
			if($livetv_new_role_based_on == "editor"){
				$live_tv_capabilities = array(
					'read' => true,
					'delete_posts' => true,
					'edit_posts' => true,
					'edit_published_posts' => true,	
					'upload_files' => true,
					'publish_posts' => true,	
					'delete_published_posts' => true,
					'read_private_pages' => true,
					'edit_private_pages' => true,				
					'delete_private_pages' => true,
					'read_private_posts' => true,
					'edit_private_posts' => true,
					'delete_private_posts' => true,
					'delete_published_pages' => true,
					'delete_pages' => true,
					'publish_pages' => true,
					'edit_published_pages' => true,
					'edit_others_pages' => true,
					'edit_pages' => true,
					'edit_others_posts' => true,
					'manage_links' => true,
					'manage_categories' => true,
					'moderate_comments' => true,
					'level_7' => true,
					'level_6' => true,
					'level_5' => true,
					'level_4' => true,
					'level_3' => true,
					'level_2' => true,
					'level_1' => true,
					'level_0' => true
				);
			}
			
			if(!preg_match("#^live_#", $livetv_new_role_to_add_data))
			{
				$livetv_new_role_to_add_data = 'live_' . $livetv_new_role_to_add_data; //Cheating for list of new roles and futur traitment
			}
			$roles = new WP_Roles();
			$result = $roles->add_role(''.$livetv_new_role_to_add_data.'', ''.$livetv_new_role_to_add.'', $live_tv_capabilities); 
			
			if (null !== $result)
			{
				$info = __('Yay!  New role', 'livetv');
				$info .= ' '.$livetv_new_role_to_add.' ';
				$info .= __('for your team created! Based on defaut capabilities of', 'livetv');
				$info .= ' ' . $livetv_new_role_based_on.'.';
				$class = 'success';
			}
			
			else
			{
				$info = __('Oh... the', 'livetv');
				$info .= ' '.$livetv_new_role_to_add.' ';
				$info .= __('role for your team already exists.', 'livetv');
				$class = 'info';
			}
		}
		
		if(!empty($_POST['livetv_new_role_to_delete']))
		{
			$livetv_new_role_to_delete = stripslashes($_POST['livetv_new_role_to_delete']);
			
			//Before remove role...
			//get all users in this role...
			global $blog_id, $current_user;
			
			$lastusers = array(
				'blog_id' => $blog_id,
				'role' => ''.$livetv_new_role_to_delete.'',
				'search' => ID //Passing this value with '' don't work correctly under wordpress 3.1.X...
 			);
				
			$blogusers = get_users(''.$lastusers.'' );
			
			/*var_dump($blogusers);*/
			
            //filter current admin
			wp_get_current_user();
			
			$editable_roles = get_roles();
			/*var_dump($editable_roles);*/

			//Update role of these users...
			foreach($blogusers as $key => $value)
			{
				$id = $value->ID;
				// get user objet by user ID
				$bloguser = new WP_User( $id );
				
				foreach($editable_roles as $k => $v)
				{
					$temp = preg_match("#".$livetv_new_role_to_delete."#", $k);
					
					if($temp == true)
					{
						$bloguser->remove_role( ''.$v.'' );
						
						//Filter current admin to be sure...
						if($current_user->ID != $id)
						{
							$bloguser->set_role( 'subscriber' );
						}
					}
				}
			}
				
			//Now remove this WP role...
			$roles = new WP_Roles();
			$result = $roles->remove_role(''.$livetv_new_role_to_delete.'');
			
			
			if (null == $result)
			{
				$info = __('The role with the id ', 'livetv');
				$info .= ' '.$livetv_new_role_to_delete.' ';
				$info .= __('successful deleted!', 'livetv');
				$class = 'success';
			} 
			
			else 
			{
				$info = __('Oh... the role', 'livetv');
				$info .= ' '.$livetv_new_role_to_delete.' ';
				$info .= __('already erased or not exists actually.', 'livetv');
				$class = 'error';
			}
		}
		
		if(!$info)
		{
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>";
			echo _e('Your settings have been saved.', 'livetv');
			echo "</strong></p></div>";
		}
		
		else
		{
			echo '<div id="message" class="updated fade '.$class.'"><p><strong>'.$info.'</strong></p></div>';
		}
	}


// Lest go for the html part of admin page for livestream_page
?>

<div class="wrap livetv-admin">
  <div id="icon-upload" class="icon32"><br>
  </div>
  <h2>
    <?php _e('liveTV Team - General configuration', 'livetv') ?>
  </h2>
  <form method="post" action="" class="livetv_admin">
    <p class="submit">
      <input type="submit" name="options_submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
    </p>
    <?php
		
	$editable_roles = get_roles();
	/*var_dump($editable_roles);*/

	foreach($editable_roles as $key => $value){
		$temp = preg_match("#^live_#", $key);
	}
	if($temp == true){

		?>
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Delete new roles option', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Delete new role', 'livetv'); ?>
          </label></td>
        <td class="livetv-admin-td"><select name="livetv_new_role_to_delete" id="livetv_new_role_to_delete" onchange="if(this.options[this.selectedIndex].value== ''){return;}else(confirm('<?php _e('Really want to delete this role ? Turn Off if not !', 'livetv'); ?>'));">
            <option value=""><?php echo 'Off' ?></option>
            <?php 
		foreach ($editable_roles as $key => $value)
		{ 
			if(preg_match("#^live_#", $key)) //cheating to list, dont change this
			{ ?>
            <option value="<?php echo ''.$key.'' ?>"><?php echo ''.$value.'' ?></option>
            <?php 
			}
		 } ?>
          </select>
          <span class="livetv_help" title="<?php _e('Select one role in the list and clic on registration button to delete your selected role', 'livetv'); ?>"></span> <br /></td>
      </tr>
    </table>
    <br />
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('These roles have access to streaming creation from its personal profil', 'livetv'); ?></th>
        <?php
			    
		foreach ($editable_roles as $key => $value)
		{ 
			if(preg_match("#^live_#", $key)) //cheating to list, dont change this
			{
			?>
      <tr valign="top">
        <td scope="row"><label>&nbsp;</label></td>
        <td><input type="text" style="width:125px;" maxlength="55" disabled="disabled" name="<?php echo $value; ?>" value="<?php echo $value; ?>" id="<?php echo $key; ?>" />
          <span class="livetv_help" title="<?php _e('This role have access to live stream creation', 'livetv'); ?>"></span> <br /></td>
      </tr>
      <?php 
			}
		}
		if(!$temp) { ?>
      <tr valign="top">
        <td scope="row"><label>&nbsp;</label></td>
        <td class="livetv-admin-td"><?php _e('Currently no new role', 'livetv'); ?></td>
      </tr>
      <?php } ?>
    </table>
    <?php } ?>
    <br />
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Title h3 option', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Title h3', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_h3'); ?>
        <td class="livetv-admin-td"><select name="livetv_h3" id="livetv_h3">
            <option <?php if($temp == 'img'){echo 'selected="selected"';} ?> value="img">Image</option>
            <option <?php if($temp == 'txt'){echo 'selected="selected"';} ?> value="txt">Texte</option>
            <option <?php if($temp == 'off'){echo 'selected="selected"';} ?> value="off">No</option>
          </select>
          <span class="livetv_help" title="<?php _e('Select a value to displaying on livestream page', 'livetv'); ?>"></span> <br /></td>
      </tr>
    </table>
    <br />
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Live streams offline option', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Live stream offline', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_view_offline'); ?>
        <td class="livetv-admin-td"><select name="livetv_view_offline" id="livetv_view_offline">
            <option <?php if($temp == 'on'){echo 'selected="selected"';} ?> value="on">On (all)</option>
            <option <?php if($temp == 'off'){echo 'selected="selected"';} ?> value="off">Off (all)</option>
            <option <?php if($temp == 'widget_off'){echo 'selected="selected"';} ?> value="widget_off">Off (widget)</option>
          </select>
          <span class="livetv_help" title="<?php _e('Choose - on - to view all livestreams offline on the livestream page (a macaroon appears for each status). To view the changes more rapidely, visit your page livestream to regenerate the cache system.', 'livetv'); ?>"></span> <br /></td>
      </tr>
    </table>
    <br />
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Display live streams list option', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Display live streams list', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_list_display'); ?>
        <td class="livetv-admin-td"><select name="livetv_list_display" id="livetv_list_display">
            <option <?php if($temp == 'on'){echo 'selected="selected"';} ?> value="on">On</option>
            <option <?php if($temp == 'off'){echo 'selected="selected"';} ?> value="off">Off</option>
          </select>
          <span class="livetv_help" title="<?php _e('Select your choice to displaying or not displaying the list of live streams under a current single view.', 'livetv'); ?>"></span> <br /></td>
      </tr>
    </table>
    <br />
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Special effects option', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Slide effect', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_effect'); ?>
        <td class="livetv-admin-td"><select name="livetv_effect" id="livetv_effect">
            <option <?php if($temp == 'top_left'){echo 'selected="selected"';} ?> value="top_left">top-left</option>
            <option <?php if($temp == 'top'){echo 'selected="selected"';} ?> value="top">top</option>
            <option <?php if($temp == 'top_right'){echo 'selected="selected"';} ?> value="top_right">top-right</option>
            <option <?php if($temp == 'slide_right'){echo 'selected="selected"';} ?> value="slide_right">slide-right</option>
            <option <?php if($temp == 'bottom_right'){echo 'selected="selected"';} ?> value="bottom_right">bottom-right</option>
            <option <?php if($temp == 'bottom'){echo 'selected="selected"';} ?> value="bottom">bottom</option>
            <option <?php if($temp == 'bottom_left'){echo 'selected="selected"';} ?> value="bottom_left">bottom-left</option>
            <option <?php if($temp == 'slide_left'){echo 'selected="selected"';} ?> value="slide_left">slide-left</option>
          </select>
          <span class="livetv_help" title="<?php _e('Choose the default slide effect for some elements like chatIRC', 'livetv'); ?>"></span> <br /></td>
      </tr>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Dialog bubble color/style', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_qtip'); ?>
        <td class="livetv-admin-td"><select name="livetv_qtip" id="livetv_qtip">
            <option <?php if($temp == 'light'){echo 'selected="selected"';} ?> value="light">
            <?php _e('White', 'livetv'); ?>
            </option>
            <option <?php if($temp == 'grey'){echo 'selected="selected"';} ?> value="grey">
            <?php _e('Grey', 'livetv'); ?>
            </option>
            <option <?php if($temp == 'dark'){echo 'selected="selected"';} ?> value="dark">
            <?php _e('Black', 'livetv'); ?>
            </option>
          </select>
          <span class="livetv_help" title="<?php _e('Choose the default color for dialog effect for some elements like livestream page and help in administration', 'livetv'); ?>"></span> <br /></td>
      </tr>
    </table>
    <br />
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Cache time option', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Cache time', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_cache'); ?>
        <td class="livetv-admin-td"><select name="livetv_cache" id="livetv_cache">
            <?php for($i = 1; $i <= 10; $i++){ ?>
            <option <?php if($temp == ''.$i.''){echo 'selected="selected"';} ?> value="<?php echo ''.$i.''; ?>"><?php echo ''.$i.''; ?></option>
            <?php } ?>
          </select>
          <span class="livetv_help" title="<?php _e('Cache is expressed in minutes before expiration', 'livetv'); ?>"></span> <br /></td>
      </tr>
    </table>
    <br />
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Chat irc quakenet option', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Format', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_irc'); ?>
        <td class="livetv-admin-td"><select name="livetv_irc" id="livetv_irc">
            <option <?php if($temp == 'sitename'){echo 'selected="selected"';} ?> value="sitename">#sitename</option>
            <option <?php if($temp == 'userName'){echo 'selected="selected"';} ?> value="userName">#user</option>
            <option <?php if($temp == 'channelName'){echo 'selected="selected"';} ?> value="channelName">#liveID</option>
            <option <?php if($temp == 'sitename_userName'){echo 'selected="selected"';} ?> value="sitename_userName">#sitename.user</option>
            <option <?php if($temp == 'sitename_channelName'){echo 'selected="selected"';} ?> value="sitename_channelName">#sitename.liveID</option>
            <option <?php if($temp == 'sitename_userName_channelName'){echo 'selected="selected"';} ?> value="sitename_userName_channelName">#sitename.user.liveID</option>
            <option <?php if($temp == 'sitename_channelName_userName'){echo 'selected="selected"';} ?> value="sitename_channelName_userName">#sitename.liveID.user</option>
          </select>
          <span class="livetv_help" title="<?php _e('This option change the #channel format of the chat IRC under the current livestream e.g. if you use #sitename only one channel irc exist for all irc channel on your site and all members and visitors speak under this irc channel. If you use #sitename.user only one channel irc exist for all livestreams shared by this user. If you use a parameter with #chanID each livestream has its personal irc channel', 'livetv'); ?>"></span> <br /></td>
      </tr>
    </table>
    <br />
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Size and limits options', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Live streams limit by user', 'livetv'); ?>
          </label></td>
        <td class="livetv-admin-td"><input type="number" maxlength="4" name="livetv_limit" value="<?php echo get_option('livetv_limit'); ?>" id="livetv_limit" />
          <span class="livetv_help" title="<?php _e('Define a limit of live stream creation by user * $type active. Leave empty for unlimited.', 'livetv'); ?>"></span> <br /></td>
      </tr>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Live streams limit for pagination', 'livetv'); ?>
          </label></td>
        <td class="livetv-admin-td"><input type="number" maxlength="4" name="livetv_pagination_limit" value="<?php echo get_option('livetv_pagination_limit'); ?>" id="livetv_pagination_limit" />
          <span class="livetv_help" title="<?php _e('Define a number limit of thumbnails by page for navigation with pagination. Leave empty to disable pagination.', 'livetv'); ?>"></span> <br /></td>
      </tr>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Define width for each thumbnail', 'livetv'); ?>
          </label></td>
        <td class="livetv-admin-td"><input type="number" maxlength="4" name="livetv_width_thumbnails" value="<?php echo get_option('livetv_width_thumbnails'); ?>" id="livetv_width_thumbnails" />
          <span class="livetv_help" title="<?php _e('Define a width limit of thumbnails images. If nothing is defined, default 170px', 'livetv'); ?>"></span> <br /></td>
      </tr>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Define height for each thumbnail', 'livetv'); ?>
          </label></td>
        <td class="livetv-admin-td"><input type="number" maxlength="4" name="livetv_height_thumbnails" value="<?php echo get_option('livetv_height_thumbnails'); ?>" id="livetv_height_thumbnails" />
          <span class="livetv_help" title="<?php _e('Define a height limit for all thumbnails images. If nothing is defined, default 100px', 'livetv'); ?>"></span> <br /></td>
      </tr>
    </table>
    <br />
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Quakenet replacement options', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Replace quakenet by original chat for all your own3d.tv', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_irc_own3d'); ?>
        <td class="livetv-admin-td"><select name="livetv_irc_own3d" id="livetv_irc_own3d">
            <option <?php if($temp == 'quakenet'){echo 'selected="selected"';} ?> value="quakenet">Off</option>
            <option <?php if($temp == 'own3d'){echo 'selected="selected"';} ?> value="own3d">On</option>
          </select>
          <span class="livetv_help" title="<?php _e('For the live stream page [LivesOnline] - replace all quakenet by original own3d chat (now debugged with a cheating to work correctly)', 'livetv'); ?>"></span> <br /></td>
      </tr>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Replace quakenet by original chat for all your twitch.tv', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_irc_twitch'); ?>
        <td class="livetv-admin-td"><select name="livetv_irc_twitch" id="livetv_irc_twitch">
            <option <?php if($temp == 'quakenet'){echo 'selected="selected"';} ?> value="quakenet">Off</option>
            <option <?php if($temp == 'twitch'){echo 'selected="selected"';} ?> value="twitch">On</option>
          </select>
          <span class="livetv_help" title="<?php _e('For the live stream page [LivesOnline] - replace all quakenet by original twitch chat', 'livetv'); ?>"></span> <br /></td>
      </tr>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Replace quakenet by original chat for all your justin.tv', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_irc_justin'); ?>
        <td class="livetv-admin-td"><select name="livetv_irc_justin" id="livetv_irc_justin">
            <option <?php if($temp == 'quakenet'){echo 'selected="selected"';} ?> value="quakenet">Off</option>
            <option <?php if($temp == 'justin'){echo 'selected="selected"';} ?> value="justin">On</option>
          </select>
          <span class="livetv_help" title="<?php _e('For the live stream page [LivesOnline] - replace all quakenet by original justin chat', 'livetv'); ?>"></span> <br /></td>
      </tr>
    </table>
    <br />
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Page template cheating', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Disable button normal view', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_disable_normal'); ?>
        <td class="livetv-admin-td"><select name="livetv_disable_normal" id="livetv_disable_normal">
            <option <?php if($temp == 'off'){echo 'selected="selected"';} ?> value="off">Off</option>
            <option <?php if($temp == 'on'){echo 'selected="selected"';} ?> value="on">On</option>
          </select>
          <span class="livetv_help" title="<?php _e('If you have a page template without sidebar maybe the normal view have a css bug, you may disable the button normal view here.', 'livetv'); ?> <?php _e('Info: Please be patient to view the result with the cache system.', 'livetv'); ?>"></span> <br /></td>
      </tr>
    </table>
    <br />
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Change color for text informations', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Change color', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_span_color'); ?>
        <td class="livetv-admin-td"><input class="color" id="livetv_span_color" name="livetv_span_color" value="<?php echo ''.$temp.''; ?>" />
          <span class="livetv_help" title="<?php _e('This option change the color value for the text on the right side of each thumbnails', 'livetv'); ?> <?php _e('Info: Please be patient to view the result with the cache system.', 'livetv'); ?>"></span><br /></td>
      </tr>
    </table>
    <br />
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Change order by live stream types', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Livestream order', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_types_order'); ?>
        <td class="livetv-admin-td"><select name="livetv_types_order" id="livetv_types_order">
            <option <?php if($temp == 'own3d'){echo 'selected="selected"';} ?> value="own3d">own3d only</option>
            <option <?php if($temp == 'twitch'){echo 'selected="selected"';} ?> value="twitch">twitch only</option>
            <option <?php if($temp == 'justin'){echo 'selected="selected"';} ?> value="justin">justin only</option>
            <option <?php if($temp == 'own3d_twitch' || $temp == ''){echo 'selected="selected"';} ?> value="own3d_twitch">own3d twitch</option>
            <option <?php if($temp == 'twitch_own3d'){echo 'selected="selected"';} ?> value="twitch_own3d">twitch own3d</option>
            <option <?php if($temp == 'own3d_justin'){echo 'selected="selected"';} ?> value="own3d_justin">own3d justin</option>
            <option <?php if($temp == 'justin_own3d'){echo 'selected="selected"';} ?> value="justin_own3d">justin own3d</option>
            <option <?php if($temp == 'twitch_justin'){echo 'selected="selected"';} ?> value="twitch_justin">twitch justin</option>
            <option <?php if($temp == 'justin_twitch'){echo 'selected="selected"';} ?> value="justin_twitch">justin twitch</option>
            <option <?php if($temp == 'own3d_twitch_justin'){echo 'selected="selected"';} ?> value="own3d_twitch_justin">own3d twitch justin</option>
            <option <?php if($temp == 'own3d_justin_twitch'){echo 'selected="selected"';} ?> value="own3d_justin_twitch">own3d justin twitch</option>
            <option <?php if($temp == 'twitch_own3d_justin'){echo 'selected="selected"';} ?> value="twitch_own3d_justin">twitch own3d justin</option>
            <option <?php if($temp == 'twitch_justin_own3d'){echo 'selected="selected"';} ?> value="twitch_justin_own3d">twitch justin own3d</option>
            <option <?php if($temp == 'justin_own3d_twitch'){echo 'selected="selected"';} ?> value="justin_own3d_twitch">justin own3d twitch</option>
            <option <?php if($temp == 'justin_twitch_own3d'){echo 'selected="selected"';} ?> value="justin_twitch_own3d">justin twitch own3d</option>
          </select>
          <span class="livetv_help" title="<?php _e('This option change the order to display. Act also on each profil and for the frontend in the same time.', 'livetv'); ?> <?php _e('Info: Please be patient to view the result with the cache system.', 'livetv'); ?>"></span> <br /></td>
      </tr>
    </table>
    <br />
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Social media link options', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><?php _e('Link to Facebook', 'livetv'); ?></td>
        <?php $general_join_facebook = get_option('livetv_facebook'); $general_join_twitter = get_option('livetv_twitter'); ?>
        <td class="livetv-admin-td"><input type="text" style="width:125px;" maxlength="150" name="livetv_facebook" value="<?php echo $general_join_facebook; ?>" id="livetv_facebook" />
          <span class="livetv_help" title="<?php _e('Choose your entire link to go on your social media page', 'livetv'); ?> Facebook"></span> <br /></td>
      </tr>
      <tr valign="top">
        <td scope="row"><?php _e('Link to Twitter', 'livetv'); ?></td>
        <td class="livetv-admin-td"><input type="text" style="width:125px;" maxlength="150" name="livetv_twitter" value="<?php echo $general_join_twitter; ?>" id="livetv_twitter" />
          <span class="livetv_help" title="<?php _e('Choose your entire link to go on your social media page', 'livetv'); ?> Twitter"></span> <br /></td>
      </tr>
    </table>
    <br />
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Special roles or normal roles configuration', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Give access to profil to', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_activate_creation_role'); ?>
        <td class="livetv-admin-td"><input type="radio" name="livetv_activate_creation_role" id="livetv_activate_creation_role" value="on" <?php if($temp == 'on'){echo 'checked="checked"';} ?> />
          <?php _e('special role', 'livetv'); ?>
          <span class="livetv_help" title="<?php _e('Create some special roles of your choice. After a change here, a new zone at the bottom on this page appears in replacement of the actual zone to set your preferences.', 'livetv'); ?>"></span> <br />
          <input type="radio" name="livetv_activate_creation_role" id="livetv_activate_creation_role" value="off" <?php if($temp == 'off'){echo 'checked="checked"';} ?> />
          <?php _e('normal role', 'livetv'); ?>
          <span class="livetv_help" title="<?php _e('Continue with wordpress default roles only. After a change here, a new zone at the bottom on this page appears in replacement of the actual zone to set your preferences.', 'livetv'); ?>"></span> <br /></td>
      </tr>
    </table>
    <?php if($temp == 'on'){ ?>
    <br />
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Special roles of your choice - extension to configure', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Add a new role', 'livetv'); ?>
          </label></td>
        <td class="livetv-admin-td"><input type="text"  style="width:125px;" maxlength="55" name="livetv_new_role_to_add" id="livetv_new_role_to_add" value="" />
          <select name="livetv_new_role_based_on" id="livetv_new_role_based_on">
            <option value="subscriber">
            <?php _e('Members', 'livetv'); ?>
            </option>
            <option value="contributor">
            <?php _e('Contributors', 'livetv'); ?>
            </option>
            <option value="author">
            <?php _e('Authors', 'livetv'); ?>
            </option>
            <option value="editor">
            <?php _e('Editors', 'livetv'); ?>
            </option>
          </select>
          <span class="livetv_help" title="<?php _e('Choose default capabilities (based on one of this default wordpress roles) for your new role before updating. Info: editor is not the original version. No capabilities to delete on other posts/pages. No capabilities for html scripts. All the rest is the same.', 'livetv'); ?>"></span> <br /></td>
      </tr>
      <?php } ?>
    </table>
    <?php if($temp == 'off'){ ?>
    <br />
    <?php $temp = get_option('livetv_defaut_role_wordpress'); ?>
    <table class="widefat options" style="width: 650px">
      
        <th colspan="2" class="dashboard-widget-title"><?php _e('Normal roles wordpress - extension to configure', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Defaut wordpress roles', 'livetv'); ?>
          </label></td>
        <td class="livetv-admin-td"><select name="livetv_defaut_role_wordpress" id="livetv_defaut_role_wordpress">
            <option value="off" <?php if($temp == 'off'){echo 'selected="selected"';} ?>>Off</option>
            <option <?php if($temp == 'contributor'){echo 'selected="selected"';} ?> value="contributor">Contributors</option>
            <option <?php if($temp == 'author'){echo 'selected="selected"';} ?> value="author">Authors</option>
            <option <?php if($temp == 'administrator'){echo 'selected="selected"';} ?> value="administrator">Administrator(s)</option>
          </select>
          <span class="livetv_help" title="<?php _e('Choose the default role who have access to create live streams from its profil (administrator have already access to create live stream from its profil and manage live stream on all others profils)', 'livetv'); ?>"></span> <br /></td>
      </tr>
      <?php } ?>
    </table>
    <p class="submit">
      <input name="submitted" type="hidden" value="yes" />
      <input type="submit" name="options_submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
    </p>
  </form>
</div>
<?php
}
//End admin page


//Now hook to create extra profil fields
add_action( 'edit_user_profile', 'livetv_show_extra_profile_fields' ); 
//When the current on the current profil has capabilties to edit -> display
add_action( 'edit_user_profile_update', 'livetv_save_extra_profile_fields' ); 
//When the current on the current profil has capabilties to edit -> update

add_action( 'show_user_profile', 'livetv_show_extra_profile_fields' ); 
//When the current on the current profil has capabilties to show -> display
add_action( 'personal_options_update', 'livetv_save_extra_profile_fields' ); 
//When the current on the current profil is the current and edit -> update

if(!is_admin())
{
//Frontend plugins compatibility
add_action('profile_personal_options', 'livetv_show_extra_profile_fields');
//When the current on the current profil is the current and show -> display
}

//Now construct new profil fields access, based on option of the plugin (default wordpress roles or new roles)
function livetv_show_extra_profile_fields( $user ) 
{
	$userID = $user->ID;

	$access = get_option('livetv_activate_creation_role');
	
	//If based on new roles 
	if($access == 'on')
	{
		$editable_roles = get_roles();
		
		foreach ($editable_roles as $key => $value)
		{ 
			$temp = preg_match("#^live_#", $key);
			
			if($temp == true)
			{
				if (current_user_can(''.$key.''))
				{
					$live_profil = '1';
				}
				else
				{
					$live_profil = '0';
				}
			}
		}
	}
	
	//If based on wordpress role	
	if($access == 'off')
	{
		$defautRole = get_option('livetv_defaut_role_wordpress');
	
		if (current_user_can(''.$defautRole.''))
		{
			$live_profil = '1';
		}
		else
		{
			$live_profil = '0';
		}
	}
	
	//Administrator have already access to all profils
	if (current_user_can('administrator'))
	{
		$live_profil = '1';
	}
	
	//Result to displaying profil fields
	if($live_profil == '1')
	{
		if(is_admin())
		{
			$tableclass = 'widefat options';
		}
		else
		{
			$tableclass = 'form-table';
		} ?>
<table class="<?php echo $tableclass; ?>" style="width: 600px">
  <h3>
    <?php _e('LiveTV general configuration'); ?>
  </h3>
  
    <th colspan="2" class="dashboard-widget-title"><?php _e('Add your channels', 'livetv'); ?></th>
    <?php 
	  	$types = explode('_', get_option('livetv_types_order'));
	  	foreach($types as $key => $type)
		{ ?>
  <tr valign="top">
    <td scope="row"><label>
        <?php _e('add one for', 'livetv'); echo ' '. $type; ?>
      </label></td>
    <td class="livetv-admin-td"><input type="text" style="width:125px;" maxlength="55" name="live_<?php echo $type; ?>" id="live_<?php echo $type; ?>" class="regular-text" />
      <span class="livetv_help" title="<?php _e('Complete this input with the', 'livetv'); if($type == 'own3d'){ echo ' '; _e('channelID', 'livetv');}else{echo ' '; _e('channelName', 'livetv');} echo ' '; _e('to add one', 'livetv'); ?><?php echo ' ' . $type. '.tv '; ?><?php _e('or leave empty to do nothing', 'livetv'); ?>"></span><br /></td>
  </tr>
  <?php } ?>
</table>
<?php 
	
	wp_enqueue_script('livetv-pagination');
	
	$livetv_pagination_limit = get_option('livetv_pagination_limit');
	
	foreach($types as $key => $type)
	{
		$countlive = get_user_meta($userID, 'count_live_'.$type.'', true); 
		
		if($countlive)
		{
			if($livetv_pagination_limit)
			{ 
			?>
<script type="text/javascript">
                /* when document is ready */
                jQuery(document).ready(function($){
                    
                    $("div.livetv-holder-<?php echo $type; ?>").jPages({
                        containerID  : "livetv-container-<?php echo $type; ?>",
                        perPage      : <?php echo esc_html($livetv_pagination_limit); ?>,
                        first        : 'first',
                        last         : 'last',
                        previous	 : 'prev',
                        next	     : 'next'
                    });
                });
             </script>
<?php 
			 } 
			
			global $wpdb;	
			$meta_value = 'live_'.$userID.'_'.$type.'_%';				
			$thumbs = $wpdb->get_results($wpdb->prepare("SELECT meta_key, meta_value FROM $wpdb->usermeta WHERE meta_key LIKE %s", $meta_value));
			
			$total_limit = count($thumbs);
			$limit_to_display = get_option('livetv_pagination_limit');
			
			if($total_limit > $limit_to_display)
			{
			 ?>
<br />
<div class="livetv-holder-<?php echo $type; ?>"></div>
<?php 
			} 
			?>
<br />
<table class="widefat options" style="width: 600px">
  
    <th colspan="2" class="dashboard-widget-title"><?php _e('Your active channels for ', 'livetv'); echo $type; ?>
      <span class="mini-<?php echo esc_html($type); ?>"></span></th>
  <tr valign="top">
    <td scope="row"><label>
        <?php _e('Delete one ?', 'livetv') ?>
      </label></td>
    <td class="livetv-admin-td"><select name="delete_one_<?php echo esc_html(''.$type.''); ?>" id="delete_one_<?php echo esc_html(''.$type.''); ?>">
        <option value="">off</option>
        <?php
			foreach($thumbs as $keythumb => $valuethumb)
			{
			?>
        <option value="<?php echo esc_html($valuethumb->meta_key); ?>"><?php echo esc_html($valuethumb->meta_value); ?></option>
        <?php } ?>
      </select>
      <span class="livetv_help" title="<?php _e('Select one channel to delete and clic on default button &quot;update profil&quot; to delete this selected channel', 'livetv'); ?>"></span><br /></td>
  </tr>
</table>
<table class="widefat options" style="width: 600px">
  <thead>
    <tr>
      <th><?php _e('Counter', 'livetv'); ?></th>
      <th><?php _e('Channel', 'livetv'); ?></th>
      <th><?php _e('Help', 'livetv'); ?></th>
    </tr>
  </thead>
  <tbody id="livetv-container-<?php echo $type; ?>">
    <?php foreach($thumbs as $keythumb => $valuethumb)
			{
			?>
    <tr valign="top">
      <td scope="row"><label>
          <input type="text" style="width:50px;" maxlength="55" disabled="disabled" value="<?php echo esc_html($keythumb + 1); ?>" />
        </label></td>
      <td scope="row"><input type="text" style="width:125px;" maxlength="55" disabled="disabled" name="<?php echo esc_html($valuethumb->meta_value); ?>" value="<?php echo esc_html($valuethumb->meta_value); ?>" id="<?php echo esc_html(''.$valuethumb->meta_key.''); ?>" /></td>
      <td scope="row"><span class="livetv_help" title="<?php _e('This liveTV appears on our site', 'livetv'); ?>"></span></td>
    </tr>
    <?php } ?>
</table>
<?php }}}}

//Profil field update function
function livetv_save_extra_profile_fields( $user_id )
{
	if ( current_user_can( 'edit_user', $user_id ) )
	{

		if(isset($_POST['delete_one_own3d']) && !empty($_POST['delete_one_own3d']))
		{
			$temp = stripslashes($_POST['delete_one_own3d']);
			
			if($temp)
			{
				delete_user_meta( $user_id, ''.$temp.'');
			}
		}
		
		if(isset($_POST['delete_one_justin']) && !empty($_POST['delete_one_justin']))
		{
			$temp = stripslashes($_POST['delete_one_justin']);
			
			if($temp)
			{
				delete_user_meta( $user_id, ''.$temp.'');
			}
		}
		
		if(isset($_POST['delete_one_twitch']) && !empty($_POST['delete_one_twitch']))
		{
			$temp = stripslashes($_POST['delete_one_twitch']);
			
			if($temp)
			{
				delete_user_meta( $user_id, ''.$temp.'');
			}
		}
		
		if(isset($_POST['live_own3d']) && $_POST['live_own3d'] != '')
		{
			
			$countlive = get_user_meta($user_id, 'count_live_own3d', true);
			
			if(!$countlive)
			{
				$countlive = '0';
			}
	
			$count = $countlive + '1';
			
			$limitation = get_option('livetv_limit');
			
			if(empty($limitation) || $countlive < $limitation || current_user_can( 'administrator' ))
			{
				$validation_exclusion = preg_match('#^[a-zA-Z]|^http|/#', $_POST['live_own3d']);
				
				if(!$validation_exclusion)
				{
					update_user_meta( $user_id, 'count_live_own3d', ''.$count.'');
					update_user_meta( $user_id, 'live_'.$user_id.'_own3d_'.$count.'', $_POST['live_own3d'] );
				}
				else
				{
					wp_die(__('Bad request. Complete this input only with the channel ID from Own3d', 'livetv'));
				}
			}
			else
			{
				wp_die(__('You have reached our limit by user to add live streams', 'livetv'));
			}
		}
		
		if(isset($_POST['live_justin']) && $_POST['live_justin'] != '')
		{
			$count = 0;
				
			$countlive = get_user_meta($user_id, 'count_live_justin', true);
			
			if(!$countlive)
			{
				$countlive = '0';
			}
			
			$count = $countlive + '1';
			
			$limitation = get_option('livetv_limit');
			
			if(empty($limitation) || $countlive < $limitation || current_user_can( 'administrator' ))
			{
				$validation_exclusion = preg_match('#^http|/#i', $_POST['live_justin']);
				
				if(!$validation_exclusion)
				{
					update_user_meta( $user_id, 'count_live_justin', ''.$count.'');
					update_user_meta( $user_id, 'live_'.$user_id.'_justin_'.$count.'', $_POST['live_justin'] );
				}
				else
				{
					wp_die(__('Bad request. Complete this input only with the channel Name from Justin', 'livetv'));
				}
			}
			else
			{
				wp_die(__('You have reached our limit by user to add live streams', 'livetv'));
			}
		}
		
		if(isset($_POST['live_twitch']) && $_POST['live_twitch'] != '')
		{
			$count = 0;
				
			$countlive = get_user_meta($user_id, 'count_live_twitch', true);
			
			if(!$countlive)
			{
				$countlive = '0';
			}
			
			$count = $countlive + '1';
			
			$limitation = get_option('livetv_limit');
			
			if(empty($limitation) || $countlive < $limitation || current_user_can( 'administrator' ))
			{
				$validation_exclusion = preg_match('#^http|/#i', $_POST['live_twitch']);
				
				if(!$validation_exclusion)
				{
					update_user_meta( $user_id, 'count_live_twitch', ''.$count.'');
					update_user_meta( $user_id, 'live_'.$user_id.'_twitch_'.$count.'', $_POST['live_twitch'] );
				}
				else
				{
					wp_die(__('Bad request. Complete this input only with the channel Name from Twitch', 'livetv'));
				}
			}
			else
			{
				wp_die(__('You have reached our limit by user to add live streams', 'livetv'));
			}
		}
	}
}
?>
