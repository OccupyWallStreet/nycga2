<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <?php // <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script> ?>  
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
<?php

$options = array (

    array(
        "name" => "Blog Titles",
        "desc" => "Select the font and style for blog titles.",
        "id" => "post_title",
        "selector" => "h2.posttitle",
        "type" => "typography",
        "fontsize" => "26px",
        "letterspacing" => "-0.5px",
        "lineheight" => "26px",                
        "default" => ""
        ),
    
    array(
        "name" => "Widget Titles",
        "desc" => "Select the font and style for product titles.",
        "id" => "widget_title",
        "selector" => "h3.widgettitle",
        "type" => "typography",
        "fontsize" => "18px",
        "letterspacing" => "-0.5px",
        "lineheight" => "33px",
        "default" => "")
);

/* Add Multple Selector Support */
if(function_exists('upfw_multiple_typography'))
    $options = upfw_multiple_typography($options);


/* ------------ Do not edit below this line ----------- */

//Check if theme options set
global $default_check;
global $default_options;

if(!$default_check):
    foreach($options as $option):
        if($option['type'] != 'image'):
            $default_options[$option['id']] = $option['value'];
        else:
            $default_options[$option['id']] = $option['url'];
        endif;
    endforeach;
    $update_option = get_option('up_themes_'.UPTHEMES_SHORT_NAME);
    if(is_array($update_option)):
        $update_option = array_merge($update_option, $default_options);
        update_option('up_themes_'.UPTHEMES_SHORT_NAME, $update_option);
    else:
        update_option('up_themes_'.UPTHEMES_SHORT_NAME, $default_options);
    endif;
endif;

render_options($options);

?>