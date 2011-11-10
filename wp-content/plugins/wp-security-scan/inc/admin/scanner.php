<?php
function mrt_sub0(){

mrt_wpss_menu_head('WP - Security Scan');?>

          <div class="metabox-holder">
              <div class="postbox">
                  <h3 class="hndle"><span><?php echo __('Directory Info');?></span></h3>
                  <div class="inside">
<table id="wsd_permissions_table" width="100%"  border="0" cellspacing="0" cellpadding="3" 
       style="text-align:center; border: solid 1px #333;">
         <thead style="background: #333;">
            <th style="border:0px; padding: 4px 4px;"><strong style="color: #f5f5f5">Name</strong></th>
            <th style="border:0px; padding: 4px 4px;"><strong style="color: #f5f5f5">File/Dir</strong></th>
            <th style="border:0px; padding: 4px 4px;"><strong style="color: #f5f5f5">Needed Chmod</strong></th>
            <th style="border:0px; padding: 4px 4px;"><strong style="color: #f5f5f5">Current Chmod</strong></th>
        </thead>
        <tbody>
    <?php
        // DIR_NAME | DIR_PATH | EXPECTED_PERMISSION
        check_perms("root directory","../","0755");
        check_perms("wp-includes/","../wp-includes","0755");
        check_perms(".htaccess","../.htaccess","0644");
        check_perms("wp-admin/index.php","index.php","0644");
        check_perms("wp-admin/js/","js/","0755");
        check_perms("wp-content/themes/","../wp-content/themes","0755");
        check_perms("wp-content/plugins/","../wp-content/plugins","0755");
        check_perms("wp-admin/","../wp-admin","0755");
        check_perms("wp-content/","../wp-content","0755");
    ?>
        </tbody>
</table>

                  </div></div></div>
<?php
   mrt_wpss_menu_footer();
 } ?>
