<?php
require_once (preg_replace("/wp-content.*/","wp-blog-header.php",__FILE__));
require_once (preg_replace("/wp-content.*/","/wp-admin/includes/admin.php",__FILE__));

//redirect to the login page if user is not authenticated
auth_redirect();

if(!RGForms::current_user_can_any(array("gravityforms_edit_forms", "gravityforms_create_form")))
    die(__("You don't have adequate permission to preview forms.", "gravityforms"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta http-equiv="Imagetoolbar" content="No" />
  	   <title><?php _e("Form Preview", "gravityforms") ?></title>
        <link rel='stylesheet' href='<?php echo RGForms::get_base_url() ?>/css/reset.css' type='text/css' />
        <link rel='stylesheet' href='<?php echo RGForms::get_base_url() ?>/css/preview.css' type='text/css' />
        <link rel='stylesheet' href='<?php echo RGForms::get_base_url() ?>/css/forms.css' type='text/css' />
    </head>
    <body>
    <div id="preview_hdr"><span class="actionlinks"><a href="javascript:window.close()" class="close_window"><?php _e("close window", "gravityforms") ?></a></span><?php _e("Form Preview", "gravityforms") ?></div>
        <?php
        echo RGForms::get_form($_GET["id"], true, true, true);
        wp_print_scripts();
        ?>
    </body>
</html>
