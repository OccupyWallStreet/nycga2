<?php

	/**
	 * List of available shortcodes
	 */
	function su_shortcodes( $shortcode = false ) {
		$shortcodes = array(
			# basic shortcodes - start
			'basic-shortcodes-open' => array(
				'name' => __( 'Basic shortcodes', 'shortcodes-ultimate' ),
				'type' => 'opengroup'
			),
			# heading
			'heading' => array(
				'name' => 'Heading',
				'type' => 'wrap',
				'atts' => array(
					'style' => array(
						'values' => array(
							'1',
							'2'
						),
						'desc' => __( 'Heading style', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[heading] Content [/heading]<br/>[heading style="2"] Content [/heading]',
				'content' => __( 'Heading', 'shortcodes-ultimate' ),
				'desc' => __( 'Styled heading', 'shortcodes-ultimate' )
			),
			# frame
			'frame' => array(
				'name' => 'Image frame',
				'type' => 'wrap',
				'atts' => array(
					'align' => array(
						'values' => array(
							'left',
							'center',
							'none',
							'right'
						),
						'desc' => __( 'Frame align', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[frame align="center"] <img src="image.jpg" alt="" /> [/frame]',
				'content' => __( 'Image tag', 'shortcodes-ultimate' ),
				'desc' => __( 'Styled image frame', 'shortcodes-ultimate' )
			),
			# tabs
			'tabs' => array(
				'name' => 'Tabs',
				'type' => 'wrap',
				'atts' => array(
					'style' => array(
						'values' => array(
							'1',
							'2',
							'3'
						),
						'default' => '1',
						'desc' => __( 'Tabs style', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[tabs style="1"] [tab title="Tab name"] Tab content [/tab] [/tabs]',
				'desc' => __( 'Tabs container', 'shortcodes-ultimate' )
			),
			# tab
			'tab' => array(
				'name' => 'Tab',
				'type' => 'wrap',
				'atts' => array(
					'title' => array(
						'values' => array( ),
						'default' => __( 'Title', 'shortcodes-ultimate' ),
						'desc' => __( 'Tab title', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[tabs style="1"] [tab title="Tab name"] Tab content [/tab] [/tabs]',
				'content' => __( 'Tab content', 'shortcodes-ultimate' ),
				'desc' => __( 'Single tab', 'shortcodes-ultimate' )
			),
			# spoiler
			'spoiler' => array(
				'name' => 'Spoiler',
				'type' => 'wrap',
				'atts' => array(
					'title' => array(
						'values' => array( ),
						'default' => __( 'Spoiler title', 'shortcodes-ultimate' ),
						'desc' => __( 'Spoiler title', 'shortcodes-ultimate' )
					),
					'open' => array(
						'values' => array(
							'0',
							'1'
						),
						'default' => '0',
						'desc' => __( 'Is spoiler open?', 'shortcodes-ultimate' )
					),
					'style' => array(
						'values' => array(
							'1',
							'2'
						),
						'default' => '1',
						'desc' => __( 'Spoiler style', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[spoiler title="Spoiler title"] Hidden text [/spoiler]',
				'content' => __( 'Hidden content', 'shortcodes-ultimate' ),
				'desc' => __( 'Hidden text', 'shortcodes-ultimate' )
			),
			# accordion
			'accordion' => array(
				'name' => 'Accordion',
				'type' => 'wrap',
				'atts' => array( ),
				'usage' => '[accordion]<br/>[spoiler open="true"] content [/spoiler]<br/>[spoiler] content [/spoiler]<br/>[spoiler] content [/spoiler]<br/>[/accordion]',
				'content' => '[spoiler] content [/spoiler]',
				'desc' => __( 'Accordion', 'shortcodes-ultimate' )
			),
			# divider
			'divider' => array(
				'name' => 'Divider',
				'type' => 'single',
				'atts' => array(
					'top' => array(
						'values' => array(
							'0',
							'1'
						),
						'default' => '0',
						'desc' => __( 'Show TOP link', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[divider top="1"]',
				'desc' => __( 'Content divider with optional TOP link', 'shortcodes-ultimate' )
			),
			# spacer
			'spacer' => array(
				'name' => 'Spacer',
				'type' => 'single',
				'atts' => array(
					'size' => array(
						'values' => array(
							'0',
							'5',
							'10',
							'20',
							'40'
						),
						'default' => '20',
						'desc' => __( 'Spacer height in pixels', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[spacer size="20"]',
				'desc' => __( 'Empty space with adjustable height', 'shortcodes-ultimate' )
			),
			# quote
			'quote' => array(
				'name' => 'Quote',
				'type' => 'wrap',
				'atts' => array(
					'style' => array(
						'values' => array(
							'1',
							'2',
							'3'
						),
						'default' => '1',
						'desc' => __( 'Quote style', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[quote style="1"] Content [/quote]',
				'content' => __( 'Quote', 'shortcodes-ultimate' ),
				'desc' => __( 'Blockquote alternative', 'shortcodes-ultimate' )
			),
			# pullquote
			'pullquote' => array(
				'name' => 'Pullquote',
				'type' => 'wrap',
				'atts' => array(
					'align' => array(
						'values' => array(
							'left',
							'right'
						),
						'default' => 'left',
						'desc' => __( 'Pullquote alignment', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[pullquote align="left"] Content [/pullquote]',
				'content' => __( 'Pullquote', 'shortcodes-ultimate' ),
				'desc' => __( 'Pullquote', 'shortcodes-ultimate' )
			),
			# highlight
			'highlight' => array(
				'name' => 'Highlight',
				'type' => 'wrap',
				'atts' => array(
					'bg' => array(
						'values' => array( ),
						'default' => '#DDFF99',
						'desc' => __( 'Background color', 'shortcodes-ultimate' ),
						'type' => 'color'
					),
					'color' => array(
						'values' => array( ),
						'default' => '#000000',
						'desc' => __( 'Text color', 'shortcodes-ultimate' ),
						'type' => 'color'
					)
				),
				'usage' => '[highlight bg="#fc0" color="#000"] Content [/highlight]',
				'content' => __( 'Highlighted text', 'shortcodes-ultimate' ),
				'desc' => __( 'Highlighted text', 'shortcodes-ultimate' )
			),
			# label
			'label' => array(
				'name' => 'Label',
				'type' => 'wrap',
				'atts' => array(
					'style' => array(
						'values' => array(
							'default',
							'success',
							'warning',
							'important',
							'info'
						),
						'default' => 'default',
						'desc' => __( 'Label style', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[label style="info"]Something[/label]',
				'content' => __( 'Label', 'shortcodes-ultimate' ),
				'desc' => __( 'Styled label', 'shortcodes-ultimate' )
			),
			# dropcap
			'dropcap' => array(
				'name' => 'Dropcap',
				'type' => 'wrap',
				'atts' => array(
					'style' => array(
						'values' => array(
							'1',
							'2',
							'3'
						),
						'default' => '1',
						'desc' => __( 'Dropcap style', 'shortcodes-ultimate' )
					),
					'size' => array(
						'values' => array(
							'1',
							'2',
							'3',
							'4',
							'5'
						),
						'default' => '3',
						'desc' => __( 'Dropcap size', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[dropcap style="1"]D[/dropcap]ropcap',
				'content' => __( 'D', 'shortcodes-ultimate' ),
				'desc' => __( 'Dropcap', 'shortcodes-ultimate' )
			),
			# bloginfo
			'bloginfo' => array(
				'name' => 'Bloginfo',
				'type' => 'single',
				'atts' => array(
					'option' => array(
						'values' => array(
							'name',
							'description',
							'siteurl',
							'admin_email',
							'charset',
							'version',
							'html_type',
							'text_direction',
							'language',
							'template_url',
							'pingback_url',
							'rss2_url'
						),
						'default' => 'left',
						'desc' => __( 'Option name', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[bloginfo option="name"]',
				'desc' => __( 'Blog info', 'shortcodes-ultimate' )
			),
			# permalink
			'permalink' => array(
				'name' => 'Permalink',
				'type' => 'mixed',
				'atts' => array(
					'p' => array(
						'values' => array( ),
						'default' => '1',
						'desc' => __( 'Post/page ID', 'shortcodes-ultimate' )
					),
					'target' => array(
						'values' => array(
							'self',
							'blank'
						),
						'default' => 'self',
						'desc' => __( 'Link target', 'shortcodes-ultimate' )
					),
				),
				'usage' => '[permalink p=52]<br/>[permalink p="52" target="blank"] Content [/permalink]',
				'content' => __( 'Permalink text', 'shortcodes-ultimate' ),
				'desc' => __( 'Permalink to specified post/page', 'shortcodes-ultimate' )
			),
			# button
			'button' => array(
				'name' => 'Button',
				'type' => 'wrap',
				'atts' => array(
					'link' => array(
						'values' => array( ),
						'default' => '#',
						'desc' => __( 'Button link', 'shortcodes-ultimate' )
					),
					'color' => array(
						'values' => array( ),
						'default' => '#AAAAAA',
						'desc' => __( 'Button background color', 'shortcodes-ultimate' ),
						'type' => 'color'
					),
					'size' => array(
						'values' => array(
							'1',
							'2',
							'3',
							'4',
							'5',
							'6',
							'7',
							'8',
							'9',
							'10',
							'11',
							'12'
						),
						'default' => '3',
						'desc' => __( 'Button size', 'shortcodes-ultimate' )
					),
					'style' => array(
						'values' => array(
							'1',
							'2',
							'3',
							'4',
							'5'
						),
						'default' => '1',
						'desc' => __( 'Button background style', 'shortcodes-ultimate' )
					),
					'dark' => array(
						'values' => array(
							'0',
							'1'
						),
						'default' => '0',
						'desc' => __( 'Dark text color', 'shortcodes-ultimate' )
					),
					'radius' => array(
						'values' => array(
							'auto',
							'0',
							'5',
							'10',
							'20'
						),
						'default' => 'auto',
						'desc' => __( 'Corners radius', 'shortcodes-ultimate' )
					),
					'icon' => array(
						'values' => array( ),
						'default' => '',
						'desc' => __( 'Button icon', 'shortcodes-ultimate' )
					),
					'class' => array(
						'values' => array( ),
						'default' => '',
						'desc' => __( 'Button class', 'shortcodes-ultimate' )
					),
					'target' => array(
						'values' => array(
							'self',
							'blank'
						),
						'default' => 'self',
						'desc' => __( 'Button link target', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[button link="#" color="#b00" size="3" style="3" dark="1" square="1" icon="image.png"] Button text [/button]',
				'content' => __( 'Button text', 'shortcodes-ultimate' ),
				'desc' => __( 'Styled button', 'shortcodes-ultimate' )
			),
			# fancy_link
			'fancy_link' => array(
				'name' => 'Fancy link',
				'type' => 'wrap',
				'atts' => array(
					'color' => array(
						'values' => array(
							'black',
							'white'
						),
						'default' => 'black',
						'desc' => __( 'Link color', 'shortcodes-ultimate' )
					),
					'link' => array(
						'values' => array( ),
						'default' => '#',
						'desc' => __( 'URL', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[fancy_link color="black" link="http://example.com/"] Read more [/fancy_link]',
				'content' => __( 'Link text', 'shortcodes-ultimate' ),
				'desc' => __( 'Fancy link', 'shortcodes-ultimate' )
			),
			# service
			'service' => array(
				'name' => 'Service',
				'type' => 'wrap',
				'atts' => array(
					'title' => array(
						'values' => array( ),
						'default' => __( 'Service title', 'shortcodes-ultimate' ),
						'desc' => __( 'Service title', 'shortcodes-ultimate' )
					),
					'icon' => array(
						'values' => array( ),
						'default' => '',
						'desc' => __( 'Service icon', 'shortcodes-ultimate' )
					),
					'size' => array(
						'values' => array(
							'24',
							'32',
							'48'
						),
						'default' => '32',
						'desc' => __( 'Icon size', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[service title="Service title" icon="service.png" size="32"] Service description [/service]',
				'content' => __( 'Service description', 'shortcodes-ultimate' ),
				'desc' => __( 'Service box with title', 'shortcodes-ultimate' )
			),
			# members
			'members' => array(
				'name' => 'Members',
				'type' => 'wrap',
				'atts' => array(
					'style' => array(
						'values' => array(
							'0',
							'1',
							'2'
						),
						'default' => '1',
						'desc' => __( 'Box style', 'shortcodes-ultimate' )
					),
					'login' => array(
						'values' => array(
							'0',
							'1'
						),
						'default' => '1',
						'desc' => __( 'Show login message', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[members style="2" login="1"] Content for logged members [/members]',
				'content' => __( 'Content for logged members', 'shortcodes-ultimate' ),
				'desc' => __( 'Content for logged in members only', 'shortcodes-ultimate' )
			),
			# guests
			'guests' => array(
				'name' => 'Guests',
				'type' => 'wrap',
				'atts' => array( ),
				'usage' => '[guests] Content for guests [/guests]',
				'content' => __( 'Content for guests', 'shortcodes-ultimate' ),
				'desc' => __( 'Content for guests only', 'shortcodes-ultimate' )
			),
			# box
			'box' => array(
				'name' => 'Box',
				'type' => 'wrap',
				'atts' => array(
					'title' => array(
						'values' => array( ),
						'default' => __( 'Box title', 'shortcodes-ultimate' ),
						'desc' => __( 'Box title', 'shortcodes-ultimate' )
					),
					'color' => array(
						'values' => array( ),
						'default' => '#333333',
						'desc' => __( 'Box color', 'shortcodes-ultimate' ),
						'type' => 'color'
					)
				),
				'usage' => '[box title="Box title" color="#f00"] Content [/box]',
				'content' => __( 'Box content', 'shortcodes-ultimate' ),
				'desc' => __( 'Colored box with caption', 'shortcodes-ultimate' )
			),
			# note
			'note' => array(
				'name' => 'Note',
				'type' => 'wrap',
				'atts' => array(
					'color' => array(
						'values' => array( ),
						'default' => '#FFCC00',
						'desc' => __( 'Note color', 'shortcodes-ultimate' ),
						'type' => 'color'
					)
				),
				'usage' => '[note color="#FFCC00"] Content [/note]',
				'content' => __( 'Note text', 'shortcodes-ultimate' ),
				'desc' => __( 'Colored box', 'shortcodes-ultimate' )
			),
			# private
			'private' => array(
				'name' => 'Private',
				'type' => 'wrap',
				'atts' => array( ),
				'usage' => '[private] Private content [/private]',
				'content' => __( 'Private note text', 'shortcodes-ultimate' ),
				'desc' => __( 'Private note for post authors', 'shortcodes-ultimate' )
			),
			# list
			'list' => array(
				'name' => 'List',
				'type' => 'wrap',
				'atts' => array(
					'style' => array(
						'values' => array(
							'star',
							'arrow',
							'check',
							'cross',
							'thumbs',
							'link',
							'gear',
							'time',
							'note',
							'plus',
							'guard',
							'event',
							'idea',
							'settings',
							'twitter'
						),
						'default' => 'star',
						'desc' => __( 'List style', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[list style="check"] <ul> <li> List item </li> </ul> [/list]',
				'content' => '<ul><li>' . __( 'List item ', 'shortcodes-ultimate' ) . '</li></ul>',
				'desc' => __( 'Styled unordered list', 'shortcodes-ultimate' )
			),
			# feed
			'feed' => array(
				'name' => 'Feed',
				'type' => 'single',
				'atts' => array(
					'url' => array(
						'values' => array( ),
						'default' => '',
						'desc' => __( 'Feed URL', 'shortcodes-ultimate' )
					),
					'limit' => array(
						'values' => array(
							'1',
							'3',
							'5',
							'7',
							'10'
						),
						'default' => '3',
						'desc' => __( 'Number of item to show', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[feed url="http://rss1.smashingmagazine.com/feed/" limit="5"]',
				'desc' => __( 'Feed grabber', 'shortcodes-ultimate' )
			),
			# menu
			'menu' => array(
				'name' => 'Menu',
				'type' => 'single',
				'atts' => array(
					'name' => array(
						'values' => array( ),
						'default' => '',
						'desc' => __( 'Custom menu name', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[menu name="Main menu"]',
				'desc' => __( 'Custom menu by name', 'shortcodes-ultimate' )
			),
			# subpages
			'subpages' => array(
				'name' => 'Sub pages',
				'type' => 'single',
				'atts' => array(
					'depth' => array(
						'values' => array(
							'1',
							'2',
							'3'
						),
						'default' => '1',
						'desc' => __( 'Depth level', 'shortcodes-ultimate' )
					),
					'p' => array(
						'values' => false,
						'default' => '',
						'desc' => __( 'Parent page ID', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[subpages]<br/>[subpages depth="2" p="122"]',
				'desc' => __( 'Page childrens', 'shortcodes-ultimate' )
			),
			# siblings
			'siblings' => array(
				'name' => 'Siblings',
				'type' => 'single',
				'atts' => array(
					'depth' => array(
						'values' => array(
							'1',
							'2',
							'3'
						),
						'default' => '1',
						'desc' => __( 'Depth level', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[siblings]<br/>[siblings depth="2"]',
				'desc' => __( 'Page siblings', 'shortcodes-ultimate' )
			),
			# column
			'column' => array(
				'name' => 'Column',
				'type' => 'wrap',
				'atts' => array(
					'size' => array(
						'values' => array(
							'1-2',
							'1-3',
							'1-4',
							'1-5',
							'1-6',
							'2-3',
							'2-5',
							'3-4',
							'3-5',
							'4-5',
							'5-6'
						),
						'default' => '1-2',
						'desc' => __( 'Column width', 'shortcodes-ultimate' )
					),
					'last' => array(
						'values' => array(
							'0',
							'1'
						),
						'default' => '0',
						'desc' => __( 'Last column', 'shortcodes-ultimate' )
					),
					'style' => array(
						'values' => array(
							'0',
							'1',
							'2'
						),
						'default' => '0',
						'desc' => __( 'Column style', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[column size="1-2"] Content [/column]<br/>[column size="1-2" last="1"] Content [/column]',
				'content' => __( 'Column content', 'shortcodes-ultimate' ),
				'desc' => __( 'Flexible columns', 'shortcodes-ultimate' )
			),
			# table
			'table' => array(
				'name' => 'Table',
				'type' => 'mixed',
				'atts' => array(
					'style' => array(
						'values' => array(
							'1',
							'2',
							'3'
						),
						'default' => '1',
						'desc' => __( 'Table style', 'shortcodes-ultimate' )
					),
					'file' => array(
						'values' => false,
						'default' => '',
						'desc' => __( 'Create table from CSV', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[table style="1"] <table> … <table> [/table]<br/>[table style="1" file="http://example.com/file.csv"] [/table]',
				'content' => '<table><tr><td></td></tr></table>',
				'desc' => __( 'Styled table from HTML or CSV file', 'shortcodes-ultimate' )
			),
//			# pricing
//			'pricing' => array(
//				'name' => 'Pricing table',
//				'type' => 'wrap',
//				'atts' => array(
//					'style' => array(
//						'values' => array(
//							'1',
//							'2'
//						),
//						'default' => '1',
//						'desc' => __( 'Table style', 'shortcodes-ultimate' )
//					)
//				),
//				'usage' => '[pricing]<br/>[plan name="Plan 1"] Plan description [/plan]<br/>[/pricing]',
//				'content' => '[plan][/plan]',
//				'desc' => __( 'Customizable pricing table', 'shortcodes-ultimate' )
//			),
//			# plan
//			'plan' => array(
//				'name' => 'Pricing plan',
//				'type' => 'wrap',
//				'atts' => array(
//					'name' => array(
//						'values' => false,
//						'default' => '&hellip;',
//						'desc' => __( 'Plan name', 'shortcodes-ultimate' )
//					),
//					'price' => array(
//						'values' => false,
//						'default' => '$100',
//						'desc' => __( 'Plan price', 'shortcodes-ultimate' )
//					),
//					'per' => array(
//						'values' => false,
//						'default' => '$100',
//						'desc' => __( 'Price period', 'shortcodes-ultimate' )
//					),
//					'width' => array(
//						'values' => array(
//							'100',
//							'150',
//							'200',
//							'250'
//						),
//						'default' => '150',
//						'desc' => __( 'Box width', 'shortcodes-ultimate' )
//					),
//					'primary' => array(
//						'values' => array(
//							'0',
//							'1'
//						),
//						'default' => '0',
//						'desc' => __( 'Is primary plan?', 'shortcodes-ultimate' )
//					),
//					'class' => array(
//						'values' => false,
//						'default' => '',
//						'desc' => __( 'Custom box class', 'shortcodes-ultimate' )
//					)
//				),
//				'usage' => '[pricing]<br/>[plan name="Plan 1" price="$100" per="per month" width="150" primary="0"] Plan description [/plan]<br/>[/pricing]',
//				'content' => '<ul><li>List item</li></ul>[button link="#"]Choose[/button]',
//				'desc' => __( 'Customizable pricing table', 'shortcodes-ultimate' )
//			),
			# media
			'media' => array(
				'name' => 'Media',
				'type' => 'single',
				'atts' => array(
					'url' => array(
						'values' => false,
						'default' => '',
						'desc' => __( 'Media URL', 'shortcodes-ultimate' )
					),
					'width' => array(
						'values' => false,
						'default' => '600',
						'desc' => __( 'Width', 'shortcodes-ultimate' )
					),
					'height' => array(
						'values' => false,
						'default' => '400',
						'desc' => __( 'Height', 'shortcodes-ultimate' )
					),
					'jwplayer' => array(
						'values' => array(
							'',
							'autostart=true',
							'controlbar=bottom'
						),
						'default' => '',
						'desc' => __( 'jwPlayer url-encoded params', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[media url="http://www.youtube.com/watch?v=2c2EEacfC1M"]<br/>[media url="http://vimeo.com/15069551"]<br/>[media url="video.mp4"]<br/>[media url="video.flv"]<br/>[media url="audio.mp3"]<br/>[media url="image.jpg"]<br/>[media url="video.flv" jwplayer="controlbar=bottom&autostart=true"]',
				'desc' => __( 'YouTube video, Vimeo video, .mp4/.flv video, .mp3 file or images', 'shortcodes-ultimate' )
			),
			# document
			'document' => array(
				'name' => 'Document',
				'type' => 'single',
				'atts' => array(
					'file' => array(
						'values' => false,
						'default' => '',
						'desc' => __( 'Document URL', 'shortcodes-ultimate' )
					),
					'width' => array(
						'values' => false,
						'default' => '600',
						'desc' => __( 'Width', 'shortcodes-ultimate' )
					),
					'height' => array(
						'values' => false,
						'default' => '400',
						'desc' => __( 'Height', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[document file="file.doc" width="600" height="400"]',
				'desc' => __( '.doc, .xls, .pdf viewer by Google', 'shortcodes-ultimate' )
			),
			# gmap
			'gmap' => array(
				'name' => 'Gmap',
				'type' => 'single',
				'atts' => array(
					'width' => array(
						'values' => false,
						'default' => '600',
						'desc' => __( 'Width', 'shortcodes-ultimate' )
					),
					'height' => array(
						'values' => false,
						'default' => '400',
						'desc' => __( 'Height', 'shortcodes-ultimate' )
					),
					'address' => array(
						'values' => false,
						'default' => '',
						'desc' => __( 'Marker address', 'shortcodes-ultimate' )
					),
				),
				'usage' => '[gmap width="600" height="400" address="Russia, Moscow"]',
				'desc' => __( 'Maps by Google', 'shortcodes-ultimate' )
			),
			# nivo_slider
			'nivo_slider' => array(
				'name' => 'Nivo slider',
				'type' => 'single',
				'atts' => array(
					'source' => array(
						'values' => array(
							'post',
							'post=%post_id%',
							'cat=%cat_id%'
						),
						'default' => 'post',
						'desc' => __( 'Source of images', 'shortcodes-ultimate' )
					),
					'link' => array(
						'values' => array(
							'none',
							'image',
							'permalink',
							'caption',
							'meta'
						),
						'default' => 'image',
						'desc' => __( 'Images links', 'shortcodes-ultimate' )
					),
					'size' => array(
						'values' => array(
							'100x100',
							'150x150',
							'200x200',
							'300x200',
							'500x300'
						),
						'default' => '500x300',
						'desc' => __( 'Slider size', 'shortcodes-ultimate' )
					),
					'limit' => array(
						'values' => array(
							'3',
							'5',
							'10',
							'20'
						),
						'default' => '10',
						'desc' => __( 'Number of slides', 'shortcodes-ultimate' )
					),
					'effect' => array(
						'values' => array(
							'random',
							'boxRandom',
							'fold',
							'fade'
						),
						'default' => 'random',
						'desc' => __( 'Animation effect', 'shortcodes-ultimate' )
					),
					'speed' => array(
						'values' => false,
						'default' => '600',
						'desc' => __( 'Animation speed (1000 = 1 second)', 'shortcodes-ultimate' )
					),
					'delay' => array(
						'values' => false,
						'default' => '3000',
						'desc' => __( 'Animation delay (1000 = 1 second)', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[nivo_slider]<br/>[nivo_slider source="post" link="image" size="500x300" limit="10" effect="boxRandom"]<br/>[nivo_slider source="cat=1" link="permalink" size="500x300" limit="10" effect="boxRandom"]',
				'desc' => __( 'Nivo slider by attached to post images', 'shortcodes-ultimate' )
			),
			# jcarousel
			'jcarousel' => array(
				'name' => 'jCarousel',
				'type' => 'single',
				'atts' => array(
					'source' => array(
						'values' => array(
							'post',
							'post=%post_id%',
							'cat=%cat_id%'
						),
						'default' => 'post',
						'desc' => __( 'Source of images', 'shortcodes-ultimate' )
					),
					'link' => array(
						'values' => array(
							'none',
							'image',
							'permalink',
							'caption',
							'meta'
						),
						'default' => 'image',
						'desc' => __( 'Images links', 'shortcodes-ultimate' )
					),
					'size' => array(
						'values' => array(
							'100x100',
							'150x150',
							'200x200',
							'150x300'
						),
						'default' => '150x150',
						'desc' => __( 'Carousel item size', 'shortcodes-ultimate' )
					),
					'limit' => array(
						'values' => array(
							'3',
							'5',
							'10',
							'20'
						),
						'default' => '10',
						'desc' => __( 'Number of items', 'shortcodes-ultimate' )
					),
					'items' => array(
						'values' => array(
							'3',
							'4',
							'5'
						),
						'default' => '3',
						'desc' => __( 'Number of items in viewport', 'shortcodes-ultimate' )
					),
					'speed' => array(
						'values' => false,
						'default' => '400',
						'desc' => __( 'Animation speed (1000 = 1 second)', 'shortcodes-ultimate' )
					),
					'margin' => array(
						'values' => array(
							'5',
							'10',
							'15'
						),
						'default' => '10',
						'desc' => __( 'Space between items in pixels', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[jcarousel]<br/>[jcarousel source="post" link="image" size="150x150" limit="10" items="3"]<br/>[jcarousel source="cat=1" link="permalink" size="150x150" limit="10" items="3"]',
				'desc' => __( 'jCarousel by attached to post images', 'shortcodes-ultimate' )
			),
			# custom_gallery
			'custom_gallery' => array(
				'name' => 'Custom gallery',
				'type' => 'single',
				'atts' => array(
					'style' => array(
						'values' => array(
							'1'
						),
						'default' => '1',
						'desc' => __( 'Gallery style', 'shortcodes-ultimate' )
					),
					'source' => array(
						'values' => array(
							'post',
							'post=%post_id%',
							'cat=%cat_id%'
						),
						'default' => 'post',
						'desc' => __( 'Source of images', 'shortcodes-ultimate' )
					),
					'link' => array(
						'values' => array(
							'none',
							'image',
							'permalink',
							'caption',
							'meta'
						),
						'default' => 'image',
						'desc' => __( 'Images links', 'shortcodes-ultimate' )
					),
					'description' => array(
						'values' => array(
							'0',
							'1'
						),
						'default' => '0',
						'desc' => __( 'Show image description', 'shortcodes-ultimate' )
					),
					'size' => array(
						'values' => array(
							'100x100',
							'150x150',
							'200x200',
							'150x300'
						),
						'default' => '200x200',
						'desc' => __( 'Gallery item size', 'shortcodes-ultimate' )
					),
					'limit' => array(
						'values' => array(
							'3',
							'5',
							'10',
							'20'
						),
						'default' => '10',
						'desc' => __( 'Number of items', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[custom_gallery]<br/>[custom_gallery source="post" link="image" size="200x200" limit="10"]<br/>[custom_gallery source="cat=1" link="permalink" size="200x200" limit="10"]',
				'desc' => __( 'Custom gallery by attached to post images', 'shortcodes-ultimate' )
			),
			# tweets
			'tweets' => array(
				'name' => 'Tweets',
				'type' => 'single',
				'atts' => array(
					'username' => array(
						'values' => array( ),
						'default' => 'twitter',
						'desc' => __( 'Twitter username', 'shortcodes-ultimate' )
					),
					'limit' => array(
						'values' => array(
							'1',
							'3',
							'5',
							'7',
							'10'
						),
						'default' => '3',
						'desc' => __( 'Number of tweets to show', 'shortcodes-ultimate' )
					),
					'style' => array(
						'values' => array(
							'1',
							'2'
						),
						'default' => '1',
						'desc' => __( 'Tweets style', 'shortcodes-ultimate' )
					),
					'show_time' => array(
						'values' => array(
							'0',
							'1'
						),
						'default' => '1',
						'desc' => __( 'Show relative time', 'shortcodes-ultimate' )
					)
				),
				'usage' => '[tweets username="gn_themes" limit="3" style="1" show_time="1"]',
				'desc' => __( 'Recent tweets', 'shortcodes-ultimate' )
			),
			# basic shortcodes - end
			'basic-shortcodes-close' => array(
				'type' => 'closegroup'
			),
		);

		if ( $shortcode )
			return $shortcodes[$shortcode];
		else
			return $shortcodes;
	}

?>