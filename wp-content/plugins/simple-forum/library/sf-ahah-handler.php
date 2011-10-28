<?php
/*
Simple:Press
Public - ahah handler
$LastChangedDate: 2010-08-14 04:52:34 -0700 (Sat, 14 Aug 2010) $
$Rev: 4424 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}


function sf_ahah_handler($wp)
{
    # only process our ahah requests
    if (array_key_exists('sf_ahah', $wp->query_vars))
    {
		header("Content-Type: text/html; charset=".get_option('blog_charset'));

		# process the request
        switch ($wp->query_vars['sf_ahah'])
        {
            case 'acknowledge':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-acknowledge.php');
                break;

            case 'regpolicy':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-policy.php');
                break;

            case 'privpolicy':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-policy.php');
                break;

            case 'adminlinks':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-adminlinks.php');
                break;

            case 'admintools':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-admintools.php');
                break;

            case 'announce':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-announce.php');
                break;

            case 'autoupdate':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-autoupdate.php');
                break;

            case 'categories':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-categories.php');
                break;

            case 'moderation':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-moderation.php');
                break;

            case 'newposts':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-newposts.php');
                break;

            case 'permissions':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-permissions.php');
                break;

            case 'postrating':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-postrating.php');
                break;

            case 'quickreply':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-quickreply.php');
                break;

            case 'quote':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-quote.php');
                break;

            case 'tags':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-tags.php');
                break;

            case 'watch-subs':
                include (SF_PLUGIN_DIR.'/forum/ahah/sf-ahah-watch-subs.php');
                break;

            case 'common':
                include (SF_PLUGIN_DIR.'/library/ahah/sfc-ahah.php');
                break;

            case 'pm-manage':
                include (SF_PLUGIN_DIR.'/messaging/ahah/sf-ahah-pm-manage.php');
                break;

            case 'profile':
                include (SF_PLUGIN_DIR.'/profile/ahah/sf-ahah-profile.php');
                break;

            case 'profile-save':
                include (SF_PLUGIN_DIR.'/profile/ahah/sf-ahah-profile-save.php');
                break;

            case 'post':
                include (SF_PLUGIN_DIR.'/library/sf-post.php');
                break;

            case 'pm-post':
                include (SF_PLUGIN_DIR.'/library/sf-pm-post.php');
                break;

            case 'search':
                include (SF_PLUGIN_DIR.'/library/sf-search.php');
                break;

            case 'tag-suggest':
                include (SF_PLUGIN_DIR.'/library/sf-tags.php');
                break;

            case 'linking':
            	include (SF_PLUGIN_DIR.'/linking/ahah/sf-ahah-linking.php');
            	break;

            case 'help':
            	include (SF_PLUGIN_DIR.'/admin/library/ahah/sfa-ahah-help.php');
            	break;

            case 'admins-loader':
            	include (SF_PLUGIN_DIR.'/admin/panel-admins/ahah/sfa-ahah-admins-loader.php');
            	break;

            case 'components-loader':
            	include (SF_PLUGIN_DIR.'/admin/panel-components/ahah/sfa-ahah-components-loader.php');
            	break;

            case 'config-loader':
            	include (SF_PLUGIN_DIR.'/admin/panel-config/ahah/sfa-ahah-config-loader.php');
            	break;

            case 'forums-loader':
            	include (SF_PLUGIN_DIR.'/admin/panel-forums/ahah/sfa-ahah-forums-loader.php');
            	break;

            case 'options-loader':
            	include (SF_PLUGIN_DIR.'/admin/panel-options/ahah/sfa-ahah-options-loader.php');
            	break;

            case 'permissions-loader':
            	include (SF_PLUGIN_DIR.'/admin/panel-permissions/ahah/sfa-ahah-permissions-loader.php');
            	break;

            case 'profiles-loader':
            	include (SF_PLUGIN_DIR.'/admin/panel-profiles/ahah/sfa-ahah-profiles-loader.php');
            	break;

            case 'tags-loader':
            	include (SF_PLUGIN_DIR.'/admin/panel-tags/ahah/sfa-ahah-tags-loader.php');
            	break;

            case 'toolbox-loader':
            	include (SF_PLUGIN_DIR.'/admin/panel-toolbox/ahah/sfa-ahah-toolbox-loader.php');
            	break;

            case 'integration-loader':
            	include (SF_PLUGIN_DIR.'/admin/panel-integration/ahah/sfa-ahah-integration-loader.php');
            	break;

            case 'integration-perm':
            	include (SF_PLUGIN_DIR.'/admin/panel-integration/ahah/sfa-ahah-integration-perm.php');
            	break;

            case 'usergroups-loader':
            	include (SF_PLUGIN_DIR.'/admin/panel-usergroups/ahah/sfa-ahah-usergroups-loader.php');
            	break;

            case 'users-loader':
            	include (SF_PLUGIN_DIR.'/admin/panel-users/ahah/sfa-ahah-users-loader.php');
            	break;

            case 'components':
            	include (SF_PLUGIN_DIR.'/admin/panel-components/ahah/sfa-ahah-components.php');
            	break;

            case 'forums':
            	include (SF_PLUGIN_DIR.'/admin/panel-forums/ahah/sfa-ahah-forums.php');
            	break;

            case 'profiles':
            	include (SF_PLUGIN_DIR.'/admin/panel-profiles/ahah/sfa-ahah-profiles.php');
            	break;

            case 'admin-tags':
            	include (SF_PLUGIN_DIR.'/admin/panel-tags/support/sfa-tags-tags.php');
            	break;

            case 'toolbox':
            	include (SF_PLUGIN_DIR.'/admin/panel-toolbox/ahah/sfa-ahah-toolbox.php');
            	break;

            case 'usergroups':
            	include (SF_PLUGIN_DIR.'/admin/panel-usergroups/ahah/sfa-ahah-usergroups.php');
            	break;

            case 'subswatches':
            	include (SF_PLUGIN_DIR.'/admin/panel-users/ahah/sfa-ahah-subswatches.php');
            	break;

            case 'user':
            	include (SF_PLUGIN_DIR.'/admin/panel-users/ahah/sfa-ahah-user.php');
            	break;

            case 'upgrade':
            	include (SF_PLUGIN_DIR.'/install/sf-upgrade.php');
            	break;

            case 'install':
            	include (SF_PLUGIN_DIR.'/install/sf-install.php');
            	break;

            case 'uploader':
            	include (SF_PLUGIN_DIR.'/resources/jscript/ajaxupload/sf-uploader.php');
            	break;

            case 'filemanagerbrowse':
            	include (SF_PLUGIN_DIR.'/editors/tinymce/plugins/filemanager/fm-browse-tab.php');
            	break;

            case 'filemanagerupload':
            	include (SF_PLUGIN_DIR.'/editors/tinymce/plugins/filemanager/fm-upload-tab.php');
            	break;

            case 'filemanageredit':
            	include (SF_PLUGIN_DIR.'/editors/tinymce/plugins/filemanager/fm-edit-tab.php');
            	break;

            case 'filemanagerfolder':
            	include (SF_PLUGIN_DIR.'/editors/tinymce/plugins/filemanager/fm-folder-tab.php');
            	break;

            case 'filemanagerprocess':
            	include (SF_PLUGIN_DIR.'/editors/tinymce/plugins/filemanager/upload_process.php');
            	break;
        }
    }
}
?>