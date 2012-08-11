<?php include ( TEMPLATEPATH . '/options-var.php' );

$tn_blogsmu_featured_sub_intro_status = "";
if($tn_blogsmu_featured_sub_intro_status == "enable" || $tn_blogsmu_featured_sub_intro_status == "") { ?>
<div id="top-content">
<div id="wrap-top-content">
<div id="content-top-content">
<div class="one-community"><?php echo stripslashes($tn_blogsmu_featured_sub_intro_post); ?></div>
</div>
</div>
</div>
<?php } ?>

<?php if($tn_blogsmu_section_status == "enable" || $tn_blogsmu_section_status == "") { ?>

<div id="intro-content">
<div id="wrap-intro-content">

<?php if($bp_existed == 'true') { ?>
<?php if($tn_blogsmu_home_feat_groups == 'enable' || $tn_blogsmu_home_feat_groups == '') { ?>
<?php if( groups_get_total_group_count() != '0') { ?>
<?php locate_template ( array('lib/templates/bp-template/random-groups.php'), true ); ?>
<?php } ?>
<?php } } ?>

<div id="content-intro-content">

<div id="section1" class="services-box">
<?php if($tn_blogsmu_section_one_headline != "") { ?>
<h2><?php if($tn_blogsmu_section_one_headline == "") { ?>
<?php _e("Section intro 1",TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php echo stripslashes($tn_blogsmu_section_one_headline); ?>
<?php } ?></h2>
<p class="stext">
<?php if($tn_blogsmu_section_one_post_text == ""){ ?>
<?php _e('You can replace this area with a new text of your choice in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and also place widget in this area',TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php
$com_short = $tn_blogsmu_section_one_post_text;
$chars = 999;
$com_short = $com_short . " ";
$com_short = substr($com_short,0,$chars);
$com_short = substr($com_short,0,strrpos($com_short,' '));
$com_short = $com_short . "";
?>
<?php
if( function_exists('do_shortcode') ) {
echo do_shortcode(stripslashes($com_short));
} else {
echo stripslashes($com_short);
}
?>
<?php }  ?>
</p>
<?php } // if section empty..lets ignore and leave it blank ?>

<?php if($tn_blogsmu_section_widget_status != "hide") { ?>
<ul class="hlist"><?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar( __('header 1', TEMPLATE_DOMAIN) )) : ?><?php endif; ?></ul>
<?php } else { ?>
<?php if (is_user_logged_in()) { ?>
<ul class="hlist"><?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar( __('header 1', TEMPLATE_DOMAIN) )) : ?><?php endif; ?></ul>
<?php } ?>
<?php } ?>
</div>



<div id="section2" class="services-box">
<?php if($tn_blogsmu_section_two_headline != "") { ?>
<h2><?php if($tn_blogsmu_section_two_headline == "") { ?>
<?php _e("Section intro 2",TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php echo stripslashes($tn_blogsmu_section_two_headline); ?>
<?php } ?></h2>
<p class="stext">
<?php if($tn_blogsmu_section_two_post_text == ""){ ?>
<?php _e('You can replace this area with a new text of your choice in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and also place widget in this area', TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php
$com_short = $tn_blogsmu_section_two_post_text;
$chars = 999;
$com_short = $com_short . " ";
$com_short = substr($com_short,0,$chars);
$com_short = substr($com_short,0,strrpos($com_short,' '));
$com_short = $com_short . "";
?>
<?php
if( function_exists('do_shortcode') ) {
echo do_shortcode(stripslashes($com_short));
} else {
echo stripslashes($com_short);
}
?>
<?php }  ?>
</p>
<?php } // if section empty..lets ignore and leave it blank ?>

<?php if($tn_blogsmu_section_widget_status != "hide") { ?>
<ul class="hlist"><?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar( __('header 2', TEMPLATE_DOMAIN ))) : ?><?php endif; ?></ul>
<?php } else { ?>
<?php if (is_user_logged_in()) { ?>
<ul class="hlist"><?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar( __('header 2', TEMPLATE_DOMAIN ))) : ?><?php endif; ?></ul>
<?php } ?>
<?php } ?>


</div>




<div id="section3" class="services-box">
<?php if($tn_blogsmu_section_three_headline != "") { ?>
<h2><?php if($tn_blogsmu_section_three_headline == "") { ?>
<?php _e("Section intro 3",TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php echo $tn_blogsmu_section_three_headline; ?>
<?php } ?></h2>
<p class="stext">
<?php if($tn_blogsmu_section_three_post_text == ""){ ?>
<?php _e('You can replace this area with a new text of your choice in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and also place widget in this area', TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php
$com_short = $tn_blogsmu_section_three_post_text;
$chars = 999;
$com_short = $com_short . " ";
$com_short = substr($com_short,0,$chars);
$com_short = substr($com_short,0,strrpos($com_short,' '));
$com_short = $com_short . "";
?>
<?php
if( function_exists('do_shortcode') ) {
echo do_shortcode(stripslashes($com_short));
} else {
echo stripslashes($com_short);
}
?>
<?php }  ?>
</p>
<?php } // if section empty..lets ignore and leave it blank ?>

<?php if($tn_blogsmu_section_widget_status != "hide") { ?>
<ul class="hlist"><?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar( __('header 3', TEMPLATE_DOMAIN) )) : ?><?php endif; ?></ul>
<?php } else { ?>
<?php if (is_user_logged_in()) { ?>
<ul class="hlist"><?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar( __('header 3', TEMPLATE_DOMAIN) )) : ?><?php endif; ?></ul>
<?php } ?>
<?php } ?>

</div>



</div><!-- end content intro content -->
</div><!-- end wrap intro content -->
</div><!-- end intro content -->

<?php } ?>
