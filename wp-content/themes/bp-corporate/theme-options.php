body {
font-family: <?php echo $tn_buddycorp_body_font; ?> !important;
}
h1,h2,h3,h4,h5,h6 {
font-family: <?php echo $tn_buddycorp_headline_font; ?> !important;
line-height: 1.2;
}
<?php if(($tn_buddycorp_font_size == "normal") || ($tn_buddycorp_font_size == "")) { ?>
#wrapper, #footer { font-size: 0.75em; line-height: 1.6em !important; }
<?php } elseif ($tn_buddycorp_font_size == "medium") { ?>
#wrapper, #footer { font-size: 0.785em; line-height: 1.6em !important;   }
<?php } elseif ($tn_buddycorp_font_size == "bigger") { ?>
#wrapper, #footer { font-size: 0.85em; line-height: 1.6em !important;   }
<?php } elseif ($tn_buddycorp_font_size == "largest") { ?>
#wrapper, #footer { font-size: 1em; line-height: 1.6em !important;   }
<?php } ?>

<?php if($tn_buddycorp_home_featured_bg_color != "") { ?>
div#feat-content {
  background: <?php echo $tn_buddycorp_home_featured_bg_color; ?>;
  }
<?php } ?>

<?php if($tn_buddycorp_home_featured_link_color != "") { ?>
div#feat-content a {
  color: <?php echo $tn_buddycorp_home_featured_link_color; ?>;
  }
<?php } ?>

<?php if($tn_buddycorp_home_featured_border_color != "") { ?>
div.feat-post .feat-tag, div#feat-content ul.more-article li {
  border-bottom: 1px solid <?php echo $tn_buddycorp_home_featured_border_color; ?>;
  }
<?php } ?>

.custom-img-header {
height: <?php echo $tn_buddycorp_image_height; ?>px!important;
}