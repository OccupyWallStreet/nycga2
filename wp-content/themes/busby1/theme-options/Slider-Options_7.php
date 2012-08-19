<?php 
echo '
<p>Use these field below to adjust the settings for the Homepage Slider. <br />Each Post / Page has a new option with a tickbox "Feature in Featured Content Slider?" tick / untick to show in slider.</p>
';
$options = array (

	array(  "name" => "Slider visible in homepage",
            "desc" => "Show or hide slider in the homepage.",
            "id" => "slider",
            "type" => "select",
			"value" => "visible",
            "options" => array(
                'Visible' => 'visible',
            )
    ),

	array(  "name" => "Choose an effect",
            "desc" => "Choose an effect for the slider",
            "id" => "slider_effect",
            "type" => "select",
			"value" => "",
			"default_text" => 'Fade',
			"default_value" => 'fade',
			"custom" => true,
            "options" => array(
                //'Fade' => 'fade',
                'Scroll Left' => 'scrollLeft',
                'Scroll Right' => 'scrollRight',
                'Shuffle' => 'shuffle',
            )
    ),
    
    array(  "name" => "Slider Timeout (in ms)",
            "desc" => "Set Slider Timeout (in ms)",
            "id" => "slider_timeout",
            "type" => "text",
            "value" => "3000"           
    ),  
    
    
    
);

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