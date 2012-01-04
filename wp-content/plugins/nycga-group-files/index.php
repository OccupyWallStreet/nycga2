<?php
/*
Plugin Name: NYCGA Group Files
PLugin URI: http://www.nycga.net
Description: Fork of BuddyPress Group Documents Plugin for NYCGA.net.  Now Multisite compatible, this plugin creates a document storage area within each group
Version: 1.0
Revision Date: December 28, 2011
Requires at least: WPMU 3.0, BuddyPress 1.5
Tested up to: WP 3.2.1, BuddyPress 1.5
License: Example: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: Rachel Baker
Author URI: http://www.rachelbaker.me
Network: true
*/

//some constants that can be checked when extending this plugin
define ( 'NYCGA_GROUP_FILES_IS_INSTALLED', 1 );
define ( 'NYCGA_GROUP_FILES_VERSION', '1.0' );
define ( 'NYCGA_GROUP_FILES_DB_VERSION', '3' );

//allow override of URL slug
if ( !defined( 'NYCGA_GROUP_FILES_SLUG' ) )
	define ( 'NYCGA_GROUP_FILES_SLUG', 'files' );

//we must hook this on to an action, otherwise it will get called before bp-custom.php
function nycga_group_files_set_constants() {

	//This is where to look for admin bulk uploads
	if( !defined( 'NYCGA_GROUP_FILES_ADMIN_UPLOAD_PATH') )
		define ( 'NYCGA_GROUP_FILES_ADMIN_UPLOAD_PATH', WP_PLUGIN_DIR . '/nycga-group-files/uploads/');

	//Widgets can be set to only show documents in certain (site-admin specified) groups
	if( !defined( 'NYCGA_GROUP_FILES_WIDGET_GROUP_FILTER' ) )
		define( 'NYCGA_GROUP_FILES_WIDGET_GROUP_FILTER', false );

	//if enabled, documents can be flagged as "featured"
	//widget will have an option to only show featured docs
	if( !defined( 'NYCGA_GROUP_FILES_FEATURED' ) )
		define( 'NYCGA_GROUP_FILES_FEATURED', false );

	//longer text descriptions to go with the documents can be toggled on or off.
	//this will toggle both the textarea input, and the display;
	if ( !defined( 'NYCGA_GROUP_FILES_SHOW_DESCRIPTIONS' ) )
		define ( 'NYCGA_GROUP_FILES_SHOW_DESCRIPTIONS', true );

}
add_action('plugins_loaded','nycga_group_files_set_constants');

//load i18n files if present
if ( file_exists( dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' ) ) {
	load_textdomain( 'nycga-group-files', dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' );
	load_plugin_textdomain( 'nycga-group-files', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

//Go get me some files!
require (dirname(__FILE__) . '/include/classes.php');
require (dirname(__FILE__) . '/include/cssjs.php');
require (dirname(__FILE__) . '/include/widgets.php');
require (dirname(__FILE__) . '/include/notifications.php');
require (dirname(__FILE__) . '/include/activity.php');
require (dirname(__FILE__) . '/include/templatetags.php');
require (dirname(__FILE__) . '/include/admin-uploads.php');
require (dirname(__FILE__) . '/include/filters.php');
require (dirname(__FILE__) . '/include/ajax.php');

//only get the group admin page if they have options to edit
if( get_option('nycga_group_files_use_categories') ||
	('mods_decide' == get_option( 'nycga_group_files_upload_permission' ) ) ) {
	require (dirname(__FILE__) . '/include/group-admin.php');
}

//only get the forum extension if it's been specified by the admin
if( get_option('nycga_group_files_forum_attachments') ) {
	require( dirname( __FILE__ ) . '/include/group-forum-attachments.php' );
}

/*************************************************************************
*********************SETUP AND INSTALLATION*******************************
*************************************************************************/

/**
 * NYCGA_GROUP_FILES_install()
 *
 * Installs and/or upgrades the database tables
 * This will only run if the database version constant is
 * greater than the stored database version value
 */
function nycga_group_files_install() {
	global $wpdb, $bp;

	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";

	$sql[] = "CREATE TABLE {$bp->group_files->table_name} (
		  		id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  		user_id bigint(20) NOT NULL,
		  		group_id bigint(20) NOT NULL,
		  		created_ts int NOT NULL,
				modified_ts int NOT NULL,
				file VARCHAR(255) NOT NULL,
				name VARCHAR(255) NULL,
				description TEXT NULL,
				featured BOOL DEFAULT FALSE,
				download_count bigint(20) NOT NULL DEFAULT 0,
			    KEY user_id (user_id),
			    KEY group_id (group_id),
				KEY created_ts (created_ts),
				KEY modified_ts (modified_ts),
				KEY download_count (download_count)
		 	   ) {$charset_collate};";

	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	dbDelta($sql);

	update_site_option( 'nycga-group-files-db-version', NYCGA_GROUP_FILES_DB_VERSION );
}


/**
 * NYCGA_GROUP_FILES_setup_globals()
 *
 * Sets up global variables for group documents
 */
function nycga_group_files_setup_globals() {
	global $bp, $wpdb;

	/* For internal identification */
	$bp->group_files->id = 'group_files';
	$bp->group_files->table_name = $wpdb->base_prefix . 'NYCGA_GROUP_FILES';
	$bp->group_files->format_notification_function = 'nycga_group_files_format_notifications';
	$bp->group_files->slug = NYCGA_GROUP_FILES_SLUG;

	/* Register this in the active components array */
	$bp->active_components[$bp->group_files->slug] = $bp->group_files->id;

	if( !defined( 'NYCGA_GROUP_FILES_THEME_VERSION' ) ) {
		$nycga_group_files_theme_version = substr( BP_VERSION, 0, 3 );
		define( 'NYCGA_GROUP_FILES_THEME_VERSION',$nycga_group_files_theme_version);
	}

	do_action('nycga_group_files_globals_loaded');
}
add_action( 'plugins_loaded', 'nycga_group_files_setup_globals', 5 );
	add_action( 'network_admin_menu', 'nycga_group_files_setup_globals', 2 );



/**
 * NYCGA_GROUP_FILES_check_installed()
 *
 * Adds the site administrator menu.
 * Checks to see if the DB tables exist or if we are running an old version
 * of the component. If the value has increased, it will run the installation function.
 * Also sets defaults for site admin options
 */
function NYCGA_GROUP_FILES_check_installed() {
	global $wpdb, $bp;

	if ( !current_user_can('manage_options') )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require (dirname(__FILE__) . '/include/admin.php');

	add_submenu_page( 'bp-general-settings', __( 'Group Files Admin', 'nycga-group-files' ), __( 'Group Files', 'nycga-group-files' ), 'manage_options', 'nycga-group-files-settings', 'NYCGA_GROUP_FILES_admin' );


	/* Need to check db tables are current */
	if ( get_site_option('nycga-group-files-db-version') < NYCGA_GROUP_FILES_DB_VERSION )
		NYCGA_GROUP_FILES_install();

	//set admin option defaults if they're not already defined
	add_option('NYCGA_GROUP_FILES_valid_file_formats', 'odt,rtf,txt,doc,docx,xls,xlsx,ppt,pps,pptx,pdf,jpg,jpeg,gif,png,zip,tar,gz' );
	add_option('NYCGA_GROUP_FILES_items_per_page', 20 );
	add_option('NYCGA_GROUP_FILES_upload_permission','members');
	add_option('NYCGA_GROUP_FILES_display_icons', true );
	add_option('NYCGA_GROUP_FILES_use_categories', false );
	add_option('NYCGA_GROUP_FILES_enable_all_groups',true);
	add_option('NYCGA_GROUP_FILES_progress_bar',true);
	add_option('NYCGA_GROUP_FILES_forum_attachments',false);
}

	add_action( 'network_admin_menu', 'NYCGA_GROUP_FILES_check_installed',50);


/**************************************************************************
*************************LOADING AND STAGING*******************************
***************************************************************************/


/**
 * NYCGA_GROUP_FILES_setup_nav()
 *
 * Sets up the navigation items for the component.  
 * Adds documents item under the group navigation
 */
function NYCGA_GROUP_FILES_setup_nav() {
	global $bp,$current_blog,$group_object;

	if( !class_exists('BP_Groups_Group') )
		return false;

	if ( !isset($bp->groups->current_group->id) ) {
		return false;
	}

	//if documents have been explicitly deactivated for this group, do not show menu
	if( !get_option('NYCGA_GROUP_FILES_enable_all_groups') && groups_get_groupmeta($bp->groups->current_group->id, 'group_documents_documents_disabled'))
		return false;

	$groups_link = $bp->root_domain . '/' . $bp->groups->slug . '/';
    if ( isset( $bp->groups->current_group->slug ) )
        $groups_link .= $bp->groups->current_group->slug . '/';

	/* Add the subnav item only to the single group nav item*/
	if ( $bp->is_single_item )
    bp_core_new_subnav_item( array( 
		'name' => __( 'Files', 'nycga-group-files' ), 
		'slug' => $bp->group_documents->slug, 
		'parent_url' => $groups_link, 
		'parent_slug' => bp_get_current_group_slug(),
		'screen_function' => 'NYCGA_GROUP_FILES_display', 
		'position' => 35, 
		'user_has_access' => $bp->groups->current_group->user_has_access,
		'item_css_id' => 'group-documents' ) );

	do_action('NYCGA_GROUP_FILES_nav_setup');
}
add_action( 'bp_setup_nav', 'NYCGA_GROUP_FILES_setup_nav', 2 );

	add_action( 'network_admin_menu', 'NYCGA_GROUP_FILES_setup_nav', 2 );



/**
 * bp_group_document_set_cookies()
 *
 * Set any cookies for our component.  This will usually be for list filtering and sorting.
 * We must create a dedicated function for this, to fire before the headers are sent
 * (doing this in the template object with the rest of the filtering/sorting is too late)
 */
function NYCGA_GROUP_FILES_set_cookies() {
	if( isset( $_GET['order'] ) ){
		setcookie('nycga-group-files-order',$_GET['order'],time()+60*60+24); //expires in one day
	}
	if( isset( $_GET['category'] ) ) {
		setcookie('nycga-group-files-category',$_GET['category'],time()+60*60*24);
	}
}
add_action('plugins_loaded','NYCGA_GROUP_FILES_set_cookies');


/**
 * NYCGA_GROUP_FILES_register_taxonomies()
 *
 * registers the taxonomies to use with the Wordpress Custom Taxonomy API
 */
function NYCGA_GROUP_FILES_register_taxonomies() {
	register_taxonomy('group-documents-category','group-document',array('hierarchical'=>true,'label'=>__('Group File Categories','nycga-group-files'),'query_var'=>false));
}
add_action('init','NYCGA_GROUP_FILES_register_taxonomies');


/**
 * NYCGA_GROUP_FILES_display()
 *
 * Sets up the plugin template file and calls the dislay output function
 */
function NYCGA_GROUP_FILES_display() {
	global $bp;

	do_action( 'NYCGA_GROUP_FILES_display' );

	add_action( 'bp_template_content_header', 'NYCGA_GROUP_FILES_display_header' );
	add_action( 'bp_template_title', 'NYCGA_GROUP_FILES_display_title' );
	add_action( 'bp_template_content', 'NYCGA_GROUP_FILES_display_content' );

	// Load the plugin template file.
	// BP 1.2 breaks it out into a group-specific template
	// BP 1.1 includes a generic "plugin-template file
	//this is a roundabout way of doing it, because I can't find a way to use bp_core_template
	//to either return a useful value or handle an array of templates
	$templates = array('groups/single/plugins.php','plugin-template.php');
	if( strstr( locate_template($templates), 'groups/single/plugins.php' ) ) {
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'groups/single/plugins' ) );
	} else {
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'plugin-template' ) );
	}

}


function NYCGA_GROUP_FILES_display_header() {
	_e( 'Group Files', 'nycga-group-files' );
}
function NYCGA_GROUP_FILES_display_title() {
	_e( 'File List', 'nycga-group-files' );
}

/****************************************************************************
************************BEGIN MAIN DISPLAY***********************************
****************************************************************************/

function NYCGA_GROUP_FILES_display_content() {
	global $bp;

	//instanciating the template will do the heavy lifting with all the superglobal variables
	$template = new NYCGA_GROUP_FILES_Template();?>

	<div id="nycga-group-files">

	<?php do_action( 'template_notices' ) // (error/success feedback) ?>

	<?php //-----------------------------------------------------------------------LIST VIEW-- ?>

	<?php if( $template->document_list && count($template->document_list >= 1) ) { ?>

		<?php if( get_option('NYCGA_GROUP_FILES_use_categories')) { ?>
		<div id="nycga-group-files-categories">
			<form id="nycga-group-files-category-form" method="get" action="<?php echo $template->action_link; ?>">
			 &nbsp; <?php echo __('Category:','nycga-group-files'); ?>
			<select name="category">
				<option value="" ><?php echo __('All','nycga-group-files'); ?></option>
				<?php foreach( $template->get_group_categories() as $category ) { ?>
				<option value="<?php echo $category->term_id; ?>" <?php if( $template->category == $category->term_id ) echo 'selected="selected"'; ?>><?php echo $category->name; ?></option>
				<?php } ?>
			</select>
			<input type="submit" class="button" value="<?php echo __('Go','nycga-group-files'); ?>" />
			</form>
		</div>
		<?php } ?>

		<div id="nycga-group-files-sorting">
			<form id="nycga-group-files-sort-form" method="get" action="<?php echo $template->action_link; ?>">
			<?php _e('Order by:','nycga-group-files'); ?>
			<select name="order">
				<option value="newest" <?php if( 'newest' == $template->order ) echo 'selected="selected"'; ?>><?php _e('Newest','nycga-group-files'); ?></option>
				<option value="alpha" <?php if( 'alpha' == $template->order ) echo 'selected="selected"'; ?>><?php _e('Alphabetical','nycga-group-files'); ?></option>
				<option value="popular" <?php if( 'popular' == $template->order ) echo 'selected="selected"'; ?>><?php _e('Most Popular','nycga-group-files'); ?></option>
			</select>
			<input type="submit" class="button" value="<?php _e('Go','nycga-group-files'); ?>" />
			</form>
		</div>


		<?php if( '1.1' != substr(BP_VERSION,0,3) ) { ?>
		<h3><?php _e('File List','nycga-group-files'); ?></h3>
		<?php } ?>


		<div class="pagination no-ajax">
			<div id="group-documents-page-count" class="pag-count">
				<?php $template->pagination_count(); ?>
			</div>
		<?php if( $template->show_pagination() ){ ?>
			<div id="group-documents-page-links" class="pagination-links">
				<?php $template->pagination_links(); ?>
			</div>
		<?php } ?>
		</div>

		<?php if( '1.1' == substr(BP_VERSION,0,3) ) { ?>
			<ul id="forum-topic-list" class="item-list">
		<?php } else { ?>
			<ul id="nycga-group-files-list" class="item-list">
		<?php } ?>

		<?php //loop through each document and display content along with admin options
		$count = 0;
		foreach( $template->document_list as $document_params ) {
			$document = new NYCGA_GROUP_FILES($document_params['id'], $document_params); ?>

			<li <?php if( ++$count%2 ) echo 'class="alt"';?> >
			<?php if( get_option( 'NYCGA_GROUP_FILES_display_icons' )) $document->icon(); ?>

			<a class="group-documents-title" id="group-document-link-<?php echo $document->id; ?>" href="<?php $document->url(); ?>" target="_blank"><?php echo $document->name; ?>

			<?php if( get_option( 'NYCGA_GROUP_FILES_display_file_size' )) { echo ' <span class="group-documents-filesize">(' . get_file_size( $document ) . ')</span>'; } ?></a> &nbsp;
			
			<span class="group-documents-meta"><?php printf( __( 'Uploaded by %s on %s', 'nycga-group-files'),bp_core_get_userlink($document->user_id),date( get_option('date_format'), $document->created_ts )); ?></span>

			<?php if( NYCGA_GROUP_FILES_SHOW_DESCRIPTIONS && $document->description ){ echo '<br /><span class="group-documents-description">' . nl2br($document->description) . '</span>'; }

			//show edit and delete options if user is privileged
			echo '<div class="admin-links">';
			if( $document->current_user_can('edit') ) {
				$edit_link = wp_nonce_url( $template->action_link . 'edit/' . $document->id, 'group-documents-edit-link' );
				echo "<a href='$edit_link'>" . __('Edit','nycga-group-files') . "</a> | ";
			}
			if( $document->current_user_can('delete') ) {
				$delete_link = wp_nonce_url( $template->action_link . 'delete/' . $document->id, 'group-documents-delete-link' );
				echo "<a href='$delete_link' id='nycga-group-files-delete'>" . __('Delete','nycga-group-files') . "</a>";
			}

			echo '</div>';
			echo '</li>';
		} ?>
		</ul>

	<?php } else { ?>
	<div id="message" class="info">
		<p><?php _e( 'There have been no files uploaded for this group', 'nycga-group-files') ?></p>
	</div>

	<?php } ?>
	<div class="spacer">&nbsp;</div>

	<?php //-------------------------------------------------------------------DETAIL VIEW-- ?>

	<?php if( $template->show_detail ){ ?>

	<?php if( $template->operation == 'add' ) { ?>
	<div id="nycga-group-files-upload-new">
	<?php } else { ?>
	<div id="nycga-group-files-edit">
	<?php } ?>

	<h3><?php echo $template->header ?></h3>

	<form method="post" id="nycga-group-files-form" class="standard-form" action="<?php echo $template->action_link; ?>" enctype="multipart/form-data" />
	<input type="hidden" name="NYCGA_GROUP_FILES_operation" value="<?php echo $template->operation; ?>" />
	<input type="hidden" name="NYCGA_GROUP_FILES_id" value="<?php echo $template->id; ?>" />

		<?php if( $template->operation == 'add' ) { ?>

		<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo return_bytes(ini_get('post_max_size')) ?>"
		<label><?php _e('Choose File:','nycga-group-files'); ?></label>
		<input type="file" name="NYCGA_GROUP_FILES_file" class="nycga-group-files-file" />
		<?php } ?>

		<?php if( NYCGA_GROUP_FILES_FEATURED ) { ?>
		<label class="nycga-group-files-featured-label"><input type="checkbox" name="NYCGA_GROUP_FILES_featured" class="nycga-group-files-featured" value="1" <?php if( $template->featured ) echo 'checked="checked"'; ?> > <?php _e('Featured File','nycga-group-files'); ?></label>
		<?php } ?>

		<div id="document-detail-clear" class="clear"></div>
		<div class="document-info">
		<label><?php _e('Display Name:','nycga-group-files'); ?></label>
		<input type="text" name="NYCGA_GROUP_FILES_name" id="nycga-group-files-name" value="<?php echo $template->name ?>" />
		<?php if( NYCGA_GROUP_FILES_SHOW_DESCRIPTIONS ) { ?>
		<label><?php _e('Description:', 'nycga-group-files'); ?></label>
		<textarea name="NYCGA_GROUP_FILES_description" id="nycga-group-files-description"><?php echo $template->description; ?></textarea>
		<?php } ?>
		<label></label>
		<input type="submit" class="button" value="<?php _e('Submit','nycga-group-files'); ?>" />
		</div>

		<?php if( get_option('NYCGA_GROUP_FILES_use_categories')) { ?>
		<div class="nycga-group-files-category-wrapper">
		<label><?php _e('Category:','nycga-group-files'); ?></label>
		<div class="nycga-group-files-category-list">
			<ul>
			<?php foreach( $template->get_group_categories(false) as $category ) { ?>
				<li><input type="checkbox" name="NYCGA_GROUP_FILES_categories[]" value="<?php echo $category->term_id; ?>" <?php if( $template->doc_in_category($category->term_id)) echo 'checked="checked"'; ?> /><?php echo $category->name; ?></li>
			<?php } ?>
			</ul>
		</div>
		<input type="text" name="NYCGA_GROUP_FILES_new_category" class="nycga-group-files-new-category" />
		</div><!-- .nycga-group-files-category-wrapper -->
		<?php } ?>

	</form>
	</div><!--end #post-new-topic-->

	<?php if( $template->operation == 'add' ) { ?>
	<a class="button" id="nycga-group-files-upload-button" href="" style="display:none;"><?php _e('Upload a New File','nycga-group-files'); ?></a>
	<?php } ?>

	<?php } ?>

	</div><!--end #group-documents-->
<?php }



/*************************************************************************
***********************EVERYTHING ELSE************************************
*************************************************************************/

/*
 * NYCGA_GROUP_FILES_delete()
 *
 * after perfoming several validation checks, deletes both the uploaded
 * file and the reference in the database
 */
function NYCGA_GROUP_FILES_delete( $id ) {
	if( !ctype_digit( $id ) ) {
		bp_core_add_message( __('The item to delete could not be found','nycga-group-files'),'error');
		return false;
	}

	//check nonce
	if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'group-documents-delete-link')) {
	  bp_core_add_message( __('There was a security problem', 'nycga-group-files'), 'error' );
	  return false;
	}

	$document = new NYCGA_GROUP_FILES($id);
	if( $document->current_user_can('delete') ){
		if( $document->delete() ){
			do_action('NYCGA_GROUP_FILES_delete_success',$document);
			return true;
		}
	}
	return false;
}

/*
 * NYCGA_GROUP_FILES_check_ext()
 *
 * checks whether the passed filename ends in an extension
 * that is allowed by the site admin
 */
function NYCGA_GROUP_FILES_check_ext( $filename ) {

	if( !$filename ) {
		return false;
	}

	$valid_formats_string = get_option( 'NYCGA_GROUP_FILES_valid_file_formats');
	$valid_formats_array = explode( ',', $valid_formats_string );

	$extension = substr($filename,(strrpos($filename, ".")+1));
	$extension =  strtolower($extension);

	if(in_array($extension, $valid_formats_array)){
		return true;
	}
	return false;
}


/*
 * get_file_size()
 *
 * returns a human-readable file-size for the passed file
 * adapted from a function in the PHP manual comments
 */
function get_file_size( $document, $precision = 1 ) {

    $units = array('b', 'k', 'm', 'g');
  
	$bytes = filesize( $document->get_path(1) );
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
  
    $bytes /= pow(1024, $pow);
  
    return round($bytes, $precision) .  $units[$pow];
}

/**
 * return_bytes()
 *
 * taken from the PHP manual examples.  Returns the number of bites
 * when given an abrevition (eg, max_upload_size)
 */
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

/**
 * NYCGA_GROUP_FILES_remove_data()
 *
 * Cleans out both the files and the database records when a group is deleted
 */
function NYCGA_GROUP_FILES_remove_data( $group_id ) {

	$results = NYCGA_GROUP_FILES::get_list_by_group( $group_id );
	if( count( $results ) >= 1 ) {
		foreach($results as $document_params) {
			$document = new NYCGA_GROUP_FILES( $document_params['id'], $document_params);
			$document->delete();
			do_action('NYCGA_GROUP_FILES_delete_with_group',$document);
		}
	}
}
add_action('groups_group_deleted','NYCGA_GROUP_FILES_remove_data');

?>
