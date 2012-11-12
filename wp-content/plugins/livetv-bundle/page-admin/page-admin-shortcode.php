<?php
/*  
	Copyright 2012  Laurent (KwarK) Bertrand  (email : kwark@allwebtuts.net)
	
	Please consider a small donation for my work. Behind each code, there is a geek who has to eat.
	This is not because we're geeks that each geek can go without food lol. We are human not a machine lol
	Thank you for my futur bundle...pizza-cola lol. Bundle vs bundle, it's a good deal, no ? 
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
 

// only necessary when the plugin part "shortcode" is active (plugin lvl 1)
function livetv_general_options_page()
{
	add_submenu_page('plugin-livetv-fork.php', 'General shortcode', __('General shortcodes', 'livetv'), 'manage_options', 'page-admin/page-admin-shortcode.php', 'livetv_do_admin_page_level_shortcode');
}
add_action( 'livetv_add_submenu_page', 'livetv_general_options_page'); //plugin api // view plugin-main-fork.php line ~26 for more information



//Do admin options page General config for shortcode (plugin level 1)
function livetv_do_admin_page_level_shortcode()
{
		
    if(isset($_POST['submitted']) && $_POST['submitted'] == "yes")
	{
		
		$livetv_width = stripslashes($_POST['livetv_width']);
		update_option("livetv_width", $livetv_width);
	
		$livetv_height = stripslashes($_POST['livetv_height']);
		update_option("livetv_height", $livetv_height);
		
		$livetv_color = stripslashes($_POST['livetv_color']);
		update_option("livetv_color", $livetv_color);
		
		$livetv_visibility = stripslashes($_POST['livetv_visibility']);
		update_option("livetv_visibility", $livetv_visibility);
		
		$livetv_message = stripslashes($_POST['livetv_message']);
		update_option("livetv_message", $livetv_message);
		
		$livetv_registration = stripslashes($_POST['livetv_registration']);
		update_option("livetv_registration", $livetv_registration);
		
		$livetv_autoplay = stripslashes($_POST['livetv_autoplay']);
		update_option("livetv_autoplay", $livetv_autoplay);
		
		/*	
			dependent of all Globals
			$channel = $current_livestream_id;
			$type = $current_livestream_type;
		*/
		
        echo "<div id=\"message\" class=\"updated fade\"><p><strong>Your settings have been saved.</strong></p></div>";
    }


//Lest go for the html part of admin page for shortcode and main fork
?>

<div class="wrap livetv-admin">
  <div id="icon-upload" class="icon32"><br>
  </div>
  <h2>
    <?php _e('liveTV Team - General configuration', 'livetv'); ?>
  </h2>
  <?php if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'page-admin/page-admin-shortcode.php')
	{ ?> <div id="message" class="updated fade"><p><strong>
    <?php
		_e('For shortcodes, settings of general configuration are sufficient', 'livetv');
	?>
    </strong>
    </p>
    </div>
	<?php } ?>
  <form method="post" action="" class="livetv_admin">
    <p class="submit">
      <input type="submit" name="options_submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
    </p>
    <table class="widefat options" style="width: 650px">
        <th colspan="2" class="dashboard-widget-title"><?php _e('Minimal configurations', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('General default width for all livestreams', 'livetv'); ?>
          </label></td>
        <td><input type="number" style="width:125px;" maxlength="6" name="livetv_width" id="livetv_width" value="<?php echo get_option("livetv_width"); ?>" />
          px<span class="livetv_help" title="<?php _e('Define default width for manual shortcode. This option will not affect livestream page with its mode: Large view.', 'livetv'); ?>"></span><br /></td>
      </tr>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('General default height for all livestreams', 'livetv'); ?>
          </label></td>
        <td><input type="number" style="width:125px;"  maxlength="6" name="livetv_height" id="livetv_height" value="<?php echo get_option("livetv_height"); ?>" />
          px<span class="livetv_help" title="<?php _e('Define default height for manual shortcode. This option will not affect livestream page with its mode: Large view.', 'livetv'); ?>"></span><br /></td>
      </tr>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('General theme for all livestreams', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_color'); ?>
        <td><select name="livetv_color" id="livetv_color">
         <option <?php if($temp == 'dark'){echo 'selected="selected"';} ?> value="dark"><?php _e('Dark', 'livetv'); ?></option>
     	 <option <?php if($temp == 'light'){echo 'selected="selected"';} ?> value="light"><?php _e('Light', 'livetv'); ?></option>
         <option <?php if($temp == 'transparent'){echo 'selected="selected"';} ?> value="transparent"><?php _e('Transparent', 'livetv'); ?></option></select><span class="livetv_help" title="<?php _e('Choose the defaut general style for all elements', 'livetv'); ?>"></span><br /></td>
      </tr>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('General autoplay for all livestreams', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_autoplay'); ?>
        <td><select name="livetv_autoplay" id="livetv_autoplay">
            <option <?php if($temp == 'true'){echo 'selected="selected"';} ?> value="true">On</option>
            <option <?php if($temp == 'false'){echo 'selected="selected"';} ?> value="false">Off</option>
          </select>
          <span class="livetv_help" title="<?php _e('This option turn on or off the autoplay for all livestreams', 'livetv'); ?>"></span> <br /></td>
      </tr>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('General visibility for all livestreams', 'livetv'); ?>
          </label></td>
        <?php $temp = get_option('livetv_visibility'); ?>
        <td><input type="radio" name="livetv_visibility" id="livetv_visibility" value="members only" <?php if($temp == 'members only'){echo 'checked="checked"';} ?> />
          <?php _e('Members only', 'livetv'); ?>
          <span class="livetv_help" title="<?php _e('only members can see livestreams', 'livetv'); ?>"></span> <br />
          <input type="radio" name="livetv_visibility" id="livetv_visibility" value="public" <?php if($temp == 'public'){echo 'checked="checked"';} ?> />
          <?php _e('Public', 'livetv'); ?>
          <span class="livetv_help" title="<?php _e('all visitors can see livestreams', 'livetv'); ?>"></span> <br /></td>
      </tr>
    </table>
    <?php if($temp == 'members only'){ ?>
    <br />
    <table class="widefat options" style="width: 650px">
        <th colspan="2" class="dashboard-widget-title"><?php _e('Define global message and login url for visitors', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('General message for visitors', 'livetv'); ?>
          </label></td>
        <td><input type="text" style="width:125px;"  maxlength="200" name="livetv_message" id="livetv_message" value="<?php echo get_option("livetv_message"); ?>" />
          <span class="livetv_help" title="<?php _e('e.g. Please login to view this livestream', 'livetv'); ?>"></span></td>
      </tr>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('General url to your login page', 'livetv'); ?>
          </label></td>
        <td><input type="text" style="width:125px;"  maxlength="200" name="livetv_registration" id="livetv_registration" value="<?php echo get_option("livetv_registration"); ?>" />
          <span class="livetv_help" title="<?php _e('e.g.', 'livetv'); ?> http(s)://yourdomain.tld/login"></span></td>
      </tr>
    </table>
    <?php } ?>
    <?php if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'page-admin/page-admin-shortcode.php'){ ?>
    <br />
    <table class="widefat options" style="width: 650px">
        <th colspan="2" class="dashboard-widget-title"><?php _e('Exemple of shortcodes with required parameters', 'livetv'); ?></th>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Exemple manual shortcode own3d', 'livetv'); ?>
          </label></td>
        <td><input type="text" style="width:300px;"  maxlength="200" disabled="disabled" value='[livestream type="own3d" channel="channelID"]' />
          <span class="livetv_help" title="<?php _e('may take also these optional parameters  width, height, visibility, message, registration, autoplay', 'livetv'); ?>"></span><br /></td>
      </tr>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Exemple manual shortcode justin', 'livetv'); ?>
          </label></td>
        <td><input type="text" style="width:300px;"  maxlength="200" disabled="disabled" value='[livestream type="justin" channel="channelName"]' />
          <span class="livetv_help" title="<?php _e('may take also these optional parameters  width, height, visibility, message, registration, autoplay', 'livetv'); ?>"></span><br /></td>
      </tr>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Exemple manual shortcode twitch', 'livetv'); ?>
          </label></td>
        <td><input type="text" style="width:300px;"  maxlength="200" disabled="disabled" value='[livestream type="twitch" channel="channelName"]' />
          <span class="livetv_help" title="<?php _e('may take also these optional parameters  width, height, visibility, message, registration, autoplay', 'livetv'); ?>"></span><br /></td>
      </tr>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Exemple manual shortcode livestream', 'livetv'); ?>
          </label></td>
        <td><input type="text" style="width:300px;"  maxlength="200" disabled="disabled" value='[livestream type="livestream" channel="channelName"]' />
          <span class="livetv_help" title="<?php _e('may take also these optional parameters  width, height, visibility, message, registration', 'livetv'); ?>"></span><br /></td>
      </tr>
      <tr valign="top">
        <td scope="row"><label>
            <?php _e('Exemple manual shortcode ustream', 'livetv'); ?>
          </label></td>
        <td><input type="text" style="width:300px;"  maxlength="200" disabled="disabled" value='[livestream type="ustream" channel="channelCID"]' />
          <span class="livetv_help" title="<?php _e('may take also these optional parameters  width, height, visibility, message, registration', 'livetv'); ?>"></span><br /></td>
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
?>
