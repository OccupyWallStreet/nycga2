<?php
//functions to add an ajax link to store post or page in bkmk widget.

/**
* Load the necessary css and js
*/
function bpmh_auto_bkmk_add_js(){
	if((is_single() || (bp_is_blog_page() && is_page())) && !is_front_page() && is_user_logged_in()) {
		wp_enqueue_script('jquery');
	}
}
add_action('get_header','bpmh_auto_bkmk_add_js');

/**
* add the js scripts and css for bkmk automation
*/
function bpmh_auto_bkmks_add_js_css(){
	global $bp;
	$img_dir = BP_MYHOME_PLUGIN_URL.'/images/';
	if((is_single() || (bp_is_blog_page() && is_page())) && !is_front_page() && is_user_logged_in()) {
		$bp_my_home_url = $bp->loggedin_user->domain. BP_MYHOME_SLUG .'/';
			?>
			<style>
			#bpmh-bkmk-auto{
				position:relative;
				width:100%;
				padding-bottom:20px;
				text-align:right;
			}
			#bpmh-bkmk-auto a{
				display:inline-block;
				width:auto;
				padding-left:20px;
			}
			.to_fav{
				background:url(<?php echo $img_dir;?>addfav.png) no-repeat left;
			}
			.fav_ok{
				background:none;
				background:url(<?php echo $img_dir;?>addfav-ok.png) no-repeat left;
			}
			</style>
			<script type="text/javascript">
			function bpmh_add_widget_bkmk(alias, url){
				var oldhtml = jQuery("#bpmh-bkmk-auto").html();
				jQuery("#bpmh-bkmk-auto").html('<img src="<?php echo $img_dir;?>ajax-loader.gif"/>');
				var data = {
					action: 'clone_the_bkmk_save_new_item',
					item_name:alias,
					item_link:url
				};
				jQuery.post(ajaxurl, data, function(response) {
					if(response=="ok"){
						jQuery("#bpmh-bkmk-auto").html('<a href="<?php echo $bp_my_home_url;?>" class="fav_ok"><?php _e("Bookmark Added","bp-my-home");?></a>');
					}
					else{
						alert(response);
						jQuery("#bpmh-bkmk-auto").html(oldhtml);
					}
				});
			}
			</script>
			<?php
	}
}

add_action('wp_head','bpmh_auto_bkmks_add_js_css');

/**
* filter the content to add the ajax link to add bkmk
*/
function bpmh_bkmks_add_in_content($content) {
  	if((is_single() || (bp_is_blog_page() && is_page())) && !is_front_page() && is_user_logged_in()){
    global $post, $bp, $the_active_widgets;
	$bp_my_home_url = $bp->loggedin_user->domain. BP_MYHOME_SLUG .'/';
	$getuser_bkmks = get_user_meta($bp->loggedin_user->id, 'bpmh_bkmks_list',true);
		if(bpmh_in_array('bpmh-bkmks', $the_active_widgets)==1){
			if(bpmh_in_array(get_permalink($post->ID), $getuser_bkmks, 'user-saved')==1){
				$content = '<div id="bpmh-bkmk-auto"><a href="'.$bp_my_home_url.'" class="fav_ok">'.__("Bookmark Added","bp-my-home").'</a></div>'.$content;
				}else{
				$content= '<div id="bpmh-bkmk-auto"><a href="javascript:bpmh_add_widget_bkmk(\''.get_the_title($post->ID).'\',\''.get_permalink($post->ID).'\')" class="to_fav">'.__("Add to My Bookmarks widget","bp-my-home").'</a></div>'.$content;
			}
		}
	}
    return $content;
}

if(function_exists('get_blog_option')){
	//Bookmarks use add_filter to content
	if("yes" != get_blog_option('1','bp-my-home-auto-bkmk-use-tag')){
		add_filter('the_content', 'bpmh_bkmks_add_in_content');
	}
}
else{
	//Bookmarks
	if("yes" != get_option('bp-my-home-auto-bkmk-use-tag')){
		add_filter('the_content', 'bpmh_bkmks_add_in_content');
	}
}

function the_bpmh_bkmks_tag(){
	echo bpmh_bkmks_add_in_content('');
}

/**
* this is a clone of bpmh_bkmk_ajax_save_item, just dont want to load the all thing !
*/
function clone_the_bpmh_bkmk_ajax_save_item(){
	$user = wp_get_current_user();
	$alias = wp_kses(stripslashes($_POST['item_name']), '');
	$link = wp_kses(stripslashes($_POST['item_link']), '');
	$getuser_bkmks = get_user_meta($user->id, 'bpmh_bkmks_list',true);
	if($getuser_bkmks!="" && count($getuser_bkmks)>0){
		$array_bkmks = $getuser_bkmks;
	}
	else $array_bkmks = array();
	$array_bkmks[]=array('alias'=>$alias, 'url'=>$link);
	if(update_user_meta( $user->id, 'bpmh_bkmks_list', $array_bkmks )){
		echo "ok";
	}
	else _e('Oops, something went wrong !','bp-my-home');
	die();
}

add_action( 'wp_ajax_clone_the_bkmk_save_new_item', 'clone_the_bpmh_bkmk_ajax_save_item' );
?>