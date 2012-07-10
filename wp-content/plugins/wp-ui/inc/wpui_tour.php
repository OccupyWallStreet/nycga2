<?php
/**
 *	WP UI Tour. 
 *	
 *	Offer a tour on WP UI features. 
 *	
 *	sda
 *		
 * @since $Id$
 * @package wp-ui
 * @subpackage wpui_tour
 **/

/**
* WP UI Tours
*/
class WPUITour
{
	private $items;
	
	function __construct( $items ) {
		$this->items = $items;
		$wpui_opts = get_option( 'wpUI_options' );
		
		
		if ( isset( $wpui_opts ) && $wpui_opts['tour'] == 'on' ) {
		add_action( 'admin_print_scripts', array( &$this, 'enqueue_pointer' ), 10, 2);
		add_action( 'admin_print_scripts', array( &$this, 'show_item' ), 999);
		}
		// $this->show_item();
	}
	
	function enqueue_pointer() {
		wp_enqueue_script( 'jquery-ui-effects' );
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
		wp_enqueue_script( 'utils' );
		wp_enqueue_script( 'wpui-tour', wpui_url( '/js/wpui_tour.js' ), array( 'wp-pointer' ), WPUI_VER );
		add_action( 'admin_head', array( &$this, 'wpui_pointer_styles' ) );
	}
	
	function show_item() {
		global $pagenow;
		$item = $this->items[ 0 ];
		$tour_items = json_encode( $this->items );
		$this->show_pointer( $tour_items );
	}	
	
	// function show_pointer( $id, $content, $position, $buttons ) {
	function show_pointer( $items ) {
		// $buttons = $buttons || 'Next';
	?>
	<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready( function() {
	var Titems = <?php echo $items ?>;
	w84TMCE = setInterval( function() {
		if ( ed = tinyMCE.activeEditor ) {
			jQuery.wpui.tour.dismiss = function() {
				jQuery.post( ajaxurl, {
					action : 'wpui_setopts',
					nonce : '<?php echo wp_create_nonce( "wpui-setopts-nonce" ); ?>',
					option : {
						'tour' : 'off'
					}					
				}, function( data ) {
					console.info( data ); 
				});
			};
			jQuery.wpui.tour.lastBefore = function() {
				jQuery( '#wpui-editor-dialog' ).dialog( 'close' );
			};
			jQuery.wpui.tour.closeContent = '<p>You can view this tour by enabling <code>View Tour</code> on <a href="<?php echo admin_url("options-general.php?page=wpUI-options") ?>">WP UI options</a> page.</p>'
			jQuery.wpui.tour.init( Titems );
			clearInterval( w84TMCE );
		}
	}, 1000);
	
	});

	//]]>
	</script>	
	<?php		
	}
	
	function wpui_pointer_styles() {
	?>
	<style type="text/css">
	.wp-pointer .wpuih {
		margin: 5px 10px;
	}
	.wp-pointer span.hiliters {
		color : #09C;
		border-bottom : 1px dotted #03C;
		cursor : pointer;
	}
	.highlighted-item {
		border : 1px solid red !important;
		background : yellow;
	}
	.wpbut {
	    background: #F4F2F4;
	    border: 1px solid #AAAAAA;
		-moz-box-shadow    : 1px 1px 0 #888, 1px 1px 0 #FFFFFF inset, -1px -1px 0 #FFFFFF inset;
		-webkit-box-shadow : 1px 1px 0 #888, 1px 1px 0 #FFFFFF inset, -1px -1px 0 #FFFFFF inset;
		-o-box-shadow      : 1px 1px 0 #888, 1px 1px 0 #FFFFFF inset, -1px -1px 0 #FFFFFF inset;
		box-shadow         : 1px 1px 0 #888, 1px 1px 0 #FFFFFF inset, -1px -1px 0 #FFFFFF inset;
	    color: #333333;
	}
	</style>		
	<?php
	}

	
} // END class WPUITour


$wpui_tourz_items = array(
	array(
		'id'		=>	'#content_wpuimce_open',
		'content'	=>	'<h3>Welcome to WP UI</h3><p>Congratulations, you’ve installed one of the most useful plugins available. Click "Next" to learn more on the new features of the plugin. Prepare to be surprised. </p><p><i>Best viewed with window maximized.</i></p>',
		'position'	=>	'left middle',
		'arrow'		=>	array(
			'edge'		=> 'top',
			'align'		=> 'top',
			'offset'	=>	 10
		),
		'callback'	=>	'tinyMCE.activeEditor.controlManager.get("wpuimce").showMenu()'	
		
	),
	array(
		'id'		=>	'#menu_content_content_wpuimce_menu',
		'content'	=>	'<h3>Tabs/Accordions</h3><p>WP UI menu turns implementing widgets super easy!</p><h4 class="wpuih">Adding Tabs/Accordion</h4><p>The <span class="hiliters" rel="tr#mce_3, tr#mce_4" id="menu_tabs_buttons">first and second menu buttons</span> allow you to insert tabs. Use <span class="hiliters" rel="tr#mce_3">Add tab set</span> to insert multiple tab set, that are finally wrapped as tabs with the <span class="hiliters" rel="tr#mce_4">Wrap tab set</span>.</p>',
		'position'	=>	'left',
		'addl'		=>	'jQuery( ".hiliters" ).wpuiHilite();',
		'callback'	=>	'tinyMCE.activeEditor.controlManager.get("wpuimce").showMenu()'	
		
	),
	array(
		'id'		=>	'#menu_content_content_wpuimce_menu',
		'content'	=>	'<h3>Spoilers and dialogs</h3><h4 class="wpuih">Spoilers</h4><p>Spoilers aka. collapsibles are ready to add with the <span rel="tr#mce_5" class="hiliters">Spoilers button</span>. Select some text, click the button, Enter a title -> Insert -> Save. That\'s all!</p>',
		'position'	=>	'left',
		'addl'		=>	'jQuery( ".hiliters" ).wpuiHilite();',
		'callback'	=>	'tinyMCE.activeEditor.controlManager.get("wpuimce").showMenu()'	
		
	),
	array(
		'id'		=>	'#menu_content_content_wpuimce_menu',
		'content'	=>	'<h3>Spoilers and dialogs</h3><h4 class="wpuih">Dialogs</h4><p>Dialogs or inline modal windows can be implemented with the <span rel="tr#mce_6" class="hiliters">Dialogs button</span>. Select some text, click the button, Enter a title -> Insert -> Save. That should take totally 2 seconds. Fun, isn\'t it!</p>',
		'position'	=>	'left',
		'addl'		=>	'jQuery( ".hiliters" ).wpuiHilite();',
		'callback'	=>	'jQuery( "#wpui-editor-dialog").wpuiEditor({ mode : "addtab" }); jQuery( "#wpui-editor-dialog").find("p.wpui-reveal").eq( 1 ).click();'	
		
	),
	array(
		'id'		=>	'#wpui-editor-dialog .wpui-search-posts',
		'content'	=>	'<h3>Posts</h3><p>Now, the unique feature of WP UI - Post widgets. So what exactly does this do?</p><p><code class="wpbut">Add tab set</code>, <code class="wpbut">spoilers</code> and <code class="wpbut">dialog</code> buttons has <span class="hiliters" rel="#wpui-editor-dialog p.wpui-reveal:eq(1)">an option</span> to choose the post</a> you wish to display inside either of them. All that\'s needed is click the post, <span class="hiliters" rel="#wpui-editor-dialog #wpui-tab-name">input a title</span> and Click insert.</p><p>But what if we want to display multiple posts from category or tag? Click <code class="wpbut">Next</code> to find out how easy it is.</p>',
		'position'	=>	'left',
		'addl'		=>	'jQuery( ".hiliters" ).wpuiHilite();',
		'callback'	=>	'jQuery( "#wpui-editor-dialog").dialog("close"); jQuery( "#wpui-editor-dialog").wpuiEditor({ mode : "wraptab" }); jQuery( "#wpui-editor-dialog").find("p.wpui-reveal").eq( 1 ).click(); '	
		
	),
	array(
		'id'		=>	'#wpui-editor-dialog #wpui-search-tax',
		'content'	=>	'<h3>Multiple posts</h3><p>Implement tabs/accordions automatically from selected categories/tags. More, you can choose to display recent, popular or random posts.</p><p>Click on any <span class="hiliters" rel="#wpui-editor-dialog .wpui-search-results ul li">list items</span> on the left to toggle selection. <span class="hiliters" rel="#wpui-editor-dialog #wpui-tax-number">Enter the number</span> of posts, and finally click insert.</p>',
		'position'	=>	'left',
		'addl'		=>	'jQuery( ".hiliters" ).wpuiHilite();',
		'callback'	=>	'jQuery( "#wpui-editor-dialog").wpuiEditor({ mode : "wraptab", selection : "multiple" }); jQuery( "#wpui-editor-dialog").find("p.wpui-reveal").eq( 1 ).click(); '		
	),
	
	
);



$wpui_tourz = new WPUITour( $wpui_tourz_items );


?>