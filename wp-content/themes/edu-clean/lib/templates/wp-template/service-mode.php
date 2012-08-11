<?php include (TEMPLATEPATH . '/options.php'); ?>

<div class="service-block">
<?php if(file_exists($upload_path . 'edu1_normal.jpg')) { ?>
<a href="<?php echo $tn_edus_headline1_link; ?>"><img src="<?php echo "$ttpl_path/edu1_normal.jpg"; ?>" width="200" height="100" alt="<?php echo stripslashes($tn_edus_headline1); ?>" /></a>
<?php } elseif(file_exists($upload_path . 'edu1_thumb.jpg')) { ?>
<a href="<?php echo $tn_edus_headline1_link; ?>"><img src="<?php echo "$ttpl_path/edu1_thumb.jpg"; ?>" width="200" height="100" alt="<?php echo stripslashes($tn_edus_headline1); ?>" /></a>
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg" width="200" height="100" alt="img" />
<?php } ?>
<h3><?php echo stripslashes($tn_edus_headline1); ?></h3>
<p>
<?php if($tn_edus_text1 == ""){ ?>
<?php _e('You can replace this area with a new text in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and <a href="wp-admin/themes.php?page=custom-homepage.php">upload and crop new images</a> to replace the image you can see here already.', TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php
$com_short = $tn_edus_text1;
$chars = 120;
$com_short = $com_short . " ";
$com_short = substr($com_short,0,$chars);
$com_short = substr($com_short,0,strrpos($com_short,' '));
$com_short = $com_short . "...";
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
</div>



<div class="service-block">
<?php if(file_exists($upload_path . 'edu2_normal.jpg')) {?>
<a href="<?php echo $tn_edus_headline2_link; ?>"><img src="<?php echo "$ttpl_path/edu2_normal.jpg"; ?>" width="200" height="100" alt="<?php echo stripslashes($tn_edus_headline2); ?>" /></a>
<?php } elseif(file_exists($upload_path . 'edu2_thumb.jpg')) { ?>
<a href="<?php echo $tn_edus_headline2_link; ?>"><img src="<?php echo "$ttpl_path/edu2_thumb.jpg"; ?>" width="200" height="100" alt="<?php echo stripslashes($tn_edus_headline2); ?>" /></a>
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg" width="200" height="100" alt="img" />
<?php } ?>
<h3><?php echo stripslashes($tn_edus_headline2); ?></h3>
<p>
<?php if($tn_edus_text2 == ""){ ?>
<?php _e('You can replace this area with a new text in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and <a href="wp-admin/themes.php?page=custom-homepage.php">upload and crop new images</a> to replace the image you can see here already.', TEMPLATE_DOMAIN); ?>
<?php } else { ?>

<?php
$com_short = $tn_edus_text2;
$chars = 120;
$com_short = $com_short . " ";
$com_short = substr($com_short,0,$chars);
$com_short = substr($com_short,0,strrpos($com_short,' '));
$com_short = $com_short . "...";
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
</div>


<div class="service-block">
<?php if(file_exists($upload_path . 'edu3_normal.jpg')) {?>
<a href="<?php echo $tn_edus_headline3_link; ?>"><img src="<?php echo "$ttpl_path/edu3_normal.jpg"; ?>" width="200" height="100" alt="<?php echo stripslashes($tn_edus_headline3); ?>" /></a>
<?php } elseif(file_exists($upload_path . 'edu3_thumb.jpg')) { ?>
<a href="<?php echo $tn_edus_headline3_link; ?>"><img src="<?php echo "$ttpl_path/edu3_thumb.jpg"; ?>" width="200" height="100" alt="<?php echo stripslashes($tn_edus_headline3); ?>" /></a>
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg" width="200" height="100" alt="img" />
<?php } ?>
<h3><?php echo stripslashes($tn_edus_headline3); ?></h3>
<p>
<?php if($tn_edus_text3 == ""){ ?>
<?php _e('You can replace this area with a new text in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and <a href="wp-admin/themes.php?page=custom-homepage.php">upload and crop new images</a> to replace the image you can see here already.', TEMPLATE_DOMAIN); ?>
<?php } else { ?>

<?php
$com_short = $tn_edus_text3;
$chars = 120;
$com_short = $com_short . " ";
$com_short = substr($com_short,0,$chars);
$com_short = substr($com_short,0,strrpos($com_short,' '));
$com_short = $com_short . "...";
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
</div>



<div class="service-block">
<?php if(file_exists($upload_path . 'edu4_normal.jpg')) {?>
<a href="<?php echo $tn_edus_headline4_link; ?>"><img src="<?php echo "$ttpl_path/edu4_normal.jpg"; ?>" width="200" height="100" alt="<?php echo stripslashes($tn_edus_headline4); ?>" /></a>
<?php } elseif(file_exists($upload_path . 'edu4_thumb.jpg')) { ?>
<a href="<?php echo $tn_edus_headline4_link; ?>"><img src="<?php echo "$ttpl_path/edu4_thumb.jpg"; ?>" width="200" height="100" alt="<?php echo stripslashes($tn_edus_headline4); ?>" /></a>
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg" width="200" height="100" alt="img" />
<?php } ?>
<h3><?php echo stripslashes($tn_edus_headline4); ?></h3>
<p>
<?php if($tn_edus_text4 == ""){ ?>
<?php _e('You can replace this area with a new text in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and <a href="wp-admin/themes.php?page=custom-homepage.php">upload and crop new images</a> to replace the image you can see here already.', TEMPLATE_DOMAIN); ?>
<?php } else { ?>

<?php
$com_short = $tn_edus_text4;
$chars = 120;
$com_short = $com_short . " ";
$com_short = substr($com_short,0,$chars);
$com_short = substr($com_short,0,strrpos($com_short,' '));
$com_short = $com_short . "...";
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
</div>



<div class="service-block">
<?php if(file_exists($upload_path . 'edu5_normal.jpg')) {?>
<a href="<?php echo $tn_edus_headline5_link; ?>"><img src="<?php echo "$ttpl_path/edu5_normal.jpg"; ?>" width="200" height="100" alt="<?php echo stripslashes($tn_edus_headline5); ?>" /></a>
<?php } elseif(file_exists($upload_path . 'edu5_thumb.jpg')) { ?>
<a href="<?php echo $tn_edus_headline5_link; ?>"><img src="<?php echo "$ttpl_path/edu5_thumb.jpg"; ?>" width="200" height="100" alt="<?php echo stripslashes($tn_edus_headline5); ?>" /></a>
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg" width="200" height="100" alt="img" />
<?php } ?>
<h3><?php echo stripslashes($tn_edus_headline5); ?></h3>
<p>
<?php if($tn_edus_text5 == ""){ ?>
<?php _e('You can replace this area with a new text in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and <a href="wp-admin/themes.php?page=custom-homepage.php">upload and crop new images</a> to replace the image you can see here already.', TEMPLATE_DOMAIN); ?>
<?php } else { ?>

<?php
$com_short = $tn_edus_text5;
$chars = 120;
$com_short = $com_short . " ";
$com_short = substr($com_short,0,$chars);
$com_short = substr($com_short,0,strrpos($com_short,' '));
$com_short = $com_short . "...";
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
</div>



<div class="service-block">
<?php if(file_exists($upload_path . 'edu6_normal.jpg')) {?>
<a href="<?php echo $tn_edus_headline6_link; ?>"><img src="<?php echo "$ttpl_path/edu6_normal.jpg"; ?>" width="200" height="100" alt="<?php echo stripslashes($tn_edus_headline6); ?>" /></a>
<?php } elseif(file_exists($upload_path . 'edu6_thumb.jpg')) { ?>
<a href="<?php echo $tn_edus_headline6_link; ?>"><img src="<?php echo "$ttpl_path/edu6_thumb.jpg"; ?>" width="200" height="100" alt="<?php echo stripslashes($tn_edus_headline6); ?>" /></a>
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg" width="200" height="100" alt="img" />
<?php } ?>
<h3><?php echo stripslashes($tn_edus_headline6); ?></h3>
<p>
<?php if($tn_edus_text6 == ""){ ?>
<?php _e('You can replace this area with a new text in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and <a href="wp-admin/themes.php?page=custom-homepage.php">upload and crop new images</a> to replace the image you can see here already.', TEMPLATE_DOMAIN); ?>
<?php } else { ?>

<?php
$com_short = $tn_edus_text6;
$chars = 120;
$com_short = $com_short . " ";
$com_short = substr($com_short,0,$chars);
$com_short = substr($com_short,0,strrpos($com_short,' '));
$com_short = $com_short . "...";
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
</div>