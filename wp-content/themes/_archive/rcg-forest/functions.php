<?php
load_theme_textdomain('rcg-forest');

if ( function_exists('register_sidebar') )
    register_sidebar(array(
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h2 class="widgettitle">',
        'after_title' => '</h2>',
    ));

function widget_my_links($args) {
  global $wp_db_version;
  extract($args, EXTR_SKIP);
  if ( $wp_db_version < 3582 ) {
    // This ONLY works with li/h2 sidebars.
    get_links_list();
  } else {
    $before_widget = preg_replace('/id="[^"]*"/','id="%id"', $before_widget);
    wp_list_bookmarks(array(
      'title_before' => $before_title, 'title_after' => $after_title,
      'category_before' => $before_widget, 'category_after' => $after_widget,
      'show_images' => true, 'class' => 'linkcat widget'
    ));
  }
}
        
if ( function_exists('register_sidebar_widget') )
  register_sidebar_widget(__('My Links'), 'widget_my_links');


//Custom image header
define('HEADER_TEXTCOLOR', '');
define('HEADER_IMAGE_WIDTH', 774);
define('HEADER_IMAGE', '%s/images/forest.png');
define('HEADER_IMAGE_HEIGHT', 120);
define('NO_HEADER_TEXT', true);

function header_style() {
?>
<style type="text/css">
#header {
  background: url(<?php header_image() ?>) no-repeat;
  height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
  width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
  border: #fff solid;
}
</style>
<?php
}

function rcg_admin_header_style() {
?>
<style type="text/css">
#headimg {
  background: url(<?php header_image() ?>) no-repeat;
  height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
  width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
  border: #fff solid;
}

#headimg * {
  display:none;
}
</style>
<?php
}

add_custom_image_header('header_style', 'rcg_admin_header_style');

?>
