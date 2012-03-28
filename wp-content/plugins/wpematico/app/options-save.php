<?PHP
// don't load directly
if ( !defined('ABSPATH') )
	die('-1');

function wpematico_job_operations($action) {
	switch($action) {
	case 'delete': //Delete Campaign
		$jobs=get_option('wpematico_jobs');
		if (is_array($_REQUEST['jobs'])) {
			check_admin_referer('bulk-jobs');
			foreach ($_REQUEST['jobs'] as $jobid) {
				unset($jobs[$jobid]);
			}
		}
		update_option('wpematico_jobs',$jobs);
		break;
	case 'reset': //Reset posts count of Campaign
		$jobs=get_option('wpematico_jobs');
		$jobid = (int) $_GET['jobid'];
		check_admin_referer('reset-job_'.$jobid);
		$jobs=get_option('wpematico_jobs');
		$jobs[$jobid]['postscount']=0;
		$jobs[$jobid]['lastpostscount']=0;
		update_option('wpematico_jobs',$jobs);
		break;
	case 'copy': //Copy Campaign
		$jobid = (int) $_GET['jobid'];
		check_admin_referer('copy-job_'.$jobid);
		$jobs=get_option('wpematico_jobs');
		//generate new ID
		foreach ($jobs as $jobkey => $jobvalue) {
			if ($jobkey>$heighestid) $heighestid=$jobkey;
		}
		$newjobid=$heighestid+1;
		$jobs[$newjobid]=$jobs[$jobid];
		$jobs[$newjobid]['name']=__('Copy of','wpematico').' '.$jobs[$newjobid]['name'];
		$jobs[$newjobid]['activated']=false;
		update_option('wpematico_jobs',$jobs);
		break;
	case 'toggle': //toggle Activate/Deactivate Cron Campaign
		$jobid = (int) $_GET['jobid'];
		check_admin_referer('toggle-job_'.$jobid);
		$jobs=get_option('wpematico_jobs');
		//Change the activate value
		$jobs[$jobid]['activated'] = (!$jobs[$jobid]['activated']); 
		update_option('wpematico_jobs',$jobs);
		break;
	case 'clear': //Abort Campaign
		$jobid = (int) $_GET['jobid'];
		check_admin_referer('clear-job_'.$jobid);
		$jobs=get_option('wpematico_jobs');
		$jobs[$jobid]['cronnextrun']=wpematico_cron_next($jobs[$jobid]['cron']);
		$jobs[$jobid]['stoptime']=current_time('timestamp');
		$jobs[$jobid]['lastrun']=$jobs[$jobid]['starttime'];
		$jobs[$jobid]['lastruntime']=$jobs[$jobid]['stoptime']-$jobs[$jobid]['starttime'];
		$jobs[$jobid]['starttime']='';
		update_option('wpematico_jobs',$jobs);
		break;
	}
}

/* ******************************************* GRABA OPCIONES ******************************************************** */
function wpematico_save_settings() {
	check_admin_referer('wpematico-cfg');
	$cfg=get_option('wpematico'); //Load Settings
	$cfg['mailsndemail']	= sanitize_email($_POST['mailsndemail']);
	$cfg['mailsndname']		= $_POST['mailsndname'];
	$cfg['mailmethod']		= $_POST['mailmethod'];
	$cfg['mailsendmail']	= untrailingslashit(str_replace('//','/',str_replace('\\','/',stripslashes($_POST['mailsendmail']))));
	$cfg['mailsecure']		= $_POST['mailsecure'];
	$cfg['mailhost']		= $_POST['mailhost'];
	$cfg['mailuser']		= $_POST['mailuser'];
	$cfg['mailpass']		= base64_encode($_POST['mailpass']);
	$cfg['disabledashboard']= $_POST['disabledashboard']==1 ? true : false;
	$cfg['enableword2cats']	= $_POST['enableword2cats']==1 ? true : false;
	$cfg['disablewpcron']	= $_POST['disablewpcron']==1 ? true : false;
	$cfg['imgcache']		= $_POST['imgcache']==1 ? true : false;
	$cfg['imgattach']		= $_POST['imgattach']==1 ? true : false;

	if (update_option('wpematico',$cfg)){
		$success_message=__('Settings saved', 'wpematico');
		$wpematico_message.='<div id="message" class="updated fade"><p><strong>'.$success_message.'</strong></p></div>';
	}else{
		$err_message = __('Settings NOT saved:  Something wrong happened.', 'wpematico').' ';
		$wpematico_message.='<div id="message" class="error fade">'.$err_message.'<a href="javascript:history.go(-1);" class="button add-new-h2">'.esc_html__('Go Back').'</a></div>';
	}
	return $wpematico_message;
}

/* ******************************************* GRABA CAMPAÑA ******************************************************** */
function wpematico_save_job() { //Save Campaign settings
	$jobid = (int) $_POST['jobid'];
	check_admin_referer('edit-job');
	$cfg=get_option('wpematico'); //Load Settings
	$jobs=get_option('wpematico_jobs'); //Load Settings

	if (empty($jobid)) { //generate a new id for new job
		if (is_array($jobs)) {
			foreach ($jobs as $jobkey => $jobvalue) {
				if ($jobkey>$heighestid) $heighestid=$jobkey;
			}
			$jobid=$heighestid+1;
		} else {
			$jobid=1;
		}
	}

	$jobs[$jobid]['campaign_posttype']=$_POST['campaign_posttype'];
	$jobs[$jobid]['name']= esc_html($_POST['name']);
	$jobs[$jobid]['activated']= $_POST['activated']==1 ? true : false;
	
		if ($_POST['cronminutes'][0]=='*' or empty($_POST['cronminutes'])) {
			if (!empty($_POST['cronminutes'][1]))
				$_POST['cronminutes']=array('*/'.$_POST['cronminutes'][1]);
			else
				$_POST['cronminutes']=array('*');
		}
		if ($_POST['cronhours'][0]=='*' or empty($_POST['cronhours'])) {
			if (!empty($_POST['cronhours'][1]))
				$_POST['cronhours']=array('*/'.$_POST['cronhours'][1]);
			else
				$_POST['cronhours']=array('*');
		}
		if ($_POST['cronmday'][0]=='*' or empty($_POST['cronmday'])) {
			if (!empty($_POST['cronmday'][1]))
				$_POST['cronmday']=array('*/'.$_POST['cronmday'][1]);
			else
				$_POST['cronmday']=array('*');
		}
		if ($_POST['cronmon'][0]=='*' or empty($_POST['cronmon'])) {
			if (!empty($_POST['cronmon'][1]))
				$_POST['cronmon']=array('*/'.$_POST['cronmon'][1]);
			else
				$_POST['cronmon']=array('*');
		}
		if ($_POST['cronwday'][0]=='*' or empty($_POST['cronwday'])) {
			if (!empty($_POST['cronwday'][1]))
				$_POST['cronwday']=array('*/'.$_POST['cronwday'][1]);
			else
				$_POST['cronwday']=array('*');
		}
	$jobs[$jobid]['cron']=implode(",",$_POST['cronminutes']).' '.implode(",",$_POST['cronhours']).' '.implode(",",$_POST['cronmday']).' '.implode(",",$_POST['cronmon']).' '.implode(",",$_POST['cronwday']);
	$jobs[$jobid]['cronnextrun']=wpematico_cron_next($jobs[$jobid]['cron']);
	// Direccion de e-mail donde enviar los logs
	$jobs[$jobid]['mailaddresslog']=sanitize_email($_POST['mailaddresslog']);
	$jobs[$jobid]['mailerroronly']= $_POST['mailerroronly']==1 ? true : false;
	// Process categories 
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
	  $jobs[$jobid]['campaign_categories']=(array)$_POST['campaign_categories'];
   }
	#Proceso las Words to Category sacando los que estan en blanco
    //campaign_wrd2cat, campaign_wrd2cat_regex, campaign_wrd2cat_category
	if(isset($_POST['campaign_wrd2cat'])) {
		foreach($_POST['campaign_wrd2cat'] as $id => $w2cword) {       
			$word = $_POST['campaign_wrd2cat'][$id];
			$regex = ($_POST['campaign_wrd2cat_regex'][$id]==1) ? true : false ;
			$cases = ($_POST['campaign_wrd2cat_cases'][$id]==1) ? true : false ;
			$w2ccateg = $_POST['campaign_wrd2cat_category'][$id];
			if(!empty($word))  {
				if($regex) 
					if(false === @preg_match($word, '')) {
						$err_message = sprintf(__('There\'s an error with the supplied RegEx expression in word: %s', 'wpematico'),'<br />'.$word).' ';
						$wpematico_message.='<div id="message" class="error fade">'.$err_message.'<a href="javascript:history.go(-1);" class="button add-new-h2">'.esc_html__('Go Back').'</a></div>';
						return $wpematico_message;				
					}
				if(!isset($campaign_wrd2cat)) 
					$campaign_wrd2cat = Array();
				
				$campaign_wrd2cat['word'][]=$word ;
				$campaign_wrd2cat['regex'][]= $regex;
				$campaign_wrd2cat['cases'][]= $cases;
				$campaign_wrd2cat['w2ccateg'][]=$w2ccateg ;
			}
		}
	}
	$jobs[$jobid]['campaign_wrd2cat']=(array)$campaign_wrd2cat ;
	
   
	// Si no hay ningun feed no graba y devuelve mensaje de error
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

	if(!isset($campaign_feeds)) {
		$err_message = str_replace('%1',$jobs[$jobid]['name'],__('Campaign \'%1\' NOT saved: At least one feed URL must be filled.', 'wpematico')).' ';
		$wpematico_message.='<div id="message" class="error fade">'.$err_message.'<a href="javascript:history.go(-1);" class="button add-new-h2">'.esc_html__('Go Back').'</a></div>';
		return $wpematico_message;
	} else {  
      foreach($campaign_feeds as $feed) {
			$simplepie = fetchFeed($feed, true);
			if($simplepie->error()) {
				$err_message = sprintf(__('Feed <strong>%s</strong> could not be parsed.<br />(SimplePie said: %s)', 'wpematico'), $feed, $simplepie->error()).' ';
				$wpematico_message.='<div id="message" class="error fade">'.$err_message.'<a href="javascript:history.go(-1);" class="button add-new-h2">'.esc_html__('Go Back').'</a></div>';
				return $wpematico_message;
			}          
      }
    }

	$jobs[$jobid]['campaign_feeds'] = (array)$campaign_feeds ;
// *** Campaign Options
	$jobs[$jobid]['campaign_max']				= (int)$_POST['campaign_max'];
	$jobs[$jobid]['campaign_author']			= $_POST['campaign_author'];
	$jobs[$jobid]['campaign_linktosource']	= $_POST['campaign_linktosource']==1 ? true : false;
	$jobs[$jobid]['campaign_commentstatus']= $_POST['campaign_commentstatus'];
	$jobs[$jobid]['campaign_allowpings']	= $_POST['campaign_allowpings']==1 ? true : false;

// *** Campaign Images
	$jobs[$jobid]['campaign_imgcache']		= $_POST['campaign_imgcache']==1 ? true : false;
	$jobs[$jobid]['campaign_cancel_imgcache']		= $_POST['campaign_cancel_imgcache']==1 ? true : false;
	if ($cfg['imgcache']) {
		if ($jobs[$jobid]['campaign_cancel_imgcache']) $jobs[$jobid]['campaign_imgcache'] = false;
	}else{
		if ($jobs[$jobid]['campaign_imgcache']) $jobs[$jobid]['campaign_cancel_imgcache'] = false;
	}
	$jobs[$jobid]['campaign_nolinkimg']		= $_POST['campaign_nolinkimg']==1 ? true : false;
	
// *** Campaign Template
	$jobs[$jobid]['campaign_enable_template'] = $_POST['campaign_enable_template']==1 ? true : false;
	if ($jobs[$jobid]['campaign_enable_template'])
		if(isset($_POST['campaign_template']))
			$jobs[$jobid]['campaign_template'] = $_POST['campaign_template'];
		else{
			$jobs[$jobid]['campaign_enable_template'] = false;
			$jobs[$jobid]['campaign_template'] = '';
		}

// *** Campaign Rewrites	
	// Proceso los rewrites sacando los que estan en blanco
	if(isset($_POST['campaign_word_origin'])) {
		foreach($_POST['campaign_word_origin'] as $id => $rewrite) {       
			$origin = $_POST['campaign_word_origin'][$id];
			$regex = $_POST['campaign_word_option_regex'][$id]==1 ? true : false ;
			$rewrite = $_POST['campaign_word_rewrite'][$id];
			$relink = $_POST['campaign_word_relink'][$id];
			if(!empty($origin))  {
				if($regex) 
					if(false === @preg_match($origin, '')) {
						$err_message = sprintf(__('There\'s an error with the supplied RegEx expression in origin: %s', 'wpematico'),'<br />'.$origin).' ';
						$wpematico_message.='<div id="message" class="error fade">'.$err_message.'<a href="javascript:history.go(-1);" class="button add-new-h2">'.esc_html__('Go Back').'</a></div>';
						return $wpematico_message;				
					}
				if(!isset($campaign_rewrites)) 
					$campaign_rewrites = Array();
				
				$campaign_rewrites['origin'][]=$origin ;
				$campaign_rewrites['regex'][]= $regex;
				$campaign_rewrites['rewrite'][]=$rewrite ;
				$campaign_rewrites['relink'][]=$relink ;
			}
		}
	}

	$jobs[$jobid]['campaign_rewrites']=(array)$campaign_rewrites ;

	//unset old vars
	unset($jobs[$jobid]['scheduletime']);
	unset($jobs[$jobid]['scheduleintervaltype']);
	unset($jobs[$jobid]['scheduleintervalteimes']);
	unset($jobs[$jobid]['scheduleinterval']);

	$jobs[$jobid]=wpematico_check_job_vars($jobs[$jobid],$jobid); //check vars and set def.

	//save chages
	update_option('wpematico_jobs',$jobs);
	$_POST['jobid']  = $jobid;
	$success_message = str_replace('%1',$jobs[$jobid]['name'],__('Campaign \'%1\' changes saved.', 'wpematico')).' <a href="admin.php?page=WPeMatico">'.__('Campaigns overview.', 'wpematico').'</a>';
	$wpematico_message.='<div id="message" class="updated fade"><p><strong>'.$success_message.'</strong></p></div>';
	return $wpematico_message;
}
?>