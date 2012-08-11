<?php
require_once('dcwp_plugin_admin.php');

class dc_jqslicksocial_buttons {
	
	/** constructor */
    function dc_jqslicksocial_buttons() {

		$show = '0';
		if($this->dcssb_check_category() == '0'){
			if(is_single()){
				$show = $this->get_dcssb_default('show_post') == true ? '1' : '0';
			}
			if(is_page()){
				$show = $this->get_dcssb_default('show_page') == true ? '1' : '0';
			}
			if(is_front_page()){
				$show = $this->get_dcssb_default('show_home') == true ? '1' : '0';
			}
			if(is_home()){
				$show = $this->get_dcssb_default('show_blog') == true ? '1' : '0';
			}
			if(is_category()){
				$show = $this->get_dcssb_default('show_category') == true ? '1' : '0';
			}
			if(is_archive()){
				$show = $this->get_dcssb_default('show_archive') == true ? '1' : '0';
			}
		}
		if($show == '1'){
			echo $this->slick_buttons();
			echo $this->dcssb_initialisation();
		}
	}
	
	/** Check current category */
	function dcssb_check_category(){
	
		global $wp_query;
		$check = '0';
		$catId = '';
		$categories = dc_jqslicksocial_buttons::get_dcssb_default('exclude_category');
		
		if($categories != ''){
			if(is_category()){
				$catId = get_query_var('cat');
			} elseif (is_single()){
				$category = get_the_category();
				$catId = $category[0]->cat_ID;
			}
		}
		$check = strlen(strstr($categories,','.$catId.',')) > 0 ? 1 : 0 ;
		return $check;
	}
	
	/** Get current category */
	function dcssb_get_category(){
	
		global $wp_query;
		$catId = '';
		if(is_category()){
			$catId = get_query_var('cat');
		} elseif (is_single()){
			$category = get_the_category();
			$catId = $category[0]->cat_ID;
		}
		return $catId;
	}
	
	/** Creates the buttons */
	function slick_buttons(){
	
		if(is_front_page()){
			$link = get_bloginfo('url');
			
		} else if(is_category() || is_archive() || is_home()){
			$domain = 'http://'.$_SERVER['SERVER_NAME'];
			$link = $domain.$_SERVER['REQUEST_URI'];
			
		} else {
			$link = get_permalink($_SESSION['dcssb_page_id']);
		}
		
		$direction = $this->get_dcssb_default('dcssb_direction');
		$order = $this->get_dcssb_default('dcssb_order');
		$functions = explode(',', $order);
		
		$dcssb = '<div id="dc-dcssb">
		<ul id="nav-dcssb" class="'.$direction.'">';
		
		foreach($functions as $function) {
		
			if($function != '' && $function != 'buzz'){
				$f_name = 'dcssb_inc_'.$function;
				$dcssb .= $this->$f_name($link);
			}
		}
		
		$dcssb .= '</ul>
		<div class="clear"></div>
		<div class="dc-corner"><span></span></div>
		</div>';
		
		return $dcssb;
	}
	
	/* Facebook */
	function dcssb_inc_facebook($link){
		
		$elink = urlencode($link);
		$button = '';
		$size = $this->get_dcssb_default('size_facebook');
		$classSize = $size == 'standard' || $size == 'button_count' ? 'size-small' : 'size-box' ;
		$appId = $this->get_dcssb_default('app_facebook');
		
		if($this->get_dcssb_default('incFacebook')){
		
			$method = $this->get_dcssb_default('method_facebook');
			if($method == 'xfbml'){
			$button .= '<div id="fb-root"></div>
			<script>
	window.fbAsyncInit = function() {
	FB.init({appId: "'.$appId.'", status: true, cookie: true, xfbml: true});};
	(function() {
		var e = document.createElement("script");
		e.type = "text/javascript";
		e.src = document.location.protocol + "//connect.facebook.net/en_US/all.js";
		e.async = true;
	document.getElementById("fb-root").appendChild(e);
	}());
</script>
';
			$button .= '<li id="dcssb-facebook" class="'.$classSize.'"><fb:like href="'.$elink.'" send="false" layout="'.$size.'" show_faces="false" font=""></fb:like></li>
			';
		} else {
			if($classSize == 'size-small'){
				$button .= '<li id="dcssb-facebook" class="'.$classSize.'"><iframe src="http://www.facebook.com/plugins/like.php?app_id='.$appId.'&amp;href='.$elink.'&amp;send=false&amp;layout='.$size.'&amp;width=100&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=30" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:30px;" allowTransparency="true"></iframe></li>';
			} else {
				$button .= '<li id="dcssb-facebook" class="'.$classSize.'"><iframe src="http://www.facebook.com/plugins/like.php?app_id='.$appId.'&amp;href='.$elink.'&amp;send=false&amp;layout='.$size.'&amp;width=50&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=62" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:50px; height:62px;" allowTransparency="true"></iframe></li>
				';
			}
		}
		}
		
		return $button;
	}
	
	/* Google +1 */
	function dcssb_inc_plusone($link){
	
		$button = '';
		$size = $this->get_dcssb_default('size_plusone');
		$parts = explode('_', $size);
		$size = $parts[0];
		$count = $parts[1] ? ' count="true"': ' count="false"';
		$classSize = $size == 'standard' || $size == 'small' || $size == 'medium' ? 'size-small' : 'size-box' ;
		
		if($this->get_dcssb_default('incPlusone')){
		
			$button .= '<li id="dcssb-plusone" class="'.$classSize.'"><g:plusone size="'.$size.'" href="'.$link.'"'.$count.'></g:plusone></li>
			';
			$button .= '<script type="text/javascript">
				(function() {
					var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
					po.src = "https://apis.google.com/js/plusone.js";
					var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
				})();
				</script>
				';
		}
		
		return $button;
	}
	
	/* Twitter */
	function dcssb_inc_twitter($link){
	
		$pageId = $_SESSION['dcssb_page_id'];
		$title = is_front_page() ? get_bloginfo('name') : substr(str_replace(array(">","<"),"",get_the_title($pageId)), 0, 120);
		$button = '';
		$twitterId = '';
		$size = $this->get_dcssb_default('size_twitter');
		$classSize = $size == 'horizontal' || $size == 'none' ? 'size-small' : 'size-box' ;
		$short_link = $this->dcssb_url_shortener($link) ? $this->dcssb_url_shortener($link) : $link ;
		
		if($this->get_dcssb_default('incTwitter')){
		
			$twitterId = $this->get_dcssb_default('user_twitter');
			$button .= '<li id="dcssb-twitter" class="'.$classSize.'"><a href="http://twitter.com/share" data-url="'.$short_link.'" data-counturl="'.$link.'" data-text="'.$title.'" class="twitter-share-button" data-count="'.$size.'" data-via="'.$twitterId.'"></a></li>
			';
			$button .= '<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
			';
		}
		return $button;
	}
	
	/* LinkedIn */
	function dcssb_inc_linkedin($link){
	
		$button = '';
		$size = $this->get_dcssb_default('size_linkedin');
		$classSize = $size == 'right' || $size == 'none' ? 'size-small' : 'size-box' ;
		
		if($this->get_dcssb_default('incLinkedin')){
		
			$button .= '<script type="text/javascript" src="http://platform.linkedin.com/in.js"></script>
			';
			$button .= '<li id="dcssb-linkedin" class="'.$classSize.'"><script type="in/share" data-url="'.$link.'" data-counter="'.$size.'"></script></li>
			';
		}
		return $button;
	}
	
	/* Stumbleupon */
	function dcssb_inc_stumble($link){
	
		$button = '';
		$elink = urlencode($link);
		$size = $this->get_dcssb_default('size_stumble');
		$classSize = $size == '1' || $size == '2' || $size == '3' || $size == '4' ? 'size-small' : 'size-box' ;
		$dim = 'width:50px; height: 60px;';
		
		switch($size)
		{
			case '1':
			$dim = 'width:80px; height: 30px;';
			break;
			case '2':
			$dim = 'width:70px; height: 30px;';
			break;
			case '3':
			$dim = 'width:50px; height: 30px;';
			break;
			case '4':
			$dim = 'width:50px; height: 30px;';
			break;
		}
		
		if($this->get_dcssb_default('incStumble')){
			$button = '<li id="dcssb-stumble" class="'.$classSize.'"><iframe src="http://www.stumbleupon.com/badge/embed/'.$size.'/?url='.$elink.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; '.$dim.'" allowTransparency="true"></iframe></li>';
		}
		return $button;
	}
	
	/* Digg */
	function dcssb_inc_digg($link){
	
		$pageId = $_SESSION['dcssb_page_id'];
		$elink = urlencode($link);
		$title = is_front_page() || is_home() ? get_bloginfo('name') : substr(str_replace(array(">","<"),"",get_the_title($pageId)), 0, 120);
		$post_by_id = get_post($pageId, ARRAY_A);
		@$description = is_front_page() || is_home() ? get_bloginfo('description') : strip_tags(substr($post_by_id['post_content'], 0, 350));
		$button = '';
		$size = $this->get_dcssb_default('size_digg');
		$classSize = $size == 'DiggCompact' || $size == 'DiggIcon' ? 'size-small' : 'size-box' ;
		
		if($this->get_dcssb_default('incDigg')){
		
			$button = '<script type="text/javascript">
(function() {
var s = document.createElement("SCRIPT"), s1 = document.getElementsByTagName("SCRIPT")[0];
s.type = "text/javascript";
s.async = true;
s.src = "http://widgets.digg.com/buttons.js";
s1.parentNode.insertBefore(s, s1);
})();
</script>
';
			$button .= '<li id="dcssb-digg" class="'.$classSize.'"><a href="http://digg.com/submit?url='.$elink.'&amp;title='.$title.'" class="DiggThisButton '.$size.'"></a>
			';
			$button .= '<span style="display: none;">'.$description.'</span></li>
			';
		}
		return $button;
	}
	
	/* Delicious */
	function dcssb_inc_delicious($link){
	
		$pageId = $_SESSION['dcssb_page_id'];
		$elink = urlencode($link);
		$title = is_front_page() || is_home() ? get_bloginfo('name') : substr(str_replace(array(">","<"),"",get_the_title($pageId)), 0, 120);
		$button = '';
		$size = $this->get_dcssb_default('size_delicious');
		$classSize = $size == 'wide' ? 'size-small' : 'size-box' ;

		if($this->get_dcssb_default('incDelicious')){

			$button = '<script type="text/javascript" src="http://delicious-button.googlecode.com/files/jquery.delicious-button-1.1.min.js"></script>
			';
			$button .= '<li id="dcssb-delicious" class="'.$classSize.'"><a class="delicious-button" href="http://delicious.com/save">
 <!-- {
 url:"'.$link.'"
 ,title:"'.$title.'"
 ,button:"'.$size.'"
 } -->
 Delicious
</a></li>
			';
			
		}
		return $button;
	}
	
	/* Reddit */
	function dcssb_inc_reddit($link){
	
		$pageId = $_SESSION['dcssb_page_id'];
		$title = is_front_page() || is_home() ? get_bloginfo('name') : substr(str_replace(array(">","<"),"",get_the_title($pageId)), 0, 120);
		$size = $this->get_dcssb_default('size_reddit');
		$classSize = $size == 'horizontal' || $size == 'none' ? 'size-small' : 'size-box' ;
		
		switch($size){
			case 'horizontal':
			$src = "http://www.reddit.com/static/button/button1.js";
			break;
			case 'none':
			$src = "http://www.reddit.com/buttonlite.js?i=2";
			break;
			default:
			$src = "http://www.reddit.com/static/button/button2.js";
			break;
		}
		
		if($this->get_dcssb_default('incReddit')){
			$button = '<li id="dcssb-reddit" class="'.$classSize.'">';
			$button .= '<script type="text/javascript">
							  reddit_url = "'.$link.'";
							  reddit_title = "'.$title.'";
							  reddit_newwindow="1"
							  </script>
							  <script type="text/javascript" src="'.$src.'"></script>';
			$button .= '</li>';
		}
		return $button;
	}
	
	/* Pin It */
	function dcssb_inc_pinit($link){
	
		$pageId = $_SESSION['dcssb_page_id'];
		$elink = urlencode($link);
		$title = is_front_page() || is_home() ? get_bloginfo('name') : substr(str_replace(array(">","<"),"",get_the_title($pageId)), 0, 120);
		$post_by_id = get_post($pageId, ARRAY_A);
		@$description = is_front_page() || is_home() ? get_bloginfo('description') : urlencode(strip_tags(substr($post_by_id['post_content'], 0, 350)));
		$button = '';
		$size = $this->get_dcssb_default('size_pinit');
		$classSize = $size == 'none' || $size == 'horizontal' ? 'size-small '.$size : 'size-box '.$size ;
		$method = $this->get_dcssb_default('method_pinit');
		
		if($this->get_dcssb_default('incPinit')){
		
			$button = '<li id="dcssb-pinit" class="'.$classSize.'">';
		
			if($method == 'featured')
			{
				if(function_exists('get_post_thumbnail_id')){
					$imageId = get_post_thumbnail_id($pageId);
					$image_url = wp_get_attachment_image_src($imageId,'large');
					$image_url = $image_url[0];
				} else {
					$image_url = '';
				}
				$image_default = ($image_url == '' ? dc_jqslicksocial_buttons::get_dcssb_default('image_pinit') : $image_url);
				
				$button .= '<a href="http://pinterest.com/pin/create/button/?url='.$elink.'&amp;media='.urlencode($image_default).'&amp;description='.$description.'" class="pin-it-button" count-layout="'.$size.'">Pin It</a>';
				$button .= '<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script></li>';
		
			} else {
			
				$count = dc_jqslicksocial_stats::dcssb_pinit_count($link);
				$button .= '<div class="pinit-counter-count">'.$count.'</div><a href="#" class="pinItButton" title="Pin It on Pinterest">Pin it</a></li>';
				$button .= '<script type="text/javascript">function exec_pinmarklet(){
    var e=document.createElement("script");
    e.setAttribute("type","text/javascript");
    e.setAttribute("charset","UTF-8");
    e.setAttribute("src","http://assets.pinterest.com/js/pinmarklet.js?r=" + Math.random()*99999999);
    document.body.appendChild(e);
}</script>';
			}
		}
		
	return $button;
	}
	
	/* Buffer */
	function dcssb_inc_buffer($link){
	
		$pageId = $_SESSION['dcssb_page_id'];
		$title = is_front_page() ? get_bloginfo('name') : substr(str_replace(array(">","<"),"",get_the_title($pageId)), 0, 120);
		$button = '';
		$twitterId = '';
		$size = $this->get_dcssb_default('size_buffer');
		$classSize = $size == 'horizontal' || $size == 'none' ? 'size-small' : 'size-box' ;
		
		if($this->get_dcssb_default('incBuffer')){
			$twitterId = $this->get_dcssb_default('user_twitter');
			$button .= '<li id="dcssb-buffer" class="'.$classSize.'"><a href="http://bufferapp.com/add" data-url="'.$link.'" data-text="'.$title.'" class="buffer-add-button" data-count="'.$size.'" data-via="'.$twitterId.'">Buffer</a></li>
			';
			$button .= '<script type="text/javascript" src="http://static.bufferapp.com/js/button.js"></script>
			';
		}
		return $button;
	}
	
	/** Adds ID based slick skin to the header. */
	function dcssb_styles(){
		
		if(!is_admin()){
			$options = get_option('dcssb_options');
			$skin = $options['skin'];
			if($skin == true){
				echo "\n\t<link rel=\"stylesheet\" href=\"".dc_jqslicksocial::get_plugin_directory()."/css/dcssb.css\" type=\"text/css\" media=\"screen\"  />";
			}
		}
	}

	/** Adds jQuery initialisation code */
	function dcssb_initialisation(){
		
		if(!is_admin()){
		
			$options = get_option('dcssb_options');
			$social_id = 'dc-dcssb';
			
				$method = $options['method'];
				if($method == ''){$method = 'stick';}
				
				$position = $options['position'];
				switch($position)
				{
					case 'top-left':
					$location = 'top';
					$align = 'left';
					break;
					case 'top-right':
					$location = 'top';
					$align = 'right';
					break;
					case 'bottom-left':
					$location = 'bottom';
					$align = 'left';
					break;
					case 'bottom-right':
					$location = 'bottom';
					$align = 'right';
					break;
					case 'left':
					if($method == 'float'){
						$location = 'top';
						$align = 'left';
					} else {
						$location = 'left';
						$align = 'top';
					}
					break;
					case 'right':
					if($method == 'float'){
						$location = 'top';
						$align = 'right';
					} else {
						$location = 'right';
						$align = 'top';
					}
					break;
				}
				
				$width = $options['width'];
				if($width == ''){$width = '200';}
				
				$speedMenu = $options['speedMenu'];
				if($speedMenu == ''){$speedMenu = '600';}
				
				$speedFloat = $options['speedFloat'];
				if($speedFloat == ''){$speedFloat = '1500';}
				
				$disableFloat = $options['disableFloat'] == 'true' ? 'disableFloat: true,' : '' ;
				
				$center = $options['center'];
				if($center == ''){$center = 'false';}
				
				$centerpx = $options['centerpx'];
				if($centerpx == ''){$centerpx = '0';}
				
				$offsetL = $options['offsetL'];
				if($offsetL == ''){$offsetL = '0';}
				
				$offsetA = $options['offsetA'];
				if($offsetA == ''){$offset = '0';}
				
				$autoClose = $options['autoClose'];
				if($autoClose == ''){$autoClose = 'false';}
				
				$loadOpen = $options['loadOpen'];
				if($loadOpen == ''){$loadOpen = 'false';}
				
				$direction = $options['direction'];
				if($direction == ''){$direction = 'vertical';}
				$classWrapper = 'dc-social-slick '.$direction;
				
				$width = $this->get_dcssb_default('size_twitter') == 'horizontal' ? '130' : '98';
				
				$tabImage = $options['tabImage'];
				if($tabImage == ''){
					if($method == 'stick'){
						if($tabImage == ''){
							$tabImage = '<img src="'.dc_jqslicksocial::get_plugin_directory().'/css/images/tab_'.$location.'_'.$direction.'.png" alt="Share" />';
						}
					} else {
							if($width == '130'){
								$tabImage = '<img src="'.dc_jqslicksocial::get_plugin_directory().'/css/images/tab_130.png" alt="Share" />';
							} else {
								$tabImage = '<img src="'.dc_jqslicksocial::get_plugin_directory().'/css/images/tab_'.$location.'_floating.png" alt="Share" />';
							}
					}
				} else {
					$tabImage = '<img src="'.$tabImage.'" alt="" />';
				}
				$idWrapper = $method == 'stick' ? 'dcssb-slick' : 'dcssb-float';
				$classOpen = 'dcssb-open';
				$classClose = 'dcssb-close';
				$classToggle = 'dcssb-link';
				
			?>
			<script type="text/javascript">_ga.trackFacebook();</script>
			<script type="text/javascript">
				jQuery(window).load(function() {

				<?php if($method == 'stick'){ ?>
				
					jQuery('#dc-dcssb').dcSocialSlick({
						idWrapper : '<?php echo $idWrapper; ?>',
						location: '<?php echo $location; ?>',
						align: '<?php echo $align; ?>',
						offset: '<?php echo $offsetL; ?>px',
						speed: <?php echo $speedMenu; ?>,
						tabText: '<?php echo $tabImage; ?>',
						autoClose: <?php echo $autoClose; ?>,
						loadOpen: <?php echo $loadOpen; ?>,
						classWrapper: '<?php echo $classWrapper; ?>',
						classOpen: '<?php echo $classOpen; ?>',
						classClose: '<?php echo $classClose; ?>',
						classToggle: '<?php echo $classToggle; ?>'
						
					});
					
				<?php } else { ?>
				
					jQuery('#dc-dcssb').dcSocialFloater({
						idWrapper : '<?php echo $idWrapper; ?>',
						width: '<?php echo $width; ?>',
						location: '<?php echo $location; ?>',
						align: '<?php echo $align; ?>',
						offsetLocation: <?php echo $offsetL; ?>,
						offsetAlign: <?php echo $offsetA; ?>,
						center: <?php echo $center; ?>,
						centerPx: <?php echo $centerpx; ?>,
						speedContent: <?php echo $speedMenu; ?>,
						speedFloat: <?php echo $speedFloat; ?>,
						<?php echo $disableFloat; ?>
						tabText: '<?php echo $tabImage; ?>',
						autoClose: <?php echo $autoClose; ?>,
						loadOpen: <?php echo $loadOpen; ?>,
						tabClose: true,
						classOpen: '<?php echo $classOpen; ?>',
						classClose: '<?php echo $classClose; ?>',
						classToggle: '<?php echo $classToggle; ?>'
					});
				<?php } ?>
				});
			</script>
		
			<?php
		}
	}
	
	/* Facebook opengraph tags */
	function dcssb_opengraph(){
	
		global $post;
		$pageId = get_the_ID();
		$_SESSION['dcssb_page_id'] = $pageId;
		$name = get_bloginfo('name');
		$post_title = get_the_title($post->post_parent);
		$link = get_permalink(get_the_ID());
		$post_by_id = get_post(get_the_ID(), ARRAY_A);
		
		$domain = 'http://'.$_SERVER['SERVER_NAME'];
		$rurl = $domain.$_SERVER['REQUEST_URI'];
		
        if(function_exists('get_post_thumbnail_id')){
			$imageId = get_post_thumbnail_id();
			$image_url = wp_get_attachment_image_src($imageId,'large');
			$image_url = $image_url[0];
        } else {
            $image_url = '';
        }
	
		$image_default = ($image_url == '' ? dc_jqslicksocial_buttons::get_dcssb_default('image_facebook') : $image_url);
		
		$meta = '
	<!--Facebook OpenGraph Slick Social Share Buttons -->';
		$meta .= '
	<meta property="og:site_name" content="'.$name.'"/>';
	
		if(is_front_page()){
			
			$title ='
		<meta property="og:title" content="'.get_bloginfo('name').'"/>';
			$url = 
		'
		<meta property="og:url" content="'.site_url().'"/>
		';
		} else if(is_home()){
			
			$title ='
		<meta property="og:title" content="'.get_bloginfo('name').'"/>';
			$url = 
		'
		<meta property="og:url" content="'.$rurl.'"/>
		';
		} else {
		$title = '
		<meta property="og:title" content="'.$post_title.'"/>';
		$url = 
		'
		<meta property="og:url" content="'.$link.'"/>
		';
		$title .= '
			<meta property="og:description" content="'.substr(@strip_tags(str_replace(array("[","]",'"'),"",$post_by_id['post_content'])), 0, 200).'"/>
		';
		}
		$image = 
		'
		<meta property="og:image" content="'.$image_default.'"/>
		';
		$meta_admin = '<meta property="fb:admins" content="'.dc_jqslicksocial_buttons::get_dcssb_default('admin_facebook').'" />';
		$meta_admin.= 
		'
		<meta property="fb:app_id" content="'.dc_jqslicksocial_buttons::get_dcssb_default('app_facebook').'" />
		';
		if(is_front_page() || is_home()){
			$meta_admin .= '<meta property="og:type" content="blog" />';
		} else {
			$meta_admin .= '<meta property="og:type" content="article" />';
		}
		
		$meta_admin .= '<!--End Facebook OpenGraph Settings -->
		';
		
		$opengraph = $meta . $title . $url . $image . $meta_admin;
		
		echo $opengraph;
	}
	
	function dcssb_url_shortener($url){
	
		$shortener = $this->get_dcssb_default('shortener');
		$api = $this->get_dcssb_default('shortener_api');
		$login = $this->get_dcssb_default('shortener_login');
		$short_url = null;
		
		if (!get_post_meta($_SESSION['dcssb_page_id'], 'dcssb_short_url', true) ){
		
			if ((function_exists('curl_init') || function_exists('file_get_contents')) && function_exists('unserialize')) {

					switch ($shortener) {
						case 'bitly':
							$short_url = $this->dcssb_shortener_bitly($url, $api, $login);
						break;
						case 'tinyurl':
							$short_url = $this->dcssb_shortener_tinyurl($url);
						break;
						case 'digg':
							$short_url = $this->dcssb_shortener_digg($url);
						break;
						case 'supr':
							$short_url = $this->dcssb_shortener_supr($url, $api, $login);
						break;
					}
					if ($short_url) {
						add_post_meta($_SESSION['dcssb_page_id'], 'dcssb_short_url', $short_url);
					}
			}
		} else {
			$short_url = get_post_meta($_SESSION['dcssb_page_id'], 'dcssb_short_url', true);
		}
		return $short_url;
	}
	
	// URL shortener bit.ly
	function dcssb_shortener_bitly($url, $api, $login='') {
		if ($api && function_exists('json_decode')) {
			$bitly_url = 'http://api.bit.ly/shorten';
			$bitly_version = '2.0.1';
			$bitly_vars = '?version=' . $bitly_version . '&longUrl=' . urlencode($url) . '&login=' . $login . '&apiKey=' .$api;
							
			$response =  $this->dcssb_urlopen($bitly_url . $bitly_vars);
			if ($response) {
				$data = json_decode($response, true);
				if (isset($data['results'])) {
					$keys = array_keys($data['results']);
					if (isset($data['results'][$keys[0]]['shortCNAMEUrl'])) {
						return $data['results'][$keys[0]]['shortCNAMEUrl'];
					} elseif (isset($data['results'][$keys[0]]['shortUrl'])) {
						return $data['results'][$keys[0]]['shortUrl'];
					}
				}
			}
		}
		return false;
	}

	// URL shortener digg
	function dcssb_shortener_digg($url) {
		if (function_exists('curl_init')) {
			
			$digg_url = 'http://services.digg.com/url/short/create';
			$digg_vars = '?type=php&url=' . urlencode($url) . '&appkey=http%3A%2F%2Ftools.awe.sm%2Ftweet-button%2Fwordpress';
			$req_url = $digg_url . $digg_vars;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $req_url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_USERAGENT, 'slick-social-share-buttons');
			$response = curl_exec($ch);
			curl_close($ch);
			
			if ($response) {
				$data = unserialize($response);
				if (isset($data->shorturls[0]->short_url)) {
					return $data->shorturls[0]->short_url;
				}
			}
		}
		return false;
	}

	// URL shortener tinyurl
	function dcssb_shortener_tinyurl($url) {
		$tinyurl_url = 'http://tinyurl.com/api-create.php';
		$tinyurl_vars = '?url=' . urlencode($url);
		$response = $this->dcssb_urlopen($tinyurl_url . $tinyurl_vars);
		if ($response) {
			return $response;
		}
		return false;
	}

	// URL shortener su.pr
	function dcssb_shortener_supr($url, $api='', $login='') {
		$su_url = 'http://su.pr/api';
		$su_vars = '?url=' . urlencode($url) . '&login=' . $login . '&apiKey=' .$api;
		$req_url = $su_url . $su_vars;
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $req_url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_HTTPGET, 1); 
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
		$su_short_url = $buffer;
		// uncomment if hosting off own domain
		//$su_short_url = str_replace('su.pr/', '', $buffer);

		return $su_short_url;
}

	function dcssb_urlopen($url) {
		if (function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$result = curl_exec($ch);
			curl_close($ch);
			return $result;
		} else {
			return file_get_contents($url);
		}
	}

	function get_dcssb_default($option){

		$options = get_option('dcssb_options');
		$default = $options[$option];
		return $default;
	}
} // class dc_jqslicksocial_buttons