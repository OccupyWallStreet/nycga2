<?php
// don't load directly
if ( !defined('ABSPATH') )
	die('-1');

if ( class_exists( 'wpematico_campaign_fetch' ) ) return;
include_once("campaign_fetch_functions.php");

class wpematico_campaign_fetch extends wpematico_campaign_fetch_functions {
	public $cfg			   = array();
	public $campaign_id	   = 0;  // $post_id of campaign
	public $campaign	   = array();
	private $feeds		   = array();
	private $fetched_posts = 0;
	private $lasthash	   = array();
	private $currenthash   = array();
	public $current_item   = array();

	public function __construct($campaign_id) {
		global $wpdb,$campaign_log_message, $jobwarnings, $joberrors;
		$jobwarnings=0;
		$joberrors=0;
		@ini_get('safe_mode','Off'); //disable safe mode
		@ini_set('ignore_user_abort','Off'); //Set PHP ini setting
		ignore_user_abort(true);			//user can't abort script (close windows or so.)
		$this->campaign_id=$campaign_id;			   //set campaign id
		$this->campaign = WPeMatico :: get_campaign($this->campaign_id);
		
		//$this->fetched_posts = $this->campaign['postscount'];
		$this->cfg = get_option(WPeMatico :: OPTION_KEY);

		//set function for PHP user defined error handling
		if (defined(WP_DEBUG) and WP_DEBUG)
			set_error_handler('wpematico_joberrorhandler',E_ALL | E_STRICT);
		else
			set_error_handler('wpematico_joberrorhandler',E_ALL & ~E_NOTICE);
		
		//Set job start settings
		$this->campaign['starttime']		= current_time('timestamp'); //set start time for job
		$this->campaign['cronnextrun']		= WPeMatico :: time_cron_next($this->campaign['cron']); //set next run
		$this->campaign['lastpostscount'] 	= 0; // Lo pone en 0 y lo asigna al final

		WPeMatico :: update_campaign($this->campaign_id, $this->campaign); //Save start time data

		//check max script execution tme
		if (ini_get('safe_mode') or strtolower(ini_get('safe_mode'))=='on' or ini_get('safe_mode')=='1')
			trigger_error(sprintf(__('PHP Safe Mode is on!!! Max exec time is %1$d sec.', WPeMatico :: TEXTDOMAIN ),ini_get('max_execution_time')),E_USER_WARNING);
		// check function for memorylimit
		if (!function_exists('memory_get_usage')) {
			ini_set('memory_limit', apply_filters( 'admin_memory_limit', '256M' )); //Wordpress default
			trigger_error(sprintf(__('Memory limit set to %1$s ,because can not use PHP: memory_get_usage() function to dynamically increase the Memory!', WPeMatico :: TEXTDOMAIN ),ini_get('memory_limit')),E_USER_WARNING);
		}
		//run job parts
		$postcount = 0;
		$this->feeds = $this->campaign['campaign_feeds'] ; // --- Obtengo los feeds de la campaña
		
		foreach($this->feeds as $feed) {
			$postcount += $this->processFeed($feed);         #- ---- Proceso todos los feeds      
		}

		$this->fetched_posts += $postcount; 

		$this->fetch_end(); // if everything ok call fetch_end  and end class
	}
	
	
	/**
	* Processes a feed 
	*
	* @param   $feed       URL string    Feed 
	* @return  The number of posts added
	*/
	private function processFeed(&$feed)  {
		@set_time_limit(0);
		trigger_error('<span class="coderr b">'.sprintf(__('Processing feed %1s.', WPeMatico :: TEXTDOMAIN ),$feed).'</span>' , E_USER_NOTICE);   // Log
		
		$items = array();
		$count = 0;
		$prime = true;

		// Access the feed
		$simplepie =  WPeMatico :: fetchFeed($feed, false, $this->campaign['campaign_max']);
		foreach($simplepie->get_items() as $item) {
			if($prime){
				//Siempre guardo el PRIMERO leido por feed  (es el ultimo item, mas nuevo)
				$this->lasthash[$feed] = md5($item->get_permalink()); 
				$prime=false;
			}

			$this->currenthash[$feed] = md5($item->get_permalink()); // el hash del item actual del feed feed  
			// chequeo a la primer coincidencia sale del foreach
			$dupi = ( @$this->campaign[$feed]['lasthash'] == $this->currenthash[$feed] ); 
			if ($dupi) {
				trigger_error(sprintf(__('Found duplicated hash \'%1s\'', WPeMatico :: TEXTDOMAIN ),$item->get_permalink()).': '.$this->currenthash[$feed] ,E_USER_NOTICE);
				trigger_error(__('Filtering duplicated posts.', WPeMatico :: TEXTDOMAIN ),E_USER_NOTICE);
				break;   
			}
			if($this->isDuplicate($this->campaign, $feed, $item)) {
				trigger_error(__('Filtering duplicated posts.', WPeMatico :: TEXTDOMAIN ),E_USER_NOTICE);
				break;
			}

			$count++;
			array_unshift($items, $item); // add at Post stack in correct order by date 		  
			if($count == $this->campaign['campaign_max']) {
				trigger_error(sprintf(__('Campaign fetch limit reached at %1s.', WPeMatico :: TEXTDOMAIN ),$this->campaign['campaign_max']),E_USER_NOTICE);
				break;
			}
		}
		
		// Processes post stack
		$realcount = 0;
		foreach($items as $item) {					
			$realcount++;
			$suma=$this->processItem($simplepie, $item, $feed);
			if (isset($suma) && is_int($suma)) {
				$realcount = $realcount + $suma;
				$suma="";
			}
		}
		
		if($realcount) {
			trigger_error(sprintf(__('%s posts added', WPeMatico :: TEXTDOMAIN ),$realcount),E_USER_NOTICE);
		}
		
		return $realcount;
	}
	
   /**
   * Processes an item: parses and filters
   * @param   $feed       object    Feed database object
   * @param   $item       object    SimplePie_Item object
   * @return true si lo procesó
   */
	function processItem(&$feed, &$item, $feedurl) {
		global $wpdb, $realcount;
		trigger_error(sprintf(__('Processing item %1s', WPeMatico :: TEXTDOMAIN ),$item->get_title()),E_USER_NOTICE);
		
		// First exclude filters
		if ( $this->exclude_filters($this->current_item,$this->campaign,$feed,$item )) {
			return -1 ;
		}
		
		//********** Do parses contents and titles
		$this->current_item = $this->Item_parsers($this->current_item,$this->campaign,$feed,$item,$realcount, $feedurl );
		if($this->current_item == -1 ) return -1;

	    // Item date
		$itemdate = $item->get_date('U');
		if($this->campaign['campaign_feeddate'] && (($itemdate > $this->campaign['lastrun']) && $itemdate < current_time('timestamp', 1))){
			$this->current_item['date'] = $itemdate;
			trigger_error(__('Assigning original date to post.', WPeMatico :: TEXTDOMAIN ),E_USER_NOTICE);
		}else{
			$this->current_item['date'] = null;
			trigger_error(__('Original date out of range.  Assigning current date to post.', WPeMatico :: TEXTDOMAIN ) ,E_USER_NOTICE);
		}
		
		// Primero proceso las categorias si las hay y las nuevas las agrego al final del array
		$this->current_item['categories'] = (array)$this->campaign['campaign_categories']; 
		if ($this->campaign['campaign_autocats']) 
			if ($autocats = $item->get_categories()) {
				trigger_error(__('Assigning Auto Categories.', WPeMatico :: TEXTDOMAIN ) ,E_USER_NOTICE);
				foreach($autocats as $id => $catego) {
					$catname = $catego->term;
					if(!empty($catname)) {
						trigger_error(__('Adding Category: ', WPeMatico :: TEXTDOMAIN ) . $catname ,E_USER_NOTICE);
						$this->current_item['categories'][] = wp_create_category($catname);  //Si ya existe devuelve el ID existente  // wp_insert_category(array('cat_name' => $catname));  //
					}					
				}
			}	

		$this->current_item['posttype'] = $this->campaign['campaign_posttype'];
		$this->current_item['allowpings'] = $this->campaign['campaign_allowpings'];
		$this->current_item['commentstatus'] = $this->campaign['campaign_commentstatus'];
		$this->current_item['customposttype'] = $this->campaign['campaign_customposttype'];

		//********** Do filters
		$this->current_item = $this->Item_filters($this->current_item,$this->campaign,$feed,$item );
		//ACA ARMO EL ARRAY DE IMAGENES Y MODIFICO EL CONTENT QUE APUNTEN BIEN
		$this->current_item = $this->Item_images($this->current_item,$this->campaign,$feed,$item);
		
		 // Meta
		$this->current_item['meta'] = array(
			'wpe_campaignid' => $this->campaign_id, 
			'wpe_feed' => $feed->feed_url,
			'wpe_sourcepermalink' => $item->get_permalink()
		);  
		
		if( $this->cfg['nonstatic'] ) { $this->current_item['images'] = NoNStatic :: img1s($this->current_item,$this->campaign,$item ); }
		$this->current_item = $this->Item_parseimg($this->current_item,$this->campaign,$feed,$item);
		if( $this->cfg['nonstatic'] ) { $this->current_item = NoNStatic :: metaf($this->current_item, $this->campaign, $feed, $item ); }
		// escape the content ??
		//$this->current_item['content'] = $wpdb->escape($this->current_item['content']);

		 // Create post
		$postid = $this->insertPost(
						$this->current_item['title'],
						$this->current_item['content'], 
						$this->current_item['date'], 
						$this->current_item['categories'],
						$this->current_item['posttype'], 
						$this->current_item['author'], 
						$this->current_item['allowpings'], 
						$this->current_item['commentstatus'], 
						$this->current_item['meta'],
						$this->current_item['customposttype'],
						$this->current_item['images'],
						$this->current_item['tags']
		);
		
		// Attaching images uploaded to created post in media library 
		if(!$this->campaign['campaign_cancel_imgcache']) 
			if(($this->cfg['imgcache'] || $this->campaign['campaign_imgcache']) && ($this->cfg['imgattach'])) {
				if(is_array($this->current_item['images'])) {
					if(sizeof($this->current_item['images'])) { // Si hay alguna imagen 
						trigger_error(__('Attaching images', WPeMatico :: TEXTDOMAIN ).": ".sizeof($this->current_item['images']),E_USER_NOTICE);
						$custom_imagecount = 0;
						foreach($this->current_item['images'] as $imagen_src) {
							$attachid = $this->insertfileasattach($imagen_src,$postid);
							if(($custom_imagecount == 0) && ($this->cfg['featuredimg'])) {
								trigger_error(__('Featured Image Into Post.', WPeMatico :: TEXTDOMAIN ),E_USER_NOTICE);
								add_post_meta($postid, '_thumbnail_id', $attachid);
								$custom_imagecount++;
							}
						}
					}
				}
			}
			

		 // If pingback/trackbacks
		if($this->campaign['campaign_allowpings']) {
			trigger_error(__('Processing item pingbacks', WPeMatico :: TEXTDOMAIN ),E_USER_NOTICE);
			
			require_once(ABSPATH . WPINC . '/comment.php');
			pingback($this->current_item['content'], $postid);      
		}
	}
  	

	/**
	* Writes a post to blog  *   *  
	* @param   string    $title            Post title
	* @param   string    $content          Post content
	* @param   integer   $timestamp        Post timestamp
	* @param   array     $category         Array of categories
	* @param   string    $status           'draft', 'published' or 'private'
	* @param   integer   $authorid         ID of author.
	* @param   boolean   $allowpings       Allow pings
	* @param   boolean   $comment_status   'open', 'closed', 'registered_only'
	* @param   array     $meta             Meta key / values
	* @return  integer   Created post id
	*/
	function insertPost($title, $content, $timestamp = null, $category = null, $status = 'draft', $authorid = null, $allowpings = true, $comment_status = 'open', $meta = array(), $post_type= 'post', $images = null, $tags_input = null )   {
		global $wpdb, $wp_locale, $current_blog;
		$table_name = $wpdb->prefix . "posts";  
		$blog_id 	= @$current_blog->blog_id;
		
		$date = ($timestamp) ? gmdate('Y-m-d H:i:s', $timestamp + (get_option('gmt_offset') * 3600)) : null;
		if($this->cfg['woutfilter'] && $this->campaign['campaign_woutfilter'] ) {
			$truecontent = $content;
			$content = '';
		}
		//trigger_error("tags_input:::".print_r($tags_input,true),E_USER_NOTICE);

		$post_id = wp_insert_post(array(
			'post_title' 	          => $title,
			'post_content'  	      => $content,
			'post_content_filtered'   => $content,
			'post_category'           => $category,
			'post_status' 	          => $status,
			'post_type' 	          => $post_type,
			'post_author'             => $authorid,
			'post_date'               => $date,
			'comment_status'          => $comment_status,
			'ping_status'             => ($allowpings) ? "open" : "closed"
		));
		$aaa = wp_set_post_terms( $post_id, $tags_input);
		trigger_error("Tags added:::".print_r($aaa,true) ,E_USER_WARNING);
		
		if($this->cfg['woutfilter'] && $this->campaign['campaign_woutfilter'] ) {
			$content = $truecontent;
			trigger_error(__('Adding unfiltered content', WPeMatico :: TEXTDOMAIN ),E_USER_NOTICE);
			$wpdb->update( $table_name, array( 'post_content' => $content, 'post_content_filtered' => $content ), array( 'ID' => $post_id )	);
		}
		// insert PostMeta
		foreach($meta as $key => $value) 
			add_post_meta($post_id, $key, $value, true);

		return $post_id;
	}
	
	private function fetch_end() {
		$this->campaign['lastrun'] 		  = $this->campaign['starttime'];
		$this->campaign['lastruntime'] 	  = current_time('timestamp') - $this->campaign['starttime'];
		$this->campaign['starttime'] 	  = '';
		$this->campaign['postscount'] 	 += $this->fetched_posts; // Suma los posts procesados 
		$this->campaign['lastpostscount'] = $this->fetched_posts; //  posts procesados esta vez

		foreach($this->campaign['campaign_feeds'] as $feed) {    // Grabo el ultimo hash de cada feed
			@$this->campaign[$feed]['lasthash'] = $this->lasthash[$feed]; // paraa chequear duplicados por el hash del permalink original
		}
		if($this->cfg['nonstatic']){$this->campaign=NoNStatic::ending($this->campaign,$this->fetched_posts);}

		WPeMatico :: update_campaign($this->campaign_id, $this->campaign);  //Save Campaign new data

		trigger_error(sprintf(__('Campaign fetched in %1s sec.', WPeMatico :: TEXTDOMAIN ),$this->campaign['lastruntime']),E_USER_NOTICE);
	}

	public function __destruct() {
		global $campaign_log_message, $joberrors;
		//Send mail with log
		$sendmail=false;
		if ($joberrors>0 and $this->campaign['mailerroronly'] and !empty($this->campaign['mailaddresslog']))
			$sendmail=true;
		if (!$this->campaign['mailerroronly'] and !empty($this->campaign['mailaddresslog']))
			$sendmail=true;
		if ($sendmail) {
			$title = get_the_title($this->campaign_id);
			$mailbody = "WPeMatico Log"."\n";
			$mailbody .= __("Campaign Name:", WPeMatico :: TEXTDOMAIN )." ".$title."\n";
			if (!empty($joberrors))
				$mailbody.=__("Errors:", WPeMatico :: TEXTDOMAIN )." ".$joberrors."\n";
			if (!empty($jobwarnings))
				$mailbody.=__("Warnings:", WPeMatico :: TEXTDOMAIN )." ".$jobwarnings."\n";

			$mailbody.="\n".$campaign_log_message;
			add_filter('wp_mail_content_type','wpe_change_content_type'); //function wpe_change_content_type(){ return 'text/html'; } 
			
			wp_mail($this->campaign['mailaddresslog'],__('WPeMatico Log ', WPeMatico :: TEXTDOMAIN ).' '.date_i18n('Y-m-d H:i').': '.$title ,$mailbody,'','');  //array($this->logdir.$this->logfile  
		}
		
		// Save last log as meta field in campaign, replace if exist
		add_post_meta( $this->campaign_id, 'last_campaign_log', $campaign_log_message, true )  or
          update_post_meta( $this->campaign_id, 'last_campaign_log', $campaign_log_message );
		  
		$Suss = sprintf(__('Campaign fetched in %1s sec.', WPeMatico :: TEXTDOMAIN ),$this->campaign['lastruntime']) . '  ' . sprintf(__('Processed Posts: %1s', WPeMatico :: TEXTDOMAIN ), $this->fetched_posts);
		$message = '<div>'. $Suss.'  <a href="JavaScript:void(0);" style="font-weight: bold; text-decoration:none; display:inline;" onclick="jQuery(\'#log_message\').toggle();">' . __('Show detailed Log', WPeMatico :: TEXTDOMAIN ) . '.</a></div>';
		$campaign_log_message = $message .'<div id="log_message" style="display:none;" class="error fade">'.$campaign_log_message.'</div>';

		return;
	}
}

function wpe_change_content_type(){ return 'text/html'; }

//function for PHP error handling
function wpematico_joberrorhandler($errno, $errstr, $errfile, $errline) {
	global $campaign_log_message, $jobwarnings, $joberrors;
    
	//genrate timestamp
	if (!function_exists('memory_get_usage')) { // test if memory functions compiled in
		$timestamp="<span style=\"background-color:c3c3c3;\" title=\"[Line: ".$errline."|File: ".basename($errfile)."\">".date_i18n('Y-m-d H:i.s').":</span> ";
	} else  {
		$timestamp="<span style=\"background-color:c3c3c3;\" title=\"[Line: ".$errline."|File: ".basename($errfile)."|Mem: ". WPeMatico :: formatBytes(@memory_get_usage(true))."|Mem Max: ". WPeMatico :: formatBytes( @memory_get_peak_usage(true))."|Mem Limit: ".ini_get('memory_limit')."]\">".date_i18n('Y-m-d H:i.s').":</span> ";
	}

	switch ($errno) {
    case E_NOTICE:
	case E_USER_NOTICE:
		$massage=$timestamp."<span>".$errstr."</span>";
        break;
    case E_WARNING:
    case E_USER_WARNING:
		$jobwarnings += 1;
		$massage=$timestamp."<span style=\"background-color:yellow;\">".__('[WARNING]', WPeMatico :: TEXTDOMAIN )." ".$errstr."</span>";
        break;
	case E_ERROR: 
    case E_USER_ERROR:
		$joberrors += 1;
		$massage=$timestamp."<span style=\"background-color:red;\">".__('[ERROR]', WPeMatico :: TEXTDOMAIN )." ".$errstr."</span>";
        break;
	case E_DEPRECATED:
	case E_USER_DEPRECATED:
		$massage=$timestamp."<span>".__('[DEPRECATED]', WPeMatico :: TEXTDOMAIN )." ".$errstr."</span>";
		break;
	case E_STRICT:
		$massage=$timestamp."<span>".__('[STRICT NOTICE]', WPeMatico :: TEXTDOMAIN )." ".$errstr."</span>";
		break;
	case E_RECOVERABLE_ERROR:
		$massage=$timestamp."<span>".__('[RECOVERABLE ERROR]', WPeMatico :: TEXTDOMAIN )." ".$errstr."</span>";
		break;
	default:
		$massage=$timestamp."<span>[".$errno."] ".$errstr."</span>";
        break;
    }

	if (!empty($massage)) {

		$campaign_log_message .= $massage."<br />\n";

		if ($errno==E_ERROR or $errno==E_CORE_ERROR or $errno==E_COMPILE_ERROR) {//Die on fatal php errors.
			die("Fatal Error:" . $errno);
		}
		//300 is most webserver time limit. 0= max time! Give script 5 min. more to work.
		@set_time_limit(300); 
		//true for no more php error hadling.
		return true;
	} else {
		return false;
	}

	
}
