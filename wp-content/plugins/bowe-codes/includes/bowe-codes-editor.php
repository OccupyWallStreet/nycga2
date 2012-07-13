<?php
/** Load WordPress Administration Bootstrap **/

$admin = dirname( __FILE__ ) ;
$admin = substr( $admin , 0 , strpos( $admin , 'wp-content' ) ) ;

if( file_exists( $admin . 'wp-load.php' ) )
	require_once( $admin . 'wp-load.php' ) ;
else
	require_once( $admin . 'wp-config.php' ) ;


require_once(ABSPATH.'wp-admin/admin.php');
wp_enqueue_script( 'common' );
wp_enqueue_script( 'jquery-color' );

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<title><?php bloginfo('name') ?> &rsaquo; <?php _e('Uploads'); ?> &#8212; <?php _e('WordPress'); ?></title>
	<?php
		wp_enqueue_style( 'global' );
		wp_enqueue_style( 'wp-admin' );
		wp_enqueue_style( 'colors' );
		wp_enqueue_style( 'media' );
		wp_enqueue_style('bowe-codes-admin-style', BOWE_CODES_PLUGIN_URL . '/css/admin.css');
	?>
	<script type="text/javascript">
	//<![CDATA[
		function addLoadEvent(func) {if ( typeof wpOnload!='function'){wpOnload=func;}else{ var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}}
	//]]>
	</script>
	<?php
	do_action('admin_print_styles');
	do_action('admin_print_scripts');
	do_action('admin_head');
	if ( isset($content_func) && is_string($content_func) )
		do_action( "admin_head_{$content_func}" );
	global $blog_id;
	$bc_yes = __('Yes', 'bowe-codes');
	$bc_no = __('No', 'bowe-codes');
	$bc_show = __('Show', 'bowe-codes');
	$bc_hide = __('Hide', 'bowe-codes');
	$bc_avatar = __('Avatar','bowe-codes');
	$bc_avatar_size = __('Avatar size (in pixels)','bowe-codes');
	$bc_class = __('Class', 'bowe-codes');
	$bc_insert = __('Insert','bowe-codes');
	$bc_amount = __('Amount', 'bowe-codes');
	$bc_type = __('Type', 'bowe-codes');
	$bc_popular = __('Popular', 'bowe-codes');
	$bc_newest = __('Newest', 'bowe-codes');
	$bc_active = __('Active', 'bowe-codes');
	?>
</head>
<body id="media-upload">
	<div style="margin:10px">
		<div><h3><?php _e('List of Available Bowe Codes', 'bowe-codes');?></h3></div>
			<table class="widefat" cellspacing="0">
				<thead>
					<tr>
						<th><?php _e('ShortCode Name', 'bowe-codes');?></th>
						<th class="actions-head"><?php _e('Actions', 'bowe-codes');?></th>
					</tr>
				</thead>
			</table>
			<div id="media-items">
				<div class='media-item'>
					<a class='toggle describe-toggle-on' href='javascript:void(0)'><?php echo $bc_show;?></a>
					<a class='toggle describe-toggle-off' href='javascript:void(0)'><?php echo $bc_hide;?></a>
					<div class='filename new'><span class='title'>[bc_member]</span></div>
					<table class='slidetoggle describe startclosed'>
						<thead class='media-item-info'>
							<tr valign='top'>
								<td class='A1B1 bc_desc'><?php _e('Displays a specific member.','bowe-codes');?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<table class="form-bowe-code">
										<tr>
											<th><label for="bc_member_avatar"><?php echo $bc_avatar;?></label></th>
											<td><input type="hidden" class="bc_type" name="bc_member" value="bc_member">
												<input type="radio" value="1" name="bc_member_avatar" checked/><?php echo $bc_yes;?>
												<input type="radio" value="0" name="bc_member_avatar"/><?php echo $bc_no;?></td>
										</tr>
										<tr>
											<th><label for="bc_member_size"><?php echo $bc_avatar_size;?></label></th>
											<td><input type="text" id="bc_member_size" value="50" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_member_name"><?php _e('Member login','bowe-codes');?></label></th>
											<td><input type="text" id="bc_member_name" class="bc_large_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_member_class"><?php echo $bc_class;?></label></th>
											<td><input type="text" id="bc_member_class" class="bc_large_text" value="my_member"/></td>
										</tr>
										<tr>
											<th><label for="bc_member_fields"><?php _e('xprofile fields (comma separated list without spaces between commas)','bowe-codes');?></label></th>
											<td><input type="text" id="bc_member_fields" class="bc_large_text"/></td>
										</tr>
										<tr>
											<td class="action-btn">&nbsp;</td><td class="action-btn"><a href="javascript:void(0)" class="button-secondary insertBC"><?php echo $bc_insert;?></a></td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class='media-item'>
					<a class='toggle describe-toggle-on' href='javascript:void(0)'><?php echo $bc_show;?></a>
					<a class='toggle describe-toggle-off' href='javascript:void(0)'><?php echo $bc_hide;?></a>
					<div class='filename new'><span class='title'>[bc_group]</span></div>
					<table class='slidetoggle describe startclosed'>
						<thead class='media-item-info'>
							<tr valign='top'>
								<td class='A1B1 bc_desc'><?php _e('Displays a specific group.', 'bowe-codes');?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<table class="form-bowe-code">
										<tr>
											<th><label for="bc_group_avatar"><?php echo $bc_avatar;?></label></th>
											<td><input type="hidden" class="bc_type" name="bc_group" value="bc_group">
												<input type="radio" value="1" name="bc_group_avatar" checked/><?php echo $bc_yes;?>
												<input type="radio" value="0" name="bc_group_avatar"/><?php echo $bc_no;?></td>
										</tr>
										<tr>
											<th><label for="bc_group_size"><?php echo $bc_avatar_size;?></label></th>
											<td><input type="text" id="bc_group_size" value="50" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_group_slug"><?php _e('Group slug', 'bowe-codes');?></label></th>
											<td><input type="text" id="bc_group_slug" class="bc_large_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_group_class"><?php echo $bc_class;?></label></th>
											<td><input type="text" id="bc_group_class" class="bc_large_text" value="my_group"/></td>
										</tr>
										<tr>
											<th><label for="bc_group_desc"><?php _e('Group description', 'bowe-codes');?></label></th>
											<td><input type="radio" value="1" name="bc_group_desc" checked/><?php echo $bc_yes;?>
											<input type="radio" value="0" name="bc_group_desc"/><?php echo $bc_no;?></td>
										</tr>
										<tr>
											<td class="action-btn">&nbsp;</td><td class="action-btn"><a href="javascript:void(0)" class="button-secondary insertBC"><?php echo $bc_insert;?></a></td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class='media-item'>
					<a class='toggle describe-toggle-on' href='javascript:void(0)'><?php echo $bc_show;?></a>
					<a class='toggle describe-toggle-off' href='javascript:void(0)'><?php echo $bc_hide;?></a>
					<div class='filename new'><span class='title'>[bc_groups]</span></div>
					<table class='slidetoggle describe startclosed'>
						<thead class='media-item-info'>
							<tr valign='top'>
								<td class='A1B1 bc_desc'><?php _e('Displays groups with or without avatars.', 'bowe-codes');?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<table class="form-bowe-code">
										<tr>
											<th><label for="bc_groups_amount"><?php echo $bc_amount;?></label></th>
											<td><input type="hidden" class="bc_type" name="bc_groups" value="bc_groups">
												<input type="text" id="bc_groups_amount" value="10" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_groups_avatar"><?php echo $bc_avatar;?></label></th>
											<td><input type="radio" value="1" name="bc_groups_avatar" checked/><?php echo $bc_yes;?>
												<input type="radio" value="0" name="bc_groups_avatar"/><?php echo $bc_no;?></td>
										</tr>
										<tr>
											<th><label for="bc_groups_size"><?php echo $bc_avatar_size;?></label></th>
											<td><input type="text" id="bc_groups_size" value="50" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_groups_type"><?php echo $bc_type;?></label></th>
											<td><select id="bc_groups_type">
													<option value="popular"><?php echo $bc_popular;?></option>
													<option value="newest"><?php echo $bc_newest;?></option>
													<option value="active"><?php echo $bc_active;?></option>
												</select></td>
										</tr>
										<tr>
											<th><label for="bc_groups_featured"><?php _e('Featured groups (comma separated list of group slugs without spaces between commas)', 'bowe-codes');?></label></th>
											<td><input type="text" id="bc_groups_featured" class="bc_large_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_groups_class"><?php echo $bc_class;?></label></th>
											<td><input type="text" id="bc_groups_class" class="bc_large_text" value="my_groups"/></td>
										</tr>
										<tr>
											<th><label for="bc_groups_content"><?php _e('Custom title/content', 'bowe-codes');?></label></th>
											<td><input type="text" id="bc_groups_content" class="bc_large_text"/></td>
										</tr>
										<tr>
											<td class="action-btn">&nbsp;</td><td class="action-btn"><a href="javascript:void(0)" class="button-secondary insertBC"><?php echo $bc_insert;?></a></td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class='media-item'>
					<a class='toggle describe-toggle-on' href='javascript:void(0)'><?php echo $bc_show;?></a>
					<a class='toggle describe-toggle-off' href='javascript:void(0)'><?php echo $bc_hide;?></a>
					<div class='filename new'><span class='title'>[bc_members]</span></div>
					<table class='slidetoggle describe startclosed'>
						<thead class='media-item-info'>
							<tr valign='top'>
								<td class='A1B1 bc_desc'><?php _e('Displays members with or without avatars.', 'bowe-codes');?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<table class="form-bowe-code">
										<tr>
											<th><label for="bc_members_amount"><?php echo $bc_amount;?></label></th>
											<td><input type="hidden" class="bc_type" name="bc_members" value="bc_members">
												<input type="text" id="bc_members_amount" value="10" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_members_avatar"><?php echo $bc_avatar;?></label></th>
											<td><input type="radio" value="1" name="bc_members_avatar" checked/><?php echo $bc_yes;?>
												<input type="radio" value="0" name="bc_members_avatar"/><?php echo $bc_no;?></td>
										</tr>
										<tr>
											<th><label for="bc_members_size"><?php echo $bc_avatar_size;?></label></th>
											<td><input type="text" id="bc_members_size" value="50" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_members_type"><?php echo $bc_type;?></label></th>
											<td><select id="bc_members_type">
													<option value="active"><?php echo $bc_active;?></option>
													<option value="newest"><?php echo $bc_newest;?></option>
												</select></td>
										</tr>
										<tr>
											<th><label for="bc_members_featured"><?php _e('Featured members (comma separated list of member logins without spaces between commas)', 'bowe-codes');?></label></th>
											<td><input type="text" id="bc_members_featured" class="bc_large_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_members_class"><?php echo $bc_class;?></label></th>
											<td><input type="text" id="bc_members_class" class="bc_large_text" value="my_members"/></td>
										</tr>
										<tr>
											<td class="action-btn">&nbsp;</td><td class="action-btn"><a href="javascript:void(0)" class="button-secondary insertBC"><?php echo $bc_insert;?></a></td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class='media-item'>
					<a class='toggle describe-toggle-on' href='javascript:void(0)'><?php echo $bc_show;?></a>
					<a class='toggle describe-toggle-off' href='javascript:void(0)'><?php echo $bc_hide;?></a>
					<div class='filename new'><span class='title'>[bc_friends]</span></div>
					<table class='slidetoggle describe startclosed'>
						<thead class='media-item-info'>
							<tr valign='top'>
								<td class='A1B1 bc_desc'><?php _e('Displays the friends of the current logged in user, or the friends of the member profile currently being viewed.', 'bowe-codes');?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<table class="form-bowe-code">
										<tr>
											<th><label for="bc_friends_amount"><?php echo $bc_amount;?></label></th>
											<td><input type="hidden" class="bc_type" name="bc_friends" value="bc_friends">
												<input type="text" id="bc_friends_amount" value="10" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_friends_avatar"><?php echo $bc_avatar;?></label></th>
											<td><input type="radio" value="1" name="bc_friends_avatar" checked/><?php echo $bc_yes;?>
												<input type="radio" value="0" name="bc_friends_avatar"/><?php echo $bc_no;?></td>
										</tr>
										<tr>
											<th><label for="bc_friends_size"><?php echo $bc_avatar_size;?></label></th>
											<td><input type="text" id="bc_friends_size" value="50" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_friends_type"><?php echo $bc_type;?></label></th>
											<td><select id="bc_friends_type">
													<option value="newest"><?php echo $bc_newest;?></option>
													<option value="active"><?php echo $bc_active;?></option>
												</select></td>
										</tr>
										<tr>
											<th><label for="bc_friends_class"><?php echo $bc_class;?></label></th>
											<td><input type="text" id="bc_friends_class" class="bc_large_text" value="my_friends"/></td>
										</tr>
										<tr>
											<th><label for="bc_friends_dynamic"><?php _e('Dynamic (show friends of displayed member if viewing his profile)', 'bowe-codes');?></label></th>
											<td><input type="radio" value="1" name="bc_friends_dynamic"/><?php echo $bc_yes;?>
											<input type="radio" value="0" name="bc_friends_dynamic" checked/><?php echo $bc_no;?></td>
										</tr>
										<tr>
											<td class="action-btn">&nbsp;</td><td class="action-btn"><a href="javascript:void(0)" class="button-secondary insertBC"><?php echo $bc_insert;?></a></td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class='media-item'>
					<a class='toggle describe-toggle-on' href='javascript:void(0)'><?php echo $bc_show;?></a>
					<a class='toggle describe-toggle-off' href='javascript:void(0)'><?php echo $bc_hide;?></a>
					<div class='filename new'><span class='title'>[bc_user_groups]</span></div>
					<table class='slidetoggle describe startclosed'>
						<thead class='media-item-info'>
							<tr valign='top'>
								<td class='A1B1 bc_desc'><?php _e('Displays the groups of the currently logged in user or the groups of the member profile currently being viewed.', 'bowe-codes');?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<table class="form-bowe-code">
										<tr>
											<th><label for="bc_user_groups_amount"><?php echo $bc_amount;?></label></th>
											<td><input type="hidden" class="bc_type" name="bc_user_groups" value="bc_user_groups">
												<input type="text" id="bc_user_groups_amount" value="10" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_user_groups_avatar"><?php echo $bc_avatar;?></label></th>
											<td><input type="radio" value="1" name="bc_user_groups_avatar" checked/><?php echo $bc_yes;?>
												<input type="radio" value="0" name="bc_user_groups_avatar"/><?php echo $bc_no;?></td>
										</tr>
										<tr>
											<th><label for="bc_user_groups_size"><?php echo $bc_avatar_size;?></label></th>
											<td><input type="text" id="bc_user_groups_size" value="50" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_user_groups_type"><?php echo $bc_type;?></label></th>
											<td><select id="bc_user_groups_type">
													<option value="popular"><?php echo $bc_popular;?></option>
													<option value="newest"><?php echo $bc_newest;?></option>
													<option value="active"><?php echo $bc_active;?></option>
												</select></td>
										</tr>
										<tr>
											<th><label for="bc_user_groups_class"><?php echo $bc_class;?></label></th>
											<td><input type="text" id="bc_user_groups_class" class="bc_large_text" value="user_groups"/></td>
										</tr>
										<tr>
											<th><label for="bc_user_groups_dynamic"><?php _e('Dynamic (show groups of displayed member if viewing his profile)', 'bowe-codes');?></label></th>
											<td><input type="radio" value="1" name="bc_user_groups_dynamic"/><?php echo $bc_yes;?>
											<input type="radio" value="0" name="bc_user_groups_dynamic" checked/><?php echo $bc_no;?></td>
										</tr>
										<tr>
											<td class="action-btn">&nbsp;</td><td class="action-btn"><a href="javascript:void(0)" class="button-secondary insertBC"><?php echo $bc_insert;?></a></td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class='media-item'>
					<a class='toggle describe-toggle-on' href='javascript:void(0)'><?php echo $bc_show;?></a>
					<a class='toggle describe-toggle-off' href='javascript:void(0)'><?php echo $bc_hide;?></a>
					<div class='filename new'><span class='title'>[bc_messages]</span></div>
					<table class='slidetoggle describe startclosed'>
						<thead class='media-item-info'>
							<tr valign='top'>
								<td class='A1B1 bc_desc'><?php _e('Displays the latest messages from the currently logged in user.', 'bowe-codes');?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<table class="form-bowe-code">
										<tr>
											<th><label for="bc_messages_amount"><?php echo $bc_amount;?></label></th>
											<td><input type="hidden" class="bc_type" name="bc_messages" value="bc_messages">
												<input type="text" id="bc_messages_amount" value="5" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_messages_subject"><?php _e('Subject', 'bowe-codes');?></label></th>
											<td><input type="radio" value="1" name="bc_messages_subject" checked/><?php echo $bc_yes;?>
												<input type="radio" value="0" name="bc_messages_subject"/><?php echo $bc_no;?></td>
										</tr>
										<tr>
											<th><label for="bc_messages_avatar"><?php echo $bc_avatar;?></label></th>
											<td><input type="radio" value="1" name="bc_messages_avatar" checked/><?php echo $bc_yes;?>
												<input type="radio" value="0" name="bc_messages_avatar"/><?php echo $bc_no;?></td>
										</tr>
										<tr>
											<th><label for="bc_messages_size"><?php echo $bc_avatar_size;?></label></th>
											<td><input type="text" id="bc_messages_size" value="30" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_messages_excerpt"><?php _e('Excerpt (number of words)', 'bowe-codes');?></label></th>
											<td><input type="text" id="bc_messages_excerpt" value="10" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_messages_class"><?php echo $bc_class;?></label></th>
											<td><input type="text" id="bc_messages_class" class="bc_large_text" value="my_messages"/></td>
										</tr>
										<tr>
											<td class="action-btn">&nbsp;</td><td class="action-btn"><a href="javascript:void(0)" class="button-secondary insertBC"><?php echo $bc_insert;?></a></td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class='media-item'>
					<a class='toggle describe-toggle-on' href='javascript:void(0)'><?php echo $bc_show;?></a>
					<a class='toggle describe-toggle-off' href='javascript:void(0)'><?php echo $bc_hide;?></a>
					<div class='filename new'><span class='title'>[bc_notifications]</span></div>
					<table class='slidetoggle describe startclosed'>
						<thead class='media-item-info'>
							<tr valign='top'>
								<td class='A1B1 bc_desc'><?php _e('Displays the latest notifications from the currently logged in user.', 'bowe-codes');?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<table class="form-bowe-code">
										<tr>
											<th><label for="bc_notifications_amount"><?php echo $bc_amount;?></label></th>
											<td><input type="hidden" class="bc_type" name="bc_notifications" value="bc_notifications">
												<input type="text" id="bc_notifications_amount" value="5" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_notifications_avatar"><?php echo $bc_avatar;?></label></th>
											<td><input type="radio" value="1" name="bc_notifications_avatar" checked/><?php echo $bc_yes;?>
												<input type="radio" value="0" name="bc_notifications_avatar"/><?php echo $bc_no;?></td>
										</tr>
										<tr>
											<th><label for="bc_notifications_size"><?php echo $bc_avatar_size;?></label></th>
											<td><input type="text" id="bc_notifications_size" value="30" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_notifications_class"><?php echo $bc_class;?></label></th>
											<td><input type="text" id="bc_notifications_class" class="bc_large_text" value="my_notifications"/></td>
										</tr>
										<tr>
											<td class="action-btn">&nbsp;</td><td class="action-btn"><a href="javascript:void(0)" class="button-secondary insertBC"><?php echo $bc_insert;?></a></td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php if($blog_id==1):?>
				<div class='media-item'>
					<a class='toggle describe-toggle-on' href='javascript:void(0)'><?php echo $bc_show;?></a>
					<a class='toggle describe-toggle-off' href='javascript:void(0)'><?php echo $bc_hide;?></a>
					<div class='filename new'><span class='title'>[bc_blogs]</span></div>
					<table class='slidetoggle describe startclosed'>
						<thead class='media-item-info'>
							<tr valign='top'>
								<td class='A1B1 bc_desc'><?php _e('Shows blogs from across the site.', 'bowe-codes');?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<table class="form-bowe-code">
										<tr>
											<th><label for="bc_blogs_amount"><?php echo $bc_amount;?></label></th>
											<td><input type="hidden" class="bc_type" name="bc_blogs" value="bc_blogs">
												<input type="text" id="bc_blogs_amount" value="5" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_blogs_avatar"><?php echo $bc_avatar;?></label></th>
											<td><input type="radio" value="1" name="bc_blogs_avatar" checked/><?php echo $bc_yes;?>
												<input type="radio" value="0" name="bc_blogs_avatar"/><?php echo $bc_no;?></td>
										</tr>
										<tr>
											<th><label for="bc_blogs_size"><?php echo $bc_avatar_size;?></label></th>
											<td><input type="text" id="bc_blogs_size" value="50" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_blogs_type"><?php echo $bc_type;?></label></th>
											<td><select id="bc_blogs_type">
													<option value="active"><?php echo $bc_active;?></option>
													<option value="newest"><?php echo $bc_newest;?></option>
												</select></td>
										</tr>
										<tr>
											<th><label for="bc_blogs_featured"><?php _e('Featured blogs (comma separated list of blog ids without spaces between commas)', 'bowe-codes');?></label></th>
											<td><input type="text" id="bc_blogs_featured" class="bc_large_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_blogs_class"><?php echo $bc_class;?></label></th>
											<td><input type="text" id="bc_blogs_class" class="bc_large_text" value="my_blogs"/></td>
										</tr>
										<tr>
											<th><label for="bc_blogs_desc"><?php _e('Blog description', 'bowe-codes');?></label></th>
											<td><input type="radio" value="1" name="bc_blogs_desc" checked/><?php echo $bc_yes;?>
												<input type="radio" value="0" name="bc_blogs_desc"/><?php echo $bc_no;?></td>
										</tr>
										<tr>
											<td class="action-btn">&nbsp;</td><td class="action-btn"><a href="javascript:void(0)" class="button-secondary insertBC"><?php echo $bc_insert;?></a></td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php endif;?>
				<div class='media-item'>
					<a class='toggle describe-toggle-on' href='javascript:void(0)'><?php echo $bc_show;?></a>
					<a class='toggle describe-toggle-off' href='javascript:void(0)'><?php echo $bc_hide;?></a>
					<div class='filename new'><span class='title'>[bc_posts]</span></div>
					<table class='slidetoggle describe startclosed'>
						<thead class='media-item-info'>
							<tr valign='top'>
								<td class='A1B1 bc_desc'><?php _e('Shows posts from across the site.', 'bowe-codes');?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<table class="form-bowe-code">
										<tr>
											<th><label for="bc_posts_amount"><?php echo $bc_amount;?></label></th>
											<td><input type="hidden" class="bc_type" name="bc_posts" value="bc_posts">
												<input type="text" id="bc_posts_amount" value="5" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_posts_avatar"><?php echo $bc_avatar;?></label></th>
											<td><input type="radio" value="1" name="bc_posts_avatar" checked/><?php echo $bc_yes;?>
												<input type="radio" value="0" name="bc_posts_avatar"/><?php echo $bc_no;?></td>
										</tr>
										<tr>
											<th><label for="bc_posts_size"><?php echo $bc_avatar_size;?></label></th>
											<td><input type="text" id="bc_posts_size" value="50" class="bc_small_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_posts_type"><?php echo $bc_type;?></label></th>
											<td><select id="bc_posts_type">
													<option value="newest"><?php echo $bc_newest;?></option>
													<option value="random"><?php _e('Random', 'bowe-codes');?></option>
												</select></td>
										</tr>
										<tr>
											<th><label for="bc_posts_class"><?php echo $bc_class;?></label></th>
											<td><input type="text" id="bc_posts_class" class="bc_large_text" value="my_blog_posts"/></td>
										</tr>
										<tr>
											<th><label for="bc_posts_excerpt"><?php _e('Excerpt (number of words)', 'bowe-codes');?></label></th>
											<td><input type="text" id="bc_posts_excerpt" value="10" class="bc_small_text"/></td>
										</tr>
										<tr>
											<td class="action-btn">&nbsp;</td><td class="action-btn"><a href="javascript:void(0)" class="button-secondary insertBC"><?php echo $bc_insert;?></a></td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<!-- restrict content-->
				<div class='media-item'>
					<a class='toggle describe-toggle-on' href='javascript:void(0)'><?php echo $bc_show;?></a>
					<a class='toggle describe-toggle-off' href='javascript:void(0)'><?php echo $bc_hide;?></a>
					<div class='filename new'><span class='title'>[bc_restrict_gm]</span></div>
					<table class='slidetoggle describe startclosed'>
						<thead class='media-item-info'>
							<tr valign='top'>
								<td class='A1B1 bc_desc'><?php _e('Restrict post content to group members', 'bowe-codes');?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<table class="form-bowe-code">
										<tr>
											<th><label for="bc_restrict_group_id"><?php _e('Group id or slug', 'bowe-codes');?></label></th>
											<td><input type="hidden" class="bc_type" name="bc_restrict_gm" value="bc_restrict_gm">
												<input type="text" id="bc_restrict_group_id" value="" class="bc_large_text"/></td>
										</tr>
										<tr>
											<th><label for="bc_restrict_class"><?php echo $bc_class;?></label></th>
											<td><input type="text" id="bc_restrict_class" class="bc_large_text" value="my_restrict_message"/></td>
										</tr>
										<tr>
											<td class="action-btn">&nbsp;</td><td class="action-btn"><a href="javascript:void(0)" class="button-secondary insertBC"><?php echo $bc_insert;?></a></td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			<!--bc_forum-->
			<div class='media-item'>
				<a class='toggle describe-toggle-on' href='javascript:void(0)'><?php echo $bc_show;?></a>
				<a class='toggle describe-toggle-off' href='javascript:void(0)'><?php echo $bc_hide;?></a>
				<div class='filename new'><span class='title'>[bc_forum]</span></div>
				<table class='slidetoggle describe startclosed'>
					<thead class='media-item-info'>
						<tr valign='top'>
							<td class='A1B1 bc_desc'><?php _e('Shows latest forum topics or replies.', 'bowe-codes');?></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<table class="form-bowe-code">
									<tr>
										<th><label for="bc_forum_group_id"><?php _e('Group id or slug', 'bowe-codes');?></label></th>
										<td><input type="hidden" class="bc_type" name="bc_forum" value="bc_forum">
											<input type="text" id="bc_forum_group_id" value="" class="bc_large_text"/></td>
									</tr>
									<tr>
										<th><label for="bc_forum_amount"><?php echo $bc_amount;?></label></th>
										<td><input type="text" id="bc_forum_amount" value="5" class="bc_small_text"/></td>
									</tr>
									<tr>
										<th><label for="bc_forum_avatar"><?php echo $bc_avatar;?></label></th>
										<td><input type="radio" value="1" name="bc_forum_avatar" checked/><?php echo $bc_yes;?>
											<input type="radio" value="0" name="bc_forum_avatar"/><?php echo $bc_no;?></td>
									</tr>
									<tr>
										<th><label for="bc_forum_size"><?php echo $bc_avatar_size;?></label></th>
										<td><input type="text" id="bc_forum_size" value="50" class="bc_small_text"/></td>
									</tr>
									<tr>
										<th><label for="bc_forum_type"><?php echo $bc_type;?></label></th>
										<td><select id="bc_forum_type">
												<option value="new_forum_topic"><?php _e('Forum topics', 'bowe-codes');?></option>
												<option value="new_forum_post"><?php _e('Forum replies', 'bowe-codes');?></option>
											</select></td>
									</tr>
									<tr>
										<th><label for="bc_forum_class"><?php echo $bc_class;?></label></th>
										<td><input type="text" id="bc_forum_class" class="bc_large_text" value="my_forum"/></td>
									</tr>
									<tr>
										<th><label for="bc_forum_excerpt"><?php _e('Excerpt (number of words)', 'bowe-codes');?></label></th>
										<td><input type="text" id="bc_forum_excerpt" value="10" class="bc_small_text"/></td>
									</tr>
									<tr>
										<td class="action-btn">&nbsp;</td><td class="action-btn"><a href="javascript:void(0)" class="button-secondary insertBC"><?php echo $bc_insert;?></a></td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
	</div>
		<script type="text/javascript">
			/* <![CDATA[ */
			jQuery(".describe-toggle-on").click(function(){
				jQuery(this).parent().find(".slidetoggle").show();
				jQuery(this).parent().find(".describe-toggle-off").show();
				jQuery(this).parent().find(".describe-toggle-on").hide();
			});
		
			jQuery(".describe-toggle-off").click(function(){
				jQuery(this).parent().find(".slidetoggle").hide();
				jQuery(this).parent().find(".describe-toggle-off").hide();
				jQuery(this).parent().find(".describe-toggle-on").show();
			});
			
			jQuery('.insertBC').click(function(){
				var typebc = jQuery(this).closest("table").find('.bc_type').val();
				var bcode=typebc;
				var avatar = true;
				
				/* bc_member */
				if(typebc=="bc_member"){
					if(jQuery("#bc_member_name").val().length < 1){
						bcode="";
						alert('<?php _e('Please type the login of a member', 'bowe-codes');?>');
					}
					else{
						bcode +=' name="'+jQuery("#bc_member_name").val()+'"';
						if(jQuery("input[type=radio][name=bc_member_avatar]:checked").attr('value')==0){
							bcode +=' avatar="0"';
							avatar = false;
						}
						if(jQuery("#bc_member_size").val()!=50 && avatar) bcode +=' size="'+jQuery("#bc_member_size").val()+'"';
						if(jQuery("#bc_member_class").val()!="my_member") bcode +=' class="'+jQuery("#bc_member_class").val()+'"';
						if(jQuery("#bc_member_fields").val()!="") bcode +=' fields="'+jQuery("#bc_member_fields").val()+'"';
					}
					if(bcode!="") bcode = '['+bcode+']';
				}
				
				/* bc_group */
				if(typebc=="bc_group"){
					if(jQuery("#bc_group_slug").val().length < 1){
						bcode="";
						alert('<?php _e('Please type the slug of a group', 'bowe-codes');?>');
					}
					else{
						bcode +=' slug="'+jQuery("#bc_group_slug").val()+'"';
						if(jQuery("input[type=radio][name=bc_group_avatar]:checked").attr('value')==0){
							bcode +=' avatar="0"';
							avatar = false;
						}
						if(jQuery("#bc_group_size").val()!=50 && avatar) bcode +=' size="'+jQuery("#bc_group_size").val()+'"';
						if(jQuery("#bc_group_class").val()!="my_group") bcode +=' class="'+jQuery("#bc_group_class").val()+'"';
						
						if(jQuery("input[type=radio][name=bc_group_desc]:checked").attr('value')==0){
							bcode +=' desc="0"';
						}
					}
					if(bcode!="") bcode = '['+bcode+']';
				}
				
				/* bc_groups */
				if(typebc=="bc_groups"){
					var content="";
					if(jQuery("#bc_groups_amount").val()!=10) bcode +=' amount="'+jQuery("#bc_groups_amount").val()+'"';
					if(jQuery("input[type=radio][name=bc_groups_avatar]:checked").attr('value')==0){
						bcode +=' avatar="0"';
						avatar = false;
					}
					if(jQuery("#bc_groups_size").val()!=50 && avatar) bcode +=' size="'+jQuery("#bc_groups_size").val()+'"';
					
					if(jQuery("#bc_groups_type option:selected").val()!="popular") bcode +=' type="'+jQuery("#bc_groups_type option:selected").val()+'"';
					
					if(jQuery("#bc_groups_featured").val() !="") bcode += ' featured="'+jQuery("#bc_groups_featured").val()+'"';
					
					if(jQuery("#bc_groups_class").val()!="my_groups") bcode +=' class="'+jQuery("#bc_groups_class").val()+'"';
						
					if(jQuery("#bc_groups_content").val()!="") content = jQuery("#bc_groups_content").val()+'[/bc_groups]';
				
					if(bcode!="") bcode = '['+bcode+']';
					if(content!="") bcode = bcode + content;
				}
				
				/* bc_members */
				if(typebc=="bc_members"){
					if(jQuery("#bc_members_amount").val()!=10) bcode +=' amount="'+jQuery("#bc_members_amount").val()+'"';
					if(jQuery("input[type=radio][name=bc_members_avatar]:checked").attr('value')==0){
						bcode +=' avatar="0"';
						avatar = false;
					}
					if(jQuery("#bc_members_size").val()!=50 && avatar) bcode +=' size="'+jQuery("#bc_members_size").val()+'"';
					
					if(jQuery("#bc_members_type option:selected").val()!="active") bcode +=' type="'+jQuery("#bc_members_type option:selected").val()+'"';
					
					if(jQuery("#bc_members_featured").val() !="") bcode += ' featured="'+jQuery("#bc_members_featured").val()+'"';
					
					if(jQuery("#bc_members_class").val()!="my_members") bcode +=' class="'+jQuery("#bc_members_class").val()+'"';
				
					if(bcode!="") bcode = '['+bcode+']';
				}
				
				/* bc_friends */
				if(typebc=="bc_friends"){
					if(jQuery("#bc_friends_amount").val()!=10) bcode +=' amount="'+jQuery("#bc_friends_amount").val()+'"';
					if(jQuery("input[type=radio][name=bc_friends_avatar]:checked").attr('value')==0){
						bcode +=' avatar="0"';
						avatar = false;
					}
					if(jQuery("#bc_friends_size").val()!=50 && avatar) bcode +=' size="'+jQuery("#bc_friends_size").val()+'"';
					
					if(jQuery("#bc_friends_type option:selected").val()!="newest") bcode +=' type="'+jQuery("#bc_friends_type option:selected").val()+'"';
					
					if(jQuery("#bc_friends_class").val()!="my_friends") bcode +=' class="'+jQuery("#bc_friends_class").val()+'"';
					
					if(jQuery("input[type=radio][name=bc_friends_dynamic]:checked").attr('value')==1) bcode +=' dynamic="1"';
				
					if(bcode!="") bcode = '['+bcode+']';
				}
				
				/* bc_user_groups */
				if(typebc=="bc_user_groups"){
					if(jQuery("#bc_user_groups_amount").val()!=10) bcode +=' amount="'+jQuery("#bc_user_groups_amount").val()+'"';
					if(jQuery("input[type=radio][name=bc_user_groups_avatar]:checked").attr('value')==0){
						bcode +=' avatar="0"';
						avatar = false;
					}
					if(jQuery("#bc_user_groups_size").val()!=50 && avatar) bcode +=' size="'+jQuery("#bc_user_groups_size").val()+'"';
					
					if(jQuery("#bc_user_groups_type option:selected").val()!="popular") bcode +=' type="'+jQuery("#bc_user_groups_type option:selected").val()+'"';
					
					if(jQuery("#bc_user_groups_class").val()!="user_groups") bcode +=' class="'+jQuery("#bc_user_groups_class").val()+'"';
					
					if(jQuery("input[type=radio][name=bc_user_groups_dynamic]:checked").attr('value')==1) bcode +=' dynamic="1"';
				
					if(bcode!="") bcode = '['+bcode+']';
				}
				
				/* bc_messages */
				if(typebc=="bc_messages"){
					if(jQuery("#bc_messages_amount").val()!=5) bcode +=' amount="'+jQuery("#bc_messages_amount").val()+'"';
					if(jQuery("input[type=radio][name=bc_messages_subject]:checked").attr('value')==0){
						bcode +=' subject="0"';
					}
					if(jQuery("input[type=radio][name=bc_messages_avatar]:checked").attr('value')==0){
						bcode +=' avatar="0"';
						avatar = false;
					}
					if(jQuery("#bc_messages_size").val()!=30 && avatar) bcode +=' size="'+jQuery("#bc_messages_size").val()+'"';
					
					if(jQuery("#bc_messages_excerpt").val()!=10) bcode +=' excerpt="'+jQuery("#bc_messages_excerpt").val()+'"';
					
					if(jQuery("#bc_messages_class").val()!="my_messages") bcode +=' class="'+jQuery("#bc_messages_class").val()+'"';
				
					if(bcode!="") bcode = '['+bcode+']';
				}
				
				/* bc_notifications */
				if(typebc=="bc_notifications"){
					if(jQuery("#bc_notifications_amount").val()!=5) bcode +=' amount="'+jQuery("#bc_messages_amount").val()+'"';
					
					if(jQuery("input[type=radio][name=bc_notifications_avatar]:checked").attr('value')==0){
						bcode +=' avatar="0"';
						avatar = false;
					}
					if(jQuery("#bc_notifications_size").val()!=30 && avatar) bcode +=' size="'+jQuery("#bc_notifications_size").val()+'"';
					
					if(jQuery("#bc_notifications_class").val()!="my_notifications") bcode +=' class="'+jQuery("#bc_notifications_class").val()+'"';
				
					if(bcode!="") bcode = '['+bcode+']';
				}
				
				<?php if($blog_id==1):?>
					/* bc_blogs */
					if(typebc=="bc_blogs"){
						var content="";
						if(jQuery("#bc_blogs_amount").val()!=5) bcode +=' amount="'+jQuery("#bc_blogs_amount").val()+'"';
						if(jQuery("input[type=radio][name=bc_blogs_avatar]:checked").attr('value')==0){
							bcode +=' avatar="0"';
							avatar = false;
						}
						if(jQuery("#bc_blogs_size").val()!=50 && avatar) bcode +=' size="'+jQuery("#bc_blogs_size").val()+'"';

						if(jQuery("#bc_blogs_type option:selected").val()!="active") bcode +=' type="'+jQuery("#bc_blogs_type option:selected").val()+'"';

						if(jQuery("#bc_blogs_featured").val() !="") bcode += ' featured="'+jQuery("#bc_blogs_featured").val()+'"';

						if(jQuery("#bc_blogs_class").val()!="my_blogs") bcode +=' class="'+jQuery("#bc_blogs_class").val()+'"';

						if(jQuery("input[type=radio][name=bc_blogs_desc]:checked").attr('value')==0){
							bcode +=' desc="0"';
						}

						if(bcode!="") bcode = '['+bcode+']';
					}
				<?php endif;?>
				
				/* bc_posts */
				if(typebc=="bc_posts"){
					if(jQuery("#bc_posts_amount").val()!=5) bcode +=' amount="'+jQuery("#bc_posts_amount").val()+'"';
					if(jQuery("input[type=radio][name=bc_posts_avatar]:checked").attr('value')==0){
						bcode +=' avatar="0"';
						avatar = false;
					}
					if(jQuery("#bc_posts_size").val()!=50 && avatar) bcode +=' size="'+jQuery("#bc_posts_size").val()+'"';
					
					if(jQuery("#bc_posts_type option:selected").val()!="newest") bcode +=' type="'+jQuery("#bc_posts_type option:selected").val()+'"';
					
					if(jQuery("#bc_posts_class").val()!="my_blog_posts") bcode +=' class="'+jQuery("#bc_posts_class").val()+'"';
					
					if(jQuery("#bc_posts_excerpt").val()!=10) bcode +=' excerpt="'+jQuery("#bc_posts_excerpt").val()+'"';
				
					if(bcode!="") bcode = '['+bcode+']';
				}
				
				/* bc_restrict_gm */
				if(typebc=="bc_restrict_gm"){
					
					if(jQuery('#bc_restrict_group_id').val().length < 1){
						bcode="";
						alert('<?php _e('Please type the id or slug of a group', 'bowe-codes');?>');
					} else {
						bcode +=' group_id="'+jQuery("#bc_restrict_group_id").val()+'"';
						
						if(jQuery("#bc_restrict_class").val()!="my_restrict_message") bcode +=' class="'+jQuery("#bc_restrict_class").val()+'"';
						
					}
					
					if(bcode!="") bcode = '['+bcode+']<?php _e('The content to hide', 'bowe-codes');?>[/bc_restrict_gm]';
					
				}
				
				/* bc_forum */
				if(typebc=="bc_forum"){
					
					if(jQuery('#bc_forum_group_id').val().length >= 1)
						bcode +=' group_id="'+jQuery("#bc_forum_group_id").val()+'"';
					
					if(jQuery("#bc_forum_amount").val()!=5) bcode +=' amount="'+jQuery("#bc_forum_amount").val()+'"';
					if(jQuery("input[type=radio][name=bc_forum_avatar]:checked").attr('value')==0){
						bcode +=' avatar="0"';
						avatar = false;
					}
					if(jQuery("#bc_forum_size").val()!=50 && avatar) bcode +=' size="'+jQuery("#bc_forum_size").val()+'"';
					
					if(jQuery("#bc_forum_type option:selected").val()!="new_forum_topic") bcode +=' type="'+jQuery("#bc_forum_type option:selected").val()+'"';
					
					if(jQuery("#bc_forum_class").val()!="my_forum") bcode +=' class="'+jQuery("#bc_forum_class").val()+'"';
					
					if(jQuery("#bc_forum_excerpt").val()!=10) bcode +=' excerpt="'+jQuery("#bc_forum_excerpt").val()+'"';
				
					if(bcode!="") bcode = '['+bcode+']';
				}
				
				
				// sending the bcode if everything is ok !
				if(bcode!=""){
					var win = window.dialogArguments || opener || parent || top;
					win.send_to_editor(bcode);
				}
			});
			/* ]]> */
		</script>
</body>
</html>