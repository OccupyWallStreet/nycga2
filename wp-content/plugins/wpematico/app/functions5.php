<?PHP
// don't load directly
if ( !defined('ABSPATH') )
	die('-1');
/*
$jobvalue['campaign_posttype']
$jobvalue['activated']
$jobvalue['cron']
$jobvalue['mailaddresslog']
$jobvalue['mailerroronly']
$jobvalue['name']
$jobvalue['campaign_feeds']
$jobvalue['campaign_max']
$jobvalue['postscount']= 0;
$jobvalue['campaign_author']
$jobvalue['campaign_linktosource']
$jobvalue['campaign_commentstatus']
$jobvalue['campaign_allowpings']
$jobvalue['campaign_imgcache']
$jobvalue['campaign_cancel_imgcache']
$jobvalue['campaign_nolinkimg']
$jobvalue['campaign_enable_template']
$jobvalue['campaign_template']
$jobvalue['campaign_rewrites']
$jobvalue['campaign_rewrites']['origin']['search']
$jobvalue['campaign_rewrites']['origin']['regex']
$jobvalue['campaign_rewrites']['rewrite']
$jobvalue['campaign_rewrites']['relink']
*/	
	
	//Checking,upgrade and default job setting
	function wpematico_check_job_vars($jobsettings,$jobid='') {
		global $wpdb;
		if (empty($jobsettings['campaign_posttype']) or !is_string($jobsettings['campaign_posttype']))
			$jobsettings['campaign_posttype']= 'publish';

		if (empty($jobsettings['name']) or !is_string($jobsettings['name']))
			$jobsettings['name']= __('New');

		if (!isset($jobsettings['activated']) or !is_bool($jobsettings['activated']))
			$jobsettings['activated']=false;

			//if (!isset($jobsettings['campaign_categories']) or !is_array($jobsettings['activated']))
			if (!isset($jobsettings['campaign_categories']))
			$jobsettings['campaign_categories']=array();

		if (!isset($jobsettings['campaign_linktosource']) or !is_bool($jobsettings['campaign_linktosource']))
			$jobsettings['campaign_linktosource']=false;

		if (!isset($jobsettings['campaign_commentstatus']) or !is_string($jobsettings['campaign_commentstatus']))
			$jobsettings['campaign_commentstatus']='open';

		if (!isset($jobsettings['campaign_allowpings']) or !is_bool($jobsettings['campaign_allowpings']))
			$jobsettings['campaign_allowpings']=true;

		if (!isset($jobsettings['campaign_max']) or !is_int($jobsettings['campaign_max']))
			$jobsettings['campaign_max']=10;
	// *** Processed posts count
		if (!isset($jobsettings['postscount']) or !is_int($jobsettings['postscount']))
			$jobsettings['postscount']= 0;
		if (!isset($jobsettings['lastpostscount']) or !is_int($jobsettings['lastpostscount']))
			$jobsettings['lastpostscount']= 0;
	// *** Campaign Images
		$jobsettings['campaign_imgcache'] = (!isset($jobsettings['campaign_imgcache']) or !is_bool($jobsettings['campaign_imgcache']) or (!$jobsettings['campaign_imgcache'])==1) ? false : true ;
		$jobsettings['campaign_cancel_imgcache'] = (!isset($jobsettings['campaign_cancel_imgcache']) or !is_bool($jobsettings['campaign_cancel_imgcache']) or (!$jobsettings['campaign_cancel_imgcache'])==1) ? false : true ;
		$jobsettings['campaign_nolinkimg'] = (!isset($jobsettings['campaign_nolinkimg']) or !is_bool($jobsettings['campaign_nolinkimg']) or (!$jobsettings['campaign_nolinkimg'])==1) ? false : true ;
		
		//upgrade old schedule
		if (!isset($jobsettings['cron']) and isset($jobsettings['scheduletime']) and isset($jobsettings['scheduleintervaltype']) and isset($jobsettings['scheduleintervalteimes'])) {  //Upgrade to cron string
			if ($jobsettings['scheduleintervaltype']==60) { //Min
				$jobsettings['cron']='*/'.$jobsettings['scheduleintervalteimes'].' * * * *';
			}
			if ($jobsettings['scheduleintervaltype']==3600) { //Houer
				$jobsettings['cron']=(date('i',$jobsettings['scheduletime'])*1).' */'.$jobsettings['scheduleintervalteimes'].' * * *';
			}
			if ($jobsettings['scheduleintervaltype']==86400) {  //Days
				$jobsettings['cron']=(date('i',$jobsettings['scheduletime'])*1).' '.date('G',$jobsettings['scheduletime']).' */'.$jobsettings['scheduleintervalteimes'].' * *';
			}
		}

		if (!isset($jobsettings['cron']) or !is_string($jobsettings['cron']))
			$jobsettings['cron']='0 3 * * *';
			
		if (!isset($jobsettings['cronnextrun']) or !is_numeric($jobsettings['cronnextrun']))
			$jobsettings['cronnextrun']=wpematico_cron_next($jobsettings['cron']);;
			
		if (!is_string($jobsettings['mailaddresslog']) or false === $pos=strpos($jobsettings['mailaddresslog'],'@') or false === strpos($jobsettings['mailaddresslog'],'.',$pos))
			$jobsettings['mailaddresslog']=get_option('admin_email');

		if (!isset($jobsettings['mailerroronly']) or !is_bool($jobsettings['mailerroronly']))
			$jobsettings['mailerroronly']=true;

		if (!isset($jobsettings['mailefilesize']) or !is_float($jobsettings['mailefilesize']))
			$jobsettings['mailefilesize']=0;

		if (!is_string($jobsettings['mailaddress']) or false === $pos=strpos($jobsettings['mailaddress'],'@') or false === strpos($jobsettings['mailaddress'],'.',$pos))
			$jobsettings['mailaddress']='';

		if (!isset($jobsettings['campaign_enable_template']) or !is_bool($jobsettings['campaign_enable_template']))
			$jobsettings['campaign_enable_template']=false;

		return $jobsettings;
	}	

	
/*********** 	 Funciones para procesar campañas ******************/
/**
* Processes all campaigns
*
*/
function processAll() {
	@set_time_limit(0);    

	$jobs=get_option('wpematico_jobs'); //load jobdata
	foreach($jobs as $job) {
		wpematico_dojob($job['jobid']);
	}
}


?>