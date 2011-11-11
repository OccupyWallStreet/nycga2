<?php

/*
*** GLOBAL FRAMEWORK OPTIONS
*/

$options[] = array(	"type" => "maintabletop");

    ////// General Framework Settings
	
	    $options[] = array(	"name" => "General Framework Settings",
						    "type" => "heading");
			
			$options[] = array(	"name" => "Favicon",
						        "toggle" => "true",
								"type" => "subheadingtop");

				$options[] = array(	"name" => "Choose Your Favicon Image",
				                    "desc" => "Upload your favicon image or paste the full URL address to it next to upload button. Use 16x16px image, if you don't have one use free <a href='http://www.favicon.cc/'>Favicon tool</a> and start rocking those browsers. <span class='important'>Your upload will start after you save changes</span>",
						            "id" => $shortname."_favicon",
						            "std" => "",
						            "type" => "upload");
			
			$options[] = array(	"type" => "subheadingbottom");
			
			$options[] = array(	"name" => "Syndication / Feed",
						        "toggle" => "true",
								"type" => "subheadingtop");			
						
				$options[] = array( "name" => "RSS Feed Address",
				                    "desc" => "If you are using a service like Feedburner to manage your RSS feed, enter full URL to your feed into box above. If you'd prefer to use the default WordPress feed, simply leave this box blank.",
			    		            "id" => $shortname."_feedburner_url",
			    		            "std" => "",
			    		            "type" => "text");	
						
			$options[] = array(	"type" => "subheadingbottom");
			
			$options[] = array(	"name" => "PrettyPhoto Lightbox",
						        "toggle" => "true",
								"type" => "subheadingtop");			
						
				if (base64_decode($themecode) == 'pack_3'){
				
				$options[] = array(	"name" => "To use these options, please <a href='" . $themeurl . "'>Upgrade to Standard or Agency Theme Package</a>.",
				                    "type" => "help");
				
				} else {
				
				$options[] = array(	"name" => "PrettyPhoto Lighbox Effect",
				                    "desc" => "If you want your photos or any other links to pop up with lightbox effect, check this option. To learn more, please <a href='http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone/'>visit this site</a>. <span class='important'>Add prettyPhoto effect to any content by adding the rel attribute 'prettyPhoto' to links, example: <br/><code>&lt;a href='http://somelink' rel='prettyPhoto'&gt;some text&lt;/a&gt;</code>.</span>",
						            "label" => "Enable prettyPhoto Script",
						            "id" => $shortname."_prettyphoto",
						            "std" => "false",
						            "type" => "checkbox");	
									
				}
						
			$options[] = array(	"type" => "subheadingbottom");
			
			$options[] = array(	"name" => "Image Setup",
						        "toggle" => "true",
								"type" => "subheadingtop");
						
				$options[] = array(	"name" => "Display Thumbnails?",
				                    "label" => "Display Thumbnails",
						            "desc" => "If you want to show image thumbnails, check this option.",
						            "id" => $shortname."_thumb_show",
						            "std" => "true",
						            "type" => "checkbox");

				$options[] = array(	"name" => "Resize Images Dynamically?",
				                    "label" => "Resize Images Dynamically",
						            "desc" => "Resize images with thumb.php script &rarr; smooth pics ;)",
						            "id" => $shortname."_resize",
						            "std" => "true",
						            "type" => "checkbox");					
									
				$options[] = array(	"name" => "Automatic Image Handling?",
				                    "label" => "Automatic Image Handling",
						            "desc" => "If no image in the custom field then first uploaded image is used.",
						            "id" => $shortname."_auto_img",
						            "std" => "true",
						            "type" => "checkbox");	
									
				$options[] = array(	"name" => "Show in RSS feed?",
				                    "label" => "Show in RSS feed",
						            "desc" => "Show thumbnail images in RSS feeds.",
						            "id" => $shortname."_image_rss",
						            "std" => "false",
						            "type" => "checkbox");
						
			$options[] = array(	"type" => "subheadingbottom");
								
		$options[] = array(	"type" => "maintablebreak");
		
	/// Blog Stats and Scripts											
				
		$options[] = array(	"name" => "Blog Stats and Scripts",
						    "type" => "heading");
										
			$options[] = array(	"name" => "Blog Header Scripts",
						        "toggle" => "true",
								"type" => "subheadingtop");	
						
				$options[] = array(	"name" => "Header Scripts (just before the <code>&lt;/head&gt;</code> tag)",
						            "desc" => "If you need to add scripts to your header (like <a href='http://haveamint.com/'>Mint</a> tracking code), do so here. These scripts will be included just before the <code>&lt;/head&gt;</code> tag. You may paste multiple scripts.",
					                "id" => $shortname."_scripts_header",
					                "std" => "",
					                "type" => "textarea");
			
			$options[] = array(	"type" => "subheadingbottom");
			
			$options[] = array(	"name" => "Body Scripts",
						        "toggle" => "true",
								"type" => "subheadingtop");	
						
				$options[] = array(	"name" => "Body Scripts (just after the <code>&lt;body&gt;</code> tag)",
						            "desc" => "If you need to add scripts to your body (like <a href='http://www.google.com/analytics/'>Google Analytics</a> tracking code), do so here. These scripts will be included just after the <code>&lt;body&gt;</code> tag. You may paste multiple scripts.",
					                "id" => $shortname."_scripts_body",
					                "std" => "",
					                "type" => "textarea");
			
			$options[] = array(	"type" => "subheadingbottom");
			
			$options[] = array(	"name" => "Footer Scripts",
						        "toggle" => "true",
								"type" => "subheadingtop");	
						
				$options[] = array(	"name" => "Footer Scripts (just before the <code>&lt;/body&gt;</code> tag)",
						            "desc" => "If you need to add scripts to your footer (like <a href='http://www.google.com/analytics/'>Google Analytics</a> tracking code), do so here. These scripts will be included just before the <code>&lt;/body&gt;</code> tag. You may paste multiple scripts.",
					                "id" => $shortname."_google_analytics",
					                "std" => "",
					                "type" => "textarea");
			
			$options[] = array(	"type" => "subheadingbottom");
						
		$options[] = array(	"type" => "maintablebreak");
		
	/// SEO Options
				
		$options[] = array(	"name" => "Complete SEO Control",
						    "type" => "heading");
						
			$options[] = array(	"name" => "Head <code>&lt;title&gt;</code> tags",
						        "toggle" => "true",
								"type" => "subheadingtop");
								
			    if (base64_decode($themecode) == 'pack_3'){
				
				$options[] = array(	"name" => "To use these options, please <a href='" . $themeurl . "'>Upgrade to Standard or Agency Theme Package</a>.",
				                    "type" => "help");
				
				} else {

				$options[] = array(	"name" => "Site name in Title?",
						            "label" => "Show site name in title (on your homepage). Example: Sitename",
						            "desc" => "You may edit Site name (Blog Title) <a href='" . $bloghomeurl . "wp-admin/options-general.php'>here</a>",
									"id" => $shortname."_title_title",
						            "std" => "true",
						            "type" => "checkbox");
									
				$options[] = array(	"name" => "Tagline in Title?",
						            "label" => "Show site tagline in title (on your homepage). Example: Tagline|Sitename",
						            "desc" => "You may edit Tagline <a href='" . $bloghomeurl . "wp-admin/options-general.php'>here</a>",
									"id" => $shortname."_title_tagline",
						            "std" => "true",
						            "type" => "checkbox");

				$options[] = array(	"name" => "Site name in Title across All Pages?",
						            "label" => "Add site name to all other page titles.",
						            "desc" => "Add site name in title across all pages. Example: About|Sitename",
									"id" => $shortname."_title_other",
						            "std" => "false",
						            "type" => "checkbox");
									
				}
						
			$options[] = array(	"type" => "subheadingbottom");
			
			$options[] = array(	"name" => "Head <code>&lt;meta&gt;</code> tags",
						        "toggle" => "true",
								"type" => "subheadingtop");
								
				if (base64_decode($themecode) == 'pack_3'){
				
				$options[] = array(	"name" => "To use these options, please <a href='" . $themeurl . "'>Upgrade to Standard or Agency Theme Package</a>.",
				                    "type" => "help");
				
				} else {

				$options[] = array(	"name" => "Meta Description",
					                "desc" => "You should use meta descriptions to provide search engines with additional information about topics that appear on your site. This only applies to your home page.",
					                "id" => $shortname."_meta_description",
					                "std" => "",
					                "type" => "textarea");

				$options[] = array(	"name" => "Meta Keywords (comma separated)",
					                "desc" => "Meta keywords are rarely used nowadays but you can still provide search engines with additional information about topics that appear on your site. This only applies to your home page.",
						            "id" => $shortname."_meta_keywords",
						            "std" => "",
						            "type" => "text");
									
				$options[] = array(	"name" => "Meta Author",
					                "desc" => "You should write your <em>full name</em> here but only do so if this blog is writen only by one outhor. This only applies to your home page.",
						            "id" => $shortname."_meta_author",
						            "std" => "",
						            "type" => "text");
									
				}
						
			$options[] = array(	"type" => "subheadingbottom");
			
			$options[] = array(	"name" => "Head <code>&lt;noindex&gt;</code> tags",
						        "toggle" => "true",
								"type" => "subheadingtop");
								
				if (base64_decode($themecode) == 'pack_3'){
				
				$options[] = array(	"name" => "To use these options, please <a href='" . $themeurl . "'>Upgrade to Standard or Agency Theme Package</a>.",
				                    "type" => "help");
				
				} else {

				$options[] = array(	"name" => "Options for <code>noindex</code> tag",
						            "desc" => "By adding <code>noindex</code> robot meta tag you are significantly improving your site SEO and prevent search engines from indexing very large database or pages that are very transitory. This way your are preventing spiders from indexing pages that only worsen your search results and keep you from ranking as well as you should.",
				                    "type" => "help");
									
				$options[] = array(	"label" => "Add <code>&lt;noindex&gt;</code> to category archives.",
						            "id" => $shortname."_noindex_category",
						            "std" => "false",
						            "type" => "checkbox");
									
				$options[] = array(	"label" => "Add <code>&lt;noindex&gt;</code> to tag archives.",
						            "id" => $shortname."_noindex_tag",
						            "std" => "true",
						            "type" => "checkbox");
				
				$options[] = array(	"label" => "Add <code>&lt;noindex&gt;</code> to author archives.",
						            "id" => $shortname."_noindex_author",
						            "std" => "true",
						            "type" => "checkbox");
				
				$options[] = array(	"label" => "Add <code>&lt;noindex&gt;</code> to daily archives.",
						            "id" => $shortname."_noindex_daily",
						            "std" => "true",
						            "type" => "checkbox");
				
				$options[] = array(	"label" => "Add <code>&lt;noindex&gt;</code> to monthly archives.",
						            "id" => $shortname."_noindex_monthly",
						            "std" => "true",
						            "type" => "checkbox");
				
				$options[] = array(	"label" => "Add <code>&lt;noindex&gt;</code> to yearly archives.",
						            "id" => $shortname."_noindex_yearly",
						            "std" => "true",
						            "type" => "checkbox");
								
				$options[] = array(	"name" => "Add <code>&lt;noindex&gt;</code> to checked pages.",
				                    "desc" => "Check all pages you would like to hide from search engine spiders.",
				                    "type" => "help");
												
				$options = pages_exclude_seo($options);
				
				}
					
			$options[] = array(	"type" => "subheadingbottom");
			
			$options[] = array(	"name" => "Head <code>&lt;noodp&gt;</code> <code>&lt;noydir&gt;</code> attributes",
						        "toggle" => "true",
								"type" => "subheadingtop");
								
				if (base64_decode($themecode) == 'pack_3'){
				
				$options[] = array(	"name" => "To use these options, please <a href='" . $themeurl . "'>Upgrade to Standard or Agency Theme Package</a>.",
				                    "type" => "help");
				
				} else {

				$options[] = array(	"name" => "Options for <code>noodp</code> <code>noydir</code> tag",
						            "desc" => "By adding <code>noodp</code> <code>noydir</code> robot meta tags you are preventing search engines from displaying Open Directory Project (DMOZ) and Yahoo! Directory listings in your meta descriptions.",
				                    "type" => "help");
									
				$options[] = array(	"label" => "Add <code>noodp</code> meta tag</code>",
					                "id" => $shortname."_noodp_meta",
					                "std" => "true",
					                "type" => "checkbox");

				$options[] = array(	"label" => "Add <code>noydir</code> meta tag</code>",
					                "id" => $shortname."_noydir_meta",
					                "std" => "true",
					                "type" => "checkbox");
									
				}
						
			$options[] = array(	"type" => "subheadingbottom");
			
			$options[] = array(	"name" => "Link <code>&lt;nofollow&gt;</code> attributes",
						        "toggle" => "true",
								"type" => "subheadingtop");
								
				if (base64_decode($themecode) == 'pack_3'){
				
				$options[] = array(	"name" => "To use these options, please <a href='" . $themeurl . "'>Upgrade to Standard or Agency Theme Package</a>.",
				                    "type" => "help");
				
				} else {

				$options[] = array(	"name" => "Options for <code>nofollow</code> tag",
						            "desc" => "By adding <code>nofolow</code> rel attribute to specific links you are reducing the effectiveness of certain types of search engine spam, thereby improving the quality of search engine results and preventing spamdexing from occurring.",
				                    "type" => "help");
									
				$options[] = array(	"label" => "<code>nofollow</code> Home link</code>",
					                "id" => $shortname."_nofollow_home",
					                "std" => "false",
					                "type" => "checkbox");

				$options[] = array(	"label" => "<code>nofollow</code> Author Links</code>",
					                "id" => $shortname."_nofollow_author",
					                "std" => "false",
					                "type" => "checkbox");
									
				$options[] = array(	"label" => "<code>nofollow</code> Post Tags</code>",
					                "id" => $shortname."_nofollow_tags",
					                "std" => "false",
					                "type" => "checkbox");
									
				}
						
			$options[] = array(	"type" => "subheadingbottom");
			
			$options[] = array(	"name" => "Canonical URLs",
						        "toggle" => "true",
								"type" => "subheadingtop");
								
				if (base64_decode($themecode) == 'pack_3'){
				
				$options[] = array(	"name" => "To use these options, please <a href='" . $themeurl . "'>Upgrade to Standard or Agency Theme Package</a>.",
				                    "type" => "help");
				
				} else {

				$options[] = array(	"name" => "Options for canonical URLs",
						            "desc" => "Canonical URL: the search engine friendly URL that you want the search engines to treat as authoritative.  In other words, a canonical URL is the URL that you want visitors to see.",
				                    "type" => "help");
									
				$options[] = array(	"label" => "Enable Canonical URLs",
					                "id" => $shortname."_canonical_url",
					                "std" => "true",
					                "type" => "checkbox");
									
				}
						
			$options[] = array(	"type" => "subheadingbottom");
			
		$options[] = array(	"type" => "maintablebreak");
		
	/// Theme Branding
				
		$options[] = array(	"name" => "Theme Branding Options",
						    "type" => "heading");
						
			$options[] = array(	"name" => "Front-end Branding",
						        "toggle" => "true",
								"type" => "subheadingtop");
								
				if (base64_decode($themecode) == 'pack_3' || base64_decode($themecode) == 'pack_2'){
				
				$options[] = array(	"name" => "To use these options, please <a href='" . $themeurl . "'>Upgrade to Agency Theme Package</a>.",
				                    "type" => "help");
				
				} else {
								
				$options[] = array(	"name" => "Fron-end Branding Options",
						            "desc" => "By applying front-end branding options users will acknowledge this website as your own product, with your own logo and optional backlink to theme developer. As this is GPL licensed theme, leave credits in code intact.",
				                    "type" => "help");

				$options[] = array(	"label" => "Activate front-end branding.",
						            "id" => $shortname."_branding_front",
						            "std" => "false",
						            "type" => "checkbox");
									
				$options[] = array(	"label" => "Remove footer credits alltogether",
						            "id" => $shortname."_branding_front_remove",
						            "std" => "false",
						            "type" => "checkbox");									
									
				$options[] = array(	"name" => "Footer Logo",
						            "desc" => "Upload your image or paste the full URL address to it next to upload button. Choose small image (recommended dimension within 115x30px limits). <span class='important'>Your upload will start after you save changes</span>",
						            "id" => $shortname."_branding_front_logo",
						            "std" => "",
						            "type" => "upload");
									
				$options[] = array(	"name" => "Credits Link",
						            "desc" => "Add custom link - where your logo points to. Including <code>http://</code>.",
			    		            "id" => $shortname."_branding_front_link",
			    		            "std" => "",
			    		            "type" => "text");
									
				$options[] = array(	"name" => "Credits Name",
						            "desc" => "Add custom alt attribute - text that explains your link onhover. If no custom logo is selected this text will appear instead.",
			    		            "id" => $shortname."_branding_front_alt",
			    		            "std" => "",
			    		            "type" => "text");
									
				}
						
			$options[] = array(	"type" => "subheadingbottom");
			
			$options[] = array(	"name" => "Back-end Branding",
						        "toggle" => "true",
								"type" => "subheadingtop");
								
				if (base64_decode($themecode) == 'pack_3' || base64_decode($themecode) == 'pack_2'){
				
				$options[] = array(	"name" => "To use these options, please <a href='" . $themeurl . "'>Upgrade to Agency Theme Package</a>.",
				                    "type" => "help");
				
				} else {
								
				$options[] = array(	"name" => "Back-end Branding Options",
						            "desc" => "By applying back-end branding options you or your clients will acknowledge this website as your own product, with your own branding and optional backlinks to varius sites (your own documentation, support etc.). As this is GPL licensed theme, leave credits in code intact.",
				                    "type" => "help");

				$options[] = array(	"label" => "Activate back-end branding.",
						            "id" => $shortname."_branding_back",
						            "std" => "false",
						            "type" => "checkbox");
									
				$options[] = array(	"name" => "Your Icon",
						            "desc" => "Upload your image or paste the full URL address to it next to upload button. Choose small image (16x16px limits). <span class='important'>Your upload will start after you save changes</span>",
						            "id" => $shortname."_branding_back_icon",
						            "std" => "",
						            "type" => "upload");
									
				$options[] = array(	"name" => "Theme Name",
						            "desc" => "Rename this theme to whatever name you like.",
			    		            "id" => $shortname."_branding_back_name",
			    		            "std" => "",
			    		            "type" => "text");
									
				$options[] = array(	"name" => "<b>Top Link</b>",
						            "desc" => "Instead of default Documentation and Support Forums link you may add your own one.",
				                    "type" => "help");
									
				$options[] = array(	"name" => "link Name",
									"id" => $shortname."_branding_back_link",
			    		            "std" => "",
			    		            "type" => "text");
									
			    $options[] = array(	"name" => "link destination URL, <code>http://</code> included",
									"id" => $shortname."_branding_back_link_dest",
			    		            "std" => "",
			    		            "type" => "text");
									
				}
						
			$options[] = array(	"type" => "subheadingbottom");
			
		$options[] = array(	"type" => "maintablebreak");
						
$options[] = array(	"type" => "maintablebottom");


if (base64_decode($themecode) == 'pack_3'){
	
    $meta_boxes = array(
				
        'seo' => array(
			'id' => 'bizzthemes_seo_meta',
			'title' => $GLOBALS['themename'].' &rarr; SEO',
			'function' => 'bizzthemes_seo_meta_box',
			'noncename' => 'bizzthemes_seo',
			'fields' => array(
				'bizzthemes_meta_title' => array(
					'name' => 'bizzthemes_title',
					'type' => 'text',
					'default' => '',
					'title' => "To use these options, please <a href='" . $themeurl . "'>Upgrade to Standard or Agency Theme Package</a>.",
					'description' => '',
					'label' => ''
				),
			)
		)
    );
	
} else {

    $meta_boxes = array(
		
		'seo' => array(
			'id' => 'bizzthemes_seo_meta',
			'title' => 'Bizz &rarr; SEO',
			'function' => 'bizzthemes_seo_meta_box',
			'noncename' => 'bizzthemes_seo',
			'fields' => array(
				'bizzthemes_meta_title' => array(
					'name' => 'bizzthemes_title',
					'type' => 'text_counter',
					'default' => '',
					'title' => 'SEO Title',
					'description' => 'Override default title and use SEO optimized one. Stripp of all unnecessary attributes like "and", "or", "is" etc..',
					'counter_desc' => '&rarr; Most search engines use a maximum of 60 chars for the title.',
					'label' => 'custom <code>&lt;title&gt;</code>'
				),
				'bizzthemes_meta_description' => array(
					'name' => 'bizzthemes_description',
					'type' => 'textarea_counter',
					'default' => '',
					'title' => 'SEO Meta Description',
					'description' => 'Override default meta description and enter one that better describes your page/post than first lines of text.',
					'counter_desc' => '&rarr; Most search engines use a maximum of 160 chars for the description.',
					'label' => '<code>&lt;meta&gt;</code> description'
				),
				'bizzthemes_meta_keywords' => array(
					'name' => 'bizzthemes_keywords',
					'type' => 'text',
					'default' => '',
					'title' => 'SEO Meta Keywords',
					'description' => 'Enter a few keywords that are most relevant to your post/page. Separate theme by comma (,).',
					'label' => '<code>&lt;meta&gt;</code> keywords'
				),
				'bizzthemes_meta_noindex' => array(
					'name' => 'bizzthemes_noindex',
					'type' => 'checkbox',
					'default' => false,
					'title' => 'Noindex this Post/Page',
					'description' => 'Keep this post/page private and prevent search engines from indexing it.',
					'label' => '<code>Noindex</code> this post/page'
				),
				'bizzthemes_meta_redirect' => array(
					'name' => 'bizzthemes_redirect',
					'type' => 'text',
					'default' => '',
					'title' => '301 Redirect',
					'description' => 'If you place a <acronym title="Uniform Resource Locator">URL</acronym> in the field below, users will get redirected to this <acronym title="Uniform Resource Locator">URL</acronym> whenever they visit this post/page.',
					'label' => 'destination <acronym title="Uniform Resource Locator">URL</acronym>'
				)
			)
		)
		
    );
	
}

?>