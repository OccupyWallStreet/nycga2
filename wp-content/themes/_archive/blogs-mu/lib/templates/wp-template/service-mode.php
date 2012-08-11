<?php include ( TEMPLATEPATH . '/options-var.php' ); ?>

<div id="services-content">

<div class="sbox">
<div class="simg">
<div class="img-services">
<?php if(file_exists($upload_path . 'blogsmu1_normal.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline1_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu1_normal.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline1); ?>" /></a>
<?php } elseif(file_exists($upload_path . 'blogsmu1_thumb.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline1_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu1_thumb.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline1); ?>" /></a>
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg" alt="img" />
<?php } ?>
</div>
</div>
<h3><?php echo stripslashes($tn_blogsmu_headline1); ?></h3>
<p>
<?php if($tn_blogsmu_text1 == ""){ ?>
<?php _e('You can replace this area with a new text in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and <a href="wp-admin/themes.php?page=custom-homepage.php">upload and crop new images</a> to replace the image you can see here already.', TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php
$com_short = $tn_blogsmu_text1;
$chars = 200;
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

<?php } ?>
<span class="learn-more"><a href="<?php echo $tn_blogsmu_headline1_link; ?>"><?php _e("Find out more", TEMPLATE_DOMAIN); ?></a></span>
</p>
</div>




<div class="sbox">
<div class="simg">
<div class="img-services">
<?php if(file_exists($upload_path . 'blogsmu2_normal.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline2_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu2_normal.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline2); ?>" /></a>
<?php } elseif(file_exists($upload_path . 'blogsmu2_thumb.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline2_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu2_thumb.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline2); ?>" /></a>
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg" alt="img" />
<?php } ?>
</div>
</div>
<h3><?php echo stripslashes($tn_blogsmu_headline2); ?></h3>
<p>
<?php if($tn_blogsmu_text2 == ""){ ?>
<?php _e('You can replace this area with a new text in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and <a href="wp-admin/themes.php?page=custom-homepage.php">upload and crop new images</a> to replace the image you can see here already.', TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php
$com_short = $tn_blogsmu_text2;
$chars = 200;
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
<span class="learn-more"><a href="<?php echo $tn_blogsmu_headline2_link; ?>"><?php _e("Find out more", TEMPLATE_DOMAIN); ?></a></span>
</p>
</div>



<div class="sbox">
<div class="simg">
<div class="img-services">
<?php if(file_exists($upload_path . 'blogsmu3_normal.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline3_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu3_normal.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline3); ?>" /></a>
<?php } elseif(file_exists($upload_path . 'blogsmu3_thumb.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline3_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu3_thumb.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline3); ?>" /></a>
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg" alt="img" />
<?php } ?>
</div>
</div>
<h3><?php echo stripslashes($tn_blogsmu_headline3); ?></h3>
<p>
<?php if($tn_blogsmu_text3 == ""){ ?>
<?php _e('You can replace this area with a new text in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and <a href="wp-admin/themes.php?page=custom-homepage.php">upload and crop new images</a> to replace the image you can see here already.', TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php
$com_short = $tn_blogsmu_text3;
$chars = 200;
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
<span class="learn-more"><a href="<?php echo $tn_blogsmu_headline3_link; ?>"><?php _e("Find out more", TEMPLATE_DOMAIN); ?></a></span>
</p>
</div>




<?php if( file_exists($upload_path . 'blogsmu4_normal.jpg') || file_exists($upload_path . 'blogsmu4_thumb.jpg') ) { ?>
<div class="sbox">
<div class="simg">
<div class="img-services">
<?php if(file_exists($upload_path . 'blogsmu4_normal.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline4_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu4_normal.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline4); ?>" /></a>
<?php } elseif(file_exists($upload_path . 'blogsmu4_thumb.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline4_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu4_thumb.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline4); ?>" /></a>
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg" alt="img" />
<?php } ?>
</div>
</div>
<h3><?php echo stripslashes($tn_blogsmu_headline4); ?></h3>
<p>
<?php if($tn_blogsmu_text4 == ""){ ?>
<?php _e('You can replace this area with a new text in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and <a href="wp-admin/themes.php?page=custom-homepage.php">upload and crop new images</a> to replace the image you can see here already.', TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php
$com_short = $tn_blogsmu_text4;
$chars = 200;
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
<span class="learn-more"><a href="<?php echo $tn_blogsmu_headline4_link; ?>"><?php _e("Find out more", TEMPLATE_DOMAIN); ?></a></span>
</p>
</div>
<?php } ?>


<?php if( file_exists($upload_path . 'blogsmu5_normal.jpg') || file_exists($upload_path . 'blogsmu5_thumb.jpg') ) { ?>
<div class="sbox">
<div class="simg">
<div class="img-services">
<?php if(file_exists($upload_path . 'blogsmu5_normal.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline5_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu5_normal.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline5); ?>" /></a>
<?php } elseif(file_exists($upload_path . 'blogsmu5_thumb.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline5_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu5_thumb.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline5); ?>" /></a>
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg" alt="img" />
<?php } ?>
</div>
</div>
<h3><?php echo stripslashes($tn_blogsmu_headline5); ?></h3>
<p>
<?php if($tn_blogsmu_text5 == ""){ ?>
<?php _e('You can replace this area with a new text in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and <a href="wp-admin/themes.php?page=custom-homepage.php">upload and crop new images</a> to replace the image you can see here already.', TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php
$com_short = $tn_blogsmu_text5;
$chars = 200;
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
<span class="learn-more"><a href="<?php echo $tn_blogsmu_headline5_link; ?>"><?php _e("Find out more", TEMPLATE_DOMAIN); ?></a></span>
</p>
</div>
<?php } ?>



<?php if( file_exists($upload_path . 'blogsmu6_normal.jpg') || file_exists($upload_path . 'blogsmu6_thumb.jpg') ) { ?>
<div class="sbox">
<div class="simg">
<div class="img-services">
<?php if(file_exists($upload_path . 'blogsmu6_normal.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline6_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu6_normal.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline6); ?>" /></a>
<?php } elseif(file_exists($upload_path . 'blogsmu6_thumb.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline6_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu6_thumb.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline6); ?>" /></a>
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg" alt="img" />
<?php } ?>
</div>
</div>
<h3><?php echo stripslashes($tn_blogsmu_headline6); ?></h3>
<p>
<?php if($tn_blogsmu_text6 == ""){ ?>
<?php _e('You can replace this area with a new text in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and <a href="wp-admin/themes.php?page=custom-homepage.php">upload and crop new images</a> to replace the image you can see here already.', TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php
$com_short = $tn_blogsmu_text6;
$chars = 200;
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
<span class="learn-more"><a href="<?php echo $tn_blogsmu_headline6_link; ?>"><?php _e("Find out more", TEMPLATE_DOMAIN); ?></a></span>
</p>
</div>
<?php } ?>





<!-- extra box7 -->
<?php if( file_exists($upload_path . 'blogsmu7_normal.jpg') || file_exists($upload_path . 'blogsmu7_thumb.jpg') ) { ?>
<div class="sbox">
<div class="simg">
<div class="img-services">
<?php if(file_exists($upload_path . 'blogsmu7_normal.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline7_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu7_normal.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline7); ?>" /></a>
<?php } elseif(file_exists($upload_path . 'blogsmu7_thumb.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline7_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu7_thumb.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline7); ?>" /></a>
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg" alt="img" />
<?php } ?>
</div>
</div>
<h3><?php echo stripslashes($tn_blogsmu_headline7); ?></h3>
<p>
<?php if($tn_blogsmu_text7 == ""){ ?>
<?php _e('You can replace this area with a new text in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and <a href="wp-admin/themes.php?page=custom-homepage.php">upload and crop new images</a> to replace the image you can see here already.', TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php
$com_short = $tn_blogsmu_text7;
$chars = 200;
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
<span class="learn-more"><a href="<?php echo $tn_blogsmu_headline7_link; ?>"><?php _e("Find out more", TEMPLATE_DOMAIN); ?></a></span>
</p>
</div>
<?php } ?>
<!-- end extra box 7 -->




<!-- extra box8 -->
<?php if( file_exists($upload_path . 'blogsmu8_normal.jpg') || file_exists($upload_path . 'blogsmu8_thumb.jpg') ) { ?>
<div class="sbox">
<div class="simg">
<div class="img-services">
<?php if(file_exists($upload_path . 'blogsmu8_normal.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline8_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu8_normal.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline8); ?>" /></a>
<?php } elseif(file_exists($upload_path . 'blogsmu8_thumb.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline8_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu8_thumb.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline8); ?>" /></a>
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg" alt="img" />
<?php } ?>
</div>
</div>
<h3><?php echo stripslashes($tn_blogsmu_headline8); ?></h3>
<p>
<?php if($tn_blogsmu_text8 == ""){ ?>
<?php _e('You can replace this area with a new text in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and <a href="wp-admin/themes.php?page=custom-homepage.php">upload and crop new images</a> to replace the image you can see here already.', TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php
$com_short = $tn_blogsmu_text8;
$chars = 200;
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
<span class="learn-more"><a href="<?php echo $tn_blogsmu_headline8_link; ?>"><?php _e("Find out more", TEMPLATE_DOMAIN); ?></a></span>
</p>
</div>
<?php } ?>
<!-- end extra box 8 -->




<!-- extra box9 -->
<?php if( file_exists($upload_path . 'blogsmu9_normal.jpg') || file_exists($upload_path . 'blogsmu9_thumb.jpg') ) { ?>
<div class="sbox">
<div class="simg">
<div class="img-services">
<?php if(file_exists($upload_path . 'blogsmu9_normal.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline9_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu9_normal.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline9); ?>" /></a>
<?php } elseif(file_exists($upload_path . 'blogsmu9_thumb.jpg')) { ?>
<a href="<?php echo $tn_blogsmu_headline9_link; ?>"><img src="<?php echo "$ttpl_path/blogsmu9_thumb.jpg"; ?>" alt="<?php echo stripslashes($tn_blogsmu_headline9); ?>" /></a>
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg" alt="img" />
<?php } ?>
</div>
</div>
<h3><?php echo stripslashes($tn_blogsmu_headline9); ?></h3>
<p>
<?php if($tn_blogsmu_text9 == ""){ ?>
<?php _e('You can replace this area with a new text in <a href="wp-admin/themes.php?page=custom-homepage.php">your theme options</a> and <a href="wp-admin/themes.php?page=custom-homepage.php">upload and crop new images</a> to replace the image you can see here already.', TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php
$com_short = $tn_blogsmu_text9;
$chars = 200;
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
<span class="learn-more"><a href="<?php echo $tn_blogsmu_headline9_link; ?>"><?php _e("Find out more", TEMPLATE_DOMAIN); ?></a></span>
</p>
</div>
<?php } ?>
<!-- end extra box 9 -->


</div>