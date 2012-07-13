<?php

//For backwards compatibility, load wordpress if it hasn't been loaded yet
//Will be used if this file is being called directly
if(!class_exists("RGForms")){
    for ( $i = 0; $i < $depth = 10; $i++ ) {
        $wp_root_path = str_repeat( '../', $i );

        if ( file_exists("{$wp_root_path}wp-load.php" ) ) {
            require_once("{$wp_root_path}wp-load.php");
            require_once("{$wp_root_path}wp-admin/includes/admin.php");
            break;
        }
    }

    //redirect to the login page if user is not authenticated
    auth_redirect();
}

if(!GFCommon::current_user_can_any(array("gravityforms_edit_forms", "gravityforms_create_form", "gravityforms_preview_forms")))
    die(__("You don't have adequate permission to preview forms.", "gravityforms"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <meta http-equiv="Imagetoolbar" content="No" />
        <title><?php _e("Form Preview", "gravityforms") ?></title>
        <link rel='stylesheet' href='<?php echo GFCommon::get_base_url() ?>/css/reset.css' type='text/css' />
        <link rel='stylesheet' href='<?php echo GFCommon::get_base_url() ?>/css/forms.css' type='text/css' />
        <link rel='stylesheet' href='<?php echo GFCommon::get_base_url() ?>/css/preview.css' type='text/css' />
        <?php
            require_once(GFCommon::get_base_path() . "/form_display.php");
            $form = RGFormsModel::get_form_meta($_GET["id"]);
            GFFormDisplay::enqueue_form_scripts($form);
            wp_print_scripts();

            $styles = apply_filters("gform_preview_styles", false, $form);
            if(!empty($styles)){
                wp_print_styles($styles);
            }
        ?>
    </head>
    <body>
    <div id="preview_top">
	    <div id="preview_hdr">
		    <div><span class="actionlinks"><a href="javascript:window.close()" class="close_window"><?php _e("close window", "gravityforms") ?></a></span><?php _e("Form Preview", "gravityforms") ?></div>
	    </div>
	    <div id="preview_note"><?php _e("Note: This is a simple form preview. This form may display differently when added to your page based on inheritance from individual theme styles.", "gravityforms") ?></div>
    </div>
    <div id="preview_form_container">
        <?php
        echo RGForms::get_form($_GET["id"], true, true, true);

        ?>
        </div>
        <?php
        do_action("gform_preview_footer");
        ?>
    </body>
</html>