=== WP Orbit Slider ===

Contributors: Virtual Pudding
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZY92BLQVS5BSS
Tags: orbit, orbit slider, slideshow, slider, javascript slider, jquery slider, carousel, featured content, gallery, banners, image rotation, images, rotate, auto, autoplay, shortcode, slide, media, pictures, custom post types, thumbnails, responsive
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 1.0

WP Orbit Slider is a jQuery slider that uses custom post type and taxonomies. Oh, its also responsive!

== Description ==

WP Orbit Slider is based around the jQuery Orbit Slider from the excellent team <a target="_blank" href="http://www.zurb.com/playground/orbit-jquery-image-slider">Zurb</a>.
It uses a custom post type for each slide and taxonomies to create slider groups. The restriction is one slider per post/page. Dont fill your pages with sliders. Use one. Make it bold and get your message across!


== Installation ==

1. Upload the `wp-orbit-slider` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create your slides by clicking on 'Slides' in your administration menu and selecting 'Add New Slide'. **As default the slider ONLY uses the featured image attached to any given slide.**
1. Use the shortcode `[orbit-slider]` in the content area of a page or post where you want the image slider to appear.


== Frequently Asked Questions ==

= How do I insert the Orbit Slider? =
You can insert the Orbit Slider by pasting the shortcode `[orbit-slider]` into the content area of a page or post.  **Be sure to use the HTML editor when inserting shortcodes!**  Also, be aware that if you don't have any published slides, the slider will not appear.  To customize your slider, there are optional attributes that can be used with the shortcode.


= What are the optional attributes that can be used with the shortcode? = 
The attributes supported by the `[orbit-slider]` shortcode are:
1. **category** - You can choose to display only posts from a particular category. Please note that if a category doesn't exist, all posts will show in the slider as default.  Example: `[orbit-slider category="my-category"]`
2. **numberposts** - The numberposts attribute allows you to set the number of posts to display in the slider.  The default is -1 and will show all available posts. Example: `[orbit-slider numberposts="3"]`

You can combine both of these attributes together as needed.  Example: `[orbit-slider category="my_category" numberposts="3"]`

= How do I create Slides? =
Slides can be created in the WordPress administration area by clicking on 'Slides' menu in the navigation and then selecting 'Add New Slide'.  
You will be able to provide a title, content (or caption), category, and optonal URL, as well as a featured image.  
Images can be placed within the content area of the slide but will remain unresponsive inside (this involves specific css to play nicley).
You can optionally create categories for your slides by clicking on 'Slides' in the navigation area and then selecting 'Slide Categories'.  You can create sliders that will only display certain categories using the shortcode attributes.

= How can I enable the display of additional content in the slider? =
Out the box, this slider works best just using images and 'captions'. You can however insert any text or images inside any slide via the editor. Please be aware this will need specific css added to help it sing! When adding content to the slide via the text editor it generates a numerical content class that enables specific css targetting. E.g .slide-content-190.

= How can I change the animation settings? =
All animation type settings are found in the master options tab 'slider options'.
They are based on the original available options offered by the slider http://www.zurb.com/playground/orbit-jquery-image-slider

= What about the slider size? =
The slider is responsive, therefore will stretch to the surrounding div. The featured thumbnail is set to a width of 540px and a height of 450px. It is also set to hard-crop.
If your wish to have custom dimensions...
1. In your functions.php add
`<?php if ( function_exists( 'add_image_size' ) ) { add_image_size( 'orbit-custom', 940, 300 ); } ?>` 
or and true to hard crop `<?php if ( function_exists( 'add_image_size' ) ) { add_image_size( 'orbit-custom', 940, 300, true ); } ?>`
1. Then in the slider options 'Advanced Settings' select 'Custom Size'.

More regarding add_image_size can be found at: http://codex.wordpress.org/Function_Reference/add_image_size

= Can I show more than one slider per page ? =
Currently, NO. This is a limitation of the jQuery slider itself. If such changes, the plugin will be updated to reflect such changes. Dont fill your pages with sliders. Use one. Make it bold and get your message across!

= What if I want to show the slider in my sidebar? =
All you need to do is add a text widget to your sidebar and include the shortcode as described earlier. But note, as mentioned above, this slider does not allow multiple instances on one post/page so this approach will not work.

= What if I don't want to use the shortcode?  Can I hardcode the slider into my theme? =
Hardcoding the slider into your theme is just as simple as using the shortcode.  All you do is insert the following line into your theme where you want the slider to appear:

`<?php echo do_shortcode('[orbit-slider]') ?>`

If you want to use any of the shortcode attributes when hardcoding your theme, you may do so like this:

`<?php echo do_shortcode('[orbit-slider category="my_category"]'); ?>`

= Is there anything on the todo list for the plugin ? =
Indeed.
Better conditional script loading will be in the next release.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot


== Changelog ==

= 1.0 =
* Just another WordPress plugin.


== Upgrade Notice ==
This is the first version.

