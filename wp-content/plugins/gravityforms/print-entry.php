<?php
require_once (preg_replace("/wp-content.*/","wp-blog-header.php",__FILE__));
require_once (preg_replace("/wp-content.*/","/wp-admin/includes/admin.php",__FILE__));

//redirect to the login page if user is not authenticated
auth_redirect();

if(!RGForms::current_user_can_any("gravityforms_view_entries"))
    die(__("You don't have adequate permission to view entries.", "gravityforms"));

    $form_id = absint($_GET["fid"]);
    $lead_id = absint($_GET["lid"]);

    if(empty($form_id) || empty($lead_id))
        die(__("Form Id and Lead Id are required parameters.", "gravityforms"));

    $form = RGFormsModel::get_form_meta($form_id);
    $lead = RGFormsModel::get_lead($lead_id);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="MSSmartTagsPreventParsing" content="true" />
  <meta name="Robots" content="noindex, nofollow" />
  <meta http-equiv="Imagetoolbar" content="No" />
  <title>Print Preview : <?php echo $form["title"] ?> : <?php _e("Entry # ", "gravityforms") ?> <?php echo $lead_id ?></title>
  <link rel='stylesheet' href='<?php echo RGForms::get_base_url() ?>/css/print.css' type='text/css' />
    </head>
	<body onload="window.print();">

	<div id="print-preview-header"><span class="actionlinks"><a href="javascript:;" onclick="window.print();" class="header-print-link">print this page</a> | <a href="javascript:window.close()" class="close_window"><?php _e("close window", "gravityforms") ?></a></span> Print Preview</div>
		<div id="view-container">
        <?php

        RGForms::lead_detail_grid($form, $lead);

        if($_GET["notes"]){
            $notes = RGFormsModel::get_lead_notes($lead["id"]);
            RGForms::notes_grid($notes, false);
        }
        ?>
		</div>
	</body>
</html>