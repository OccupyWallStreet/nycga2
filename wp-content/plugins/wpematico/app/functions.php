<?php 
// don't load directly 
if ( !defined('ABSPATH') ) 
	die('-1');

if ( class_exists( 'WPeMatico_functions' ) ) return;

class WPeMatico_functions {
	function wpematico_env_checks() {
		global $wp_version,$wpematico_admin_message;
		$message='';
		$checks=true;
		if (version_compare($wp_version, '3.1', '<')) { // check WP Version
			$message.=__('- WordPress 3.1 or higher needed!', self :: TEXTDOMAIN ) . '<br />';
			$checks=false;
		}
		if (version_compare(phpversion(), '5.2.0', '<')) { // check PHP Version
			$message.=__('- PHP 5.2.0 or higher needed!', self :: TEXTDOMAIN ) . '<br />';
			$checks=false;
		}

		if (wp_next_scheduled('wpematico_cron')!=0 and wp_next_scheduled('wpematico_cron')>(time()+360)) {  //check cron jobs work
			$message.=__("- WP-Cron don't working please check it!", self :: TEXTDOMAIN ) .'<br />';
		}
		//put massage if one
		if (!empty($message))
			$wpematico_admin_message = '<div id="message" class="error fade"><strong>WPeMatico:</strong><br />'.$message.'</div>';
		return $checks;
	}

/* 	//Admin header notify
	function wpematico_admin_notice() {
		global $wpematico_admin_message;
		echo $wpematico_admin_message;
	}
 */	
	//file size
	function formatBytes($bytes, $precision = 2) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
		$bytes /= pow(1024, $pow);
		return round($bytes, $precision) . ' ' . $units[$pow];
	}

	//************************* CARGA CAMPAÑASS *******************************************************
 /**
   * Load all campaigns data
   * 
   * @return an array with all campaigns data 
   **/	
	function get_campaigns() {
		$campaigns_data = array();
		$args = array(
			'orderby'         => 'ID',
			'order'           => 'ASC',
			'post_type'       => 'wpematico', 
			'numberposts' => -1
		);
		$campaigns = get_posts( $args );
		foreach( $campaigns as $post ):
			$campaigns_data[] = self::get_campaign( $post->ID );	
		endforeach; 
		return $campaigns_data;
	}
 
	//************************* CARGA CAMPAÑA *******************************************************
 /**
   * Load campaign data
   * Required @param   integer  $post_id    Campaign ID to load
   * 		  @param   boolean  $getfromdb  if set to true run get_post($post_ID) and retuirn object post
   * 
   * @return an array with campaign data 
   **/	
	public function get_campaign( $post_id , $getfromdb = false ) {
		if ( $getfromdb ){
			$campaign = get_post($post_id);
		}
		$campaign_data = get_post_meta( $post_id , 'campaign_data' );
		$campaign_data = @$campaign_data[0];
		$campaign_data['campaign_id'] = $post_id;
		$campaign_data['campaign_title'] = get_the_title($post_id);
		return $campaign_data;
	}
	
	//************************* GUARDA CAMPAÑA *******************************************************
 /**
   * save campaign data
   * Required @param   integer  $post_id    Campaign ID to load
   * 		  @param   boolean  $getfromdb  if set to true run get_post($post_ID) and retuirn object post
   * 
   * @return an array with campaign data 
   **/	
	public function update_campaign( $post_id , $campaign = array() ) {
		$campaign['cronnextrun']= WPeMatico :: time_cron_next($campaign['cron']);
		
			// *** Campaign Rewrites	
		// Proceso los rewrites agrego slashes	
		if (isset($campaign['campaign_rewrites']['origin']))
			for ($i = 0; $i < count($campaign['campaign_rewrites']['origin']); $i++) {
				$campaign['campaign_rewrites']['origin'][$i] = addslashes($campaign['campaign_rewrites']['origin'][$i]);
				$campaign['campaign_rewrites']['rewrite'][$i] = addslashes($campaign['campaign_rewrites']['rewrite'][$i]);
				$campaign['campaign_rewrites']['relink'][$i] = addslashes($campaign['campaign_rewrites']['relink'][$i]);
			}
		if (isset($campaign['campaign_wrd2cat']['word']))
			for ($i = 0; $i < count($campaign['campaign_wrd2cat']['word']); $i++) {
				$campaign['campaign_wrd2cat']['word'][$i] = addslashes($campaign['campaign_wrd2cat']['word'][$i]);
			}
				
		return add_post_meta( $post_id, 'campaign_data', $campaign, true )  or
          update_post_meta( $post_id, 'campaign_data', $campaign );
		  
	}
	
	/*********** 	 Funciones para procesar campañas ******************/
	//DoJob
	function wpematico_dojob($jobid) {
		global $campaign_log_message;
		$campaign_log_message = "";
		if (empty($jobid))
			return false;
		require_once(dirname(__FILE__).'/campaign_fetch.php');
		$fetched= new wpematico_campaign_fetch($jobid);
		unset($fetched);
		return $campaign_log_message;
	}

	// Processes all campaigns
/* 	function processAll() {
		@set_time_limit(0);    
		$args = array( 'post_type' => 'wpematico', 'orderby' => 'ID', 'order' => 'ASC' );
		$campaignsid = get_posts( $args );
		foreach( $campaignsid as $campaignid ) {
			wpematico_dojob( $campaignid->ID ); 
		}
	}
	
 */
	//Permalink to Source
	/*** Determines what the title has to link to   * @return string new text   **/
	function wpematico_permalink($url) {
		// if from admin panel
		if(get_the_ID()) {
			$campaign_id = (int) get_post_meta(get_the_ID(), 'wpe_campaignid', true);
			if($campaign_id) {
				$campaign = $this->get_campaign( $campaign_id );
				//$campaign = :: check_campaigndata($campaign);
				if($campaign['campaign_linktosource'])
					return get_post_meta(get_the_ID(), 'wpe_sourcepermalink', true);
			}  	  
		}
		return $url;      
	}
 
	
//*********************************************************************************************************
  /**
   * Parses a feed with SimplePie
   *
   * @param   boolean     $stupidly_fast    Set fast mode. Best for checks
   * @param   integer     $max              Limit of items to fetch
   * @return  SimplePie_Item    Feed object
   **/
  function fetchFeed($url, $stupidly_fast = false, $max = 0) {  # SimplePie
	$cfg = get_option(WPeMatico :: OPTION_KEY);
	if ( $cfg['force_mysimplepie']){
		include_once( dirname( __FILE__) . '/lib/simplepie.inc.php' );
	}else{
		if (!class_exists('SimplePie')) {
			if (is_file( ABSPATH . WPINC . '/class-simplepie.php'))
				include_once( ABSPATH. WPINC . '/class-simplepie.php' );
			else if (is_file( ABSPATH.'wp-admin/includes/class-simplepie.php'))
				include_once( ABSPATH.'wp-admin/includes/class-simplepie.php' );
			else
				include_once( dirname( __FILE__) . '/lib/simplepie.inc.php' );
		}		
	}
    $feed = new SimplePie();
    $feed->enable_order_by_date(false);
    $feed->set_feed_url($url);
    $feed->set_item_limit($max);
    $feed->set_stupidly_fast($stupidly_fast);
    $feed->enable_cache(false);    
    $feed->init();
    $feed->handle_content_type(); 
    
    return $feed;
  }
 
	/**
	* Tests a feed
	*
	*/
	public function Test_feed($args='') {
		if (is_array($args)) {
			extract($args);
			$ajax=false;
		} else {
			if(!isset($_POST['url'])) return false;
			$url=$_POST['url'];
			$ajax=true;
		}
		$feed = self :: fetchFeed($url, true);
		$works = !$feed->error(); // if no error returned
		if ($ajax) {
				echo intval($works);
			die();
		}else {
			if($works) printf(__('The feed %s has been parsed successfully.', WPeMatico :: TEXTDOMAIN ), $url);
			else	printf(__('The feed %s cannot be parsed. Simplepie said: %s', WPeMatico :: TEXTDOMAIN ), $url, $works);
			return;
		}
	}
  
	################### ARRAYS FUNCS
	/* * filtering an array   */
    function filter_by_value ($array, $index, $value){
        if(is_array($array) && count($array)>0) 
        {
            foreach(array_keys($array) as $key){
                $temp[$key] = $array[$key][$index];
                
                if ($temp[$key] != $value){
                    $newarray[$key] = $array[$key];
                }
            }
          }
      return $newarray;
    } 
	 //Example: array_sort($my_array,'!group','surname');
	//Output: sort the array DESCENDING by group and then ASCENDING by surname. Notice the use of ! to reverse the sort order. 
	function array_sort_func($a,$b=NULL) {
		static $keys;
		if($b===NULL) return $keys=$a;
		foreach($keys as $k) {
			if(@$k[0]=='!') {
				$k=substr($k,1);
				if(@$a[$k]!==@$b[$k]) {
					return strcmp(@$b[$k],@$a[$k]);
				}
			}
			else if(@$a[$k]!==@$b[$k]) {
				return strcmp(@$a[$k],@$b[$k]);
			}
		}
		return 0;
	}

	function array_sort(&$array) {
		if(!$array) return $keys;
		$keys=func_get_args();
		array_shift($keys);
		$this->array_sort_func($keys);
		usort($array, array($this,"array_sort_func"));
	} 
	################### END ARRAYS FUNCS

// ********************************** CRON FUNCTIONS
	public function cron_string($array_post){
		if ($array_post['cronminutes'][0]=='*' or empty($array_post['cronminutes'])) {
			if (!empty($array_post['cronminutes'][1]))
				$array_post['cronminutes']=array('*/'.$array_post['cronminutes'][1]);
			else
				$array_post['cronminutes']=array('*');
		}
		if ($array_post['cronhours'][0]=='*' or empty($array_post['cronhours'])) {
			if (!empty($array_post['cronhours'][1]))
				$array_post['cronhours']=array('*/'.$array_post['cronhours'][1]);
			else
				$array_post['cronhours']=array('*');
		}
		if ($array_post['cronmday'][0]=='*' or empty($array_post['cronmday'])) {
			if (!empty($array_post['cronmday'][1]))
				$array_post['cronmday']=array('*/'.$array_post['cronmday'][1]);
			else
				$array_post['cronmday']=array('*');
		}
		if ($array_post['cronmon'][0]=='*' or empty($array_post['cronmon'])) {
			if (!empty($array_post['cronmon'][1]))
				$array_post['cronmon']=array('*/'.$array_post['cronmon'][1]);
			else
				$array_post['cronmon']=array('*');
		}
		if ($array_post['cronwday'][0]=='*' or empty($array_post['cronwday'])) {
			if (!empty($array_post['cronwday'][1]))
				$array_post['cronwday']=array('*/'.$array_post['cronwday'][1]);
			else
				$array_post['cronwday']=array('*');
		}
		return implode(",",$array_post['cronminutes']).' '.implode(",",$array_post['cronhours']).' '.implode(",",$array_post['cronmday']).' '.implode(",",$array_post['cronmon']).' '.implode(",",$array_post['cronwday']);
	}
	
	//******************************************************************************
	//Calcs next run for a cron string as timestamp
	public function time_cron_next($cronstring) {
		//Cronstring zerlegen
		list($cronstr['minutes'],$cronstr['hours'],$cronstr['mday'],$cronstr['mon'],$cronstr['wday'])=explode(' ',$cronstring,5);

		//make arrys form string
		foreach ($cronstr as $key => $value) {
			if (strstr($value,','))
				$cronarray[$key]=explode(',',$value);
			else
				$cronarray[$key]=array(0=>$value);
		}
		//make arrys complete with ranges and steps
		foreach ($cronarray as $cronarraykey => $cronarrayvalue) {
			$cron[$cronarraykey]=array();
			foreach ($cronarrayvalue as $key => $value) {
				//steps
				$step=1;
				if (strstr($value,'/'))
					list($value,$step)=explode('/',$value,2);
				//replase weekeday 7 with 0 for sundays
				if ($cronarraykey=='wday')
					$value=str_replace('7','0',$value);
				//ranges
				if (strstr($value,'-')) {
					list($first,$last)=explode('-',$value,2);
					if (!is_numeric($first) or !is_numeric($last) or $last>60 or $first>60) //check
						return false;
					if ($cronarraykey=='minutes' and $step<5)  //set step ninmum to 5 min.
						$step=5;
					$range=array();
					for ($i=$first;$i<=$last;$i=$i+$step)
						$range[]=$i;
					$cron[$cronarraykey]=array_merge($cron[$cronarraykey],$range);
				} elseif ($value=='*') {
					$range=array();
					if ($cronarraykey=='minutes') {
						if ($step<5) //set step ninmum to 5 min.
							$step=5;
						for ($i=0;$i<=59;$i=$i+$step)
							$range[]=$i;
					}
					if ($cronarraykey=='hours') {
						for ($i=0;$i<=23;$i=$i+$step)
							$range[]=$i;
					}
					if ($cronarraykey=='mday') {
						for ($i=$step;$i<=31;$i=$i+$step)
							$range[]=$i;
					}
					if ($cronarraykey=='mon') {
						for ($i=$step;$i<=12;$i=$i+$step)
							$range[]=$i;
					}
					if ($cronarraykey=='wday') {
						for ($i=0;$i<=6;$i=$i+$step)
							$range[]=$i;
					}
					$cron[$cronarraykey]=array_merge($cron[$cronarraykey],$range);
				} else {
					//Month names
					if (strtolower($value)=='jan')
						$value=1;
					if (strtolower($value)=='feb')
						$value=2;
					if (strtolower($value)=='mar')
						$value=3;
					if (strtolower($value)=='apr')
						$value=4;
					if (strtolower($value)=='may')
						$value=5;
					if (strtolower($value)=='jun')
						$value=6;
					if (strtolower($value)=='jul')
						$value=7;
					if (strtolower($value)=='aug')
						$value=8;
					if (strtolower($value)=='sep')
						$value=9;
					if (strtolower($value)=='oct')
						$value=10;
					if (strtolower($value)=='nov')
						$value=11;
					if (strtolower($value)=='dec')
						$value=12;
					//Week Day names
					if (strtolower($value)=='sun')
						$value=0;
					if (strtolower($value)=='sat')
						$value=6;
					if (strtolower($value)=='mon')
						$value=1;
					if (strtolower($value)=='tue')
						$value=2;
					if (strtolower($value)=='wed')
						$value=3;
					if (strtolower($value)=='thu')
						$value=4;
					if (strtolower($value)=='fri')
						$value=5;
					if (!is_numeric($value) or $value>60) //check
						return false;
					$cron[$cronarraykey]=array_merge($cron[$cronarraykey],array(0=>$value));
				}
			}
		}

		//calc next timestamp
		$currenttime=current_time('timestamp');
		foreach (array(date('Y'),date('Y')+1) as $year) {
			foreach ($cron['mon'] as $mon) {
				foreach ($cron['mday'] as $mday) {
					foreach ($cron['hours'] as $hours) {
						foreach ($cron['minutes'] as $minutes) {
							$timestamp=mktime($hours,$minutes,0,$mon,$mday,$year);
							if (in_array(date('w',$timestamp),$cron['wday']) and $timestamp>$currenttime) {
									return $timestamp;
							}
						}
					}
				}
			}
		}
		return false;
	}

}

	/**
	* Add cron interval
	*
	* @access protected
	* @param array $schedules
	* @return array
	*/
	function wpematico_intervals($schedules) {
		$intervals['wpematico_int'] = array('interval' => '300', 'display' => __('WPeMatico'));
		$schedules = array_merge( $intervals, $schedules);
		return $schedules;
	}

	//cron work  (fuera de la clase hasta que wp lo soporte)
	function wpematico_cron() {
		$args = array( 'post_type' => 'wpematico', 'orderby' => 'ID', 'order' => 'ASC', 'numberposts' => -1 );
		$campaigns = get_posts( $args );
		foreach( $campaigns as $post ) {
			$campaign = WPeMatico :: get_campaign( $post->ID );
			$activated = $campaign['activated'];
			$cronnextrun = $campaign['cronnextrun'];
			if ( !$activated )
				continue;
			if ( $cronnextrun <= current_time('timestamp') ) {
				WPeMatico :: wpematico_dojob( $post->ID );
			}
		}
	}

