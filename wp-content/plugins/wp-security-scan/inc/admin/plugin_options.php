<?php
function mrt_sub4(){

	mrt_wpss_menu_head('Plugin options');

	?>

          <div class="metabox-holder">
              <div class="postbox" style="width: 60%;">
                  <h3 class="hndle"><span><?php echo __('Plugin options');?></span></h3>
                  <div class="inside">
                      <p></p>
<?php

    if (function_exists('wp_create_nonce')){
        $wsdwpss_opt_nonce = wp_create_nonce();
    }
    else {$wsdwpss_opt_nonce = '';}

//# 10/04/2011
$_checked = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (function_exists('check_admin_referer')) {
        check_admin_referer('_wsdwpss_opt_wpnonce');
        $_nonce = $_POST['_wsdwpss_opt_wpnonce'];
        if (empty($_nonce) || ($_nonce <> $wsdwpss_opt_nonce)){
            wp_die("Invalid request!");
        }
    }
    if($_POST['show_rss_widget'] == 'on'){
        update_option('WSD-RSS-WGT-DISPLAY', 'yes');
        $_checked = true;
    }
    else {
        update_option('WSD-RSS-WGT-DISPLAY', 'no');
        $_checked = false;
    }
}
$wsdRssWidgetVisible = get_option('WSD-RSS-WGT-DISPLAY');
if (empty($wsdRssWidgetVisible) || $wsdRssWidgetVisible=='yes') {
    add_option('WSD-RSS-WGT-DISPLAY', 'yes');
    $_checked = true;
}
else {
    if (strtolower($wsdRssWidgetVisible) == 'no') {
        $_checked = false;
    }
}
//@++
?>
<div class="acx-section-box">

    <form id="plugin_options_form" method="post">
	<?php if (function_exists('wp_nonce_field')) {
        echo '<input type="hidden" name="_wsdwpss_opt_wpnonce" value="'.$wsdwpss_opt_nonce.'" />';
        wp_nonce_field('_wsdwpss_opt_wpnonce');
        }
        ?>
        <div>
            <input type="checkbox" name="show_rss_widget" id="show_rss_widget" <?php echo ($_checked ? 'checked="checked"' : '');?> />
            <label for="show_rss_widget"><?php echo __("Show the WebsiteDefender News dashboard widget");?></label>
        </div>

        <div>
            <p style="margin-top: 25px">
                <input type="submit" class="button-primary" value="<?php echo __('Update');?>"/>
            </p>
        </div>
    </form>

</div>
                      <p></p>
                  </div>
              </div>
          </div>


<?php
mrt_wpss_menu_footer();

} ?>
