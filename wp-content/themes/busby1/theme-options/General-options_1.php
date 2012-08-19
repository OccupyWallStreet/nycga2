<?php
/*  Array Options:
   
   name (string)
   desc (string)
   id (string)
   type (string) - text, color, image, select, multiple, textarea, page, pages, category, categories, text_list
   value (string) 	- default value - replaced when custom value is entered - (text, color, select, textarea, page, category)
					- For multiple default values in multiple selects, separate with a comma space ("value" => "option 1, options 2")
						- For pages "value" => "Page Name, Page Name 2"
						- For categories "value" => "slug, slug2"
   options (array)
   attr (array) - any form field attributes
   url (string) - for image type only - defines the default image
   default_text (string) - overrides "None" option text in selects
	
	How to use this file:
		1. Save this template to the 'theme-options' folder in the theme root
		2. Change the file name to this syntax (remember to add the php extension):  
			tab-name_#.php - # is the position you want your tab to appear. Each tab must have a unique ordinal number.
			Example: 
				colors-and-images_0.php - will render a tab "Colors and Images" that will be the first on the list.
		3. Create your options and BAM!
*/

$options = array (

    array(  "name" => "Choose Your Color Scheme",
            "desc" => "Choose the color scheme you would like to use.",
            "id" => "theme",
            "type" => "select",
			"value" => "",
            "options" => array(
                'Dark Theme' => 'dark',
                'Light Theme'=> 'light')
    ),
    
    array(  "name" => "Logo Image",
            "desc" => "Upload your your image or select from the gallery. (200px x 50px)",
            "id" => "logo",
            "type" => "image",
            "value" => "Upload Your Logo",
            "url" => get_bloginfo('template_directory')."/img/logo.png" 
    ),
    
    array(  "name" => "Text Instead Of Logo",
            "desc" => "Enter text you want to appear instead of logo",
            "id" => "logo_text",
            "type" => "text",
            "value" => ""           
    ),
    
    array(  "name" => "Footer Text",
            "desc" => "Enter the text you want to be in footer",
            "id" => "footer_text",
            "type" => "text",
            "value" => ""           
    ),
    
    array(  "name" => "Tracking Code",
            "desc" => "Enter your analytics code",
            "id" => "footer_analytics",
            "type" => "text",
            "value" => ""           
    )
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