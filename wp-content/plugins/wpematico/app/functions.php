<?PHP
// don't load directly
if ( !defined('ABSPATH') )
	die('-1');

	/* Add Page under admin and Add Settings Page */
	function wpematico_admin_menu() {
		if (function_exists('add_menu_page')) {
			$hook = add_menu_page( __('WPeMatico','wpematico'), __('WPeMatico','wpematico'), 'manage_options', 'WPeMatico', 'wpematico_options_page','',7);
			add_submenu_page( 'WPeMatico', __('Campaigns', 'WPeMatico'), __('Campaigns', 'WPeMatico'), 'manage_options', 'WPeMatico', 'wpematico_options_page');
			add_submenu_page( 'WPeMatico', __('Add New Campaign', 'WPeMatico')  , __('Add New', 'WPeMatico'), 'manage_options', wp_nonce_url('WPeMatico&subpage=edit', 'edit-job'), 'wpematico_options_page');
			add_submenu_page( 'WPeMatico', __('Settings', 'WPeMatico') , __('Settings', 'WPeMatico') , 'manage_options', 'WPeMatico&amp;subpage=settings', 'wpematico_options_page');
			add_action('load-'.$hook, 'wpematico_options_load');
		}
	}

	//Options Page
	function wpematico_options_page() {
		global $table,$wpematico_message,$page_hook;
		if (!current_user_can(10))
			wp_die('You do not have sufficient permissions to access this page.');
		if(!empty($wpematico_message))
			echo $wpematico_message;
		switch($_REQUEST['subpage']) {
		case 'edit':
			require_once(dirname(__FILE__).'/options-edit-job.php');
			break;
		case 'settings':
			require_once(dirname(__FILE__).'/options-settings.php');
			break;
		case 'runnow':
			echo "<div class=\"wrap\">";
			echo "<div id=\"icon-tools\" class=\"icon32\"><br /></div>";
			echo "<h2>".__('WPeMatico', 'wpematico')."&nbsp;<a href=\"".wp_nonce_url('admin.php?page=WPeMatico&subpage=edit', 'edit-job')."\" class=\"button add-new-h2\">".esc_html__('Add New')."</a></h2>";
			wpematico_option_submenues();
			echo "<form id=\"posts-filter\" action=\"\" method=\"post\">";
			echo "<input type=\"hidden\" name=\"page\" value=\"WPeMatico\" />";
			echo "<input type=\"hidden\" name=\"subpage\" value=\"\" />";
			$table->display();
			echo "<div id=\"ajax-response\"></div>";
			echo "</form>"; 
			echo "</div>";
			break;

		default:
			echo "<div class=\"wrap\">";
			echo "<div id=\"icon-tools\" class=\"icon32\"><br /></div>";
			echo "<h2>".__('WPeMatico', 'wpematico')."&nbsp;<a href=\"".wp_nonce_url('admin.php?page=WPeMatico&subpage=edit', 'edit-job')."\" class=\"button add-new-h2\">".esc_html__('Add New')."</a></h2>";
			wpematico_option_submenues();
			echo "<form id=\"posts-filter\" action=\"\" method=\"post\">";
			echo "<input type=\"hidden\" name=\"page\" value=\"WPeMatico\" />";
			echo "<input type=\"hidden\" name=\"subpage\" value=\"\" />";
			$table->display();
			echo "<div id=\"ajax-response\"></div>";
			echo "</form>"; 
			echo "</div>";
			break;
		}
	}

	//Options Page
	function wpematico_options_load() {
		global $current_screen,$table,$wpematico_message;
		
		if (!current_user_can(10))
			wp_die('You do not have sufficient permissions to access this page.');
		//Css for Admin Section
		wp_enqueue_style('WPeMatico',plugins_url('css/options.css',__FILE__),'',WPEMATICO_VERSION,'screen');
		wp_enqueue_script('WPeMatico',plugins_url('js/options.js',__FILE__),'',WPEMATICO_VERSION,true);
		add_contextual_help($current_screen,
			'<!-- div class="metabox-prefs">'.
			'<a href="http://wordpress.org/tags/wpematico" target="_blank">'.__('Support').'</a>'.
			' | <a href="http://wordpress.org/extend/plugins/wpematico/faq/" target="_blank">' . __('FAQ') . '</a>'.
			' | <a href="http://http://www.netmdp.com/tag/wpematico" target="_blank">' . __('Plugin Homepage', 'wpematico') . '</a>'.
			' | <a href="http://wordpress.org/extend/plugins/wpematico" target="_blank">' . __('Plugin Home on WordPress.org', 'wpematico') . '</a>'.
			' | <a href="" target="_blank">' . __('Donate') . '</a>'.
			'</div -->'.
			'<div class="metabox-prefs">'.
			__('Version:', 'wpematico').' '.WPEMATICO_VERSION.' | '.
			__('Author:', 'wpematico').' <a href="http://www.netmdp.com" target="_blank">Esteban</a>'.
			'</div>'
		);
		
		if ($_REQUEST['action2']!='-1' and !empty($_REQUEST['doaction2']))
			$_REQUEST['action']=$_REQUEST['action2'];

		switch($_REQUEST['subpage']) {
		case 'edit':
			if (!empty($_POST['submit'])) {
				require_once(dirname(__FILE__).'/options-save.php');
				$wpematico_message=wpematico_save_job();
			}
			break;
		case 'settings':
			if (!empty($_POST['submit'])) {
				require_once(dirname(__FILE__).'/options-save.php');
				$wpematico_message=wpematico_save_settings();
			}
			break;
		case 'runnow':
			$jobid = (int) $_GET['jobid'];
			check_admin_referer('runnow-job_' . $jobid);
			$wpematico_message = wpematico_dojob($jobid);
			$table = new WPeMatico_Campaigns_Table;
			$table->check_permissions();
			$table->prepare_items();
			break;

		default:
			if (!empty($_REQUEST['action'])) {
				require_once(dirname(__FILE__).'/options-save.php');
				wpematico_job_operations($_REQUEST['action']);
			}
			$table = new WPeMatico_Campaigns_Table;
			$table->check_permissions();
			$table->prepare_items();
			break;
		}
	}

	
	function wpematico_option_submenues() {
		$maincurrent="";$logscurrent="";$backupscurrent="";$toolscurrent="";$settingscurrent="";
		if (empty($_REQUEST['subpage']))
			$maincurrent=" class=\"current\"";
		if ($_REQUEST['subpage']=='settings')
			$settingscurrent=" class=\"current\"";
		echo "<ul class=\"subsubsub\">";
		echo "<li><a href=\"admin.php?page=WPeMatico\"$maincurrent>".__('Campaigns','wpematico')."</a> |</li>";
		echo "<li><a href=\"admin.php?page=WPeMatico&amp;subpage=settings\"$settingscurrent>".__('Settings','wpematico')."</a></li>";
		echo "</ul>";
	}

	//On Plugin activate
	function wpematico_plugin_activate() {
		//remove old cron jobs
		$jobs=(array)get_option('wpematico_jobs');
		foreach ($jobs as $jobid => $jobvalue) {
			if ($time=wp_next_scheduled('wpematico_cron',array('jobid'=>$jobid))) {
				wp_unschedule_event($time,'wpematico_cron',array('jobid'=>$jobid));
			}
		}
		wp_clear_scheduled_hook('wpematico_cron');
		//make schedule
		wp_schedule_event(0, 'wpematico_int', 'wpematico_cron');
		//Set defaults
		$cfg=get_option('wpematico'); //Load Settings
 		if (empty($cfg['mailsndemail'])) $cfg['mailsndemail']	= sanitize_email(get_bloginfo( 'admin_email' ));
	   if (empty($cfg['mailsndname'])) $cfg['mailsndname']	= 'WPeMatico '.get_bloginfo( 'name' );
	   if (empty($cfg['mailmethod'])) 	$cfg['mailmethod']	= 'mail';
		if (empty($cfg['mailsendmail'])) $cfg['mailsendmail']	= substr(ini_get('sendmail_path'),0,strpos(ini_get('sendmail_path'),' -'));
		if (empty($cfg['imgcache'])) 		$cfg['imgcache'] 		= false;
		if (empty($cfg['enableword2cats'])) 		$cfg['enableword2cats'] 		= false;
		update_option('wpematico',$cfg);
	}

	//on Plugin deaktivate
	function wpematico_plugin_deactivate() {
		//remove old cron jobs
		$jobs=(array)get_option('wpematico_jobs');
		foreach ($jobs as $jobid => $jobvalue) {
			if ($time=wp_next_scheduled('wpematico_cron',array('jobid'=>$jobid))) {
				wp_unschedule_event($time,'wpematico_cron',array('jobid'=>$jobid));
			}
		}
		wp_clear_scheduled_hook('wpematico_cron');
		delete_option('wpematico');
	}

	//add edit setting to plugins page
	function wpematico_plugin_options_link($links) {
		$settings_link='<a href="admin.php?page=WPeMatico" title="' . __('Go to Settings Page','wpematico') . '" class="edit">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	//add links on plugins page
	function wpematico_plugin_links($links, $file) {
		if ($file == WPEMATICO_PLUGIN_BASEDIR.'/wpematico.php') {
			$links[] = '<a href="http://wordpress.org/extend/plugins/wpematico/faq/" target="_blank">' . __('FAQ') . '</a>';
			$links[] = '<a href="http://wordpress.org/tags/wpematico/" target="_blank">' . __('Support') . '</a>';
			$links[] = '<a href="" target="_blank">' . __('Donate') . '</a>';
		}
		return $links;
	}

	//Add cron interval
	function wpematico_intervals($schedules) {
		$intervals['wpematico_int']=array('interval' => '300', 'display' => __('WPeMatico', 'wpematico'));
		$schedules=array_merge($intervals,$schedules);
		return $schedules;
	}

	//cron work
	function wpematico_cron() {
		$jobs=(array)get_option('wpematico_jobs');
		foreach ($jobs as $jobid => $jobvalue) {
			if (!$jobvalue['activated'])
				continue;
			if ($jobvalue['cronnextrun']<=current_time('timestamp')) {
				wpematico_dojob($jobid);
			}
		}
	}

	//DoJob
	function wpematico_dojob($jobid) {
		global $wpematico_dojob_message;
		if (empty($jobid))
			return false;
		require_once(dirname(__FILE__).'/wpematico_dojob.php');
		$wpematico_dojob= new wpematico_dojob($jobid);
		unset($wpematico_dojob);
		return $wpematico_dojob_message;
	}

	//file size
	function wpematico_formatBytes($bytes, $precision = 2) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
		$bytes /= pow(1024, $pow);
		return round($bytes, $precision) . ' ' . $units[$pow];
	}
	
	//Permalink to Source
	 /*** Determines what the title has to link to   * @return string new text   **/
  function wpematico_permalink($url) {
    // if from admin panel
	if(get_the_ID()) {
		$jobid = (int) get_post_meta(get_the_ID(), 'wpe_campaignid', true);

		$jobs=(array)get_option('wpematico_jobs');
		$jobs=get_option('wpematico_jobs');

		if($jobid) {
			$jobvalue=wpematico_check_job_vars($jobs[$jobid],$jobid);
			if($jobvalue['campaign_linktosource'])
			 return get_post_meta(get_the_ID(), 'wpe_sourcepermalink', true);
		}  	  
	}
   return $url;      
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
		array_sort_func($keys);
		usort($array,"array_sort_func");       
	} 
	################### END ARRAYS FUNCS


	 //Dashboard widget
	function wpematico_dashboard_output() {
//		global $wpdb;
		$jobs=(array)get_option('wpematico_jobs');
		echo '<strong>'.__('Processed Campaigns:','wpematico').'</strong><br />';
		$jobs2 = filter_by_value($jobs, 'lastrun', '');  
		foreach ($jobs2 as $jobid => $jobvalue) $jobs2[$jobid]['jobid']=$jobid; // Le agrego el id para no perderlo al ordenar		
		array_sort($jobs2,'lastrun');
//				echo "<pre>".print_r($jobs, true)."</pre>";
		if (is_array($jobs2)) {
			$count=0;
			foreach ($jobs2 as $jobid => $jobvalue) {
				echo '<a href="'.wp_nonce_url('admin.php?page=WPeMatico&subpage=edit&jobid='.$jobvalue['jobid'], 'edit-job').'" title="'.__('Edit Campaign','wpematico').'">';
					if ($jobvalue['lastrun']) {
						echo "ID: " .$jobvalue['jobid']. ", <i>".$jobvalue['name']."</i> :: ";
						echo  date_i18n(get_option('date_format'),$jobvalue['lastrun']).'-'. date_i18n(get_option('time_format'),$jobvalue['lastrun']).'h, <i>'; 
						if ($jobvalue['lastpostscount']>0)
							echo ' <span style="color:green;">'. sprintf(__('Processed Posts: %1s','wpematico'),$jobvalue['lastpostscount']).'</span>, ';
						else
							echo ' <span style="color:red;">'. sprintf(__('Processed Posts: %1s','wpematico'), '0').'</span>, ';
							
						if ($jobvalue['lastruntime']<10)
							echo ' <span style="color:green;">'. sprintf(__('Job done in %1s sec.','wpematico'),$jobvalue['lastruntime']) .'</span>';
						else
							echo ' <span style="color:red;">'. sprintf(__('Job done in %1s sec.','wpematico'),$jobvalue['lastruntime']) .'</span>';
					} 
				echo '</i></a><br />';
				$count++;
				if ($count>=5)
					break;
			}		
		}
		unset($jobs2);
		echo '<strong>'.__('Scheduled Campaigns:','wpematico').'</strong><br />';
		foreach ($jobs as $jobid => $jobvalue) {
			if ($jobvalue['activated']) {
				echo '<a href="'.wp_nonce_url('admin.php?page=WPeMatico&subpage=edit&jobid='.$jobid, 'edit-job').'" title="'.__('Edit Campaign','wpematico').'">';
				if ($jobvalue['starttime']>0 and empty($jobvalue['stoptime'])) {
					$runtime=current_time('timestamp')-$jobvalue['starttime'];
					echo __('Running since:','wpematico').' '.$runtime.' '.__('sec.','wpematico');
				} elseif ($jobvalue['activated']) {
					echo date(get_option('date_format'),$jobvalue['cronnextrun']).' '.date(get_option('time_format'),$jobvalue['cronnextrun']);
				}
				echo ': <span>'.$jobvalue['name'].'</span></a><br />';
			}
		}
		$jobs=filter_by_value($jobs, 'activated', '');
		if (empty($jobs)) 
			echo '<i>'.__('None','wpematico').'</i><br />';

	}

	//add dashboard widget
	function wpematico_add_dashboard() {
		wp_add_dashboard_widget( 'wpematico_dashboard_widget', 'WPeMatico', 'wpematico_dashboard_output' );
	}

	//turn cache off
	function wpematico_meta_no_cache() {
		echo "<meta http-equiv=\"expires\" content=\"0\" />\n";
		echo "<meta http-equiv=\"pragma\" content=\"no-cache\" />\n";
		echo "<meta http-equiv=\"cache-control\" content=\"no-cache\" />\n";
	}

	
	//Calcs next run for a cron string as timestamp
	function wpematico_cron_next($cronstring) {
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

	function wpematico_env_checks() {
		global $wp_version,$wpematico_admin_message;
		$message='';
		$checks=true;
		$cfg=get_option('wpematico');
		if (version_compare($wp_version, '2.8', '<')) { // check WP Version
			$message.=__('- WordPress 2.8 or heiger needed!','wpematico') . '<br />';
			$checks=false;
		}
		if (version_compare(phpversion(), '5.2.0', '<')) { // check PHP Version
			$message.=__('- PHP 5.2.0 or higher needed!','wpematico') . '<br />';
			$checks=false;
		}

		$jobs=(array)get_option('wpematico_jobs'); 
		foreach ($jobs as $jobid => $jobvalue) { //check for old cheduling
			if (isset($jobvalue['scheduletime']) and empty($jobvalue['cron']))
				$message.=__('- Please Check Scheduling time for Campaign:','wpematico') . ' '.$jobid.'. '.$jobvalue['name'].'<br />';
		}
		if (wp_next_scheduled('wpematico_cron')!=0 and wp_next_scheduled('wpematico_cron')>(time()+360)) {  //check cron jobs work
			$message.=__("- WP-Cron don't working please check it!","wpematico") .'<br />';
		}
		//put massage if one
		if (!empty($message))
			$wpematico_admin_message = '<div id="message" class="error fade"><strong>WPeMatico:</strong><br />'.$message.'</div>';
		return $checks;
	}

	function wpematico_admin_notice() {
		global $wpematico_admin_message;
		echo $wpematico_admin_message;
	}
	
	// add all action and so on only if plugin loaded.
	function wpematico_plugins_loaded() {
		if (!wpematico_env_checks())
			return;
		//iclude php5 functions
		require_once(dirname(__FILE__).'/functions5.php');
		//load tables Classes
		require_once(dirname(__FILE__).'/list-tables.php');
		//Disabele WP_Corn
		$cfg=get_option('wpematico');
		if ($cfg['disablewpcron'])
			define('DISABLE_WP_CRON',true);
		//add Menu
			add_action('admin_menu', 'wpematico_admin_menu');
		//Additional links on the plugin page
		if (current_user_can(10))
			add_filter('plugin_action_links_'.WPEMATICO_PLUGIN_BASEDIR.'/wpematico.php', 'wpematico_plugin_options_link');
		if (current_user_can('install_plugins'))
			add_filter('plugin_row_meta', 'wpematico_plugin_links',10,2);
		//add cron intervals
		add_filter('cron_schedules', 'wpematico_intervals');
		//Actions for Cron job
		add_action('wpematico_cron', 'wpematico_cron');
		//test if cron active
		if (!(wp_next_scheduled('wpematico_cron')))
			wp_schedule_event(0, 'wpematico_int', 'wpematico_cron');
		//add Dashboard widget
		if (current_user_can(10) && !$cfg['disabledashboard'])
			add_action('wp_dashboard_setup', 'wpematico_add_dashboard');
		// add ajax function
		add_action('wp_ajax_test_feed', 'wpematico_Testfeed');			
	}

//*****************************************************************************************
// ** Muestro Categorías seleccionables 
function _wpe_edit_cat_row($category, $level, &$data) {  
	$category = get_category( $category );
	$name = $category->cat_name;
	echo '
	<li style="margin-left:'.$level.'5px" class="jobtype-select checkbox">
	<input type="checkbox" value="' . $category->cat_ID . '" id="category_' . $category->cat_ID . '" name="campaign_categories[]" ';
	echo (in_array($category->cat_ID, $data )) ? 'checked="checked"' : '' ;
	echo '>
    <label for="category_' . $category->cat_ID . '">' . $name . '</label></li>';}

function adminEditCategories(&$data, $parent = 0, $level = 0, $categories = 0)  {    
  	if ( !$categories )
  		$categories = get_categories(array('hide_empty' => 0));

    if(function_exists('_get_category_hierarchy'))
      $children = _get_category_hierarchy();
    elseif(function_exists('_get_term_hierarchy'))
      $children = _get_term_hierarchy('category');
    else
      $children = array();

  	if ( $categories ) {
  		ob_start();
  		foreach ( $categories as $category ) {
  			if ( $category->parent == $parent) {
  				echo "\t" . _wpe_edit_cat_row($category, $level, $data);
  				if ( isset($children[$category->term_id]) )
  					adminEditCategories($data, $category->term_id, $level + 1, $categories );
  			}
  		}
  		$output = ob_get_contents();
  		ob_end_clean();

  		echo $output;
  	} else {
  		return false;
  	}
}
//*********************************************************************************************************
  /**
   * Parses a feed with SimplePie
   *
   * @param   boolean     $stupidly_fast    Set fast mode. Best for checks
   * @param   integer     $max              Limit of items to fetch
   * @return  SimplePie_Item    Feed object
   **/
  function fetchFeed($url, $stupidly_fast = false, $max = 0) {
    # SimplePie

	if (!class_exists('SimplePie')) {
		if (is_file(trailingslashit(ABSPATH).'wp-admin/includes/class-simplepie.php'))
			include_once( trailingslashit(ABSPATH).'wp-admin/includes/class-simplepie.php' );
		else
			include_once('compatibility/class-simplepie.php');
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
   *
   */
  function wpematico_Testfeed($args='') {
	if (is_array($args)) {
		extract($args);
		$ajax=false;
	} else {
		if(!isset($_POST['url'])) return false;
		$url=$_POST['url'];
		$ajax=true;
	}

	$feed = fetchFeed($url, true);
	$works = ! $feed->error(); // if no error returned
	
	if ($ajax) {
		echo intval($works);
		die();
	}else {
		if($works) printf(__('The feed %s has been parsed successfully.', 'wpematico'), $url);
		else	printf(__('The feed %s cannot be parsed. Simplepie said: %s', 'wpematico'), $url, $works);
		return;
	}   

}

?>