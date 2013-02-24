<?php 
// don't load directly 
if ( !defined('ABSPATH') ) 
	die('-1');
/*$entre = false;
if(strstr($_SERVER['REQUEST_URI'], '/wp-admin/post-new.php?post_type=wpematico'))
	$entre = true;
elseif (strstr($_SERVER['REQUEST_URI'], 'wp-admin/post.php')) {
	if( isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && 'edit'==$_REQUEST['action'])) {
		$id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
		$post = get_post($id);
		if($post->post_type == 'wpematico') $entre = true;
	}
}else return;

if ($entre)  */ 

add_action( 'init', array( 'WPeMatico_Campaign_edit', 'init' ) );

if ( class_exists( 'WPeMatico_Campaign_edit' ) ) return;



include_once("campaign_edit_functions.php");

class WPeMatico_Campaign_edit extends WPeMatico_Campaign_edit_functions {
	
	public function init() {
		new self();
	}
	
	public function __construct( $hook_in = FALSE ) {
		add_action('save_post', array( __CLASS__ , 'save_campaigndata'));
		add_action('wp_ajax_runnowx', array( &$this, 'RunNowX'));
		add_action('wp_ajax_checkfields', array( __CLASS__, 'CheckFields'));
		add_action('wp_ajax_test_feed', array( 'WPeMatico', 'Test_feed'));
		add_action('admin_print_styles-post.php', array( __CLASS__ ,'admin_styles'));
		add_action('admin_print_styles-post-new.php', array( __CLASS__ ,'admin_styles'));
		add_action('admin_print_scripts-post.php', array( __CLASS__ ,'admin_scripts'));
		add_action('admin_print_scripts-post-new.php', array( __CLASS__ ,'admin_scripts'));  
	}

  	function admin_styles(){
		global $post;
		if($post->post_type != 'wpematico') return $post->ID;
		wp_enqueue_style('campaigns-edit',WPeMatico :: $uri .'app/css/campaigns_edit.css');	
//		add_action('admin_head', array( &$this ,'campaigns_admin_head_style'));
	}

	function admin_scripts(){
		global $post;
		if($post->post_type != 'wpematico') return $post->ID;
		wp_deregister_script('autosave');
		add_action('admin_head', array( __CLASS__ ,'campaigns_admin_head'));
	}

	function RunNowX() {
		if(!isset($_POST['campaign_ID'])) die('ERROR: ID no encontrado.'); 
		$campaign_ID=$_POST['campaign_ID'];
		echo substr( WPeMatico :: wpematico_dojob( $campaign_ID ) , 0, -1); // borro el ultimo caracter que es un 0
		return ''; 
	}
	
	function campaigns_admin_head() {
		global $post;
		if($post->post_type != 'wpematico') return $post_id;
		$post->post_password = '';
		$visibility = 'public';
		$visibility_trans = __('Public');
		$description = __('Campaign Description', WPeMatico :: TEXTDOMAIN );
		$description_help = __('Here you can write some observations.',  WPeMatico :: TEXTDOMAIN);
		$runnowbutton = '<div class="right m7 " style="margin-left: 47px;"><div style="background-color: #FFF52F;" id="run_now" class="button-primary">'. __('Run Now', WPeMatico :: TEXTDOMAIN ) . ' &nbsp;<span class="ui-icon GoIco right"></span></div></div>';
		$cfg = get_option(WPeMatico :: OPTION_KEY);
		
		?>
		<script type="text/javascript" language="javascript">
		jQuery(document).ready(function($){
			//try {
			jQuery('#post-visibility-display').text('<?php echo $visibility_trans; ?>');
			jQuery('#hidden-post-visibility').val('<?php echo $visibility; ?>');
			jQuery('#visibility-radio-<?php echo $visibility; ?>').attr('checked', true);
			jQuery('#postexcerpt .hndle span').text('<?php echo $description; ?>');
			jQuery('#postexcerpt .inside .screen-reader-text').text('<?php echo $description; ?>');
			jQuery('#postexcerpt .inside p').text('<?php echo $description_help; ?>');
			jQuery('#delete-action').append('<?php echo $runnowbutton; ?>');

			$('#psearchtext').keyup(function(tecla){
				if(tecla.keyCode==27) {
					$(this).attr('value','');
					$('.feedinput').parent().parent().show();
				}else{
					buscafeed = $(this).val();
					$('.feedinput').each(function (el,item) {
						feed = $(item).attr('value');
						if (feed.toLowerCase().indexOf(buscafeed) >= 0) {
							$(item).parent().parent().show();
						}else{
							$(item).parent().parent().hide();
						}
					});
				}
			});
			
			$('#campaign_imgcache').click(function() {
				if ( true == $('#campaign_imgcache').is(':checked')) {
					$('#nolinkimg').fadeIn();
				} else {
					$('#nolinkimg').fadeOut();
				}
			});
			
			$('.tag').click(function(){
				$('#campaign_template').attr('value',$('#campaign_template').attr('value')+$(this).html());
			});
			
			$('.w2cregex').click(function() {
				var cases = $(this).parent().children('#campaign_wrd2cat_cases');
				if ( true == $(this).is(':checked')) {
					cases.attr('checked','checked');
					cases.attr('disabled','disabled');
				}else{
					cases.removeAttr('checked');
					cases.removeAttr('disabled');
				}
			});
			
			$('#addmorerew').click(function() {
				$('#rew_max').val( parseInt($('#rew_max').val(),10) + 1 );
				newval = $('#rew_max').val();					
				nuevo= $('#nuevorew').clone();
				$('input', nuevo).eq(0).attr('name','campaign_word_option_regex['+ newval +']');
				$('textarea', nuevo).eq(0).attr('name','campaign_word_origin['+ newval +']');
				$('textarea', nuevo).eq(1).attr('name','campaign_word_rewrite['+ newval +']');
				$('textarea', nuevo).eq(2).attr('name','campaign_word_relink['+ newval +']');
				$('input', nuevo).eq(0).removeAttr('checked');
				$('textarea', nuevo).eq(0).text('');
				$('textarea', nuevo).eq(1).text('');
				$('textarea', nuevo).eq(2).text('');
				nuevo.show();
				$('#rewrites_edit').append(nuevo);
			});
			
			$('#addmorew2c').click(function() {
				$('#wrd2cat_max').val( parseInt($('#wrd2cat_max').val(),10) + 1 );
				newval = $('#wrd2cat_max').val();					
				nuevo= $('#nuevow2c').clone();
				$('input', nuevo).eq(0).attr('name','campaign_wrd2cat['+ newval +']');
				$('input', nuevo).eq(1).attr('name','campaign_wrd2cat_regex['+ newval +']');
				$('input', nuevo).eq(2).attr('name','campaign_wrd2cat_cases['+ newval +']');
				$('select', nuevo).eq(0).attr('name','campaign_wrd2cat_category['+ newval +']');
				$('input', nuevo).eq(0).attr('value','');
				$('input', nuevo).eq(1).removeAttr('checked');
				$('input', nuevo).eq(2).attr('value','');
				nuevo.show();
				$('#wrd2cat_edit').append(nuevo);
			});
			
			$('#run_now').click(function() {
				$(this).attr('style','Background:#CCC;');
				$.ajaxSetup({async:false});
				jQuery('#fieldserror').remove();
				msgdev="<img width='12' src='<?php echo get_bloginfo('wpurl'); ?>/wp-admin/images/wpspin_light.gif' class='mt2'> <?php _e('Running Campaign...', WPeMatico :: TEXTDOMAIN ); ?>";
				jQuery("#post").prepend('<div id="fieldserror" class="updated fade he20">'+msgdev+'</div>');
				c_ID = $('#post_ID').val();
				var data = {
					campaign_ID: c_ID ,
					action: "runnowx"
				};
				$.post(ajaxurl, data, function(msgdev) {  //si todo ok devuelve LOG sino 0
					$('#fieldserror').remove();
					if( msgdev.substring(0, 5) == 'ERROR' ){
						$("#post").prepend('<div id="fieldserror" class="error fade">'+msgdev+'</div>');
					}else{
						$("#post").prepend('<div id="fieldserror" class="updated fade">'+msgdev+'</div>');
					}
				});
 				$(this).attr('style','Background:#FFF52F;');
			});
			 
			$('#post').submit( function() {		//checkfields
				$('#wpcontent .ajax-loading').attr('style',' visibility: visible;');
				$.ajaxSetup({async:false});
				error=false;
				msg="Guardando...";
				wrd2cat= $("input[name='campaign_wrd2cat[]']").serialize();
				var wrd2cat_regex  = new Array();
				$("input[name='campaign_wrd2cat_regex[]']").each(function() {
					if ( true == $(this).is(':checked')) {
						wrd2cat_regex.push('1');
					}else{
						wrd2cat_regex.push('0');
					}
				});

				reword = $("textarea[name='campaign_word_origin[]']").serialize();
				var reword_regex  = new Array();
				$("input[name='campaign_word_option_regex[]']").each(function() {
					if ( true == $(this).is(':checked')) {
						reword_regex.push('1');
					}else{
						reword_regex.push('0');
					}
				});

				feeds= $("input[name='campaign_feeds[]']").serialize();
				
				var data = {
					campaign_feeds: feeds,
					campaign_word_origin: reword,
					campaign_word_option_regex: reword_regex,
					campaign_wrd2cat: wrd2cat,
					campaign_wrd2cat_regex: wrd2cat_regex,
					action: "checkfields"
				};
				$.post(ajaxurl, data, function(todok){  //si todo ok devuelve 1 sino el error
					if( todok != 1 ){
						error=true;
						msg=todok;
					}else{
						error=false;  //then submit campaign
					}
				});
				if( error == true ) {
					$('#fieldserror').remove();
					$("#post").prepend('<div id="fieldserror" class="error fade">ERROR: '+msg+'</div>');
					$('#wpcontent .ajax-loading').attr('style',' visibility: hidden;');

					return false;
				}else {
					$('.w2ccases').removeAttr('disabled'); //si todo bien habilito los check para que los tome el php
					return true;
				}
			});
			
			$('#checkfeeds').click(function() {
				$.ajaxSetup({async:false});
				var feederr = 0;
				var feedcnt = 0;
				errmsg ="Feed ERROR";
				$('.feedinput').each(function (el,item) {
					feederr += 1;
					feed = $(item).attr('value');
					if (feed !== "") {
						$('#ruedita').show();
						$(item).attr('style','Background:#CCC;');
						var data = {
							action: "test_feed",
							url: feed, 
							'cookie': encodeURIComponent(document.cookie)
						};
						$.post(ajaxurl, data, function(str){
							if(str==0){
								$(item).attr('style','Background:Red;');
								errmsg += "\n"+feed ;
								feederr = 1;
							}else{
								$(item).attr('style','Background:#75EC77;');
							}
							$('#ruedita').hide();
						});
					}else{
						if(feedcnt>1) alert("<?php _e('Type some new Feed URL/s.', WPeMatico :: TEXTDOMAIN ); ?>");
					}
				}); 
				if(feederr == 1){
					alert(errmsg);
				}else{ }
			});
			
			$('.check1feed').click(function() {
				$.ajaxSetup({async:false});
				var feederr = 0;
				errmsg ="Feed ERROR";
				item = $(this).parents('div').children('input');
				feed = item.val();					
				$(this).removeClass("yellowalert_small");
				$(this).removeClass("redalert_small");
				$(this).removeClass("checkmark_small");
				$(this).parent().children('#ruedita1').removeClass("hide");  
				if (feed !== "") {
					$(item).attr('style','Background:#CCC;');
					var data = {
						action: "test_feed",
						url: feed, 
						'cookie': encodeURIComponent(document.cookie)
					};
					$.post(ajaxurl, data, function(str){
						if(str==0){
							$(item).attr('style','Background:Red;');
							errmsg += "\n"+feed ;
							feederr = 1;
						}else{
							$(item).attr('style','Background:#75EC77;');
							feederr = 2;
						}
					});
				}
				$(this).parent().children('#ruedita1').addClass("hide");  

				switch (feederr){
				case 0: {
					$(this).addClass("yellowalert_small");
					alert("<?php _e('Type some feed URL.', WPeMatico :: TEXTDOMAIN ); ?>");
					break;
					}
				case 1: {
					$(this).addClass("redalert_small");
					alert(errmsg);
					break;
					}
				default:
					$(this).addClass("checkmark_small");
				}
			});
			
			
			$('.feedinput').focus(function() {
				$(this).attr('style','Background:#FFFFFF;');
			});

			//} catch(err)}
		});
		</script>
		<?php
	}
	/********** CHEQUEO CAMPOS ANTES DE GRABAR ****************************************************/
	function CheckFields() {  // check required fields values before save post
		$cfg = get_option(WPeMatico :: OPTION_KEY);
		$err_message = "";
		if(isset($_POST['campaign_wrd2cat'])) {
			$wrd2cat = array();
			parse_str($_POST['campaign_wrd2cat'], $wrd2cat);
			$campaign_wrd2cat = @$wrd2cat['campaign_wrd2cat'];
			for ($id = 0; $id < count($campaign_wrd2cat); $id++) {
				$word = $campaign_wrd2cat[$id];
				$regex = ($_POST['campaign_wrd2cat_regex'][$id]==1) ? true : false ;
				if(!empty($word))  {
					if($regex) 
						if(false === @preg_match($word, '')) {
							$err_message = ($err_message != "") ? $err_message."<br />" : "" ;
							$err_message .= sprintf(__('There\'s an error with the supplied RegEx expression in word: %s', WPeMatico :: TEXTDOMAIN ),'<span class="coderr">'.$word.'</span>');
						}
				}
			}
		}
		
		if(isset($_POST['campaign_word_origin'])) {
			$rewrites = array();
			parse_str($_POST['campaign_word_origin'], $rewrites);
			$campaign_word_origin = @$rewrites['campaign_word_origin'];
			for ($id = 0; $id < count($campaign_word_origin); $id++) {
				$origin = $campaign_word_origin[$id];
				$regex = $_POST['campaign_word_option_regex'][$id]==1 ? true : false ;
				if(!empty($origin))  {
					if($regex) 
						if(false === @preg_match($origin, '')) {
							$err_message = ($err_message != "") ? $err_message."<br />" : "" ;
							$err_message .= sprintf(__('There\'s an error with the supplied RegEx expression in ReWrite: %s', WPeMatico :: TEXTDOMAIN ),'<span class="coderr">'.$origin.'</span>');
						}
				}
			}
		}
		
		if(!isset($cfg['disablecheckfeeds']) || !$cfg['disablecheckfeeds'] ){  // Si no esta desactivado en settings
			// Si no hay ningun feed devuelve mensaje de error
			// Proceso los feeds sacando los que estan en blanco
			if(isset($_POST['campaign_feeds'])) {
				$feeds = array();
				parse_str($_POST['campaign_feeds'], $feeds);
				$all_feeds = $feeds['campaign_feeds'];
				for ($id = 0; $id < count($all_feeds); $id++) {
					$feedname = $all_feeds[$id];
					if(!empty($feedname))  {
						if(!isset($campaign_feeds)) 
							$campaign_feeds = array();					
						$campaign_feeds[]=$feedname ;
					}
				}
			}

			if(empty($campaign_feeds) || !isset($campaign_feeds)) {
				$err_message = ($err_message != "") ? $err_message."<br />" : "" ;
				$err_message .= __('At least one feed URL must be filled.',  WPeMatico :: TEXTDOMAIN );
			} else {  
				foreach($campaign_feeds as $feed) {
					$pos = strpos($feed, ' '); // el feed no puede tener espacios en el medio
					if ($pos === false) {
						$simplepie = WPeMatico :: fetchFeed($feed, true);
						if($simplepie->error()) {
							$err_message = ($err_message != "") ? $err_message."<br />" : "" ;
							$err_message .= sprintf(__('Feed %s could not be parsed. (SimplePie said: %s)',  WPeMatico :: TEXTDOMAIN ),'<strong class="coderr">'. $feed. '</strong>', $simplepie->error());
						}
					}else{
						$err_message = ($err_message != "") ? $err_message."<br />" : "" ;
						$err_message .= sprintf(__('Feed %s could not be parsed because has an space in url.',  WPeMatico :: TEXTDOMAIN ),'<strong class="coderr">'. $feed. '</strong>');
					}
				}
			}
		}
		if($cfg['nonstatic']) {$err_message .= NoNStatic::Checkp($_POST, $err_message);}
		
		if($err_message =="" ) $err_message="1";  //NO ERROR
		die($err_message);  // Return 1 si OK, else -> error string
	}
	
	
	//************************* GRABA CAMPAÑA *******************************************************
	function save_campaigndata( $post_id ) {
		global $post;
		// Stop WP from clearing custom fields on autosave, and also during ajax requests (e.g. quick edit) and bulk edits.
		if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || (defined('DOING_AJAX') && DOING_AJAX) || isset($_REQUEST['bulk_edit']))
			return $post_id;
		if ( !wp_verify_nonce( @$_POST['wpematico_nonce'], 'edit-campaign' ) )
			return $post_id;

		if($post->post_type != 'wpematico') return $post_id;

		$nivelerror = error_reporting(E_ERROR | E_WARNING | E_PARSE);
		$cfg = get_option(WPeMatico :: OPTION_KEY);
		
		$campaign = array();
		$campaign = WPeMatico :: get_campaign ($post_id);
		$campaign['campaign_posttype']=$_POST['campaign_posttype'];
		$campaign['campaign_customposttype']=$_POST['campaign_customposttype'];
		$campaign['activated']= $_POST['activated']==1 ? true : false;
		$campaign['campaign_feeddate']= $_POST['campaign_feeddate']==1 ? true : false;
		$campaign['cron'] = WPeMatico :: cron_string($_POST);
		
		$campaign['cronnextrun']= WPeMatico :: time_cron_next($campaign['cron']);
		// Direccion de e-mail donde enviar los logs
		$campaign['mailaddresslog']=sanitize_email($_POST['mailaddresslog']);
		$campaign['mailerroronly']= $_POST['mailerroronly']==1 ? true : false;
		
		// Process categories 
		$campaign['campaign_autocats']= $_POST['campaign_autocats']==1 ? true : false;
		// Primero proceso las categorias nuevas si las hay y las agrego al final del array
		   # New categories
		if(isset($_POST['campaign_newcat'])) {
		  foreach($_POST['campaign_newcat'] as $k => $on) {       
			$catname = $_POST['campaign_newcatname'][$k];
			if(!empty($catname))  {
			  $_POST['campaign_categories'][] = wp_insert_category(array('cat_name' => $catname));
			}
		  }
		}
		# All: Las elegidas + las nuevas ya agregadas
		if(isset($_POST['campaign_categories'])) {
		  $campaign['campaign_categories']=(array)$_POST['campaign_categories'];
		}

		//if(isset($_POST['campaign_tags'])) {
			$campaign['campaign_tags']	= $_POST['campaign_tags'];
		//}
		
		#Proceso las Words to Category sacando los que estan en blanco
		//campaign_wrd2cat, campaign_wrd2cat_regex, campaign_wrd2cat_category
		if(isset($_POST['campaign_wrd2cat'])) {
			foreach($_POST['campaign_wrd2cat'] as $id => $w2cword) {       
				$word = addslashes($_POST['campaign_wrd2cat'][$id]);
				$regex = ($_POST['campaign_wrd2cat_regex'][$id]==1) ? true : false ;
				$cases = ($_POST['campaign_wrd2cat_cases'][$id]==1) ? true : false ;
				$w2ccateg = $_POST['campaign_wrd2cat_category'][$id];
				if(!empty($word))  {
					if(!isset($campaign_wrd2cat)) 
						$campaign_wrd2cat = Array();
					$campaign_wrd2cat['word'][]=$word ;
					$campaign_wrd2cat['regex'][]= $regex;
					$campaign_wrd2cat['cases'][]= $cases;
					$campaign_wrd2cat['w2ccateg'][]=$w2ccateg ;
				}
			}
		}
		$campaign['campaign_wrd2cat']=(array)$campaign_wrd2cat ;
		

		// Proceso los feeds sacando los que estan en blanco
		if(isset($_POST['campaign_feeds'])) {
			foreach($_POST['campaign_feeds'] as $k => $on) {       
				$feedname = $_POST['campaign_feeds'][$k];
				if(!empty($feedname))  {
					if(!isset($campaign_feeds)) 
						$campaign_feeds = Array();					
					$campaign_feeds[]=$feedname ;
				}
			}
		}
		// Jamas llegaria aca si no hay feeds por el check ajax
		$campaign['campaign_feeds'] = (array)$campaign_feeds ;
		
	// *** Campaign Options
		$campaign['campaign_max']				= (int)$_POST['campaign_max'];
		$campaign['campaign_author']			= $_POST['campaign_author'];
		$campaign['campaign_linktosource']	= $_POST['campaign_linktosource']==1 ? true : false;
		$campaign['campaign_strip_links']	= $_POST['campaign_strip_links']==1 ? true : false;
		$campaign['campaign_commentstatus']= $_POST['campaign_commentstatus'];
		$campaign['campaign_allowpings']	= $_POST['campaign_allowpings']==1 ? true : false;
		$campaign['campaign_woutfilter']	= $_POST['campaign_woutfilter']==1 ? true : false;

	// *** Campaign Images
		$campaign['campaign_imgcache']		= $_POST['campaign_imgcache']==1 ? true : false;
		$campaign['campaign_cancel_imgcache']		= $_POST['campaign_cancel_imgcache']==1 ? true : false;
		if ($cfg['imgcache']) {
			if ($campaign['campaign_cancel_imgcache']) $campaign['campaign_imgcache'] = false;
		}else{
			if ($campaign['campaign_imgcache']) $campaign['campaign_cancel_imgcache'] = false;
		}
		$campaign['campaign_nolinkimg']		= $_POST['campaign_nolinkimg']==1 ? true : false;
		$campaign['campaign_solo1ra']		= $_POST['campaign_solo1ra']==1 ? true : false;

	// *** Campaign Template
		$campaign['campaign_enable_template'] = $_POST['campaign_enable_template']==1 ? true : false;
		if(isset($_POST['campaign_template']))
			$campaign['campaign_template'] = $_POST['campaign_template'];
		else{
			$campaign['campaign_enable_template'] = false;
			$campaign['campaign_template'] = '';
		}

	// *** Campaign Rewrites	
		// Proceso los rewrites sacando los que estan en blanco
		if(isset($_POST['campaign_word_origin'])) {
			foreach($_POST['campaign_word_origin'] as $id => $rewrite) {       
				$origin = addslashes($_POST['campaign_word_origin'][$id]);
				$regex = $_POST['campaign_word_option_regex'][$id]==1 ? true : false ;
				$rewrite = addslashes($_POST['campaign_word_rewrite'][$id]);
				$relink = addslashes($_POST['campaign_word_relink'][$id]);
				if(!empty($origin))  {
					if(!isset($campaign_rewrites)) 
						$campaign_rewrites = Array();
					$campaign_rewrites['origin'][]=$origin ;
					$campaign_rewrites['regex'][]= $regex;
					$campaign_rewrites['rewrite'][]=$rewrite ;
					$campaign_rewrites['relink'][]=$relink ;
				}
			}
		}

		$campaign['campaign_rewrites']=(array)$campaign_rewrites ;

		//***** Call nonstatic
		if( $cfg['nonstatic'] ) { $campaign = NoNStatic :: save_data($campaign, $_POST); }
		 
		// check and correct all fields
		$campaign = self :: check_campaigndata($campaign);
		
		error_reporting($nivelerror);
		
		// Grabo la campaña
		add_post_meta( $post_id, 'campaign_data', $campaign, true )  or
          update_post_meta( $post_id, 'campaign_data', $campaign );

		return $post_id ;
	}
	
}