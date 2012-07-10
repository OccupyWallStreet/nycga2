<?php
//functions to add an ajax rss button


/**
* Load the necessary css and js
*/
function bpmh_auto_rss_add_jscss(){
	if(!is_admin()){
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'tipsy', BP_MYHOME_PLUGIN_URL . '/js/jquery.tipsy.js' );
		wp_enqueue_style('tipsy-css', BP_MYHOME_PLUGIN_URL . '/tipsy.css');
	}
}
add_action('get_header','bpmh_auto_rss_add_jscss');

/**
* add the js scripts and css for rss automation
*/
function bpmh_auto_rss_js_css_inheader(){
	if(!is_admin()){
		global $bp;
		$img_dir = BP_MYHOME_PLUGIN_URL.'/images/';
		if(is_user_logged_in()){
			$bp_my_home_url = $bp->loggedin_user->domain. BP_MYHOME_SLUG .'/';
		}
		?>
		<style>
		#bpmh-rss-button a{
			color:#555;
			font-size:10px;
		}
		#bpmh-rss-button a.rss_ok{
			display:inline-block;
			height:25px;
			line-height:25px;
			padding-left:25px;
			background:url(<?php echo $img_dir;?>rss-green.gif) no-repeat left;
		}
		#bpmh-rss-button img{
			vertical-align:bottom;
		}
		.tipsy-inner a{
			color:orange;
		}
		</style>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery(".rss-tipsy").tipsy({
					gravity: 'n',
					trigger: 'hover',
					html:true, 
					delayOut: 3000
				});
			});
			function regularDisplay(url){
				jQuery(".rss-tipsy").tipsy("hide");
				window.open(url,'rss');
			}
			<?php if(is_user_logged_in()):?>
			function addToMyFeed(alias,url){
				jQuery(".rss-tipsy").tipsy("hide");
				var oldhtml = jQuery("#bpmh-rss-button").html();
				jQuery("#bpmh-rss-button").html('<img src="<?php echo $img_dir;?>ajax-loader.gif"/>');
				var data = {
					action: 'clone_rss_save_new_item',
					item_name:alias,
					item_link:url
				};
				jQuery.post(ajaxurl, data, function(response) {
					if(response=="ok"){
						jQuery("#bpmh-rss-button").html('<a href="<?php echo $bp_my_home_url;?>" class="rss_ok"><?php _e("Feed Added","bp-my-home");?></a>');
					}
					else{
						alert(response);
						jQuery("#bpmh-rss-button").html(oldhtml);
					}
				});
			}
			<?php endif;?>
		</script>
		<?php
	}
}

add_action('wp_head','bpmh_auto_rss_js_css_inheader');

/**
*
*/
function the_bpmh_rss_button($feedalias="", $feedlink="", $widthheight="20"){
	global $wp_query, $post, $query_string, $bp, $the_active_widgets;
	$img_dir = BP_MYHOME_PLUGIN_URL.'/images/';
	if($feedalias=="" && $feedlink==""){
		$permalink_structure = get_option('permalink_structure');
		$feedalias = get_bloginfo('name');
		if(isset($permalink_structure) && $permalink_structure!="" && !is_search()){
			$feedlink = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."feed";
		}
		else{
			$feedlink = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."&feed=rss2";
		}
		if ( is_category() ) {
			$cat = $wp_query->get_queried_object();
			$feedalias .= " | ".$cat->name;
		} elseif ( is_tag() ) {
			$tags = $wp_query->get_queried_object();
			$feedalias .= " | ".$tags->slug;
		} elseif( is_single() ){
			$title = get_the_title($post->ID);
			$feedalias .= " | ".$title;
		} elseif( is_search() ){
			$search_title = explode("=",$query_string);
			$feedalias .= " | ".__('Results for ','bp-my-home').$search_title[1];
		}
	}
	
	//is the rss widget activated by admin ?
	if(is_user_logged_in() && $the_active_widgets!="" && bpmh_in_array('bpmh-rss', $the_active_widgets)==1){
		$bp_my_home_url = $bp->loggedin_user->domain. BP_MYHOME_SLUG .'/';
		$getuser_feeds = get_user_meta($bp->loggedin_user->id, 'bpmh_rss_feeds',true);
		
		if($getuser_feeds!="" && bpmh_in_array(htmlentities($feedlink), $getuser_feeds, 'user-saved')==1){
			?>
			<span id="bpmh-rss-button"><img src="<?php echo $img_dir;?>rss-green.gif" class="rss-tipsy" title='<a href="<?php echo $bp_my_home_url;?>"><?php _e('Feed Added','bp-my-home');?></a> | <a href="javascript:regularDisplay(<?php echo urlencode("\"".$feedlink."\"");?>)"><?php _e('Subscribe','bp-my-home');?></a></a>' width="<?php echo $widthheight;?>px" height="<?php echo $widthheight;?>px"/></span>
			<?php
		}
		else{
			?>
			<span id="bpmh-rss-button"><img src="<?php echo $img_dir;?>rss-orange.gif" class="rss-tipsy" title='<a href="javascript:addToMyFeed(<?php echo urlencode("\"".$feedalias."\"");?>, <?php echo urlencode("\"".$feedlink."\"");?>)"><?php _e('Add to My Feeds widget','bp-my-home');?></a> | <a href="javascript:regularDisplay(<?php echo urlencode("\"".$feedlink."\"");?>)"><?php _e('Subscribe','bp-my-home');?></a></a>' width="<?php echo $widthheight;?>px" height="<?php echo $widthheight;?>px"/></span>
			<?php
		}
	}
	else{
		?>
		<span id="bpmh-rss-button"><img src="<?php echo $img_dir;?>rss-orange.gif" class="rss-tipsy" title='<a href="javascript:regularDisplay(<?php echo urlencode("\"".$feedlink."\"");?>)"><?php _e('Subscribe','bp-my-home');?></a>' width="<?php echo $widthheight;?>px" height="<?php echo $widthheight;?>px"/></span>
		<?php
	}
}

/**
* this is a clone of bpmh_rss_ajax_save_item, just dont want to load the all thing !
*/
function clone_bpmh_rss_ajax_save_item(){
	$user = wp_get_current_user();
	$alias = wp_kses(stripslashes(urldecode($_POST['item_name'])), '');
	$link = wp_kses(stripslashes(urldecode($_POST['item_link'])), '');
	$getuser_feeds = get_user_meta($user->id, 'bpmh_rss_feeds',true);
	if($getuser_feeds!="" && count($getuser_feeds)>0){
		$array_feeds = $getuser_feeds;
	}
	else $array_feeds = array();
	$array_feeds[]=array('alias'=>$alias, 'url'=>$link);
	if(update_user_meta( $user->id, 'bpmh_rss_feeds', $array_feeds )){
		echo "ok";
	}
	else _e('Oops, something went wrong !','bp-my-home');
	die();
}

add_action( 'wp_ajax_clone_rss_save_new_item', 'clone_bpmh_rss_ajax_save_item' );
?>