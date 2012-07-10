<?php
/**
 * PageLines_ShortCodes
 *
 * This file defines return functions to be used as shortcodes by users and developers
 *
 * @package     PageLines Framework
 * @subpackage  Sections
 * @since       2.2
 *
 *  SHORTCODE TABLE OF CONTENTS
 *  1.  Bookmark
 *  2.  Pagename
 *  3.  Theme URL
 *  4.  Google Maps
 *  5.  Google Charts
 *  6.  Post Feed
 *  7.  Dynamic Box
 *  8.  Container
 *  9.  Post Edit
 *  10. Post Categories
 *  11. Post Tags
 *  12. Post Type
 *  13. Post Comments
 *  14. Post Authors Archive
 *  15. Post Author URL
 *  16. Post Author Display Name
 *  17. Post Date
 *  18. Pinterest Button
 *      Google Plus
 *      Linkedin Share
 *  19. Tweet Button
 *  20. Like Button
 *  21. Show Authors
 *  22. Codebox
 *  23. Labels
 *  24. Badgets
 *  25. Alertbox
 *  26. Blockquote
 *  27. Button
 *  28. Button Group
 *  29. Button Dropdown
 *  30. Split Button Dropdown
 *  31. Tooltip
 *  32. Popover
 *  32. Accordion
 *  34. Carousel
 *  35. Tabs
 *  36. Modal Popup
 *  37. Post Time
 *  38. PageLines Buttons (orig)
 *  39. Enqueue jQuery
 *  40. Enqueue Bootstrap JS
 */

class PageLines_ShortCodes {
	
	
	function __construct() {
						
		self::register_shortcodes( $this->shortcodes_core() );
		
		// Make widgets process shortcodes
		add_filter( 'widget_text', 'do_shortcode' );	
//		add_action( 'template_redirect', array( &$this, 'filters' ) );
	}

	private function shortcodes_core() {
		
		$core = array( 
			
			'button'					=>	array( 'function' => 'pagelines_button_shortcode' ),
			'post_time'					=>	array( 'function' => 'pagelines_post_time_shortcode' ),

			'pl_button'					=>	array( 'function' => 'pl_button_shortcode' ),
			'pl_buttongroup'            =>  array( 'function' => 'pl_buttongroup_shortcode' ),
			'pl_buttondropdown'         =>	array( 'function' => 'pl_buttondropdown_shortcode' ),
			'pl_splitbuttondropdown'    =>  array( 'function' => 'pl_splitbuttondropdown_shortcode' ),
			'pl_tooltip'                =>  array( 'function' => 'pl_tooltip_shortcode' ),
			'pl_popover'                =>	array( 'function' => 'pl_popover_shortcode' ),
			'pl_accordion'              =>	array( 'function' => 'pl_accordion_shortcode' ),
			'pl_accordioncontent'       =>  array( 'function' => 'pl_accordioncontent_shortcode' ),
			'pl_carousel'               =>	array( 'function' => 'pl_carousel_shortcode' ),
			'pl_carouselimage'          =>	array( 'function' => 'pl_carouselimage_shortcode' ),
			'pl_tabs'                   =>	array( 'function' => 'pl_tabs_shortcode' ),
			'pl_tabtitlesection'        =>	array( 'function' => 'pl_tabtitlesection_shortcode' ),
			'pl_tabtitle'               =>	array( 'function' => 'pl_tabtitle_shortcode' ),
			'pl_tabcontentsection'      =>	array( 'function' => 'pl_tabcontentsection_shortcode' ),
			'pl_tabcontent'             =>	array( 'function' => 'pl_tabcontent_shortcode' ),
			'pl_modal'				    =>	array( 'function' => 'pl_modal_shortcode' ),
			'pl_blockquote'				=>	array( 'function' => 'pl_blockquote_shortcode' ),
			'pl_alertbox'				=>	array( 'function' => 'pl_alertbox_shortcode' ),
			'show_authors'				=>	array( 'function' => 'show_multiple_authors' ),
			'pl_codebox'			    =>	array( 'function' =>	'pl_codebox_shortcode' ),
			'pl_label'				    =>	array( 'function' => 'pl_label_shortcode' ),
			'pl_badge'			        =>	array( 'function' => 'pl_badge_shortcode' ),
			'googleplus'				=>	array( 'function' => 'pl_googleplus_button' ),
			'linkedin'			    	=>	array( 'function' => 'pl_linkedinshare_button' ),
			'like_button'				=>	array( 'function' => 'pl_facebook_shortcode' ),
			'twitter_button'			=>	array( 'function' => 'pl_twitter_button' ),
			'pinterest'		        	=>	array( 'function' => 'pl_pinterest_button' ),
			'post_date'					=>	array( 'function' => 'pagelines_post_date_shortcode' ),
			'post_author'				=>	array( 'function' => 'pagelines_post_author_shortcode' ),
			'post_author_link'			=>	array( 'function' => 'pagelines_post_author_link_shortcode' ),
			'post_author_posts_link'	=>	array( 'function' => 'pagelines_post_author_posts_link_shortcode' ),
			'post_comments'				=>	array( 'function' => 'pagelines_post_comments_shortcode' ),
			'post_tags'					=>	array( 'function' => 'pagelines_post_tags_shortcode' ),
			'post_categories'			=>	array( 'function' => 'pagelines_post_categories_shortcode' ),
			'post_type'					=>	array( 'function' => 'pagelines_post_type_shortcode' ),
			'post_edit'					=>	array( 'function' => 'pagelines_post_edit_shortcode' ),
			'container'					=>	array( 'function' => 'dynamic_container' ),
			'cbox'						=>	array( 'function' => 'dynamic_box' ),
			'post_feed'					=>	array( 'function' => 'get_postfeed' ),
			'chart'						=>	array( 'function' => 'chart_shortcode' ),
			'googlemap'					=>	array( 'function' => 'googleMaps' ),
			'themeurl'					=>	array( 'function' => 'get_themeurl' ),
			'link'						=>	array( 'function' => 'create_pagelink' ),
			'bookmark'					=>	array( 'function' => 'bookmark_link' ),
			'pl_raw'					=>	array( 'function' => 'do_raw' ),
			'pl_video'					=>	array( 'function' => 'pl_video_shortcode' )
			);
		
		return $core;
	}

	function do_raw() {
		
		global $post;
		$str = $post->post_content;
		
		$start = '[pl_raw]';
		$end = '[/pl_raw]';
		$stpos = strpos( $str, $start );
		if ( $stpos === FALSE )
			return '';
		$stpos += strlen( $start );
		$endpos = strpos( $str, $end, $stpos );
		if ( $endpos === FALSE )
			return '';
		$len = $endpos - $stpos;
		return do_shortcode( substr( $str, $stpos, $len ) );
	}


	// 1. Return link in page based on Bookmark
	// USAGE : [bookmark id="21" text="Link Text"]
	function bookmark_link( $atts ) {

	 	//extract page name from the shortcode attributes
	 	extract( shortcode_atts( array( 'id' => '0', 'text' => '' ), $atts ) );

	 	//convert the page name to a page ID
	 	$bookmark = get_bookmark( $id );
	
		if( isset( $text ) ) $ltext = $text;
		else $ltext = $bookmark->link_name;; 


		$pagelink = "<a href=\"".$bookmark->link_url."\" target=\"".$bookmark->link_target."\">".$ltext."</a>";
	 	return $pagelink;
	}

	// 2. Function for creating a link from a page name
	// USAGE : [link pagename="My Example Page" linktext="Link Text"]
	function create_pagelink( $atts ) {

	 	//extract page name from the shortcode attributes
	 	extract( shortcode_atts( array( 'pagename' => 'home', 'linktext' => '' ), $atts ) );

	 	//convert the page name to a page ID
	 	$page = get_page_by_title( $pagename );

	 	//use page ID to get the permalink for the page
	 	$link = get_permalink( $page );

	 	//create the link and output
	 	$pagelink = "<a href=\"".$link."\">".$linktext."</a>";

	 	return $pagelink;
	}

	// 3. Function for getting template path
	// USAGE: [themeurl]
	function get_themeurl( $atts ){ return get_template_directory_uri();	 }
	
	// 4. GOOGLE MAPS //////////////////////////////////////////////////

	    // you can use the default width and height
	    // The only requirement is to add the address of the map
	    // Example:
	    // [googlemap address="san diego, ca"]
	    // or with options
	    // [googlemap width="200" height="200" address="San Francisco, CA 92109"] 
	function googleMaps( $atts, $content = null ) {
	       extract( shortcode_atts( array(
	          "width"       =>  '480',
	          "height"      =>  '480',
	          "address"   =>   ''
	       ), $atts ) );
	       $src = "http://maps.google.com/maps?f=q&source=s_q&hl=en&q=".$address;
	       return '<iframe width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$src.'&amp;output=embed"></iframe>';
	}

	// 5. GOOGLE CHARTS  //////////////////////////////////////////////////

		// Gets Google charts
		// USAGE 
		//		[chart data="0,12,24,26,32,64,54,24,22,20,8,2,0,0,3" bg="F7F9FA" size="200x100" type="sparkline"]
		//		[chart data="41.52,37.79,20.67,0.03" bg="F7F9FA" labels="Reffering+sites|Search+Engines|Direct+traffic|Other" colors="058DC7,50B432,ED561B,EDEF00" size="488x200" title="Traffic Sources" type="pie"]

	function chart_shortcode( $atts ) {
		extract( shortcode_atts( array(
		    'data' => '',
		    'colors' => '',
		    'size' => '400x200',
		    'bg' => 'ffffff',
		    'title' => '',
		    'labels' => '',
		    'advanced' => '',
		    'type' => 'pie'
		), $atts ) );

				switch ( $type ) {
					case 'line' :
						$charttype = 'lc'; break;
					case 'xyline' :
						$charttype = 'lxy'; break;
					case 'sparkline' :
						$charttype = 'ls'; break;
					case 'meter' :
						$charttype = 'gom'; break;
					case 'scatter' :
						$charttype = 's'; break;
					case 'venn' :
						$charttype = 'v'; break;
					case 'pie' :
						$charttype = 'p3'; break;
					case 'pie2d' :
						$charttype = 'p'; break;
					default :
						$charttype = $type;
					break;
				}
				$string = '';
				if ( $title ) $string .= '&chtt='.$title.'';
				if ( $labels ) $string .= '&chl='.$labels.'';
				if ( $colors ) $string .= '&chco='.$colors.'';
				$string .= '&chs='.$size.'';
				$string .= '&chd=t:'.$data.'';
				$string .= '&chf='.$bg.'';

		return '<img title="'.$title.'" src="http://chart.apis.google.com/chart?cht='.$charttype.''.$string.$advanced.'" alt="'.$title.'" />';
	}	
	
	// 6. GET POST FIELD BY OFFSET //////////////////////////////////////////////////
	// Get a post based on offset from the last post published (0 for last post)
	// USAGE: [postfeed field="post_title"  offset="0" customfield="true" ]
	function get_postfeed( $atts ) {

		//extract page name from the shortcode attributes
		extract( shortcode_atts( array( 'field' => 'post_title', 'offset' => '0', 'customfield' => "" ), $atts ) );

		//returns an array of objects
		$thepost = get_posts( 'numberposts=1&offset='.$offset );

		if( $customfield == 'true' ){
			$postfield = get_post_meta( $thepost[0]->ID, $field, true );
		}else{
			$postfield = $thepost[0]->$field;
		}
		return $postfield;
	}
	
	// 7. Created a container for dynamic html layout
	// USAGE: [cbox width="50%" leftgutter="15px" rightgutter="0px"] html box content[/cbox]
	function dynamic_box( $atts, $content = null ) {

	 	//extract page name from the shortcode attributes
	 	extract( shortcode_atts( array( 'width' => '30%', 'leftgutter' => '10px', 'rightgutter' => '0px' ), $atts ) );

	 	$cbox = '<div class="cbox" style="float:left;width:'.$width.';"><div class="cbox_pad" style="margin: 0px '.$rightgutter.' 0px '.$leftgutter.'">'.do_shortcode( $content ).'</div></div>';
 	
	return $cbox;
	}

	// 8. Created a container for dynamic html layout
	// USAGE: [container id="mycontainer" class="myclass"] 'cboxes' see shortcode below [/container]
	function dynamic_container( $atts, $content = null ) {

	 	//extract page name from the shortcode attributes
	 	extract( shortcode_atts( array( 'id' => 'container', 'class' => '' ), $atts ) );
	
 		$container = '<div style="width: 100%;" class="container">'.do_shortcode( $content ).'<div class="clear"></div></div>';

	 	return $container;
	}
	
	/**
	 * 9. This function produces the edit post link for logged in users
	 * 
	 * @example <code>[post_edit]</code> is the default usage
	 * @example <code>[post_edit link="Edit", before="<b>" after="</b>"]</code>
	 */
	function pagelines_post_edit_shortcode( $atts ) {

		$defaults = array(
			'link' => __( "<span class='editpage sc'>Edit</span>", 'pagelines' ),
			'before' => '[',
			'after' => ']'
		);
		$atts = shortcode_atts( $defaults, $atts );

		// Prevent automatic WP Output
		ob_start();
		edit_post_link( $atts['link'], $atts['before'], $atts['after'] ); // if logged in
		$edit = ob_get_clean();

		$output = $edit;

		return apply_filters( 'pagelines_post_edit_shortcode', $output, $atts );

	}
	
	/**
	 * 10. This function produces the category link list
	 * 
	 * @example <code>[post_categories]</code> is the default usage
	 * @example <code>[post_categories sep=", "]</code>
	 */
	function pagelines_post_categories_shortcode( $atts ) {

		$defaults = array(
			'sep' => ', ',
			'before' => '',
			'after' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		$cats = get_the_category_list( trim( $atts['sep'] ) . ' ' );

		$output = sprintf( '<span class="categories sc">%2$s%1$s%3$s</span> ', $cats, $atts['before'], $atts['after'] );

		return apply_filters( 'pagelines_post_categories_shortcode', $output, $atts );

	}
	
	/**
	 * 11. This function produces the tag link list
	 * 
	 * @example <code>[post_tags]</code> is the default usage
	 * @example <code>[post_tags sep=", " before="Tags: " after="bar"]</code>
	 */
	function pagelines_post_tags_shortcode( $atts ) {

		$defaults = array(
			'sep' => ', ',
			'before' => __( 'Tagged With: ', 'pagelines' ),
			'after' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		$tags = get_the_tag_list( $atts['before'], trim( $atts['sep'] ) . ' ', $atts['after'] );

		if ( !$tags ) return;

		$output = sprintf( '<span class="tags sc">%s</span> ', $tags );

		return apply_filters( 'pagelines_post_tags_shortcode', $output, $atts );

	}
	
	/**
	 * 12. This function produces a post type link.
	 * 
	 * @example <code>[post_type]</code> is the default usage
	 * @example <code>[post_type before="Type: " after="bar"]</code>
	 */
	function pagelines_post_type_shortcode( $atts ) {

		$defaults = array(
			'before' => __( 'Type: ', 'pagelines' ),
			'after' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );
		
		global $post;
		
		if ( $post->post_type == 'post' )
			return;
		
		$t = get_post_type_object( $post->post_type );
				
		$name = $t->labels->name;

		$type = sprintf( '%s%s%s', $atts['before'], $name, $atts['after'] );
		if( $t->has_archive )
			$output = sprintf( '<span class="type sc"><a href="%s">%s</a></span> ', get_post_type_archive_link( $t->name ), $type );
		else
			$output = sprintf( '<span class="type sc">%s</span> ', $type );
		return apply_filters( 'pagelines_post_type_shortcode', $output, $atts );
	}
	
	/**
	 * 13. This function produces the comment link
	 * 
	 * @example <code>[post_comments]</code> is the default usage
	 * @example <code>[post_comments zero="No Comments" one="1 Comment" more="% Comments"]</code>
	 */
	function pagelines_post_comments_shortcode( $atts ) {

		$defaults = array(
			'zero' => __( 'Add Comment', 'pagelines' ),
			'one' => __( "<span class='num'>1</span> Comment", 'pagelines' ),
			'more' => __( "<span class='num'>%</span> Comments", 'pagelines' ),
			'hide_if_off' => 'disabled',
			'before' => '',
			'after' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		if ( ( !comments_open() ) && $atts['hide_if_off'] === 'enabled' )
			return;

		// Prevent automatic WP Output
		ob_start();
		comments_number( $atts['zero'], $atts['one'], $atts['more'] );
		$comments = ob_get_clean();

		$comments = sprintf( '<a href="%s">%s</a>', get_comments_link(), $comments );

		$output = sprintf( '<span class="post-comments sc">%2$s%1$s%3$s</span>', $comments, $atts['before'], $atts['after'] );

		return apply_filters( 'pagelines_post_comments_shortcode', $output, $atts );

	}
	
	/**
	 * 14. This function produces the author of the post (link to author archive)
	 * 
	 * @example <code>[post_author_posts_link]</code> is the default usage
	 * @example <code>[post_author_posts_link before="<b>" after="</b>"]</code>
	 */
	function pagelines_post_author_posts_link_shortcode( $atts ) {

		$defaults = array(
			'before' => '',
			'after' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		// Prevent automatic WP Output
		ob_start();
		the_author_posts_link();
		$author = ob_get_clean();

		$output = sprintf( '<span class="author vcard sc">%2$s<span class="fn">%1$s</span>%3$s</span>', $author, $atts['before'], $atts['after'] );

		return apply_filters( 'pagelines_post_author_shortcode', $output, $atts );
	}
	
	/**
	 * 15. This function produces the author of the post (link to author URL)
	 * 
	 * @example <code>[post_author_link]</code> is the default usage
	 * @example <code>[post_author_link before="<b>" after="</b>"]</code>
	 */
	function pagelines_post_author_link_shortcode( $atts ) {

		$defaults = array(
			'nofollow' => FALSE,
			'before' => '',
			'after' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		$author = get_the_author();

		//	Link?
		if ( get_the_author_meta( 'url' ) ) {

			//	Build the link
			$author = '<a href="' . get_the_author_meta( 'url' ) . '" title="' . esc_attr( sprintf( __( 'Visit %s&#8217;s website', 'pagelines' ), $author ) ) . '" rel="external">' . $author . '</a>';

		}

		$output = sprintf( '<span class="author vcard sc">%2$s<span class="fn">%1$s</span>%3$s</span>', $author, $atts['before'], $atts['after'] );

		return apply_filters( 'pagelines_post_author_link_shortcode', $output, $atts );

	}
	
	/**
	 * 16. This function produces the author of the post (display name)
	 * 
	 * @example <code>[post_author]</code> is the default usage
	 * @example <code>[post_author before="<b>" after="</b>"]</code>
	 */	
	function pagelines_post_author_shortcode( $atts ) {

		$defaults = array(
			'before' => '',
			'after' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		$output = sprintf( '<span class="author vcard sc">%2$s<span class="fn">%1$s</span>%3$s</span>',
						esc_html( get_the_author() ),
						$atts['before'],
						$atts['after']
					);

		return apply_filters( 'pagelines_post_author_shortcode', $output, $atts );

	}

	/**
	 * 17. Post Date
	 * 
	 */	
	function pagelines_post_date_shortcode( $atts ) {

		$defaults = array(
			'format' => get_option( 'date_format' ),
			'before' => '',
			'after' => '',
			'label' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		$output = sprintf( '<time class="date time published updated sc" datetime="%5$s">%1$s%3$s%4$s%2$s</time> ',
						$atts['before'],
						$atts['after'],
						$atts['label'],
						get_the_time( $atts['format'] ),
						get_the_time( 'c' )
					);

		return apply_filters( 'pagelines_post_date_shortcode', $output, $atts );

	}
	
	
	/**
	 * 18.Shortcode to display Pinterest button
	 * 
	 * @example <code>[pinterest_button img=""]</code> is the default usage
	 * @example <code>[pinterest_button img=""]</code>
	 */
	function pl_pinterest_button( $atts ){

			$defaults = array(
				'url' => get_permalink(),
				'img' => '',
				'title' => urlencode( the_title_attribute( array( 'echo' => false ) ) ),
			); 	

			$atts = shortcode_atts( $defaults, $atts );
			
			$out = sprintf( '<a href="http://pinterest.com/pin/create/button/?url=%s&amp;media=%s&amp;description=%s" class="pin-it-button" count-layout="horizontal"><img style="border:0px;" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a><script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>',
			$atts['url'],
			$atts['img'],
			$atts['title']
			);

			return $out;

		}

	/**
	 * 1X.Shortcode to display Google Plus 1 Button
	 * 
	 * @example <code>[google_plus]</code> is the default usage
	 * @example <code>[google_plus size="" count=""]</code>
	 * @example available attributes for size include small, medium, and tall
	 * @example avialable counts include inline, and bubble
	 */
    function pl_googleplus_button ( $atts ) {

    	$defaults = array(
    		'size' => 'medium',
    		'count' => 'inline',
    		'url' => get_permalink()
    	
	    );
    	
    	$atts = shortcode_atts($defaults, $atts);

    	ob_start();

        ?>
			<script type="text/javascript">
			  (function() { var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true; po.src = 'https://apis.google.com/js/plusone.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			  })();
			</script><?php

		 printf( '<div class="g-plusone" style="width:190px;" data-size="%s" data-annotation="%s" data-href="%s"></div>',
			$atts['size'],
			$atts['count'],
			$atts['url']
		);

		return ob_get_clean();
    }

	/**
	 * . Shortcode to display Linkedin Share Button
	 * 
	 * @example <code>[linkedin]</code> is the default usage
	 * @example <code>[linkedin count="vertical"]</code>
	 */
    function pl_linkedinshare_button ($atts) {
    	
			$defaults = array(
				'url'	=> get_permalink(), 
				'count'	=> 'horizontal'
			); 	

			$atts = wp_parse_args( $atts, $defaults );


            $out = sprintf( '<script src="//platform.linkedin.com/in.js" type="text/javascript"></script><script type="IN/Share" data-url="%s" data-counter="%s"></script>',   
					$atts['url'],
					$atts['count']
				);
           
           return $out;

    }
		
	/**
	 * 19. Shortcode to display Tweet button
	 * 
	 * @example <code>[twitter_button type=""]</code> is the default usage
	 * @example <code>[twitter_button type="follow"]</code>
	 */
	function pl_twitter_button( $args ){

		$defaults = array(
			'type'      => '',
			'permalink'	=> get_permalink(), 
			'handle'	=> ( ploption( 'twittername' ) ) ? ploption( 'twittername' ) : 'PageLines' , 
			'title'		=> ''
			); 	

			$a = wp_parse_args( $args, $defaults );

			if ($a['type'] == 'follow') {

				$out = sprintf( '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script><a href="https://twitter.com/%1$s" class="twitter-follow-button" data-show-count="true">Follow @%1$s</a>', 
					$a['handle']
						);

			} else {

				$out = sprintf( '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script><a href="https://twitter.com/share" class="twitter-share-button" data-url="%s" data-text="%s" data-via="%s">Tweet</a>',   
					$a['type'],
					$a['permalink'], 
					$a['title'],
					$a['handle']
					);
			}
			return $out;
	}
		
	/**
	 * 20. Shortcode to display Facebook Like button
	 * 
	 * @example <code>[like_button]</code> is the default usage
	 * @example <code>[like_button]</code>
	 */
	function pl_facebook_shortcode( $args ){

			$defaults = array(
				'url'	=> get_permalink(), 
				'width'		=> '80',
			); 

			$a = wp_parse_args( $args, $defaults );
			
			ob_start();
				// Facebook
				?>
				<script>(function(d, s, id) {
						var js, fjs = d.getElementsByTagName(s)[0];
						if (d.getElementById(id)) return;
						js = d.createElement(s); js.id = id;
						js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
						fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));
				</script><?php
				printf( '<div class="fb-like" data-href="%s" data-send="false" data-layout="button_count" data-width="%s" data-show-faces="false" data-font="arial" style="vertical-align: top"></div>',
					$a['url'], 
					$a['width']
				);

			return ob_get_clean();

		}
			
	/**
	 * 21. This function/shortcode will show all authors on a post
	 * 
	 * @example <code>[show_authors]</code> is the default usage
	 * @example <code>[show_authors]</code>
	 */
	function show_multiple_authors() {

		if( class_exists( 'CoAuthorsIterator' ) ) {

			$i = new CoAuthorsIterator();
			$return = '';
			$i->iterate();
			$return .= '<a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ).'">'.get_the_author_meta( 'display_name' ).'</a>';
			while( $i->iterate() ){
				$return.= $i->is_last() ? ' and ' : ', ';
				$return .= '<a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ).'">'.get_the_author_meta( 'display_name' ).'</a>';
			}

			return $return;

		} else {
			//fallback
		}
	}

	/**
	 * 22.Bootstrap Code Shortcode
	 * 
	 * @example <code>[pl_codebox]...[/pl_codebox]</code> is the default usage
	 * @example <code>[pl_codebox scrollable="yes"].box{margin:0 auto;}[/pl_codebox]</code> for lots of code
	 */

	function pl_codebox_shortcode ( $atts, $content = null ) {
		
	    extract( shortcode_atts( array(
			'scrollable' => 'no',
			'linenums' => 'yes',
			'language'	=> 'html'
		), $atts ) );

        $scrollable = ( $scrollable == 'yes' ) ? 'pre-scrollable' : '';
		$linenums = ( $linenums == 'yes' ) ? 'linenums' : '';
		$language = 'lang-'.$language;

		// Grab Shortcodes
		$pattern = array(
		
			'#([a-z]+\=[\'|"][^\'|"]*[\'|"])#m',
			'#(\[[^]]*])#m',

		);
		$replace = array(
			'<span class="sc_var">$1</span>',
			'<span class="sc_code">$1</span>'
		);

		$code = preg_replace( $pattern, $replace, esc_html( $content ) );

		$out = sprintf( '<pre class="%s prettyprint %s %s">%s</pre>',
					$scrollable,
					$language,
					$linenums,
					$code
				);

		return $out;
	}

	/**
	 * 23. Bootstrap Labels Shortcode
	 * 
	 * @example <code>[pl_label type=""]My Label[/pl_label]</code> is the default usage
	 * @example <code>[pl_label type="info"]label[/pl_label]</code>
	 * @example Available types include info, success, warning, error
	 */
	function pl_label_shortcode( $atts, $content = null ) {
	    
	    $defaults = array(
	    	'type' => 'info',
	    );

    	$atts = shortcode_atts( $defaults, $atts );

	    $out = sprintf( '<span class="label label-%s">%s</span>',
					$atts['type'],
					do_shortcode( $content )
				);

	    return $out;
	}

	/**
	 * 24. Bootstrap Badges Shortcode
	 * 
	 * @example <code>[pl_badge type="info"]My badge[/pl_badge]</code> is the default usage
	 * @example <code>[pl_badge type="info"]badge[/pl_badge]</code>
	 * @example Available types include info, success, warning, error
	 */
	function pl_badge_shortcode( $atts, $content = null ) {
	    
	    $defaults = array(
	    	'type' => 'info',
	    );
		
		$atts = shortcode_atts( $defaults, $atts );

	    $out = sprintf( '<span class="badge badge-%s">%s</span>',
					$atts['type'],
					do_shortcode( $content )
				);

	    return $out;
	}

	
	/**
	 * 25. Bootstrap Alertbox Shortcode
	 * 
	 * @example <code>[pl_alertbox type="info"]My alert[/pl_alertbox]</code> is the default usage
	 * @example <code>[pl_alertbox type="info" closable="yes"]My alert[/pl_alertbox]</code> makes an alert that can be toggled away with a close button
	 * @example <code>[pl_alertbox type="info"]<h4 class="pl-alert-heading">Heading</h4>My alert[/pl_alertbox]</code>
	 * @example Available types include info, success, warning, error
	 */
	function pl_alertbox_shortcode( $atts, $content = null ) {

		$content = str_replace( '<br />', '', str_replace( '<br>', '', $content ) );

		$defaults = array(
				    'type' => 'info',
				    'closable' =>'no',
					);

        $atts = shortcode_atts( $defaults, $atts );

        $closed = sprintf( '<div class="alert alert-%s"><a class="close" data-dismiss="alert" href="#">×</a>%s</div>',
				$atts['type'],
				do_shortcode( $content )
		);

        if ( $atts['closable'] === 'yes' ) {	
			
			return $closed;
        
    	}

	    $out = sprintf( '<div class="alert alert-%s alert-block">%2$s</div>',
					$atts['type'],
					do_shortcode( $content )
				);

		return $out;
	}
	
	/**
	 * 26. Bootstrap Blockquote Shortcode
	 * 
	 * @example <code>[pl_blockquote pull="" cite=""]My quote[/pl_blockquote]</code> is the default usage
	 * @example <code>[pl_blockquote pull="right" cite="Someone Famous"]My quote pulled right with source[/pl_blockquote]</code>
	 */
	function pl_blockquote_shortcode( $atts, $content = null) {

		$defaults = array(
			'pull'	=> '', 
			'cite'	=> ''
		); 

		$atts = shortcode_atts( $defaults, $atts );
		
		$out = sprintf( '<blockquote class="pull-%1$s"><p>%3$s<small>%2$s</small></p></blockquote>',
					$atts['pull'],
					$atts['cite'],
					do_shortcode( $content )
				);

		return $out;

	}
	
	/**
	 * 27. Bootstrap Button Shortcode
	 * 
	 * @example <code>[pl_button type="" size="" link="" target=""]...[/pl_button]</code> is the default usage
	 * @example <code>[pl_button type="info" size="small" link="#" target="blank"]My Button[/pl_button]</code>
	 * @example Available types include info, success, warning, danger, inverse
	 * @example Available sizes include large, medium, and mini
	 */
	function pl_button_shortcode( $atts, $content = null, $target = null ) {

		$defaults = array(
			'type' => 'info',
			'size' => 'small',
			'link' => '#',
			'target' => '_self'
		);

		$atts = shortcode_atts( $defaults, $atts );

	    $target = ( $target == 'blank' ) ? '_blank' : '';

	    $out = sprintf( '<a href="%1$s" target="%2$s" class="btn btn-%3$s btn-%4$s">%5$s</a>', 
					$atts['link'],
					$atts['target'],
					$atts['type'],
					$atts['size'],
					do_shortcode( $content )
					);

		return $out;
	}


	/**
	 * 28. Bootstrap Button Group Shortcode - Builds a group of buttons as a menu
	 * 
	 * @example <code>[pl_buttongroup]<a href="#" class="btn btn-info">...[/pl_buttongroup]</code> is the default usage
	 * @example <code>[pl_buttongroup]<a href="#" class="btn btn-info"><a href="#" class="btn btn-info"><a href="#" class="btn btn-info">[/pl_button]</code>
	 * @example Available types include info, success, warning, danger, inverse
	 */
	function pl_buttongroup_shortcode( $atts, $content = null ) {

		$content = str_replace( '<br />', '', str_replace( '<br>', '', $content ) );

    	return sprintf( '<div class="btn-group">%s</div>', do_shortcode( $content ) );

	}

	
	/**
	 * 29. Bootstrap Dropdown Button Shortcode - Builds a button with contained dropdown menu
	 * 
	 * @example <code>[pl_buttondropdown size="" type="" label=""]<li><a href="#">...</a></li>[/pl_buttondropdown]</code> is the default usage
	 * @example <code>[pl_buttondropdown size="large" type="info" label="button"]<li><a href="#"></li><li><a href="#"></li><li><a href="#"></li>[/pl_buttondropdown]</code>
	 * @example Available types include info, success, warning, danger, inverse
	 */
	function pl_buttondropdown_shortcode( $atts, $content = null  ) {

	    $defaults = array(
		    'size' => '',
		    'type' => '',
		    'label' => ''
		);

		$atts = shortcode_atts( $defaults, $atts );

	    $out = sprintf( '<div class="btn-group"><button class="btn btn-%s btn-%s dropdown-toggle" data-toggle="dropdown" href="#">%s <span class="caret"></span></button><ul  class="dropdown-menu">%s</ul></div>',
	        $atts['size'],
	      	$atts['type'],
	        $atts['label'],
			do_shortcode( $content )
	    );
	        
	    return $out;
	}


	/**
	 * 30. Bootstrap Split Button Dropdown - Builds a button with split button dropdown caret
	 * 
	 * @example <code>[pl_splitbuttondropdown size="" type="" label=""]<li><a href="#">...</a></li>[/pl_splitbuttondropdown]</code> is the default usage
	 * @example <code>[pl_splitbuttondropdown size="large" type="info" label="button"]<li><a href="#"></li><li><a href="#"></li><li><a href="#"></li>[/pl_splitbuttondropdown]</code>
	 * @example Available types include info, success, warning, danger, inverse
	 */
	function pl_splitbuttondropdown_shortcode( $atts, $content = null ) {
	    
	    $defaults = array(
		    'size' => '',
		    'type' => '',
		    'label' => ''
	    );

		$atts = shortcode_atts( $defaults, $atts );

	    $out = sprintf( '<div class="btn-group"><a class="btn btn-%1$s btn-%2$s" >%3$s</a><a class="btn btn-%1$s btn-%2$s dropdown-toggle" data-toggle="dropdown"><span  class="caret"></span></a><ul class="dropdown-menu">%4$s</ul></div>',
	      	$atts['size'],
	        $atts['type'],
	        $atts['label'],
			do_shortcode( $content )
	    );
	        
	    return $out;
	}

	 /**
	 * 31. Bootstrap Tooltips
	 * 
	 * @example <code>[pl_tooltip tip=""]...[/pl_tooltip]</code> is the default usage
	 * @example <code>This is a [pl_tooltip tip="Cool"]tooltip[/pl_tooltip] example.</code>
	 */
	function pl_tooltip_shortcode( $atts, $content = null ) {

	    $defaults = array(
	    	'tip' => 'Tip',
	    );

        $atts = shortcode_atts( $defaults, $atts );

			ob_start();
				
				?>
				<script>
                	jQuery(function(){
						jQuery("a[rel=tooltip]").tooltip();
					});
				</script><?php

			printf( '<a href="#" rel="tooltip" title="%s">%s</a>',
				$atts['tip'],
				do_shortcode( $content )
			);

			return ob_get_clean();

	}

	/**
	 * 32. Bootstrap Popovers
	 * 
	 * @example <code>[pl_popover title="" content=""]...[/pl_popover]</code> is the default usage
	 * @example <code>This is a [pl_popover title="Popover Title" content="Some content that you can have inside the Popover"]popover[/pl_popover] example.</code>
	 */
    function pl_popover_shortcode( $atts, $content = null ) {

	    $defaults = array(
	    	'title' => 'Popover Title',
	    	'content' => 'Content'
	    );

	    $atts = shortcode_atts( $defaults, $atts );

	    ob_start();

	    	?>
	    	<script>
                	jQuery(function(){
						 jQuery("a[rel=popover]")
      					.popover()
      					.click(function(e) {
        					e.preventDefault()
      					});
					});
	    	</script><?php

    	printf( '<a href="#" rel="popover" title="%s" data-content="%s">%s</a>',
			$atts['title'],
			$atts['content'],
			do_shortcode( $content )
		);

    	return ob_get_clean();

	}


	/**
	 * 33. Bootstrap Accordion - Collapsable Content
	 * 
	 * @example <code>[pl_accordion name="accordion"] [accordioncontent name="accordion" number="1" heading="Tile 1"]Content 1 [/accordioncontent] [accordioncontent name="accordion" number="2" heading="Title 2"]Content 2 [/accordioncontent] [/pl_accordion]</code> is the default usage
	 */
	function pl_accordion_shortcode( $atts, $content = null ) {

		$defaults = array(
			'name' => '',
	    );
         
        $atts = shortcode_atts( $defaults, $atts );

	    $out = sprintf( '<div id="%s" class="accordion">'.do_shortcode( $content ).'</div>',$atts['name'] );
	        
	    return $out;
	}
	//Accordion Content
	function pl_accordioncontent_shortcode( $atts, $content = null, $open = null ) {
	    
	    $defaults = array(
		    'name' => '',
		    'heading' => '',
		    'number' => '',
		    'open' => ''
	    );

        $atts = shortcode_atts( $defaults, $atts );
		$open = ( $atts['open'] == 'yes' ) ? 'in' : '';
	    $out = sprintf( '<div class="accordion-group"><div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#%1$s" href="#collapse%3$s">%2$s</a></div><div id="collapse%3$s" class="accordion-body collapse %4$s"><div class="accordion-inner">%5$s</div></div></div>',
	      	$atts['name'],
	        $atts['heading'],
	        $atts['number'],
	        $open,
			do_shortcode( $content )

	    );
	        
	    return $out;
	}

	/**
	 * 34. Bootstrap Carousel
	 * 
	 * @example <code>[pl_carousel name=""][pl_carouselimage first="yes" title="" imageurl="" ]Caption[/pl_carouselimage][pl_carouselimage title="" imageurl="" ]Caption[/pl_carouselimage][/pl_carousel]</code> is the default usage
	 * @example <code>[pl_carousel name="PageLinesCarousel"][pl_carouselimage first="yes" title="Feature 1" imageurl="" ]Image 1 Caption[/pl_carouselimage][pl_carouselimage title="Feature 2" imageurl=""]Image 2 Caption[/pl_carouselimage][pl_carouselimage title="Feature 3" imageurl=""]Image 3 Caption[/pl_carouselimage][/pl_carousel]</code>
	 */
    function pl_carousel_shortcode( $atts, $content = null ) {
	    
	    $defaults = array(
	    	'name' => 'PageLines Carousel',
	    );

	    $atts = shortcode_atts( $defaults, $atts );

	    	ob_start();
				
				?>
				<script>
	            	jQuery(function(){
						jQuery('.carousel').carousel();
					});
				</script><?php

		   		printf( '<div id="%2$s" class="carousel slide"><div class="carousel-inner">%1$s</div><a class="carousel-control left" href="#%2$s" data-slide="prev">&lsaquo;</a><a class="carousel-control right" href="#%2$s" data-slide="next">&rsaquo;</a></div>',
					do_shortcode( $content ),
			        $atts['name']
		        );
        
        	return ob_get_clean();

	}
	//Carousel Images
	function pl_carouselimage_shortcode( $atts, $content = null ) {
	    
	    extract( shortcode_atts( array(
		    'first' => '',
		    'title' => '',
		    'imageurl' => '',
		    'caption' => '',
	    ), $atts ) );

	    $first = ( $first == 'yes' ) ? 'active' : '';
	    $content = ( $content <> '' ) ? "<div class='carousel-caption'><h4>$title</h4><p>$content</p></div></div>" : '';

		return sprintf( '<div class="item %s"><img src="%s">%s',
				$first,
				$imageurl,
				do_shortcode( $content )
				);

	}

	/**
	 * 35. Bootstrap Tabs
	 * 
	 * @example <code>[pl_tabs][pl_tabtitlesection type=""][pl_tabtitle active="" number="1"]...[/pl_tabtitle][pl_tabtitle number="2"]...[/pl_tabtitle][/pl_tabtitlesection][pl_tabcontentsection][pl_tabcontent active="" number="1"]...[/pl_tabcontent][pl_tabcontent number=""]...[/pl_tabcontent][/pl_tabcontentsection][/pl_tabs]</code> is the default usage
	 * @example <code>[pl_tabs][pl_tabtitlesection type="tabs"][pl_tabtitle active="yes" number="1"]Title 1[/pl_tabtitle][pl_tabtitle number="2"]Title 2[/pl_tabtitle][/pl_tabtitlesection][pl_tabcontentsection][pl_tabcontent active="yes" number="1"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque ac mi enim, at consectetur justo.[/pl_tabcontent][pl_tabcontent number="2"]Second content there.[/pl_tabcontent][/pl_tabcontentsection][/pl_tabs]</code>
	 * @example Available types include tabs, pills
	 */

    function pl_tabs_shortcode( $atts, $content = null ) {

    	return sprintf( '<div class="tabs">%s</div>', do_shortcode( $content ) );

	}

	//Tab Titles Section
		function pl_tabtitlesection_shortcode( $atts, $content = null ) {
		        
			extract( shortcode_atts( array(
		    	'type' => '',
		    ), $atts ) );

		    ob_start();

		    	?>
		    		<script>
			    		jQuery(function(){
							 jQuery('a[data-toggle="tab"]').on('shown', function (e) {
							  e.target // activated tab
							  e.relatedTarget // previous tab
							})
						});
		    		</script><?php

		    printf( '<ul class="nav nav-%s">%s</ul>',
			$type,
			do_shortcode( $content )
			);
		        
		    return ob_get_clean();
		}

	//Tab Titles
		function pl_tabtitle_shortcode( $atts, $content = null ) {
		         
		    extract( shortcode_atts( array(
				'active' => '',
				'number' => ''
			), $atts ) );

		    $active = ( $active == 'yes' ) ? "class='active'" : '';
		    
		    $out = sprintf( '<li %s><a href="#%s" data-toggle="tab">%s</a></li>',
					$active,
					$number,
					do_shortcode( $content )
					);
		        
		    return $out;
		}

	//Tab Content Section
		function pl_tabcontentsection_shortcode( $atts, $content = null ) {

		    return '<div class="tab-content">'.do_shortcode( $content ).'</div>';

		}

	//Tab Content
		function pl_tabcontent_shortcode( $atts, $content = null ) {

		    extract( shortcode_atts( array(
			    'active' => '',
			    'number' => ''
		    ), $atts ) );
 	        
		    $active = ( $active == 'yes' ) ? "active" : '';

		    return sprintf( '<div class="tab-pane %s" id="%s"><p>%s</p></div>',
					$active,
					$number,
					do_shortcode( $content )
					);
		        
		}

	/**
	 * 36. Bootstrap Modal Popup Window
	 * 
	 * @example <code>[pl_modal title="" type="" colortype="" label=""]...[/pl_modal]</code>
	 * @example <code>[pl_modal title="Title" type="label" colortype="info" label="Click Me!"]Some content here for the cool modal pop up. You can have all kinds of cool stuff in here.[/pl_modal]</code>
	 * @example available types include button, label, and badge
	 * @example available color types include default, success, warning, important, info, and inverse
	 */	
	function pl_modal_shortcode( $atts, $content = null ) {
 
	    extract( shortcode_atts( array(
		    'title'		=> '',
		    'type'		=> '',
		    'colortype' => '',
		    'label' 	=> '',
			'hash'		=> rand()
	    ), $atts ) );
 
	    	ob_start();
 
	    		?>
				<script>
	            	jQuery(function(){
						jQuery('#modal_<?php echo $hash; ?>').modal({
							keyboard: true
							, show: false
						});
					});
				</script><?php
 
		   		printf( '<div id="modal_%6$s" class="modal hide fade" style="display:none;"><div class="modal-header"><a class="close" data-dismiss="modal">×</a><h3>%s</h3></div><div class="modal-body"><p>%4$s</p></div><div class="modal-footer"><a href="#" class="btn btn-%3$s" data-dismiss="modal">Close</a></div></div><a data-toggle="modal" href="#modal_%6$s" class="%2$s %2$s-%3$s">%5$s</a>',
				$title,
				$type,
				$colortype,
				do_shortcode( $content ),
				$label,
				$hash
		        );
 
        	return ob_get_clean();
 
	}
		
	/**
	 * 37. This function produces the time of post publication
	 * 
	 * @example <code>[post_time]</code> is the default usage</code>
	 * @example <code>[post_time format="g:i a" before="<b>" after="</b>"]</code>
	 */	
	function pagelines_post_time_shortcode( $atts ) {

		$defaults = array( 
			'format' => get_option( 'time_format' ),
			'before' => '',
			'after' => '',
			'label' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		$output = sprintf( '<span class="time published sc" title="%5$s">%1$s%3$s%4$s%2$s</span> ',
						$atts['before'],
						$atts['after'],
						$atts['label'],
						get_the_time( $atts['format'] ),
						get_the_time( 'Y-m-d\TH:i:sO' )
						);

		return apply_filters( 'pagelines_post_time_shortcode', $output, $atts );

	}
	
	/**
	 * 38. Used to create general buttons and button links
	 * 
	 * @example <code>[button]</code> is the default usage
	 * @example <code>[button format="edit_post" before="<b>" after="</b>"]</code>
	 */
	function pagelines_button_shortcode( $atts ) {

		$defaults = array(
			'color'	=> 'grey', 
			'size'	=> 'normal',
			'align'	=> 'right', 
			'style'	=> '',
			'type'	=> 'button', 
			'text'	=> '&nbsp;',
			'pid'	=> 0, 
			'class'	=> null, 
		);
		$atts = shortcode_atts( $defaults, $atts );

		$button = sprintf( '<div class="blink"><div class="blink-pad">%s</div></div>', $text );

		$output = sprintf( '<div class="%s %s %s blink-wrap">%s</div>', $special, $size, $color, $button );

		return apply_filters( 'pagelines_button_shortcode', $output, $atts );

	}

	/**
	 * XX. Responsive Videos
	 * 
	 * @example <code>[pl_video]</code> is the default usage
	 * @example <code>[pl_video type="youtube" url="urltovideo"]</code>
	 */
    function pl_video_shortcode ($atts) {
    	
    	extract( shortcode_atts( array(
    		'type' =>'',
	    	'id' =>'',
	    	'width' => '',
	    	'height' => ''
	    	), $atts ) );

        if ($atts['type'] == 'youtube') {
	    
	    	$out = sprintf('<div class="pl-video"><iframe src="http://www.youtube.com/embed/%2$s?wmode=transparent" width="%3$s" height="%4$s" frameborder="0" allowfullscreen wmode="transparent"></iframe></div>',$type,$id,$width,$height);
	    	return $out;

	    } elseif ($atts['type'] == 'vimeo') {

	    $out = sprintf('<div class="pl-video"><iframe src="http://player.vimeo.com/video/%2$s" width="%3$s" height="%4$s"  frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen wmode="transparent"></iframe></div>',$type,$id,$width,$height);
	    	return $out;
	    }
    }



	function filters() {

		/**
		 *  Prevent AUTOP inside of shortcodes (breaking shortcodes - removed)
		 */
		remove_filter( 'the_content', 'wpautop' );
		add_filter( 'the_content', 'wpautop' , 12);		
		remove_filter( 'the_content', 'wptexturize' );
		add_filter( 'the_content', 'wptexturize' , 12);
	}

	private function register_shortcodes( $shortcodes ) {

		foreach ( $shortcodes as $shortcode => $data ) {
			add_shortcode( $shortcode, array( &$this, $data['function']) );
		}	
	}
//		
} // end of class
//
new PageLines_ShortCodes;
