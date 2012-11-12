<?php
//  this is the default template

foreach($myarray as $items) {

if ($pag!==1){ 	
	$total = $total +1;
	if ($maxperPage>0 && $total>=$maxperPage) break;
}

$idnum=$idnum +1;

//  Today and Earlier Script


if ($sortDir==0 && $todaybefore==1){
	
	$from=date("d-m-Y",strtotime('now'));
	$to=date("d-m-Y",$items["mystrdate"]);
	$nodays=(strtotime($to) - strtotime($from))/ (60 * 60 * 24); 


if ($nodays==0){
	

	if ($todayStamp==0){
		$readable.='<span style="'.$testyle.'">Today</span>';
		$todayStamp=1;
		} 
	}

  elseif ($nodays!=0) {
	

		if ($todayStamp==1 || $total==0){

		$readable.= '<span style="'.$testyle.'">Earlier</span>';
			
		$todayStamp=2;
		}
	}
	
}





	
		$readable .=  '<div class="rss-output"><div class="title"><span style="font-size:'.$hdsize.'; font-weight:'.$hdweight.';"><a '.$openWindow.' href='.$items["mylink"].' '.($noFollow==1 ? 'rel=nofollow':'').' style="color:'.$anchorcolor.'">'.$items["mytitle"].'</a></span>';
		
		if ($showmore==1 && $showDesc==1){
			
			$readable .=  ' <a href="javascript:void(0)"><img src="'.$images_url.'/arrow_down.png"/  id="#'.$idnum.'" class="nav-toggle"></a></div>';	
			
		} else{
			
			$readable .=  '</div>';	
		}
			
	if (!empty($items["mydesc"]) && $showDesc==1){
		
		
		
		if ($showmore==1 && $showDesc==1){
			$readable .=  '<div id="'.$idnum.'" style="display:none">';
		}else{
			$readable .=  '<div class="body">';		
		}
		
		
	$readable .=  showexcerpt($items["mydesc"],$descNum,$openWindow,$stripAll,$items["mylink"],$adjustImageSize,$float,$noFollow);
	
	$readable .=  '</div>';	

	
}


	
	if (!empty($items["mystrdate"]) && $showdate==1){
	// $readable .=  '<span style="'.$datestyle.'">'. date_i18n("D, M d, Y g:i:s A",$items["mystrdate"]).'</span><br />';  // use if you want time to show
	$readable .=  '<span class="date" style="'.$datestyle.'">'. date_i18n("D, M d, Y",$items["mystrdate"]).'</span><br />';
	}
		if (!empty($items["myGroup"]) && $showgroup==1){
     $readable .=  '<span class="source" style="font-style:italic;">'.$attribution.''.$items["myGroup"].'</span>';
	}
	 $readable .=  '</div>';
	
	
	
		}
	//  This is the end of the default template
?>