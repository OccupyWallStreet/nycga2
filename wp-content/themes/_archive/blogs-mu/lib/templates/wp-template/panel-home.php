<?php include ( TEMPLATEPATH . '/options-var.php' ); ?>

<div id="panel">

<div id="left-panel">
<div id="intro-featured-img">
<?php $feat_style = get_option('tn_blogsmu_featured_blk_option'); ?>
<?php if($feat_style == "Image Intro" || $feat_style == "") { ?>
<?php if($tn_blogsmu_featured_img_url != "") { ?>
<img src="<?php echo stripslashes($tn_blogsmu_featured_img_url); ?>" alt="<?php bloginfo('description'); ?>" />
<?php } else { ?>
<img src="<?php echo get_template_directory_uri(); ?>/_inc/images/top-default.jpg" alt="<?php bloginfo('description'); ?>" />
<?php } ?>

<?php } elseif($feat_style == "Featured Slider Posts") { ?>

<?php locate_template( array('lib/templates/wp-template/feat-post.php'), true); ?>

<?php } elseif($feat_style == "Featured Slider Categories") { ?>

<?php locate_template( array('lib/templates/wp-template/feat-cat.php'), true); ?>

<?php } elseif($feat_style == "Featured Video") { ?>

<?php locate_template( array('lib/templates/wp-template/videos.php'), true); ?>

<?php } elseif($feat_style == "BP Album Rotate") { ?>

<?php locate_template( array('lib/templates/wp-template/bp-album-slideshow.php'), true); ?>

<?php } ?>

</div>
</div>


<div id="right-panel">

<h4><?php echo stripslashes($tn_blogsmu_featured_intro_headline); ?></h4>
<p class="headtext"><?php echo stripslashes( $tn_blogsmu_featured_intro_post ); ?></p>


<?php if (!is_user_logged_in()) { ?>
<div class="submit-button">
<?php if( $tn_blogsmu_featured_intro_button_text == '') { ?>
<a href='<?php echo site_url(); ?>/<?php if( $bp_existed == 'true' ) { ?><?php echo bp_get_root_slug( 'register' ) . '/'; ?><?php } else { ?>wp-login.php?action=register<?php } ?>'><?php echo stripslashes($tn_blogsmu_featured_intro_button_text); ?></a>
<?php } else { ?>
<a href="<?php echo do_shortcode( stripslashes($tn_blogsmu_featured_intro_button_link) ); ?>">
<?php echo stripslashes($tn_blogsmu_featured_intro_button_text); ?></a>
<?php } ?>
</div>

<?php } else { ?>

<div class="submit-button">
<?php if( $bp_existed == 'true' ) { global $bp; //check if bp existed ?>

<?php if($tn_blogsmu_featured_intro_button_logged_text == '') { ?>
<a href="<?php echo $bp->loggedin_user->domain; ?>"><?php _e("View Your Profile",TEMPLATE_DOMAIN); ?></a>
<?php } else { ?>
<a href="<?php echo do_shortcode( stripslashes($tn_blogsmu_featured_intro_button_logged_link) ); ?>">
<?php echo stripslashes($tn_blogsmu_featured_intro_button_logged_text); ?></a>
<?php } ?>

<?php } else { ?>


<?php if($tn_blogsmu_featured_intro_button_logged_text == '') { ?>
<a href="<?php echo site_url(); ?>/wp-admin/profile.php"><?php _e("View Your Profile",TEMPLATE_DOMAIN); ?></a>
<?php } else { ?>
<a href="<?php echo stripslashes($tn_blogsmu_featured_intro_button_logged_link); ?>">
<?php echo stripslashes($tn_blogsmu_featured_intro_button_logged_text); ?></a>
<?php } ?>

<?php } ?>
</div>
<?php } ?>

</div>
</div>