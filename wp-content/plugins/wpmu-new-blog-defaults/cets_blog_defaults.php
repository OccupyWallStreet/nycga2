<?php
/******************************************************************************************************************
 
Plugin Name: New Blog Defaults (CETS)
Plugin URI:
Description: WordPress Multisite plugin for network admin to set defaults for new blogs. 
Version: 2.2.2
Author: Deanna Schneider, Jason Lemahieu (MadtownLems)

Copyright:

    Copyright 2008 - 2012 CETS

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

            
*******************************************************************************************************************/

class cets_blog_defaults 
{
   
	
    function setup() 
    {
    	
		
	    global $wpdb, $wp_version;
	    
	    //if this is less than wp 3.0, just get out of here.
		if ( version_compare( $wp_version, '3.0', '<' ) ) {
			return;
		
		}
	    
		// Set up the array of potential defaults
		$cets_blogdefaults = array(
			 'default_pingback_flag'=>1,
			 'default_ping_status'=>'open',
			 'default_comment_status'=>'closed',
			 'comments_notify'=>1,
			 'moderation_notify'=>1,
			 'comment_moderation'=>0,
			 'require_name_email' => 1,
			 'comment_whitelist'=>1,
			 'comment_max_links'=>2,
			 'moderation_keys'=>'',
			 'blacklist_keys'=>'',
			 'show_avatars'=> 0,
			 'avatar_rating'=>'G',
			 'blogname' => 'My Blog',
			 'blogname_flag' => 0,
			 'blogdescription' => sprintf(__('Just another %s weblog'), $current_site->site_name ),
			 'comment_registration' => 0,
			 'gmt_offset' => date('Z') / 3600,
			 'timezone_string' => '',
			 'date_format' => __('F j, Y'),
			 'time_format' => __('g:i a'),
			 'start_of_week' => 0,
			 'default_post_edit_rows' => 10,
			 'use_smilies' => 1,
			 'use_balanceTags' => 0,
			 'posts_per_page' => 10,
			 'posts_per_rss' => 10,
			 'rss_use_excerpt' => 0,
			 'blog_charset' => 'UTF-8',
			 'blog_public' => '',
			 'thumbnail_size_w' => 150,
			 'thumbnail_size_h' => 150,
			 'thumbnail_crop' => 1,
			 'medium_size_w' => 300,
			 'medium_size_h' => 300,
			 'permalink_structure' => '/%year%/%monthnum%/%day%/%postname%/',
			 'tag_base' => '',
			 'category_base' => '',
			 'theme' => '',
			 'large_size_w' => 1024,
			 'large_size_h' => 1024,
			 'comment_registration' => 1,
			 'close_comments_for_old_posts' => 0,
			 'close_comments_days_old' => 14,
			 'thread_comments' => 0,
			 'page_comments' => 0,
			 'default_comments_page' => 'newest',
			 'comments_per_page' => 50,
			 'comment_order' => 'desc',
			 'avatar_default' => 'mystery',
			 'from_email' => '',
			 'from_email_name' => '',
			 'delete_blogroll_links' => '1',
			 'default_cat_name' => 'Uncategorized',
			 'default_link_cat' => 'Links',
			 'delete_first_post' => 0,
			 'delete_initial_widgets' => 0,
			 'delete_first_comment' => 0,
			 'default_links' => '',
			 'default_categories' => '',
			 'add_user_to_blog' => 0,
			 'add_user_to_blog_role' => 'subscriber',
			 'add_user_to_blog_id' => 1,
			 'close_comments_on_about_page' => 0,
			 'close_comments_on_hello_world' => 0,
			 'enable_app' => 0,
			 'enable_xmlrpc' => 0,
			 'embed_autourls' => 1,
			 'embed_size_w' => '',
			 'embed_size_h' => 600
			 
			 );
			 
	 	// Add a site option so that we'll know set up ran
		add_site_option( 'cets_blog_defaults_setup', 1 );
		add_site_option( 'cets_blog_defaults_options', $cets_blogdefaults);
		
	    		
    }
	
	

    
    
	function set_blog_defaults($blog_id, $user_id)
    {
		global $wp_rewrite, $wpdb, $current_site;
		
		
		
		switch_to_blog($blog_id);
		
		
		
		// get the site options 
		$options = get_site_option('cets_blog_defaults_options');
		
		
		// check for the blogname_flag and if it's 0, then delete the blogname option
		if ($options['blogname_flag'] == 0 && isset($options['blogname'])) {
			unset($options['blogname']);
		}
		
		// check for the blog_public setting and if it's blank, delete it
		if (strlen($options['blog_public']) == 0) {
			unset($options['blog_public']);
			
		}
		
		// bonus options - set these first and then unset each one so they don't clutter the database
		// Add User to Blog
		if ($options['add_user_to_blog'] == 1) {
			if(strlen($options['add_user_to_blog_id']) == 1) {
				add_user_to_blog( $options['add_user_to_blog_id'], $user_id, $options['add_user_to_blog_role'] );
			}
			elseif (strlen($options['add_user_to_blog_id']) > 1) {
				$blogs = explode(',', $options['add_user_to_blog_id']);
				foreach ($blogs as $blog){
					add_user_to_blog( $blog, $user_id, $options['add_user_to_blog_role'] );
				}
				
			}
			
			
		}
		unset($options['add_user_to_blog']);
		unset($options['add_user_to_blog_role']);
		unset($options['add_user_to_blog_id']);
		
		// Delete Links		
		if ($options['delete_blogroll_links'] == 1) {
			wp_delete_link(1); // documentation
			wp_delete_link(2); // wordpress blog
		 	wp_delete_link(3); //delete suggest ideas
    		wp_delete_link(4); //delete support forum
    		wp_delete_link(5); //delete plugins
    		wp_delete_link(6); //delete themes
    		wp_delete_link(7); //delete wp planet
		}
		unset($options['delete_blogroll_links']);
		
		// Default Category Name
		if (strlen($options['default_cat_name']) > 0){
			global $wpdb;
			$cat = $options['default_cat_name'];
			$slug = str_replace(' ', '-', strtolower($cat)); 
			$results = $wpdb->query( $wpdb->prepare("UPDATE $wpdb->terms SET name = %s, slug = %s  WHERE term_id = 1", $cat, $slug ) );	
			
		}
		unset($options['default_cat_name']);
		
		// Default Link Category
		if (strlen($options['default_link_cat']) > 0){
			global $wpdb;
			$cat = $options['default_link_cat'];
			$slug = str_replace(' ', '-', strtolower($cat)); 
			$results = $wpdb->query( $wpdb->prepare("UPDATE $wpdb->terms SET name = %s, slug = %s  WHERE term_id = 2", $cat, $slug ) );	
			
		}
		unset($options['default_link_cat']);
		
		//Delete First Comment
		if (isset($options['delete_first_comment']) && $options['delete_first_comment'] == 1){
			wp_delete_comment( 1 );
			
		}
		unset($options['delete_first_comment']);
		
		// Close Comments on Hello World
		if (isset($options['close_comments_on_hello_world']) && $options['close_comments_on_hello_world'] == 1){
			global $wpdb;
			$statement = "UPDATE $wpdb->posts SET comment_status = 'closed'  WHERE id = 1";
			
			$results = $wpdb->query( $statement );
			
			
		}
		unset($options['close_comments_on_hello_world']);
		
		// Close Comments on About Page
		if (isset($options['close_comments_on_about_page']) && $options['close_comments_on_about_page'] == 1){
			global $wpdb;
			$statement = "UPDATE $wpdb->posts SET comment_status = 'closed'  WHERE id = 2";
			
			$results = $wpdb->query( $statement );
			
			
		}
		unset($options['close_comments_on_about_page']);
		
		
		// Delete First Post
		if (isset($options['delete_first_post']) && $options['delete_first_post'] == 1){
			global $wpdb;
			$statement = "UPDATE $wpdb->posts SET post_status = 'draft'  WHERE id = 1";
			
			$results = $wpdb->query( $statement );
			
			
		}
		unset($options['delete_first_post']);
		
		// Delete Initial Widgets
		if (isset($options['delete_initial_widgets']) && $options['delete_initial_widgets'] == 1){
			delete_option('sidebars_widgets');
		}
		unset($options['delete_initial_widgets']);
		
		
		// Add Default Links
		if (isset($options['default_links']) && strlen($options['default_links'])){
			
			
			
			$links = explode("|+", $options['default_links']);
			
			foreach ($links as $link) {
				$thislink = explode('=>', $link);
				wp_insert_link(array('link_name' => $thislink[0], 'link_url' => $thislink[1]));
				
			}
			
		}
		unset($options['default_links']);
		
		// Add Default Categories
		if (isset($options['default_categories']) && strlen($options['default_categories'])){
			$cats = explode("|+", $options['default_categories']);
			
			
			foreach ($cats as $cat) {
				$thiscat = explode('=>', $cat);
				// get the id of the parent category
				$parentid = category_exists($thiscat[3]);
				
				$cat_array = array('cat_name'=>$thiscat[0], 'category_description'=>$thiscat[1], 'category_nicename'=> $thiscat[2], 'category_parent' => $parentid );
				
				
				
				wp_insert_category($cat_array, true);			
								
			}
			
		}
		unset($options['default_categories']);
		
		
		// deal with the themes
		if (! empty($options['theme'])) {
			// we want something other than the default theme
			$values= explode("|", $options['theme']);
			switch_theme($values[0], $values[1]);		
			//this is weird, but it would appear there might be a bug with switch theme in that it doesn't switch the current_theme. But if we kill it then the code runs to reset it.
			update_option('current_theme', '');
			
		
		}	
		
		unset($options['theme']);
		
		
		// End Bonus Options - now process all the "normal" options.
		foreach($options as $key => $value) {
			// update all the options (we know they should all be set because this runs after populate_options()
			update_option($key, $value);	
		}
		
		// run through the permalink options here and set the blog to use them
		if ( isset($options['permalink_structure']) || isset($options['category_base']) || isset($options['tag_base']) ) {
				$details = $wpdb->get_results( "SELECT * FROM {$wpdb->blogs} WHERE blog_id = $blog_id");
					if ( isset($options['permalink_structure']) ) {
						$permalink_structure = $options['permalink_structure'];
						if (! empty($permalink_structure) )
							$permalink_structure = preg_replace('#/+#', '/', '/' . $options['permalink_structure']);
						if( constant( 'VHOST' ) == 'no' && $permalink_structure != '' && $current_site->domain.$current_site->path == $details->domain.$details->path ) {
							$permalink_structure = '/blog' . $permalink_structure;
						}
						$wp_rewrite->set_permalink_structure($permalink_structure);
					}
				
					if ( isset($options['category_base']) ) {
						$category_base = $options['category_base'];
						if (! empty($category_base) )
							$category_base = preg_replace('#/+#', '/', '/' . $options['category_base']);
						if( constant( 'VHOST' ) == 'no' && $category_base != '' && $current_site->domain.$current_site->path == $details->domain.$details->path ) {
							$category_base = '/blog' . $category_base;
						}
						$wp_rewrite->set_category_base($category_base);
					}
				
					if ( isset($options['tag_base']) ) {
						$tag_base = $options['tag_base'];
						if (! empty($tag_base) )
							$tag_base = preg_replace('#/+#', '/', '/' . $options['tag_base']);
						if( constant( 'VHOST' ) == 'no' && $tag_base != '' && $current_site->domain.$current_site->path == $details->domain.$details->path ) {
							$tag_base = '/blog' . $tag_base;
						}
						$wp_rewrite->set_tag_base($tag_base);
					}
				}
				
				$permalink_structure = get_option('permalink_structure');
				$category_base = get_option('category_base');
				$tag_base = get_option( 'tag_base' );
				
				if ( (!file_exists($home_path.'.htaccess') && is_writable($home_path)) || is_writable($home_path.'.htaccess') )
					$writable = true;
				else
					$writable = false;
				
				if ($wp_rewrite->using_index_permalinks())
					$usingpi = true;
				else
					$usingpi = false;
				
				$wp_rewrite->flush_rules();
				// shouldn't have to do this - it should happen in the above code. But, maybe forcing it would work?
				update_option('rewrite_rules','');
			
			
		// end permalink mucking about
		
		
		
		
		restore_current_blog();
		
		
    	
    }
	

	

	
	
	
	
	
	function update_defaults($ff){
		// create an array to hold the chosen options
		$newoptions = array();
		$newoptions['default_pingback_flag'] = ($_POST['default_pingback_flag'] == 1) ? 1 : 0;
		$newoptions['default_ping_status'] = ($_POST['default_ping_status'] == 'open') ? 'open' : 'closed';
		$newoptions['default_comment_status'] = ($_POST['default_comment_status'] == 'open') ? 'open' : 'closed';
		$newoptions['comments_notify'] = ($_POST['comments_notify'] == 1) ? 1 : 0;
		$newoptions['moderation_notify'] = ($_POST['moderation_notify'] == 1) ? 1 : 0;
		$newoptions['comment_moderation'] = ($_POST['comment_moderation'] == 1) ? 1 : 0;
		$newoptions['require_name_email'] = ($_POST['require_name_email'] == 1) ? 1 : 0;
		$newoptions['comment_whitelist'] = ($_POST['comment_whitelist'] == 1) ? 1 : 0;
		$newoptions['comment_max_links'] = $_POST['comment_max_links'];
		$newoptions['moderation_keys'] = $_POST['moderation_keys'];
		$newoptions['blacklist_keys'] = $_POST['blacklist_keys'];
		$newoptions['show_avatars'] = $_POST['show_avatars'];
		$newoptions['avatar_rating'] = $_POST['avatar_rating'];
		$newoptions['blogname'] = $_POST['blogname'];
		if ($_POST['blogname_flag'] == 1) {
		$newoptions['blogname_flag'] = 1; }
		else {$newoptions['blogname_flag'] = 0;}
		$newoptions['blogdescription'] = $_POST['blogdescription'];
		if ($_POST['comment_registration'] == 1) {
		$newoptions['comment_registration'] = 1; }
		else {$newoptions['comment_registration'] = 0;}
		
		if ( !empty($_POST['timezone_string']) && preg_match('/^UTC[+-]/', $_POST['timezone_string']) ) {
			$_POST['gmt_offset'] = $_POST['timezone_string'];
			$_POST['gmt_offset'] = preg_replace('/UTC\+?/', '', $_POST['gmt_offset']);
			$_POST['timezone_string'] = '';
		}
		$newoptions['gmt_offset'] = $_POST['gmt_offset'];
		$newoptions['timezone_string'] = $_POST['timezone_string'];



		if ($_POST['date_format'] == 'custom') {
			$newoptions['date_format'] = $_POST['date_format_custom'];
		}
		else {
			$newoptions['date_format'] = $_POST['date_format'];
		}
		if ($_POST['time_format'] == 'custom'){
			$newoptions['time_format'] = $_POST['time_format_custom'];
		}
		else{
			$newoptions['time_format'] = $_POST['time_format'];
		}
		
		$newoptions['start_of_week'] = $_POST['start_of_week'];
		$newoptions['default_post_edit_rows'] = $_POST['default_post_edit_rows'];
		if ($_POST['use_smilies'] == 1) {
		$newoptions['use_smilies'] = 1; }
		else {$newoptions['use_smiles'] = 0;}
		if ($_POST['use_balanceTags'] == 1) {
		$newoptions['use_balanceTags'] = 1; }
		else {$newoptions['use_balanceTags'] = 0;}
		$newoptions['posts_per_page'] = $_POST['posts_per_page'];
		$newoptions['posts_per_rss'] = $_POST['posts_per_rss'];
		$newoptions['rss_use_excerpt'] = $_POST['rss_use_excerpt'];
		$newoptions['blog_charset'] = $_POST['blog_charset'];
		$newoptions['blog_public'] = $_POST['blog_public'];
		$newoptions['thumbnail_size_w'] = $_POST['thumbnail_size_w'];
		$newoptions['thumbnail_size_h'] = $_POST['thumbnail_size_h'];
		if ($_POST['thumbnail_crop'] == 1) {
		$newoptions['thumbnail_crop'] = 1; }
		else {$newoptions['thumbnail_crop'] = 0;}
		$newoptions['medium_size_w'] = $_POST['medium_size_w'];
		$newoptions['medium_size_h'] = $_POST['medium_size_h'];
		if ($_POST['permalink_choice'] != 'custom') {
		$newoptions['permalink_structure'] = $_POST['permalink_choice'];
		}
		else{
		$newoptions['permalink_structure'] = $_POST['permalink_structure'];
		}
		$newoptions['category_base'] = $_POST['category_base'];
		$newoptions['tag_base'] = $_POST['tag_base'];
		$newoptions['theme'] = $_POST['theme'];
		
		// 2.7 options
		$newoptions['large_size_h'] = $_POST['large_size_h'];
		$newoptions['large_size_w'] = $_POST['large_size_w'];
		$newoptions['comment_registration'] = $_POST['comment_registration'];
		$newoptions['close_comments_for_old_posts'] = $_POST['close_comments_for_old_posts'];
		$newoptions['close_comments_days_old'] = $_POST['close_comments_days_old'];
		$newoptions['thread_comments'] = $_POST['thread_comments'];
		$newoptions['thread_comments_depth'] = $_POST['thread_comments_depth'];
		$newoptions['page_comments'] = $_POST['page_comments'];
		$newoptions['default_comments_page'] = $_POST['default_comments_page'];
		$newoptions['comments_per_page'] = $_POST['comments_per_page'];
		$newoptions['comment_order'] = $_POST['comment_order'];
		$newoptions['avatar_default'] = $_POST['avatar_default'];
		
		
			
		// bonus options
		 $newoptions['from_email'] = $_POST['from_email'];
		 $newoptions['from_email_name'] = $_POST['from_email_name'];	
		 if ($_POST['delete_blogroll_links'] == 1) {
		$newoptions['delete_blogroll_links'] = 1; }
		else {$newoptions['delete_blogroll_links'] = 0;}
		$newoptions['default_cat_name'] = $_POST['default_cat_name'];
		$newoptions['default_link_cat'] = $_POST['default_link_cat'];
	
	if ($_POST['delete_first_post'] == 1) {
		$newoptions['delete_first_post'] = 1; }
		else {$newoptions['delete_first_post'] = 0;}
		
		if ($_POST['delete_initial_widgets'] == 1) {
		$newoptions['delete_initial_widgets'] = 1; }
		else {$newoptions['delete_initial_widgets'] = 0;}
	
		if ($_POST['delete_first_comment'] == 1) {
		$newoptions['delete_first_comment'] = 1; }
		else {$newoptions['delete_first_comment'] = 0;}
		
		if ($_POST['add_user_to_blog'] == 1) {
		$newoptions['add_user_to_blog'] = 1; }
		else {$newoptions['add_user_to_blog'] = 0;}
		
		$newoptions['add_user_to_blog_role'] = $_POST['add_user_to_blog_role'];
		$newoptions['add_user_to_blog_id'] = $_POST['add_user_to_blog_id'];
		
		if ($_POST['close_comments_on_hello_world'] == 1) {
		$newoptions['close_comments_on_hello_world'] = 1; }
		else {$newoptions['close_comments_on_hello_world'] = 0;}
		
		if ($_POST['close_comments_on_about_page'] == 1) {
		$newoptions['close_comments_on_about_page'] = 1; }
		else {$newoptions['close_comments_on_about_page'] = 0;}
		
		$newoptions['default_links'] = str_replace("\n", "|+", $_POST['default_links']);
		$newoptions['default_categories'] = str_replace("\n", "|+", $_POST['default_categories']);
		
		//2.9
		$newoptions['enable_app'] = ($_POST['enable_app'] == 1) ? 1 : 0;
		$newoptions['enable_xmlrpc'] = ($_POST['enable_xmlrpc'] == 1) ? 1 : 0;
		$newoptions['embed_autourls'] = ($_POST['embed_autourls'] == 1) ? 1 : 0;
		$newoptions['embed_size_w'] = $_POST['embed_size_w'];
		$newoptions['embed_size_h'] = $_POST['embed_size_h'];
		
		
		
		
		
		// override the site option
		update_site_option ('cets_blog_defaults_options', $newoptions); 
		
		$options = get_site_option('cets_blog_defaults_options');
		
					
	}
    
   //Add the site-wide administrator menu  
	function add_siteadmin_page(){
			
	 // don't restrict this to site admins, because it throws an error if non site admins go to the URL. Instead, control it wtih the site admin test at the next level
		
		if (function_exists('is_network_admin')) {
			//3.1+
			 add_submenu_page('settings.php', 'New Blog Defaults', 'New Blog Defaults', 'manage_sites', 'cets_blog_defaults_management_page', array(&$this, 'cets_blog_defaults_management_page'));
		} else {
			//-3.1
			 add_submenu_page('ms-admin.php', 'New Blog Defaults', 'New Blog Defaults', 'manage_sites', 'cets_blog_defaults_management_page', array(&$this, 'cets_blog_defaults_management_page'));
		}

	 }
	 
	 
	 
	 
	 function cets_blog_defaults_management_page(){
	 	// Display the defaults that can be set by site admins
	 
	 	global $wpdb;
		
		// only allow site admins to come here.
		if( is_super_admin() == false ) {
			wp_die( __('You do not have permission to access this page.') );
		}
		
		/* translators: date and time format for exact current time, mainly about timezones, see http://php.net/date */
		$timezone_format = _x('Y-m-d G:i:s', 'timezone date format');
		
		
		// process form submission    	
    	if (isset($_POST['action']) && $_POST['action'] == 'update') {
			$this->update_defaults($_POST);
			$updated = true;
    	}
		
		// make sure we're using latest data
		$opt = get_site_option('cets_blog_defaults_options');
		
    	if (isset($updated) && $updated) { ?>
        <div id="message" class="updated fade"><p><?php _e('Options saved.') ?></p></div>
        <?php	} ?>
        
        <h1>New Blog Defaults</h1>
        <form name="blogdefaultsform" action="" method="post">
        <p>Set the defaults for new blog creation. Note that these defaults can still be over-ridden by blog owners.</p>
        <div class="wrap">
        <h2><?php _e('General Settings') ?></h2>
        <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e('Blog Title') ?></th>
        <td><input name="blogname" type="text" id="blogname" value="<?php echo($opt['blogname']); ?>" size="40" /><br/>
        <input type="checkbox" name="blogname_flag" value="1" <?php checked('1', $opt[blogname_flag]) ?> /> <?php _e("I understand this will overwrite the user's chosen blog name from the setup page.") ?></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php _e('Tagline') ?></th>
        <td><input name="blogdescription" type="text" id="blogdescription" style="width: 95%" value="<?php echo($opt['blogdescription']); ?>" size="45" />
        <br />
        <?php _e('In a few words, explain what this blog is about.') ?></td>
        </tr>
		
<!-- Begin Time Zone -->
		<tr>
		<?php
		$current_offset = $opt['gmt_offset'];
		$tzstring = $opt['timezone_string'];
		
		$check_zone_info = true;
		
		// Remove old Etc mappings.  Fallback to gmt_offset.
		if ( false !== strpos($tzstring,'Etc/GMT') )
			$tzstring = '';
		
		if (empty($tzstring)) { // set the Etc zone if no timezone string exists
			$check_zone_info = false;
			if ( 0 == $current_offset )
				$tzstring = 'UTC+0';
			elseif ($current_offset < 0)
				$tzstring = 'UTC' . $current_offset;
			else
				$tzstring = 'UTC+' . $current_offset;
		}
		
		?>
		<th scope="row"><label for="timezone_string"><?php _e('Timezone') ?></label></th>
		<td>
		
		<select id="timezone_string" name="timezone_string">
		<?php echo wp_timezone_choice($tzstring); ?>
		</select>
		
		    <span id="utc-time"><?php printf(__('<abbr title="Coordinated Universal Time">UTC</abbr> time is <code>%s</code>'), date_i18n($timezone_format, false, 'gmt')); ?></span>
		<?php if ($opt['timezone_string']) : ?>
			<span id="local-time"><?php printf(__('Local time is <code>%1$s</code>'), date_i18n($timezone_format)); ?></span>
		<?php endif; ?>
		<br />
		<span class="description"><?php _e('Choose a city in the same timezone as you.'); ?></span>
		<br />
		<span>
		<?php if ($check_zone_info && $tzstring) : ?>
			<?php
			$now = localtime(time(),true);
			if ($now['tm_isdst']) _e('This timezone is currently in daylight savings time.');
			else _e('This timezone is currently in standard time.');
			?>
			<br />
			<?php
			
			if (function_exists('timezone_transitions_get')) {
				$dateTimeZoneSelected = new DateTimeZone($tzstring);
				foreach (timezone_transitions_get($dateTimeZoneSelected) as $tr) {
					if ($tr['ts'] > time()) {
					    $found = true;
						break;
					}
				}
		
				if ( isset($found) && $found === true ) {
					echo ' ';
					$message = $tr['isdst'] ?
						__('Daylight savings time begins on: <code>%s</code>.') :
						__('Standard time begins  on: <code>%s</code>.');
					printf( $message, date_i18n($opt['date_format'].' '. $opt['time_format'], $tr['ts'] ) );
				} else {
					_e('This timezone does not observe daylight savings time.');
				}
			}
			
			?>
			</span>
		<?php endif; ?>
		</td>
		
		
		</tr>

<!-- End Time Zone -->
	
	
	
		
        <tr>
        <th scope="row"><?php _e('Date Format') ?></th>
        <td>
			<fieldset><legend class="screen-reader-text"><span><?php _e('Date Format') ?></span></legend>
<?php

	$date_formats = apply_filters( 'date_formats', array(
		__('F j, Y'),
		'Y/m/d',
		'm/d/Y',
		'd/m/Y',
	) );

	$custom = TRUE;

	foreach ( $date_formats as $format ) {
		echo "\t<label title='" . esc_attr($format) . "'><input type='radio' name='date_format' value='" . esc_attr($format) . "'";
		if ( $opt['date_format'] === $format ) { // checked() uses "==" rather than "==="
			echo " checked='checked'";
			$custom = FALSE;
		}
		echo ' /> ' . date_i18n( $format ) . "</label><br />\n";
	}

	echo '	<label><input type="radio" name="date_format" id="date_format_custom_radio" value="custom"';
	checked( $custom );
	echo '/> ' . __('Custom:') . ' </label><input type="text" name="date_format_custom" value="' . esc_attr( $opt['date_format'] ) . '" class="small-text" /> ' . date_i18n( $opt['date_format'] ) . "\n";
	

	echo "\t<p>" . __('<a href="http://codex.wordpress.org/Formatting_Date_and_Time">Documentation on date formatting</a>. Click &#8220;Save Changes&#8221; to update sample output.') . "</p>\n";
?>
	</fieldset>


		</td>
        </tr>
        <tr>
        <th scope="row"><?php _e('Time Format') ?></th>
        <td>
		<fieldset><legend class="screen-reader-text"><span><?php _e('Time Format') ?></span></legend>
		<?php
		 
			$time_formats = apply_filters( 'time_formats', array(
				__('g:i a'),
				'g:i A',
				'H:i',
			) );
		
			$custom = TRUE;
		
			foreach ( $time_formats as $format ) {
				echo "\t<label title='" . esc_attr($format) . "'><input type='radio' name='time_format' value='" . esc_attr($format) . "'";
				if ( $opt['time_format'] === $format ) { // checked() uses "==" rather than "==="
					echo " checked='checked'";
					$custom = FALSE;
				}
				echo ' /> ' . date_i18n( $format ) . "</label><br />\n";
			}
		
			echo '	<label><input type="radio" name="time_format" id="time_format_custom_radio" value="custom"';
			checked( $custom );
			echo '/> ' . __('Custom:') . ' </label><input type="text" name="time_format_custom" value="' . esc_attr( $opt['time_format'] ) . '" class="small-text" /> ' . date_i18n( $opt['time_format'] ) . "\n";
		?>
			</fieldset>



		</td>
        </tr>
        <tr>
        <th scope="row"><?php _e('Week Starts On') ?></th>
        <td>
        
        <select name="start_of_week" id="start_of_week">
        <?php
		global $wp_locale;
        for ($day_index = 0; $day_index <= 6; $day_index++) :
            $selected = ($opt['start_of_week'] == $day_index) ? 'selected="selected"' : '';
			
            echo "\n\t<option value='$day_index' $selected>" . $wp_locale->get_weekday($day_index) . '</option>';
        endfor;
        ?>
        </select></td>
        </tr>
        </table>
        </div>
        <p>&nbsp;</p>
        <div class="wrap">
        <h2><?php _e('Writing Settings') ?></h2>
        <table class="form-table">
        <tr valign="top">
        <th scope="row"> <?php _e('Size of the post box') ?></th>
        <td><input name="default_post_edit_rows" type="text" id="default_post_edit_rows" value="<?php echo($opt['default_post_edit_rows']); ?>" size="3" />
        <?php _e('lines') ?></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php _e('Formatting') ?></th>
        <td>
        <label for="use_smilies">
        <input name="use_smilies" type="checkbox" id="use_smilies" value="1" <?php checked('1', $opt['use_smilies']); ?> />
        <?php _e('Convert emoticons like <code>:-)</code> and <code>:-P</code> to graphics on display') ?></label><br />
        <label for="use_balanceTags"><input name="use_balanceTags" type="checkbox" id="use_balanceTags" value="1" <?php checked('1', $opt['use_balanceTags']); ?> /> <?php _e('WordPress should correct invalidly nested XHTML automatically') ?></label>
        </td>
        </tr>
        
        </table>
		
		<h3><?php _e('Remote Publishing') ?></h3>
		<p><?php printf(__('To post to WordPress from a desktop blogging client or remote website that uses the Atom Publishing Protocol or one of the XML-RPC publishing interfaces you must enable them below.')) ?></p>
		<table class="form-table">
		<tr valign="top">
		<th scope="row"><?php _e('Atom Publishing Protocol') ?></th>
		<td><fieldset><legend class="screen-reader-text"><span><?php _e('Atom Publishing Protocol') ?></span></legend>
		<label for="enable_app">
		<input name="enable_app" type="checkbox" id="enable_app" value="1" <?php checked('1', $opt['enable_app']); ?> />
		<?php _e('Enable the Atom Publishing Protocol.') ?></label><br />
		</fieldset></td>
		</tr>
		<tr valign="top">
		<th scope="row"><?php _e('XML-RPC') ?></th>
		<td><fieldset><legend class="screen-reader-text"><span><?php _e('XML-RPC') ?></span></legend>
		<label for="enable_xmlrpc">
		<input name="enable_xmlrpc" type="checkbox" id="enable_xmlrpc" value="1" <?php checked('1', $opt['enable_xmlrpc']); ?> />
		<?php _e('Enable the WordPress, Movable Type, MetaWeblog and Blogger XML-RPC publishing protocols.') ?></label><br />
		</fieldset></td>
		</tr>
		</table>
      </div>
      
      <p>&nbsp;</p>
      <div class="wrap">
        <h2><?php _e('Reading Settings') ?></h2>
        <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e('Blog pages show at most') ?></th>
        <td>
        <input name="posts_per_page" type="text" id="posts_per_page" value="<?php echo($opt['posts_per_page']); ?>" size="3" /> <?php _e('posts') ?>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php _e('Syndication feeds show the most recent') ?></th>
        <td><input name="posts_per_rss" type="text" id="posts_per_rss" value="<?php echo($opt['posts_per_rss']); ?>" size="3" /> <?php _e('posts') ?></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php _e('For each article in a feed, show') ?> </th>
        <td>
        <p><label><input name="rss_use_excerpt"  type="radio" value="0" <?php checked(0, $opt['rss_use_excerpt']); ?>	/> <?php _e('Full text') ?></label><br />
        <label><input name="rss_use_excerpt" type="radio" value="1" <?php checked(1, $opt['rss_use_excerpt']); ?> /> <?php _e('Summary') ?></label></p>
        </td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e('Encoding for pages and feeds') ?></th>
        <td><input name="blog_charset" type="text" id="blog_charset" value="<?php echo($opt['blog_charset']); ?>" size="20" class="code" /><br />
        <?php _e('The character encoding you write your blog in (UTF-8 is <a href="http://developer.apple.com/documentation/macos8/TextIntlSvcs/TextEncodingConversionManager/TEC1.5/TEC.b0.html">recommended</a>)') ?></td>
        </tr>
        </table>
        
        </div>
        
        
        <p>&nbsp;</p>
        <div class="wrap">     
    	<h2>Discussion Settings</h2>
        <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e('Default article settings') ?></th>
        <td>
         <label for="default_pingback_flag">
		 
       <input name="default_pingback_flag" type="checkbox" id="default_pingback_flag" value="1" <?php  if ($opt['default_pingback_flag'] == 1) echo('checked="checked"'); ?> /> <?php _e('Attempt to notify any blogs linked to from the article (slows down posting.)') ?> </label>
       
        <br /> 
		<label for="default_ping_status">
		
        <input name="default_ping_status" type="checkbox" id="default_ping_status" value="open" <?php if ($opt['default_ping_status'] == 'open') echo('checked="checked"'); ?> /> <?php _e('Allow link notifications from other blogs (pingbacks and trackbacks.)') ?></label>
       
        <br />
        <label for="default_comment_status">
		
        <input name="default_comment_status" type="checkbox" id="default_comment_status" value="open" <?php if ($opt['default_comment_status'] == 'open') echo('checked="checked"'); ?> /> <?php _e('Allow people to post comments on the article') ?></label>
    
        <br />
        <small><em><?php echo '(' . __('These settings may be overridden for individual articles.') . ')'; ?></em></small>
        </td>
        </tr>
		<tr valign="top">
		<th scope="row"><?php _e('Other comment settings') ?></th>
		<td><fieldset><legend class="hidden"><?php _e('Other comment settings') ?></legend>
		
		<label for="require_name_email">
		
        <input type="checkbox" name="require_name_email" id="require_name_email" value="1" <?php if ($opt['require_name_email'] == 1) echo('checked="checked"'); ?> /> <?php _e('Comment author must fill out name and e-mail') ?></label>
		<br />
		<label for="comment_registration">
		<input name="comment_registration" type="checkbox" id="comment_registration" value="1" <?php checked('1', $opt['comment_registration']); ?> />
		<?php _e('Users must be registered and logged in to comment') ?>
		</label>
		<br />
		
		<label for="close_comments_for_old_posts">
		<input name="close_comments_for_old_posts" type="checkbox" id="close_comments_for_old_posts" value="1" <?php checked('1', $opt['close_comments_for_old_posts']); ?> />
		<?php printf( __('Automatically close comments on articles older than %s days'), '</label><input name="close_comments_days_old" type="text" id="close_comments_days_old" value="' . esc_attr($opt['close_comments_days_old']) . '" class="small-text" />') ?>
		<br />
		<label for="thread_comments">
		<input name="thread_comments" type="checkbox" id="thread_comments" value="1" <?php checked('1', $opt['thread_comments']); ?> />
		<?php
		
		$maxdeep = (int) apply_filters( 'thread_comments_depth_max', 10 );
		
		
		
		$thread_comments_depth = '</label><select name="thread_comments_depth" id="thread_comments_depth">';
		for ( $i = 1; $i <= $maxdeep; $i++ ) {
			$thread_comments_depth .= "<option value='$i'";
			if (isset($opt['thread_comments_depth']) && $opt['thread_comments_depth'] == $i ) $thread_comments_depth .= " selected='selected'";
			$thread_comments_depth .= ">$i</option>";
		}
		$thread_comments_depth .= '</select>';
		
		printf( __('Enable threaded (nested) comments %s levels deep'), $thread_comments_depth );
		
		?><br />
		<label for="page_comments">
		<input name="page_comments" type="checkbox" id="page_comments" value="1" <?php checked('1', $opt['page_comments']); ?> />
		<?php
		
		
		$default_comments_page = '</label><label for="default_comments_page"><select name="default_comments_page" id="default_comments_page"><option value="newest"';
		if ( isset($opt['default_comments_page']) && 'newest' == $opt['default_comments_page'] ) $default_comments_page .= ' selected="selected"';
		$default_comments_page .= '>' . __('last') . '</option><option value="oldest"';
		if ( 'oldest' == $opt['default_comments_page'] ) $default_comments_page .= ' selected="selected"';
		$default_comments_page .= '>' . __('first') . '</option></select>';
		
		printf( __('Break comments into pages with %1$s comments per page and the %2$s page displayed by default'), '</label><label for="comments_per_page"><input name="comments_per_page" type="text" id="comments_per_page" value="' . esc_attr($opt['comments_per_page']) . '" class="small-text" />', $default_comments_page );
		
		?></label>
		<br />
		<label for="comment_order"><?php
		
		$comment_order = '<select name="comment_order" id="comment_order"><option value="asc"';
		if ( 'asc' == $opt['comment_order'] ) $comment_order .= ' selected="selected"';
		$comment_order .= '>' . __('older') . '</option><option value="desc"';
		if ( 'desc' == $opt['comment_order'] ) $comment_order .= ' selected="selected"';
		$comment_order .= '>' . __('newer') . '</option></select>';
		
		printf( __('Comments should be displayed with the %s comments at the top of each page'), $comment_order );
		
		?></label>
		</fieldset></td>
		</tr>
		
		
		
		
        <tr valign="top">
        <th scope="row"><?php _e('E-mail me whenever') ?></th>
        <td>
		<label for="comments_notify">
		
        <input name="comments_notify" type="checkbox" id="comments_notify" value="1" <?php if ($opt['comments_notify'] == 1 ) echo('checked="checked"'); ?> /> <?php _e('Anyone posts a comment') ?> </label>
         
        <br />
		<label for="moderation_notify">
		
        <input name="moderation_notify" type="checkbox" id="moderation_notify" value="1" <?php if ($opt['moderation_notify'] == 1) echo('checked="checked"'); ?> /> <?php _e('A comment is held for moderation') ?></label>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php _e('Before a comment appears') ?></th>
        <td>
		<label for="comment_moderation">
		
        <input name="comment_moderation" type="checkbox" id="comment_moderation" value="1" <?php if ($opt['comment_moderation'] == 1) echo('checked="checked"'); ?> /> <?php _e('An administrator must always approve the comment') ?></label>
    
        
        
        <br />
		<label for="comment_whitelist">
        <input type="checkbox" name="comment_whitelist" id="comment_whitelist" value="1" <?php if ($opt['comment_whitelist'] == 1) echo('checked="checked"'); ?> /> <?php _e('Comment author must have a previously approved comment') ?></label>
       
        </td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php _e('Comment Moderation') ?></th>
        <td>
        <p><?php printf(__('Hold a comment in the queue if it contains %s or more links. (A common characteristic of comment spam is a large number of hyperlinks.)'), '<input name="comment_max_links" type="text" id="comment_max_links" size="3" value="' . $opt['comment_max_links']. '" />' ) ?></p>
        
        <p><?php _e('When a comment contains any of these words in its content, name, URL, e-mail, or IP, it will be held in the <a href="edit-comments.php?comment_status=moderated">moderation queue</a>. One word or IP per line. It will match inside words, so "press" will match "WordPress".') ?></p>
        <p>
        <textarea name="moderation_keys" cols="60" rows="10" id="moderation_keys" style="width: 98%; font-size: 12px;" class="code"><?php echo($opt['moderation_keys']); ?></textarea>
        </p>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php _e('Comment Blacklist') ?></th>
        <td>
        <p><?php _e('When a comment contains any of these words in its content, name, URL, e-mail, or IP, it will be marked as spam. One word or IP per line. It will match inside words, so "press" will match "WordPress".') ?></p>
        <p>
        <textarea name="blacklist_keys" cols="60" rows="10" id="blacklist_keys" style="width: 98%; font-size: 12px;" class="code"><?php echo($opt['blacklist_keys']); ?></textarea>
        </p>
        </td>
        </tr>
        </table>
        
        <h3><?php _e('Avatars') ?></h3>

        <p><?php _e('By default WordPress uses <a href="http://gravatar.com/">Gravatars</a> &#8212; short for Globally Recognized Avatars &#8212; for the pictures that show up next to comments. Plugins may override this.'); ?></p>
        
        <?php // the above would be a good place to link to codex documentation on the gravatar functions, for putting it in themes. anything like that? ?>
        
        <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e('Avatar display') ?></th>
        <td>
        <?php
            $yesorno = array(0 => __("Don&#8217;t show Avatars"), 1 => __('Show Avatars'));
            foreach ( $yesorno as $key => $value) {
                $selected = ($opt['show_avatars'] == $key) ? 'checked="checked"' : '';
                echo "\n\t<label><input type='radio' name='show_avatars' value='$key' $selected> $value</label><br />";
            }
        ?>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php _e('Maximum Rating') ?></th>
        <td>
        
        <?php
        $ratings = array( 'G' => __('G &#8212; Suitable for all audiences'), 'PG' => __('PG &#8212; Possibly offensive, usually for audiences 13 and above'), 'R' => __('R &#8212; Intended for adult audiences above 17'), 'X' => __('X &#8212; Even more mature than above'));
        foreach ($ratings as $key => $rating) :
          	$selected = ($opt['avatar_rating'] == $key) ? 'checked="checked"' : '';
            echo "\n\t<label><input type='radio' name='avatar_rating' value='$key' $selected> $rating</label><br />";
        endforeach;
        ?>
        
        </td>
        </tr>
		
		
		
		<tr valign="top">
		<th scope="row"><?php _e('Default Avatar') ?></th>
		<td class="defaultavatarpicker"><fieldset><legend class="hidden"><?php _e('Default Avatar') ?></legend>
		
		<?php _e('For users without a custom avatar of their own, you can either display a generic logo or a generated one based on their e-mail address.'); ?><br />
		
		<?php
		$avatar_defaults = array(
			'mystery' => __('Mystery Man'),
			'blank' => __('Blank'),
			'gravatar_default' => __('Gravatar Logo'),
			'identicon' => __('Identicon (Generated)'),
			'wavatar' => __('Wavatar (Generated)'),
			'monsterid' => __('MonsterID (Generated)')
		);
		$avatar_defaults = apply_filters('avatar_defaults', $avatar_defaults);
		$default = $opt['avatar_default'];
		if ( empty($default) )
			$default = 'mystery';
		$size = 32;
		$avatar_list = '';
		foreach ( $avatar_defaults as $default_key => $default_name ) {
			$selected = ($default == $default_key) ? 'checked="checked" ' : '';
			$avatar_list .= "\n\t<label><input type='radio' name='avatar_default' id='avatar_{$default_key}' value='{$default_key}' {$selected}/> ";
		
			//$avatar = get_avatar( $user_email, $size, $default_key );
			//$avatar_list .= preg_replace("/src='(.+?)'/", "src='\$1&amp;forcedefault=1'", $avatar);
		
			$avatar_list .= ' ' . $default_name . '</label>';
			$avatar_list .= '<br />';
		}
		echo apply_filters('default_avatar_select', $avatar_list);
		?>
		
		</fieldset></td>
		</tr>
		
		
        
        </table>

        </div>
        <p>&nbsp;</p>
        <div class="wrap">
        <h2><?php _e('Privacy Settings') ?></h2>
        <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e('Blog Visibility') ?> </th>
        <td>
        <p>Warning: It can be confusing for users to have these settings override the setting they choose on the sign up form. If you do not want to override user settings, select "Allow User Choice".</p>
		
		<p><input id="blog-public-reset" type="radio" name="blog_public" value="" <?php checked('', $opt['blog_public']); ?> />
        <label for="blog-public-reset"><?php _e('Allow User Choice'); ?></label></p>
        <p><input id="blog-public" type="radio" name="blog_public" value="1" <?php checked('1', $opt['blog_public']); ?> />
        <label for="blog-public"><?php _e('I would like my blog to be visible to everyone, including search engines (like Google, Sphere, Technorati) and archivers and in public listings around this site.') ?></label></p>
        <p><input id="blog-norobots" type="radio" name="blog_public" value="0" <?php checked('0', $opt['blog_public']); ?> />
        <label for="blog-norobots"><?php _e('I would like to block search engines, but allow normal visitors'); ?></label></p>
        <?php do_action('blog_privacy_selector'); ?>
        </td>
        </tr>
        </table>
        
       
        </div>
        <p>&nbsp;</p>
		<div class="wrap">
        <h2><?php _e('Customize Permalink Structure') ?></h2>
        <p><?php _e('By default WordPress uses web <abbr title="Universal Resource Locator">URL</abbr>s which have question marks and lots of numbers in them, however WordPress offers you the ability to create a custom URL structure for your permalinks and archives. This can improve the aesthetics, usability, and forward-compatibility of your links. A <a href="http://codex.wordpress.org/Using_Permalinks">number of tags are available</a>, and here are some examples to get you started.'); ?></p>
        
        <?php
        $prefix = '';
        
        if ( ! got_mod_rewrite() )
            $prefix = '/index.php';
        
        $structures = array(
            '',
            $prefix . '/%year%/%monthnum%/%day%/%postname%/',
            $prefix . '/%year%/%monthnum%/%postname%/',
            $prefix . '/archives/%post_id%'
            );
        ?>
        <h3><?php _e('Common settings'); ?></h3>
        
        <table class="form-table">
            <tr>
                <th><label><input name="permalink_choice" type="radio" value="" class="tog" <?php checked('', $opt['permalink_structure']); ?> /> <?php _e('Default'); ?></label></th>
                <td>&nbsp;</td>
             </tr>
            <tr>
                <th><label><input name="permalink_choice" type="radio" value="<?php echo $structures[1]; ?>" class="tog" <?php checked($structures[1], $opt['permalink_structure']); ?> /> <?php _e('Day and name'); ?></label></th>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <th><label><input name="permalink_choice" type="radio" value="<?php echo $structures[2]; ?>" class="tog" <?php checked($structures[2], $opt['permalink_structure']); ?> /> <?php _e('Month and name'); ?></label></th>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <th><label><input name="permalink_choice" type="radio" value="<?php echo $structures[3]; ?>" class="tog" <?php checked($structures[3], $opt['permalink_structure']); ?> /> <?php _e('Numeric'); ?></label></th>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <th>
                    <label><input name="permalink_choice" id="custom_selection" type="radio" value="custom" class="tog"
                    <?php if ( !in_array($opt['permalink_structure'], $structures) ) { ?>
                    checked="checked"
                    <?php } ?>
                     />
                    <?php _e('Custom Structure'); ?>
                    </label>
                </th>
                <td>
                    <?php if( constant( 'VHOST' ) == 'no' && $current_site->domain.$current_site->path == $current_blog->domain.$current_blog->path ) { echo "/blog"; $permalink_structure = str_replace( "/blog", "", $opt['permalink_structure'] ); }?>
                    <input name="permalink_structure" id="permalink_structure" type="text" class="code" style="width: 60%;" value="<?php echo esc_attr($opt['permalink_structure']); ?>" size="50" />
                </td>
            </tr>
        </table>
        
        <h3><?php _e('Optional'); ?></h3>
        <?php if ($is_apache) : ?>
            <p><?php _e('If you like, you may enter custom structures for your category and tag <abbr title="Universal Resource Locator">URL</abbr>s here. For example, using <code>/topics/</code> as your category base would make your category links like <code>http://example.org/topics/uncategorized/</code>. If you leave these blank the defaults will be used.') ?></p>
        <?php else : ?>
            <p><?php _e('If you like, you may enter custom structures for your category and tag <abbr title="Universal Resource Locator">URL</abbr>s here. For example, using <code>/topics/</code> as your category base would make your category links like <code>http://example.org/index.php/topics/uncategorized/</code>. If you leave these blank the defaults will be used.') ?></p>
        <?php endif; ?>
        
        <table class="form-table">
            <tr>
                <th><?php _e('Category base'); ?></th>
                <td><?php if( constant( 'VHOST' ) == 'no' && $current_site->domain.$current_site->path == $current_blog->domain.$current_blog->path ) { echo "/blog"; $opt['category_base'] = str_replace( "/blog", "", $opt['category_base'] ); }?> <input name="category_base" type="text" class="code"  value="<?php echo esc_attr( $opt['category_base'] ); ?>" size="30" /></td>
            </tr>
            <tr>
                <th><?php _e('Tag base'); ?></th>
                <td><?php if( constant( 'VHOST' ) == 'no' && $current_site->domain.$current_site->path == $current_blog->domain.$current_blog->path ) { echo "/blog"; $opt['tag_base'] = str_replace( "/blog", "", $opt['tag_base'] ); }?> <input name="tag_base" id="tag_base" type="text" class="code"  value="<?php echo esc_attr($opt['tag_base']); ?>" size="30" /></td>
            </tr>
        </table>
        
        </div>
                
        
        <p>&nbsp;</p>
        
        <div class="wrap">
        <h2><?php _e('Media Settings'); ?></h2>
        <h3><?php _e('Image sizes') ?></h3>
        <p><?php _e('The sizes listed below determine the maximum dimensions to use when inserting an image into the body of a post.'); ?></p>
        
        <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e('Thumbnail size') ?></th>
        <td>
        <label for="thumbnail_size_w"><?php _e('Width'); ?></label>
        <input name="thumbnail_size_w" type="text" id="thumbnail_size_w" value="<?php echo($opt['thumbnail_size_w']); ?>" size="6" />
        <label for="thumbnail_size_h"><?php _e('Height'); ?></label>
        <input name="thumbnail_size_h" type="text" id="thumbnail_size_h" value="<?php echo($opt['thumbnail_size_h']); ?>" size="6" /><br />
        <input name="thumbnail_crop" type="checkbox" id="thumbnail_crop" value="1" <?php checked('1', $opt['thumbnail_crop']); ?>/>
        <label for="thumbnail_crop"><?php _e('Crop thumbnail to exact dimensions (normally thumbnails are proportional)'); ?></label>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php _e('Medium size') ?></th>
        <td>
        <label for="medium_size_w"><?php _e('Max Width'); ?></label>
        <input name="medium_size_w" type="text" id="medium_size_w" value="<?php echo($opt['medium_size_w']); ?>" size="6" />
        <label for="medium_size_h"><?php _e('Max Height'); ?></label>
        <input name="medium_size_h" type="text" id="medium_size_h" value="<?php echo($opt['medium_size_h']); ?>" size="6" />
        </td>
        </tr>
			<tr valign="top">
		<th scope="row"><?php _e('Large size') ?></th>
		<td><fieldset><legend class="hidden"><?php _e('Large size') ?></legend>
		<label for="large_size_w"><?php _e('Max Width'); ?></label>
		<input name="large_size_w" type="text" id="large_size_w" value="<?php echo($opt['large_size_w']); ?>" class="small-text" />
		<label for="large_size_h"><?php _e('Max Height'); ?></label>
		<input name="large_size_h" type="text" id="large_size_h" value="<?php echo($opt['large_size_h']); ?>" class="small-text" />
		</fieldset></td>
		</tr>
		</table>
        <h3><?php _e('Embeds') ?></h3>

		<table class="form-table">
		
		<tr valign="top">
		<th scope="row"><?php _e('Auto-embeds'); ?></th>
		<td><fieldset><legend class="screen-reader-text"><span><?php _e('Attempt to automatically embed all plain text URLs'); ?></span></legend>
		<label for="embed_autourls"><input name="embed_autourls" type="checkbox" id="embed_autourls" value="1" <?php checked( '1', $opt['embed_autourls'] ); ?>/> <?php _e('Attempt to automatically embed all plain text URLs'); ?></label>
		</fieldset></td>
		</tr>
		
		<tr valign="top">
		<th scope="row"><?php _e('Maximum embed size') ?></th>
		<td>
		<label for="embed_size_w"><?php _e('Width'); ?></label>
		<input name="embed_size_w" type="text" id="embed_size_w" value="<?php echo $opt['embed_size_w']; ?>" class="small-text" />
		<label for="embed_size_h"><?php _e('Height'); ?></label>
		<input name="embed_size_h" type="text" id="embed_size_h" value="<?php echo $opt['embed_size_h']; ?>" class="small-text" />
		<?php if ( !empty($content_width) ) echo '<br />' . __("If the width value is left blank, embeds will default to the max width of your theme."); ?>
		</td>
		</tr>
		
		
		</table>
        </div>
        <p>&nbsp;</p>
        <div class="wrap">
        <h2>Default Theme</h2>
        <?php 
		$themes = get_themes();
		$ct = current_theme_info();
		$allowed_themes = get_site_allowed_themes();
		if( $allowed_themes == false )
			$allowed_themes = array();
		
		$blog_allowed_themes = wpmu_get_blog_allowedthemes();
		if( is_array( $blog_allowed_themes ) )
			$allowed_themes = array_merge( $allowed_themes, $blog_allowed_themes );
		
		if( $blog_id != 1 ) {
			unset( $allowed_themes[ "h3" ] );
		}
		
		if( isset( $allowed_themes[ esc_html( $ct->stylesheet ) ] ) == false )
			$allowed_themes[ esc_html( $ct->stylesheet ) ] = true;
		
		reset( $themes );
		foreach( $themes as $key => $theme ) {
			if( isset( $allowed_themes[ esc_html( $theme[ 'Stylesheet' ] ) ] ) == false ) {
				unset( $themes[ $key ] );
			}
		}
		reset( $themes );
		
		// get the names of the themes & sort them
		$theme_names = array_keys($themes);
		natcasesort($theme_names);
		?>
        <table class="form-table">
        <tr valign="top">
        <th>Select the default theme:</th>
        <td><select name="theme" size="1">
        <?php
		foreach ($theme_names as $theme_name) {
		$template = $themes[$theme_name]['Template'];
		$stylesheet = $themes[$theme_name]['Stylesheet'];
		$title = $themes[$theme_name]['Title'];
		$selected = "";
		if($opt[theme] == $template . "|" . $stylesheet) {
			$selected = "selected = 'selected' ";
		}
		echo('<option value="' . $template . "|" . $stylesheet .  '"' . $selected . '>' . $title . "</option>");
		}
		?>
        </select>
        </td>
        </tr>
        </table>
        </div>
        
        <div class="wrap">
        <h2>Bonus Settings</h2>
		<table class="form-table">
        <tr valign="top">
        <th>From Email:</th>
		<td><input name="from_email" type="text" id="from_email" size="30" value="<?php echo($opt['from_email']); ?>"  /></td>
		</tr>
		  <tr valign="top">
        <th>From Email Name:<br/>(defaults to site name if left blank)</th>
		<td><input name="from_email_name" type="text" id="from_email_name" size="30" value="<?php echo($opt['from_email_name']); ?>"  /></td>
		</tr>
		<tr>
			<th>Delete Standard WordPress Blogroll Links</th>
			<td>
		<label for="delete_blogroll_links">
		
        <input name="delete_blogroll_links" type="checkbox" id="delete_blogroll_links" value="1" <?php if ($opt['delete_blogroll_links'] == 1) echo('checked="checked"'); ?> /> <?php _e('Yes') ?></label>
		</td>
		</tr>
		<tr valign="top">
        <th>Default Link Category:<br/> (Overwrites "Blogroll")</th>
		<td><input name="default_link_cat" type="text" id="default_link_cat" size="30" value="<?php echo($opt['default_link_cat']); ?>"  /></td>
		</tr>
		<tr valign="top">
        <th scope="row"><?php _e('Additional Links') ?></th>
        <td>
        <p><?php _e('Enter links one per line with the name followed by an equals sign and a greater than sign and then the fully qualified link. Example: Google=>http://www.google.com') ?></p>
        <p>
        <textarea name="default_links" cols="60" rows="10" id="default_links" style="width: 98%; font-size: 12px;" class="code"><?php echo(str_replace('|+', "\n", $opt['default_links'])); ?></textarea>
        </p>
        </td>
        </tr>
		
		<tr valign="top">
        <th>Default Category:<br/> (Overwrites "Uncategorized")</th>
		<td><input name="default_cat_name" type="text" id="default_cat_name" size="30" value="<?php echo($opt['default_cat_name']); ?>"  /></td>
		</tr>
		<tr valign="top">
        <th scope="row"><?php _e('Additional Categories') ?></th>
        <td>
        <p><?php _e('Enter categories one per line with the name followed by an equals sign and a greater than sign and then the description => Nice Name => parent name. Example: Plugins=>Find out out about my plugins=>plugins=>code') ?></p>
        <p>
        <textarea name="default_categories" cols="60" rows="10" id="default_categories" style="width: 98%; font-size: 12px;" class="code"><?php echo(str_replace('|+', "\n", $opt['default_categories'])); ?></textarea>
        </p>
        </td>
        </tr>
		
    	<tr>
			<th>Delete Initial Comment</th>
			<td>
		<label for="delete_first_comment">
		
        <input name="delete_first_comment" type="checkbox" id="delete_first_comment" value="1" <?php if ($opt['delete_first_comment'] == 1) echo('checked="checked"'); ?> /> <?php _e('Yes') ?></label>
		</td>
		</tr>
		<tr>
			<th>Close Comments on Hello World Post</th>
			<td>
		<label for="close_comments_on_hello_world">
		
        <input name="close_comments_on_hello_world" type="checkbox" id="close_comments_on_hello_world" value="1" <?php if ($opt['close_comments_on_hello_world'] == 1) echo('checked="checked"'); ?> /> <?php _e('Yes') ?></label>
		</td>
		</tr>
		<tr>
			<th>Close Comments on About Page</th>
			<td>
		<label for="close_comments_on_about_page">
		
        <input name="close_comments_on_about_page" type="checkbox" id="close_comments_on_about_page" value="1" <?php if ($opt['close_comments_on_about_page'] == 1) echo('checked="checked"'); ?> /> <?php _e('Yes') ?></label>
		</td>
		</tr>
		<tr>
			<th>Make First Post a Draft ("Hello World")</th>
			<td>
		<label for="delete_first_post">
		
        <input name="delete_first_post" type="checkbox" id="delete_first_post" value="1" <?php if ($opt['delete_first_post'] == 1) echo('checked="checked"'); ?> /> <?php _e('Yes') ?></label>
		</td>
		</tr>
		
		<tr>
			<th>Delete Initial Widgets</th>
			<td>
			<input name="delete_initial_widgets" type="checkbox" id="delete_initial_widgets" value="1" <?php if ($opt['delete_initial_widgets'] == 1) echo('checked="checked"'); ?> /> <?php _e('Yes') ?>
			</td>
		</tr>
		
		<tr>
			<th colspan="2">
				Sites that use a combination of BBPress and BuddyPress need all users to be subscribed to the blog on which BBPress is installed. The following section lets you do that. Note that you may add people to a comma-delimited list of blogs, but they will have the same role on each blog. 
			</td>
		</tr>
		<tr valign="top">
        <th>Add User to Other Blog(s):</th>
		<td><input name="add_user_to_blog" type="checkbox" id="add_user_to_blog" value="1" <?php if ($opt['add_user_to_blog'] == 1) echo('checked="checked"'); ?> /> <?php _e('Yes') ?></label>
		</td>
		</tr>
		<tr valign="top">
        <th>Role on Other Blog (s)</th>
		<td>
			<select name="add_user_to_blog_role" id="add_user_to_blog_role">
			<option value="administrator"<?php if($opt['add_user_to_blog_role'] == 'administrator') echo ' selected="selected"';?>>Administator</option>
			<option value="editor"<?php if($opt['add_user_to_blog_role'] == 'editor') echo ' selected="selected"';?>>Editor</option>
			<option value="author"<?php if($opt['add_user_to_blog_role'] == 'author') echo ' selected="selected"';?>>Author</option>
			<option value="contributor"<?php if($opt['add_user_to_blog_role'] == 'contributor') echo ' selected="selected"';?>>Contributor</option>
			<option value="subscriber"<?php if($opt['add_user_to_blog_role'] == 'subscriber') echo ' selected="selected"';?>>Subscriber</option>				
		</select></td>
		</tr>
		<tr valign="top">
        <th>Blog ID's to add User To:</th>
		<td><input name="add_user_to_blog_id" type="text" id="add_user_to_blog_id" size="30" value="<?php echo($opt['add_user_to_blog_id']); ?>"  /><br/>Use Blog ID or comma-delimited list of ID's.</td>
		</tr>
		</table>
		</div>
        
        
         <p>  
         <input type="hidden" name="action" value="update" />
        <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
          </p> 
        
        <?php
	 }
	
	
};


$cets_wpmubd = new cets_blog_defaults();

// call set up if there's not option set yet

if( get_site_option( 'cets_blog_defaults_setup' ) == null OR (isset($_GET['reset']) && $_GET['reset'] == 1 && $_GET['page'] == 'cets_blog_defaults_management_page')) {
	
	// only allow site admins to run setup.
		//if( is_site_admin() == true ) {
			$cets_wpmubd->setup();
		//}

}

/* ***************************
* Helper functions
* ******************************/
function cets_nbd_from_email($from_email)
	{
	$options = get_site_option('cets_blog_defaults_options');
	if (isset($options['from_email']) and strlen($options['from_email']) > 0) {
	$from_email = $options['from_email'];
	
	}
	
	return $from_email; //return whatever you want as email, i just like it as default.
}

function cets_nbd_from_name($from_name)
{
	$options = get_site_option('cets_blog_defaults_options');
	if (isset($options['from_email_name']) and strlen($options['from_email_name']) > 0) {
	return $options['from_email_name'];
	
	}
	else {
	global $current_site;
	return $current_site->domain;
	}
}


	
	
// When a new blog is created, set the options (if it's over 3.0)
		if ( version_compare( $wp_version, '3.0', '>=' ) ) {
			add_action('wpmu_new_blog', array(&$cets_wpmubd, 'set_blog_defaults'), 100, 2);	
		
		// Add the site admin config page
		if (function_exists('is_network_admin')) {
			//3.1+
			add_action('network_admin_menu', array(&$cets_wpmubd, 'add_siteadmin_page'));
		} else {
			//-3.1
			add_action('admin_menu', array(&$cets_wpmubd, 'add_siteadmin_page'));
		}
		
		
		// Filters and such needed for the bonus options
		add_filter( 'wp_mail_from', 'cets_nbd_from_email' );
		add_filter( 'wp_mail_from_name', 'cets_nbd_from_name' );
		
		}

