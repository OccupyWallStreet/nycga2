<?php

$zipFileName = $PIpath . '/RateData.zip';
$RegularFile = $PIpath . '/data.txt';
$final = outbrain_export_ratings();
if (substr(trim(strtolower($final)),0,5) != "error"){
	
	if (!($handling = fopen($RegularFile, 'w'))){
		die ('error opening the file');
	} elseif (!(fwrite($handling, $final))){
		die ('error writing to file');
	}
	
/*	if( phpversion() >=5 ){	
		$zipError = (!class_exists(ZipArchive));
		if (!$zipError){
			$zip = new ZipArchive();	
			// open archive 
			if ($zip->open($zipFileName, ZIPARCHIVE::CREATE) == TRUE) {
				$zip->addFile($RegularFile, 'RateData.csv') or $zipError = true;        
				$zip->close();
				echo "<meta http-equiv=\"refresh\" content=\"0; url=".$PIurlPath."RateData.zip\" />";
			} else{
				$zipError = true;
			}
		}
	}else{
		//TODO:: use zzip-zip
	}
	
	if ($zipError){
		echo "<meta http-equiv=\"refresh\" content=\"0; url=".$PIurlPath."download_last.php\" />";
	}
*/
	echo "<meta http-equiv=\"refresh\" content=\"0; url=".$PIurlPath."download_last.php\" />";
} else {
	echo "<center><span id='outbrainErrorDiv' style='color:red'><b>$final</b></span></center>";
}
function outbrain_export_ratings(){
	$strValue = "";
	$myposts = get_posts();
	
	$arrOfPostRateOptions = get_option("postratings_ratingsvalue");
	if ((isset($arrOfPostRateOptions)) && (is_array($arrOfPostRateOptions))){
		$count = count($arrOfPostRateOptions);
	}else{
		return "Error : WP Postrating configuration is missing";
	}
	
	foreach($myposts as $post){
		$postratings_postid		= $post->ID;
	
		$postratings_perma 		= get_permalink($postratings_postid);
		$postratings_title		= $post->post_title;
		$postratings_author		= intval($post->post_author);
		$postratings_date		= $post->post_date;

		$postratings_users  	= intval(get_post_meta($postratings_postid, "ratings_users"   , true));
		$postratings_score 		= intval(get_post_meta($postratings_postid, "ratings_score"   , true));
		$postratings_average 	= intval(get_post_meta($postratings_postid, "ratings_average" , true));	
	

		$user_info = get_userdata($postratings_author);
		$postratings_author = $user_info->display_name;
		
		if ($count != 0) {
			$postratings_average = round (($postratings_average / $count) * 5 );
		}
		if ($postratings_average > 5 || $postratings_average < 1 ){
			continue;
		}

		if (isset($postratings_users) && $postratings_users > 0 
				&&  isset($postratings_score) 
					&& $postratings_score > 0 
						&& !empty($postratings_perma)) { 
		
			$strValue = $strValue .  "$postratings_perma,";
			$strValue = $strValue .  "$postratings_title,";
			$strValue = $strValue .  "$postratings_author,";
			$strValue = $strValue .  "$postratings_date,";
		
			$strValue = $strValue .  "$postratings_users,";
			$strValue = $strValue .  "$postratings_score,";
			$strValue = $strValue .  "$postratings_average";

			$strValue = $strValue .  "\n";//AGENT
		}
	} 
	return $strValue;
}


?>