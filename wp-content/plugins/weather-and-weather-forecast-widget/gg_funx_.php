<?php
function GG_funx_calculate_moonphase($term_in){
  $term_out="";
  if($term_in==0 or $term_in==29){$term_out="New";}
  elseif($term_in==7){$term_out="First Quarter";} 
  elseif($term_in==14){$term_out="Full";} 
  elseif($term_in==22){$term_out="Third Quarter";} 
  if($term_out==""){
      $term_in=floor($term_in/29*4);
      if($term_in==3){$term_out="Waning Crescent";}
      elseif($term_in==2){$term_out="Waning Gibbous";}
      elseif($term_in==1){$term_out="Waxing Gibbous";}
      elseif($term_in==0){$term_out="Waxing Crescent";} 
    }
  return $term_out;
}


function GG_funx_cutout($data, $start, $end) {
    $from = strpos($data, $start) + strlen($start);
    if($from === false) {return false;}
    $to = @strpos($data, $end, $from); 
    if($to === false) {return false;} 
    return substr($data, $from, $to-$from);
  }
  
function GG_funx_get_code($code_string){  
  $j=substr_count($code_string,"*");
  $code_array[0][0]=0;
  for($i=1;$i<=$j;$i++){$code_array[$i][0]=strpos($code_string,"*",$code_array[$i-1][0]+1);}
  $code_array[$i][0]=strlen($code_string);
  for($i=0;$i<=$j;$i++){$code_array[$i][1]=str_replace("*","",substr($code_string,$code_array[$i][0],$code_array[$i+1][0]-$code_array[$i][0]));}
  $back=$code_array[rand(0,$j)][1];
  return $back; 
}

function GG_funx_get_content($term_in,$timeout)
{
    if( ini_get('allow_url_fopen') ) 
      {
      $opts = array('http' => array('timeout' => $timeout));
      $context  = stream_context_create($opts);
      $term_out = @file_get_contents($term_in,false,$context);
    }
    else {
      $ch = curl_init();
      curl_setopt ($ch, CURLOPT_URL, $term_in);
      curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
      $term_out = @curl_exec($ch);
      curl_close($ch);  
    }
    return $term_out;
}

function GG_funx_get_weather_data($gg_weather,$key_wun,$location_string_wun,$key_fwo,$location_string_fwo,$opt_provider_preference,$opt_get_better,$imagefolder_check,$imageloc,$imagefolder,$time_corr,$timeout){

        list($wun_help_a,$wun_parsed,$fwo_help_a,$fwo_parsed)=GG_funx_initialize_GG_arrays();        
        if($opt_provider_preference=="wun")
        {
            $url="http://api.wunderground.com/api/".$key_wun."/astronomy/conditions/forecast7day/q/".$location_string_wun.".json";
            $wun_string = GG_funx_get_content($url,$timeout); 
            $wun_parsed = json_decode($wun_string,true);
            //print_r($wun_parsed); 
            $wun_help_a=$wun_parsed['current_observation'];
            if(!$wun_help_a){$gg_weather[0][99][0]="Error";}
            else
            {
              $gg_weather[0][0][0]="";   //Date_mon_weekday
              $gg_weather[0][1][0]=$wun_help_a['display_location']['full'];
              $gg_weather[0][1][1]=$wun_help_a['observation_time'];  //Ort und Land
              $gg_weather[0][2][0]=$wun_help_a['icon'];      //zB chancerain!!!!  CODE
              $gg_weather[0][2][1]=$wun_help_a['icon_url'];
              $gg_weather[0][2][2]=$wun_help_a['condition'];    //"Chance of rain" // "Rain Showers"
              $gg_weather[0][5][0]=$wun_help_a['temp_f']; //aktuelle Temperatur
              $gg_weather[0][5][1]=$wun_help_a['temp_c'];   //aktuelle Temperatur
              $gg_weather[0][6][0]=$wun_help_a['windchill_f'];    //Windchill
              $gg_weather[0][6][1]=$wun_help_a['windchill_c'];
              if($gg_weather[0][6][0]="NA"){$gg_weather[0][6][0]=$gg_weather[0][5][0];$gg_weather[0][6][1]=$gg_weather[0][5][1];}
              $gg_weather[0][7][0]="";    //hi
              $gg_weather[0][7][1]="";    //hi
              $gg_weather[0][8][0]="";    //lo
              $gg_weather[0][8][1]="";    //lo
              if(substr_count($wun_help_a['relative_humidity'],'%')){
                $gg_weather[0][10]=substr($wun_help_a['relative_humidity'],0,strlen($wun_help_a['relative_humidity'])-1);}
              else{$gg_weather[0][10]=$wun_help_a['relative_humidity'];}
              $gg_weather[0][11][0]=$wun_help_a['wind_dir'];    //Text??? West statt W???
              $gg_weather[0][11][1]="";    //Kurz
              $gg_weather[0][11][2]=$wun_help_a['wind_degrees'];    //Degrees
              $gg_weather[0][11][1]=GG_funx_translate_winddirections_degrees($gg_weather[0][11][2]);
              $gg_weather[0][11][3]=$wun_help_a['wind_mph'];    //speed mph
              $gg_weather[0][11][4]=gg_funx_translate_speed($gg_weather[0][11][3],"kmph");    //speed kmh
              $gg_weather[0][11][5]=$wun_help_a['wind_gusts_mph'];    //Gusts  mph
              $gg_weather[0][11][6]=gg_funx_translate_speed($gg_weather[0][11][5],"kmph");;    //Gusts  kmh
              $gg_weather[0][12][1]=$wun_help_a['pressure_mb'];    //Pressure_MB
              $gg_weather[0][12][0]=$wun_help_a['pressure_in'];    //Presssure_IN
              $gg_weather[0][13]=$wun_help_a['pressure_trend'];    //Presssure_trend
              $gg_weather[0][14][0]=$wun_help_a['dewpoint_f'];    //dewpoint f
              $gg_weather[0][14][1]=$wun_help_a['dewpoint_c'];    //dewpoint c
              $gg_weather[0][15][0]=$wun_help_a['visibility_mi']; //visibility
              $gg_weather[0][15][1]=$wun_help_a['visibility_km'];
              $gg_weather[0][16]=$wun_help_a['pop'];           //probABILITY of precipitation 
              $gg_weather[0][17][0]=$wun_help_a['precip_today_inch'];           //amount of precipitation 
              $gg_weather[0][17][1]=$wun_help_a['precip_today_metric'];    
              $gg_weather[0][18]="";           //Cloadcover
              $wun_help_a=$wun_parsed['moon_phase'];
              $gg_weather[0][19][0]=$wun_help_a['percentIlluminated'];
              $gg_weather[0][19][1]=$wun_help_a['ageOfMoon'];
              $gg_weather[0][19][2]=GG_funx_calculate_moonphase($gg_weather[0][19][1]);
              $gg_weather[0][19][3]=$wun_help_a['sunset']['hour'];
              $gg_weather[0][19][4]=$wun_help_a['sunset']['minute'];
              $gg_weather[0][19][5]=$wun_help_a['sunrise']['hour'];
              $gg_weather[0][19][6]=$wun_help_a['sunrise']['minute'];
              $gg_weather[0][19][7]=""; //resesrved for day night flag           
              $wun_help_a=$wun_parsed['forecast']['simpleforecast']['forecastday']['0'];
              $gg_weather[0][0][1]= $wun_help_a['date']['month'];
              $gg_weather[0][0][2]= $wun_help_a['date']['day'];
              $gg_weather[0][0][3]= $wun_help_a['date']['weekday'];   
              $wun_counter=count($wun_parsed['forecast']['simpleforecast']['forecastday']);
              for($i=0;$i<count($wun_parsed['forecast']['simpleforecast']['forecastday']);$i++)
              {
                $wun_help_a=$wun_parsed['forecast']['simpleforecast']['forecastday'][$i];
                $gg_weather[$i+1][0][1]= $wun_help_a['date']['month'];
                $gg_weather[$i+1][0][2]= $wun_help_a['date']['day'];
                if(substr($gg_weather[$i+1][0][2],0,1)=="0"){$gg_weather[$i+1][0][2]=substr($gg_weather[$i+1][0][2],1,1);}
                $gg_weather[$i+1][0][3]= $wun_help_a['date']['weekday'];
                $gg_weather[$i+1][2][0]= $wun_help_a['icon'];
                $gg_weather[$i+1][2][1]= $wun_help_a['icon_url'];
                $gg_weather[$i+1][2][2]="";    
                $gg_weather[$i+1][2][3]="";    
                $gg_weather[$i+1][2][4]="";  
                $gg_weather[$i+1][2][5]="";
                $gg_weather[$i+1][2][6]="";
                $gg_weather[$i+1][7][0]= $wun_help_a['high']['fahrenheit'];
                $gg_weather[$i+1][7][1]= $wun_help_a['high']['celsius'];
                $gg_weather[$i+1][8][0]= $wun_help_a['low']['fahrenheit'];
                $gg_weather[$i+1][8][1]= $wun_help_a['low']['celsius'];
                $gg_weather[$i+1][10]=""; //'relative_humidity'];}}
                $gg_weather[$i+1][11][0]=""; //'wind_dir'];}    //Text??? West statt W???
                $gg_weather[$i+1][11][1]="";    //Kurz   WNW for example
                $gg_weather[$i+1][11][2]="";   //Degrees
                $gg_weather[$i+1][11][3]="";   //speed mph
                $gg_weather[$i+1][11][4]="";  //speed kmh
                $gg_weather[$i+1][11][5]="";  //Gusts  mph
                $gg_weather[$i+1][11][6]=""; //Gusts  kmh
                $gg_weather[$i+1][16]=$wun_help_a['pop'];
                $gg_weather[$i+1][17][0]=$wun_help_a['precip_today_inch'];           //amount of precipitation 
                $gg_weather[$i+1][17][1]=$wun_help_a['precip_today_metric'];  
              }   
              $j=0;
              for($i=0;$i<count($wun_parsed['forecast']['txt_forecast']['forecastday'])-1;$i++)
                {
                $wun_help_a=$wun_parsed['forecast']['txt_forecast']['forecastday'][$i];
                $pos=strpos($wun_help_a['title'],"ight",0);
                  if($pos>0){
                  $gg_weather[-$j+$i][4][0]= $wun_help_a['fcttext'];
                  $gg_weather[-$j+$i][4][1]= $wun_help_a['fcttext_metric'];
                  $j=$j+1;
                  }
                  else
                  {
                  //echo "Old: ".$gg_weather[-$j+$i+1][2][0];
                  $gg_weather[-$j+$i+1][3][0]= $wun_help_a['fcttext'];
                  $gg_weather[-$j+$i+1][3][1]= $wun_help_a['fcttext_metric'];
                  $gg_weather[-$j+$i+1][2][0]= $wun_help_a['icon'];
                  $gg_weather[-$j+$i+1][2][1]= $wun_help_a['icon_url'];
                  ///echo "New: ".$wun_help_a['icon']." ".$gg_weather[-$j+$i+1][3][0]."<br />";
                  }
                }
              if($gg_weather[0][2][0]==""){  //there are some stations which dont deliver an actual weather image -> take it from the fc_text, if this fails as well from today
                $wun_help_a=$wun_parsed['forecast']['txt_forecast']['forecastday'][0];
                $gg_weather[0][2][0]=str_replace("nt_","",$wun_help_a['icon']);
                $gg_weather[0][2][1]=$wun_help_a['icon_url'];
                $gg_weather[0][2][2]=$wun_help_a['conditions'];  
              }
              if($gg_weather[0][2][0]==""){  //there are some stations which dont deliver an actual weather image -> take it from the fc_text, if this fails as well from today
                $wun_help_a=$wun_parsed['forecast']['txt_forecast']['forecastday'][1];
                $gg_weather[0][2][0]=str_replace("nt_","",$wun_help_a['icon']);
                $gg_weather[0][2][1]=$wun_help_a['icon_url'];
                $gg_weather[0][2][2]=$wun_help_a['conditions'];  
              }
            }
        } // end wu
        if($opt_provider_preference=="fwo")
        {
            $url=  "http://free.worldweatheronline.com/feed/weather.ashx?q=".$location_string_fwo."&format=json&num_of_days=5&key=".$key_fwo;
            $fwo_string = GG_funx_get_content($url,$timeout); 
            $fwo_parsed = json_decode($fwo_string,true);
            $fwo_help_a=$fwo_parsed['data']['request'];
            $gg_weather[0][1][0]=  $fwo_help_a['0']['query'];  //Ort und Land
            $fwo_help_a=$fwo_parsed['data']['current_condition'];
            if(!$fwo_help_a){$gg_weather[0][99][1]="Error";}
            else
            {
              $gg_weather[0][1][1]=$fwo_help_a['0']['observation_time'];
              $gg_weather[0][2][0]=$fwo_help_a['0']['weatherCode'];
              $gg_weather[0][2][1]=$fwo_help_a['0']['weatherIconUrl']['0']['value'];
              $gg_weather[0][2][2]=$fwo_help_a['0']['weatherDesc']['0']['value'];
              $gg_weather[0][5][0]=$fwo_help_a['0']['temp_F']; //aktuelle Temperatur
              $gg_weather[0][6][1]=$fwo_help_a['0']['temp_C'];   //aktuelle Temperatur
              $gg_weather[0][6][0]=$fwo_help_a['0']['temp_F']; //aktuelle Temperatur
              $gg_weather[0][5][1]=$fwo_help_a['0']['temp_C']; 
              $gg_weather[0][10]=$fwo_help_a['0']['humidity'];
              $gg_weather[0][11][1]=$fwo_help_a['0']['winddir16Point'];    //Kurz
              $gg_weather[0][11][2]=$fwo_help_a['0']['winddirDegree'];    //Degrees
              $gg_weather[0][11][3]=$fwo_help_a['0']['windspeedMiles'];    //Speed  mph
              $gg_weather[0][11][4]=$fwo_help_a['0']['windspeedKmph'];    //speed  kmmh
              $gg_weather[0][11][5]="";    //Gusts  mph
              $gg_weather[0][11][6]="";    //Gusts  kmh
              $gg_weather[0][12][1]=$fwo_help_a['0']['pressure'];    //Pressure_MB
              $gg_weather[0][12][0]=GG_funx_translate_pressure($gg_weather[0][12][1],"in","xx");    //Presssure_IN
              $gg_weather[0][15][1]=$fwo_help_a['0']['visibility'];
              $gg_weather[0][17][1]=$fwo_help_a['0']['precipMM'];
              $gg_weather[0][17][0]=GG_funx_translate_inch($gg_weather[0][17][1],"m");             //amount of precipitation 
              $gg_weather[0][18]=$fwo_help_a['0']['cloudcover'];           //Cloadcover            
              for($i=0;$i<count($fwo_parsed['data']['weather']);$i++)
              {
                $fwo_help_a=$fwo_parsed['data']['weather'][$i];
                $gg_weather[$i+1][0][0]= $fwo_help_a['date'];      
                $date=mktime(0,0,0,substr($gg_weather[$i+1][0][0],5,2),substr($gg_weather[$i+1][0][0],8,2),substr($gg_weather[$i+1][0][0],0,4));
                $gg_weather[$i+1][0][1]=substr($gg_weather[$i+1][0][0],5,2);
                $gg_weather[$i+1][0][2]= substr($gg_weather[$i+1][0][0],8,2);
                if(substr($gg_weather[$i+1][0][2],0,1)=="0"){$gg_weather[$i+1][0][2]=substr($gg_weather[$i+1][0][2],1,1);}
                $gg_weather[$i+1][0][3]=date("l",$date);
                $gg_weather[$i+1][2][0]=$fwo_help_a['weatherCode'];
                $gg_weather[$i+1][2][1]= $fwo_help_a['weatherIconUrl']['0']['value'];
                $gg_weather[$i+1][2][2]= $fwo_help_a['weatherDesc']['0']['value'];
                $gg_weather[$i+1][2][3]="";  
                $gg_weather[$i+1][2][4]="";  
                $gg_weather[$i+1][2][5]="";
                $gg_weather[$i+1][2][6]="";
                $gg_weather[$i+1][3][0]="";  
                $gg_weather[$i+1][3][1]="";  
                $gg_weather[$i+1][4][0]="";
                $gg_weather[$i+1][4][1]="";
                $gg_weather[$i+1][7][0]= $fwo_help_a['tempMaxF'];
                $gg_weather[$i+1][7][1]= $fwo_help_a['tempMaxC'];
                $gg_weather[$i+1][8][0]= $fwo_help_a['tempMinF'];
                $gg_weather[$i+1][8][1]= $fwo_help_a['tempMinC'];
                $gg_weather[$i+1][11][0]=""; //'wind_dir'];}    //Text??? West statt W???
                $gg_weather[$i+1][11][1]=$fwo_help_a['winddir16Point'];    //Kurz
                $gg_weather[$i+1][11][2]=$fwo_help_a['winddirDegree'];    //Degrees
                $gg_weather[$i+1][11][3]=$fwo_help_a['windspeedMiles'];    //Speed  mph
                $gg_weather[$i+1][11][4]=$fwo_help_a['windspeedKmph'];
                $gg_weather[$i+1][11][5]="";  //Gusts  mph
                $gg_weather[$i+1][11][6]=""; //Gusts  kmh
                $gg_weather[$i+1][16]="";
                $gg_weather[$i+1][17][1]=$fwo_help_a['precipMM'];
                $gg_weather[$i+1][17][0]=GG_funx_translate_inch($gg_weather[$i+1][17][1],"m");                                           
              }   
                $gg_weather[0][0][1]=$gg_weather[1][0][1];
                $gg_weather[0][0][2]=$gg_weather[1][0][2]; 
                $gg_weather[0][0][3]=$gg_weather[1][0][3];
          }           
        } //end fwo       
        if(!$gg_weather[0][99][0] and !$gg_weather[0][99][1]){
          
          if($opt_get_better=="checked")
           {//echo "if opt_better";              
              if($opt_provider_preference=="wun"){
                $url=  "http://free.worldweatheronline.com/feed/weather.ashx?q=".$location_string_fwo."&format=json&num_of_days=5&key=".$key_fwo;
                $fwo_string = GG_funx_get_content($url,$timeout); 
                $fwo_parsed = json_decode($fwo_string,true);
                $fwo_help_a=$fwo_parsed['data']['request'];
                if($gg_weather[0][1][0]=="" or !$gg_weather[0][1][0]){$gg_weather[0][1]=  $fwo_help_a['0']['query'];}  //Ort und Land
                $fwo_help_a=$fwo_parsed['data']['current_condition'];
                if($gg_weather[0][1][1]=="") {$gg_weather[0][1][1]=  $fwo_help_a['0']['observation_time'];}
                if($gg_weather[0][2][0]=="") {$gg_weather[0][2][0]=$fwo_help_a['0']['weatherCode'];}
                if($gg_weather[0][2][1]=="") {$gg_weather[0][2][1]=$fwo_help_a['0']['weatherIconUrl']['0']['value'];}
                if($gg_weather[0][2][2]=="") {$gg_weather[0][2][2]=$fwo_help_a['0']['weatherDesc']['0']['value'];}
                if($gg_weather[0][5][0]=="") {$gg_weather[0][5][0]=$fwo_help_a['0']['temp_F'];} //aktuelle Temperatur
                if($gg_weather[0][5][1]=="") {$gg_weather[0][5][1]=$fwo_help_a['0']['temp_C'];}  //aktuelle Temperatur
                if($gg_weather[0][6][0]=="") {$gg_weather[0][6][0]=$fwo_help_a['0']['temp_F'];} //aktuelle Temperatur
                if($gg_weather[0][6][1]=="") {$gg_weather[0][6][1]=$fwo_help_a['0']['temp_C'];}
                if($gg_weather[0][10]==""){$gg_weather[0][10]=$fwo_help_a['0']['humidity'];}
                if($gg_weather[0][11][1]==""){$gg_weather[0][11][0]=$fwo_help_a['0']['winddir16Point'];}    //Kurz
                if($gg_weather[0][11][1]==""){$gg_weather[0][11][1]=$fwo_help_a['0']['winddir16Point'];}    //Kurz
                if($gg_weather[0][11][2]==""){$gg_weather[0][11][2]=$fwo_help_a['0']['winddirDegree'];}    //Degrees
                if($gg_weather[0][11][3]==""){$gg_weather[0][11][3]=$fwo_help_a['0']['windspeedMiles'];}    //Speed  mph
                if($gg_weather[0][11][4]==""){$gg_weather[0][11][4]=$fwo_help_a['0']['windspeedKmph'];}    //speed  kmmh
                if($gg_weather[0][12][1]==""){$gg_weather[0][12][1]=$fwo_help_a['0']['pressure'];}    //Pressure_MB
                if($gg_weather[0][12][0]==""){$gg_weather[0][12][0]=GG_funx_translate_pressure($gg_weather[0][12][0],"in","xx");}    //Presssure_IN
                if($gg_weather[0][15][1]==""){$gg_weather[0][15][1]=$fwo_help_a['0']['visibility'];}
                if($gg_weather[0][17][1]==""){$gg_weather[0][17][1]=$fwo_help_a['0']['precipMM'];}            //amount of precipitation 
                $gg_weather[0][17][0]=GG_funx_translate_inch($gg_weather[$i+1][17][1],"m");
                if($gg_weather[0][18]==""){$gg_weather[0][18]=$fwo_help_a['0']['cloudcover'];}           //Cloadcover 
                for($i=0;$i<count($fwo_parsed['data']['weather']);$i++)
                {
                  $fwo_help_a=$fwo_parsed['data']['weather'][$i];
                  $gg_weather[$i+1][0][0]= $fwo_help_a['date'];      
                  $date=mktime(0,0,0,substr($gg_weather[$i+1][0][0],5,2),substr($gg_weather[$i+1][0][0],8,2),substr($gg_weather[$i+1][0][0],0,4));
                  if($gg_weather[$i+1][0][1]==""){$gg_weather[$i+1][0][1]=substr($gg_weather[$i+1][0][0],5,2); }
                  if($gg_weather[$i+1][0][2]==""){$gg_weather[$i+1][0][2]= substr($gg_weather[$i+1][0][0],8,2);}
                  if($gg_weather[$i+1][0][3]==""){$gg_weather[$i+1][0][3]=date("l",$date);}
                  if($gg_weather[$i+1][7][0]==""){$gg_weather[$i+1][7][0]= $fwo_help_a['tempMaxF'];}
                  if($gg_weather[$i+1][7][1]==""){$gg_weather[$i+1][7][1]= $fwo_help_a['tempMaxC'];}
                  if($gg_weather[$i+1][8][0]==""){$gg_weather[$i+1][8][0]= $fwo_help_a['tempMinF'];}
                  if($gg_weather[$i+1][8][1]==""){$gg_weather[$i+1][8][1]= $fwo_help_a['tempMinC'];}
                  if($gg_weather[$i+1][2][0]==""){$gg_weather[$i+1][2][0]= $fwo_help_a['weatherCode'];}
                  if($gg_weather[$i+1][2][1]==""){$gg_weather[$i+1][2][1]= $fwo_help_a['weatherIconUrl']['0']['value'];}
                  if($gg_weather[$i+1][2][2]==""){$gg_weather[$i+1][2][2]=$fwo_help_a['weatherDesc']['0']['value'];}
                  if($gg_weather[$i+1][11][1]==""){$gg_weather[$i+1][11][1]=$fwo_help_a['winddir16Point'];}    //Kurz
                  if($gg_weather[$i+1][11][2]==""){$gg_weather[$i+1][11][2]=$fwo_help_a['winddirDegree'];}    //Degrees
                  if($gg_weather[$i+1][11][3]==""){$gg_weather[$i+1][11][3]=$fwo_help_a['windspeedMiles'];}    //Speed  mph
                  if($gg_weather[$i+1][11][4]==""){$gg_weather[$i+1][11][4]=$fwo_help_a['windspeedKmph'];}
                  if($gg_weather[$i+1][17][1]==""){$gg_weather[$i+1][17][1]=$fwo_help_a['precipMM'];}
                  $gg_weather[$i+1][17][0]=GG_funx_translate_inch($gg_weather[$i+1][17][1],"m");
                  if($gg_weather[0][18]==""){$gg_weather[0][18]=$fwo_help_a[0]['cloudcover'];}        
                }   
              if($gg_weather[0][0][1]==""){$gg_weather[0][0][1]=$gg_weather[1][0][1];}
              if($gg_weather[0][0][2]==""){$gg_weather[0][0][2]=$gg_weather[1][0][2];} 
              if($gg_weather[0][0][3]==""){$gg_weather[0][0][3]=$gg_weather[1][0][3];}         
            } //end fwo get better
            if($opt_provider_preference=="fwo"){     
                  $url="http://api.wunderground.com/api/".$key_wun."/astronomy/conditions/forecast7day/q/".$location_string_wun.".json";
                  $wun_string = GG_funx_get_content($url,$timeout); 
                  $wun_parsed = json_decode($wun_string,true);
                  $wun_help_a=$wun_parsed['current_observation'];
                  if($gg_weather[0][1][0]==""){$gg_weather[0][1]=$wun_help_a['display_location']['full'];}
                  if($gg_weather[0][1][1]==""){$gg_weather[0][1][1]=$wun_help_a['observation_time'];}
                  if($gg_weather[0][2][0]==""){$gg_weather[0][2][0]=$wun_help_a['icon'];}      //zB chancerain!!!!
                  if($gg_weather[0][2][1]==""){$gg_weather[0][2][1]=$wun_help_a['icon_url'];}
                  if($gg_weather[0][2][2]==""){$gg_weather[0][2][2]=$wun_help_a['condition'];}    //"Chance of rain" // "Rain Showers"
                  if($gg_weather[0][5][0]==""){$gg_weather[0][5][0]=$wun_help_a['temp_f'];} //aktuelle Temperatur
                  if($gg_weather[0][5][1]==""){$gg_weather[0][5][1]=$wun_help_a['temp_c'];}   //aktuelle Temperatur
                  if($gg_weather[0][6][0]==""){$gg_weather[0][6][0]=$wun_help_a['windchill_c'];}    //Windchill
                  if($gg_weather[0][6][1]==""){$gg_weather[0][6][1]=$wun_help_a['windchill_f'];}  
                  if($gg_weather[0][10]==""){  if(substr_count($wun_help_a['relative_humidity'],'%')){
                      $gg_weather[0][10]=substr($wun_help_a['relative_humidity'],0,strlen($wun_help_a['relative_humidity'])-1);}
                    else{$gg_weather[0][10]=$wun_help_a['relative_humidity'];}}
                  if($gg_weather[0][11][0]==""){$gg_weather[0][11][0]=$wun_help_a['wind_dir'];}    //Text??? West statt W???
                  if($gg_weather[0][11][2]==""){$gg_weather[0][11][2]=$wun_help_a['wind_degrees'];}    //Degrees
                  if($gg_weather[0][11][1]==""){$gg_weather[0][11][1]=GG_funx_translate_winddirections_degrees($gg_weather[0][11][2]);}
                  if($gg_weather[0][11][3]==""){$gg_weather[0][11][3]=$wun_help_a['wind_mph'];}    //speed mph
                  if($gg_weather[0][11][4]==""){$gg_weather[0][11][4]=gg_funx_translate_speed($gg_weather[0][11][3],"kmph");}    //speed kmh
                  if($gg_weather[0][11][5]==""){$gg_weather[0][11][5]=$wun_help_a['wind_gusts_mph'];}    //Gusts  mph
                  if($gg_weather[0][11][6]==""){$gg_weather[0][11][6]=gg_funx_translate_speed($gg_weather[0][11][5],"kmph");}    //Gusts  kmh
                  if($gg_weather[0][12][1]==""){$gg_weather[0][12][1]=$wun_help_a['pressure_mb'];}    //Pressure_MB
                  if($gg_weather[0][12][0]==""){$gg_weather[0][12][0]=$wun_help_a['pressure_in'];}    //Presssure_IN
                  if($gg_weather[0][13]==""){$gg_weather[0][13]=$wun_help_a['pressure_trend'];}    //Presssure_trend
                  if($gg_weather[0][14][0]==""){$gg_weather[0][14][0]=$wun_help_a['dewpoint_f'];}    //dewpoint f
                  if($gg_weather[0][14][1]==""){$gg_weather[0][14][1]=$wun_help_a['dewpoint_c'];}    //dewpoint c
                  if($gg_weather[0][15][0]==""){$gg_weather[0][15][0]=$wun_help_a['visibility_mi'];} //visibility
                  if($gg_weather[0][15][1]==""){$gg_weather[0][15][1]=$wun_help_a['visibility_km'];}
                  if($gg_weather[0][16]==""){$gg_weather[0][16]=$wun_help_a['pop'];}           //probABILITY of precipitation 
                  $wun_help_a=$wun_parsed['moon_phase'];
                  if($gg_weather[0][19][0]==""){$gg_weather[0][19][0]=$wun_help_a['percentIlluminated'];}
                  if($gg_weather[0][19][1]==""){$gg_weather[0][19][1]=$wun_help_a['ageOfMoon'];}
                  if($gg_weather[0][19][2]==""){$gg_weather[0][19][2]=GG_funx_calculate_moonphase($gg_weather[0][19][1]);}
                  if($gg_weather[0][19][3]==""){$gg_weather[0][19][3]=$wun_help_a['sunset']['hour'];}
                  if($gg_weather[0][19][4]==""){$gg_weather[0][19][4]=$wun_help_a['sunset']['minute'];}
                  if($gg_weather[0][19][5]==""){$gg_weather[0][19][5]=$wun_help_a['sunrise']['hour'];}
                  if($gg_weather[0][19][6]==""){$gg_weather[0][19][6]=$wun_help_a['sunrise']['minute'];}           
                  $wun_help_a=$wun_parsed['forecast']['simpleforecast']['forecastday'][0];
                  if($gg_weather[0][0][0]==""){$gg_weather[0][0][0]= $wun_help_a['date']['month'];}
                  if($gg_weather[0][0][1]==""){$gg_weather[0][0][1]= $wun_help_a['date']['day'];}
                  if($gg_weather[0][0][2]==""){$gg_weather[0][0][2]= $wun_help_a['date']['weekday'];}   
                  $wun_counter=count($wun_parsed['forecast']['simpleforecast']['forecastday']);
                  for($i=0;$i<count($wun_parsed['forecast']['simpleforecast']['forecastday']);$i++)
                  {
                      $wun_help_a=$wun_parsed['forecast']['simpleforecast']['forecastday'][$i];
                      if($gg_weather[$i+1][0][1]==""){$gg_weather[$i+1][0][1]= $wun_help_a['date']['month'];}
                      if($gg_weather[$i+1][0][2]==""){$gg_weather[$i+1][0][2]= $wun_help_a['date']['day'];}
                      if($gg_weather[$i+1][0][3]==""){$gg_weather[$i+1][0][3]= $wun_help_a['date']['weekday'];}
                      if($gg_weather[$i+1][7][0]==""){$gg_weather[$i+1][7][0]= $wun_help_a['high']['fahrenheit'];}
                      if($gg_weather[$i+1][7][1]==""){$gg_weather[$i+1][7][1]= $wun_help_a['high']['celsius'];}
                      if($gg_weather[$i+1][8][0]==""){$gg_weather[$i+1][8][0]= $wun_help_a['low']['fahrenheit'];}
                      if($gg_weather[$i+1][8][1]==""){$gg_weather[$i+1][8][1]= $wun_help_a['low']['celsius'];}
                      if($gg_weather[$i+1][2][0]==""){$gg_weather[$i+1][2][0]= $wun_help_a['icon'];}
                      if($gg_weather[$i+1][2][1]==""){$gg_weather[$i+1][2][1]= $wun_help_a['icon_url'];}
                      if($gg_weather[$i+1][16]==""){$gg_weather[$i+1][16]=$wun_help_a['pop'];} 
                  }   
                  $j=0;
                  for($i=0;$i<count($wun_parsed['forecast']['txt_forecast']['forecastday'])-1;$i++)
                  {             
                      $wun_help_a=$wun_parsed['forecast']['txt_forecast']['forecastday'][$i];
                      $pos=strpos($wun_help_a['title'],"ight",0);
                        if($pos>0){
                        $gg_weather[-$j+$i][4][0]= $wun_help_a['fcttext'];
                        $gg_weather[-$j+$i][4][1]= $wun_help_a['fcttext_metric'];
                        $j=$j+1;
                        }
                        else
                        {
                        $gg_weather[-$j+$i+1][3][0]= $wun_help_a['fcttext'];
                        $gg_weather[-$j+$i+1][3][1]= $wun_help_a['fcttext_metric'];
                        }
                }
            }  //end wu get better
        } //end get better
        $gg_weather[0][19][7]="day";
        $pos1=strpos($gg_weather[0][2][1],"night");
        $pos2=strpos($gg_weather[0][2][1],"nt_");
        $pos=$pos1+$pos2;       
        if($pos>0){
          $gg_weather[0][19][7]="night";
        }    
        $term_out=GG_funx_translate_weather_code_into_icon($gg_weather[0][2][0],$gg_weather[0][19][7]);        
        $gg_weather[0][2][3] = $term_out[0];    
        $gg_weather[0][2][4] = $term_out[1];
        if($gg_weather[0][2][2]==""){$gg_weather[0][2][2]=$gg_weather[0][2][4];}
        if($imagefolder_check=="WeatherCom"){    //Image for actual
          $gg_weather[0][2][5] = $imageloc.$imagefolder."93/".$gg_weather[0][2][3].'.png';}
        else{
          $gg_weather[0][2][5] = $imageloc.$imagefolder.$gg_weather[0][2][3].'.png';}
        for($i=1;$i<=count($gg_weather)-1;$i++){
          $term_out=GG_funx_translate_weather_code_into_icon($gg_weather[$i][2][0],"day");
          $gg_weather[$i][2][3] = $term_out[0];
          $gg_weather[$i][2][4] = $term_out[1]; 
          if($imagefolder_check=="WeatherCom"){  //Images for forecast
              $gg_weather[$i][2][5] = $imageloc.$imagefolder."61/".$gg_weather[$i][2][3].'.png';
              $gg_weather[$i][2][6] = $imageloc.$imagefolder."31/".$gg_weather[$i][2][3].'.png';}
          else{
              $gg_weather[$i][2][5] = $imageloc.$imagefolder.$gg_weather[$i][2][3].'.png';
              $gg_weather[$i][2][6] = $imageloc.$imagefolder.$gg_weather[$i][2][3].'.png';}
        
          }
        }
        return $gg_weather;
}

function GG_funx_get_sun_moon_text($gg_weather,$opt_language,$opt_language_index,$opt_auto_location_select,$time_corr,$args){
      $now = time();    
      $sr_hour=$gg_weather[0][19][5];
      $sr_minute=$gg_weather[0][19][6];
      $sunrise = mktime($sr_hour,$sr_minute,0,strftime("%m",$now),strftime("%d",$now),strftime("%y",$now));     
      if (str_replace("-","",$time_corr) != $time_corr)
      {
        $time_corr_sign="neg";
        $time_corr_flag=-1;
        $time_corr_alt= str_replace("-","",$time_corr);
      }
      else
      {
        $time_corr_sign="pos";
        $time_corr_flag=1;
        $time_corr_alt=$time_corr;
      }
      $check=strpos($time_corr_alt,'.');
      $time_corr_alt = str_replace(".","",$time_corr_alt);
      if ($check===false){
        $time_corr_hrs=$time_corr_alt;
        $time_corr_min=0;
      }
      else
      {
        $time_corr_hrs = substr($time_corr_alt,0,$check);
        $time_corr_min = round((substr($time_corr_alt,$check,strlen($time_corr_alt)-$check))*60/pow(10,strlen($time_corr_alt)-$check),2);
      }      
      $ss_hour=$gg_weather[0][19][3];
			$ss_hour=$ss_hour;
			$ss_minute=$gg_weather[0][19][4];
      $sunset = mktime($ss_hour,$ss_minute,0,strftime("%m",$now),strftime("%d",$now),strftime("%y",$now));
			$daylight_time=date("H:i",$sunset-$sunrise);
      $daylight_left=date("H:i",$sunset-$now-mktime($time_corr_hrs*$time_corr_flag,$time_corr_min*$time_corr_flag,0,0,0,0));
      $flag_day_night="night";
      $now_min=60*(int)substr(date("H:i",$now),0,2)+$time_corr_hrs*60+$time_corr_min+(int)substr(date("H:i",$now),3,2);
      $ss_min=$ss_hour*60+$ss_minute;
      $sr_min=$sr_hour*60+$sr_minute;
      if($now_min>=$sr_min and $now_min<=$ss_min){$flag_day_night="day";}   
      $night_left=date("H:i",$sunrise-$now-mktime($time_corr_hrs*$time_corr_flag,$time_corr_min*$time_corr_flag,0,0,0,0));
      $sunset =date("H:i",$sunset);
      $sunrise =date("H:i",$sunrise);
      $now =date("H:i",$now);
      if($gg_weather[0][19][3]==""){$flag_day_night="unknown";}
      if($gg_weather[0][19][3]<>""){
        $term_out=GG_funx_translate_array("Sunrise at",$opt_language)." ".$sunrise." ".GG_funx_translate_array("hrs",$opt_language)." ";
        if($opt_auto_location_select<>"checked" and !$args['flag']){
          if ($flag_day_night == "night"){
            $term_out=$term_out." (".GG_funx_translate_array("in",$opt_language)." ".$night_left."h) ";}
        }
        $term_out=$term_out." - ".GG_funx_translate_array("Sunset at",$opt_language)." ".$sunset." ".GG_funx_translate_array("hrs",$opt_language); 
        if($opt_auto_location_select<>"checked" and !$args['flag']){
          if ($flag_day_night == "day"){$term_out=$term_out." (".GG_funx_translate_array("in",$opt_language)." ".$daylight_left."h) ";}
        }
        $term_out=$term_out." - ".GG_funx_translate_array("Length of day",$opt_language).": ".$daylight_time."h - ";
      }
       if($gg_weather[0][19][2]<>""){
        $term_out=$term_out.GG_funx_translate_array("Moonphase",$opt_language).": ".GG_funx_translate_moonphase($gg_weather[0][19][2],$opt_language_index);
      }
      return array($term_out,$flag_day_night);     
}

function GG_funx_initialize_GG_arrays()

{
        $wun_parsed=array();
        $wun_help_a=array();
        $fwo_parsed=array();
        $fwo_help_a=array();
        $wun_parsed['current_observation']="";
        $wun_parsed['moon_phase']="";
        $wun_help_a['display_location']['full']="";
        $wun_help_a['observation_time']="";
        $wun_help_a['icon_url']="";
        $wun_help_a['condition']="";
        $wun_help_a['temp_f']=""; 
        $wun_help_a['temp_c']="";
        $wun_help_a['windchill_f']="";
        $wun_help_a['windchill_c']="";
        $wun_help_a['relative_humidity']="";
        $wun_help_a['wind_dir']="";
        $wun_help_a['wind_mph']="";
        $wun_help_a['wind_gusts_mph']="";
        $wun_help_a['pressure_mb']="";  
        $wun_help_a['pressure_in']="";   
        $wun_help_a['pressure_trend']="";  
        $wun_help_a['dewpoint_f']="";    
        $wun_help_a['dewpoint_c']="";   
        $wun_help_a['visibility_mi']=""; 
        $wun_help_a['visibility_km']="";
        $wun_help_a['pop']="";           
        $wun_help_a['precip_today_inch']="";           
        $wun_help_a['precip_today_metric']="";  
        $wun_help_a['percentIlluminated']="";
        $wun_help_a['ageOfMoon']="";
        $wun_help_a['sunset']="";
        $wun_help_a['sunset']['hour']="";
        $wun_help_a['sunset']['minute']="";
        $wun_help_a['sunrise']['hour']="";
        $wun_help_a['sunrise']['minute']="";
        $wun_parsed['forecast']="";
        $wun_parsed['forecast']['simpleforecast']="";
        $wun_parsed['forecast']['simpleforecast']['forecastday']="";
        $wun_parsed['forecast']['simpleforecast']['forecastday']['0']="";
        $wun_help_a['date']="";
        $wun_help_a['date']['month']="";
        $wun_help_a['date']['day']="";
        $wun_help_a['date']['weekday']="";
        $wun_help_a['high']="";   
        $wun_help_a['high']['fahrenheit']="";
        $wun_help_a['high']['celsius']="";
        $wun_help_a['low']="";
        $wun_help_a['low']['fahrenheit']="";
        $wun_help_a['low']['celsius']="";
        $wun_help_a['pop']="";
        $wun_help_a['precip_today_inch']="";          
        $wun_help_a['precip_today_metric']=""; 
        $wun_parsed['forecast']['txt_forecast']="";
        $wun_parsed['forecast']['txt_forecast']['forecastday']=""; 
        $wun_parsed['forecast']['txt_forecast']['forecastday'][1]="";
        $wun_help_a['fcttext']="";
        $wun_help_a['title']="";
        $wun_help_a['fcttext_metric']="";
        $fwo_parsed['data']="";
        $fwo_parsed['data']['request']="";
        $fwo_help_a['0']="";
        $fwo_help_a['0']['query']="";
        $fwo_parsed['data']['current_condition']="";
        $fwo_help_a['0']['observation_time']="";
        $fwo_help_a['0']['weatherCode']="";
        $fwo_help_a['0']['weatherIconUrl']="";
        $fwo_help_a['0']['weatherIconUrl']['0']="";
        $fwo_help_a['0']['weatherIconUrl']['0']['value']="";
        $fwo_help_a['0']['weatherDesc']="";
        $fwo_help_a['0']['weatherDesc']['0']="";
        $fwo_help_a['0']['weatherDesc']['0']['value']="";
        $fwo_help_a['0']['temp_F']="";
        $fwo_help_a['0']['temp_C']="";
        $fwo_help_a['0']['temp_F']="";
        $fwo_help_a['0']['temp_C']=""; 
        $fwo_help_a['0']['humidity']="";
        $fwo_help_a['0']['winddir16Point']="";
        $fwo_help_a['0']['winddirDegree']="";
        $fwo_help_a['0']['windspeedMiles']="";
        $fwo_help_a['0']['windspeedKmph']="";
        $fwo_help_a['0']['pressure']="";
        $fwo_help_a['0']['visibility']="";
        $fwo_help_a['0']['precipMM']="";
        $fwo_help_a['0']['cloudcover']="";
        $fwo_parsed['data']['weather']="";
        $fwo_help_a['date']="";
        $fwo_help_a['weatherCode']="";
        $fwo_help_a['weatherIconUrl']="";
        $fwo_help_a['weatherIconUrl']['0']="";
        $fwo_help_a['weatherIconUrl']['0']['value']="";
        $fwo_help_a['weatherDesc']="";
        $fwo_help_a['weatherDesc']['0']="";
        $fwo_help_a['weatherDesc']['0']['value']="";
        $fwo_help_a['tempMaxF']="";
        $fwo_help_a['tempMaxC']="";
        $fwo_help_a['tempMinF']="";
        $fwo_help_a['tempMinC']="";
        $fwo_help_a['winddir16Point']="";   
        $fwo_help_a['winddirDegree']="";   
        $fwo_help_a['windspeedMiles']="";    
        $fwo_help_a['windspeedKmph']="";
        $fwo_help_a['precipMM']="";
        return array($wun_help_a,$wun_parsed,$fwo_help_a,$fwo_parsed);
}

function GG_funx_initialize_GG_weather()
{
              $gg_weather[0][0][0]="";   //Date_mon_weekday
              $gg_weather[0][0][1]="";//month'];}
              $gg_weather[0][0][2]=""; //day'];}
              $gg_weather[0][0][3]=""; //weekday'];} 
              $gg_weather[0][1][0]=""; //Location
              $gg_weather[0][1][1]=""; //observation_time'];  //Ort und Land
              $gg_weather[0][2][0]=""; //icon'];      //zB chancerain!!!!  CODE
              $gg_weather[0][2][1]=""; //'icon_url'];
              $gg_weather[0][2][2]=""; //'condition'];}    //"Chance of rain" // "Rain Showers"
              $gg_weather[0][2][3]="";    //GG_weather_icon
              $gg_weather[0][2][4]="";    //GG_weather_icon_text_precode
              $gg_weather[0][2][5]="";    //GG_weather_icon_url _ Large/Middle 
              $gg_weather[0][2][6]="";    //GG_weather_icon_url _ SmALL     
              $gg_weather[0][3][0]="";        //txt_day_forecast_imp
              $gg_weather[0][3][1]="";        //txt_forecast_metric
              $gg_weather[0][4][0]="";        //txt_night_forecast_imp
              $gg_weather[0][4][1]="";        //txt_forecast_metric
              $gg_weather[0][5][0]=""; //temp_f'];} //aktuelle Temperatur
              $gg_weather[0][5][1]=""; //temp_c'];}   //aktuelle Temperatur
              $gg_weather[0][6][0]=""; //windchill_f'];}    //Windchill
              $gg_weather[0][6][1]=""; //windchill_c'];}
              $gg_weather[0][7][0]="";    //hi f
              $gg_weather[0][7][1]="";    //hi c
              $gg_weather[0][8][0]="";    //lo f
              $gg_weather[0][8][1]="";    //lo c
              $gg_weather[0][10]=""; //'relative_humidity'];}}
              $gg_weather[0][11][0]=""; //'wind_dir'];}    //Text??? West statt W???
              $gg_weather[0][11][1]="";    //Kurz   WNW for example
              $gg_weather[0][11][2]="";   //Degrees
              $gg_weather[0][11][3]="";   //speed mph
              $gg_weather[0][11][4]="";  //speed kmh
              $gg_weather[0][11][5]="";  //Gusts  mph
              $gg_weather[0][11][6]=""; //Gusts  kmh
              $gg_weather[0][12][1]=""; //Pressure_MB
              $gg_weather[0][12][0]="";   //Presssure_IN
              $gg_weather[0][13]="";    //Presssure_trend
              $gg_weather[0][14][0]="";   //dewpoint f
              $gg_weather[0][14][1]="";   //dewpoint c
              $gg_weather[0][15][0]=""; //visibility
              $gg_weather[0][15][1]="";//visibility_km'];}
              $gg_weather[0][16]="";//pop'];}           //probABILITY of precipitation 
              $gg_weather[0][17][0]="";        //amount of precipitation 
              $gg_weather[0][17][1]=""; //precip_today_metric'];}    
              $gg_weather[0][18]="";           //Cloadcover
              $gg_weather[0][19][0]=""; //percentIlluminated'];}
              $gg_weather[0][19][1]=""; //'ageOfMoon'];}
              $gg_weather[0][19][2]=""; //Moonphase GG_funx_calculate_moonphase($gg_weather[0][19][1]);}
              $gg_weather[0][19][3]=""; //'sunset']['hour'];}
              $gg_weather[0][19][4]=""; //'sunset']['minute'];}
              $gg_weather[0][19][5]=""; //'sunrise']['hour']; }
              $gg_weather[0][19][6]=""; //'sunrise']['minute']; }
              $gg_weather[0][19][7]="";        
              $gg_weather[0][99][0]="";  //error wun
              $gg_weather[0][99][1]="";  //error fwo
              $gg_weather[0][99][2]="";  //reserved
              $gg_weather[0][99][3]="";  //error result from above
              
          return $gg_weather;
}

function GG_funx_translate_moonphase($term_in,$opt_language_index){
	
    $moonphase_lang = array("New"=> array("Neumond","Nouvelle Lune","Luna Nueva","Luna nuova","n&#243;w","&uacute;jhold","Lua Nova","هلال"),
                      "Waxing Crescent" =>array ("Zunehmend nach Neumond (erstes Viertel)","Premier Croissant","Luna Nueva Visible","Luna crescente","rosn&#261;cy sierp","n&ouml;vekv&ocirc; &uacute;jhold","Lua Nova","بعد نحو متزايد القمر الجديد (الربع الأول)"),
	                    "First Quarter" => array ("Halbmond (erstes Viertel)","Premier quartier","Cuarto Creciente","Primo quarto","pierwsza kwadra","f&eacute;lhold","Quarto Crescente","نصف القمر (الربع الأول)"),
	                    "Waxing Gibbous" => array ("Zunehmend nach Halbmond (zweites Viertel)","Lune gibbeuse","Luna Gibosa Crecientee","Gibbosa crescente","rosn&#261;cy przed pe&#322;ni&#261;","n&ouml;vekv&ocirc; f&eacute;lhold","Crescente","على نحو متزايد بعد نصف القمر (الربع الثاني)"),
	                    "Full" => array ("Vollmond","Pleine lune","Luna Llena","Luna piena","pe&#322;nia","telihold","Lua cheia","بدر كامل"),
	                    "Waning Gibbous" => array ("Abnehmend nach Vollmond (drittes Viertel)","Lune gibbeuse","Luna Gibosa Menguante","Gibbosa calante","malej&#261;cy po pe&#322;ni","cs&ouml;kken&#337; telihold","Minguante","تناقص بعد اكتمال القمر (الربع الثالث)"),
	                    "Third Quarter" => array ("Halbmond (drittes Viertel)","Dernier quartier","Cuarto Menguante","Ultimo quarto","ostatnia kwadra","cs&ouml;kken&#337; telihold","Quarto Minguante","نصف القمر (الربع الثالث)"),
	                    "Waning Crescent" => array ("Abnehmend nach Halbmond (viertes Viertel)","Dernier Croissant","Luna Menguante","Luna calante","malej&#261;cy sierp","cs&ouml;kken&#337; f&eacute;lhold","Nova","تراجع القمر (الربع الرابع)"),
	                    "Last Quarter" => array ("Abnehmend nach Halbmond (viertes Viertel)","Dernier Croissant","Luna Menguante","Luna calante","malej&#261;cy sierp","cs&ouml;kken&#337; f&eacute;lhold","Nova","تراجع القمر (الربع الأخير)"), //same as waning crescent                    
                      );
    if(!isset($moonphase_lang[$term_in][$opt_language_index]))
      {$term_out=$term_in;}
    else
      {$term_out=$moonphase_lang[$term_in][$opt_language_index];}
    return $term_out;
    
}

function GG_funx_translate_weather_code_into_icon($term_in,$flag_day_night){

   $step=0;
   if($flag_day_night=="night"){$step=1;}
   
   $decode_array= array('395'=> array('41','46','Moderate or heavy snow in area with thunder'),
                        '392'=> array('41','46','Patchy light snow in area with thunder'),
                        '389'=> array('38','47','Moderate or heavy rain in area with thunder'),
                        '386'=> array('37','47','Patchy light rain in area with thunder'),
                        '377'=> array('6','6','Moderate or heavy showers of ice pellets'),
                        '374'=> array('6','6','Light showers of ice pellets'),
                        '371'=> array('14','14','Moderate or heavy snow showers'),
                        '368'=> array('13','13','Light snow showers'),
                        '365'=> array('6','6','Moderate or heavy sleet showers'),
                        '362'=> array('6','6','Light sleet showers'),
                        '359'=> array('11','11','Torrential rain shower'),
                        '356'=> array('11','11','Moderate or heavy rain shower'),
                        '353'=> array('9','9','Light rain shower'),
                        '350'=> array('18','18','Ice pellets'),
                        '338'=> array('16','16','Heavy snow'),
                        '335'=> array('16','16','Patchy heavy snow'),
                        '332'=> array('14','14','Moderate snow'),
                        '329'=> array('14','14','Patchy moderate snow'),
                        '326'=> array('13','13','Light snow'),
                        '323'=> array('13','13','Patchy light snow'),
                        '320'=> array('18','18','Moderate or heavy sleet'),
                        '317'=> array('18','18','Light sleet'),
                        '314'=> array('8','8','Moderate or Heavy freezing rain'),
                        '311'=> array('8','8','Light freezing rain'),
                        '308'=> array('40','40','Heavy rain'),
                        '305'=> array('39','45','Heavy rain at times'),
                        '302'=> array('11','11','Moderate rain'),
                        '299'=> array('39','45','Moderate rain at times'),
                        '296'=> array('9','9','Light rain'),
                        '293'=> array('9','9','Patchy light rain'),
                        '284'=> array('10','10','Heavy freezing drizzle'),
                        '281'=> array('9','9','Freezing drizzle'),
                        '266'=> array('9','9','Light drizzle'),
                        '263'=> array('9','9','Patchy light drizzle'),
                        '260'=> array('20','20','Freezing fog'),
                        '248'=> array('20','20','Fog'),
                        '230'=> array('16','16','Blizzard'),
                        '227'=> array('15','15','Blowing snow'),
                        '200'=> array('38','47','Thundery outbreaks in nearby'),
                        '185'=> array('10','10','Patchy freezing drizzle nearby'),
                        '182'=> array('18','18','Patchy sleet nearby'),
                        '179'=> array('16','16','Patchy snow nearby'),
                        '176'=> array('40','49','Patchy rain nearby'),
                        '143'=> array('20','20','Mist'),
                        '122'=> array('26','26','Overcast'),
                        '119'=> array('28','27','Cloudy'),
                        '116'=> array('30','29','Partly Cloudy'),
                        '113'=> array('32','31','Clear/Sunny'),
                        'chanceflurries'=> array('41','46','Chance of Flurries'),
                        'chancerain'=> array('39','45','Chance of Rain'),
                        'chancesleet'=> array('39','45','Chance of Freezing Rain'),
                        'chancesleet'=> array('41','46','Chance of Sleet'),
                        'chancesnow'=> array('41','46','Chance of Snow'),
                        'chancetstorms'=> array('38','47','Chance of Thunderstorms'),
                        'chancetstorms'=> array('38','47','Chance of a Thunderstorm'),
                        'clear'=> array('32','31','Clear'),
                        'cloudy'=> array('26','26','Cloudy'),
                        'flurries'=> array('15','15','Flurries'),
                        'fog'=> array('20','20','Fog'),
                        'hazy'=> array('21','21','Haze'),
                        'mostlycloudy'=> array('28','27','Mostly Cloudy'),
                        'mostlysunny'=> array('34','33','Mostly Sunny'),
                        'partlycloudy'=> array('30','29','Partly Cloudy'),
                        'partlysunny'=> array('28','27','Partly Sunny'),
                        'sleet'=> array('5','5','Freezing Rain'),
                        'rain'=> array('11','11','Rain'),
                        'sleet'=> array('5','5','Sleet'),
                        'snow'=> array('16','16','Snow'),
                        'sunny'=> array('32','31','Sunny'),
                        'tstorms'=> array('4','4','Thunderstorms'),
                        'tstorms'=> array('4','4','Thunderstorm'),
                        'unknown'=> array('4','4','Unknown'),
                        'cloudy'=> array('26','26','Overcast'),
                        'partlycloudy'=> array('30','29','Scattered Clouds'),
  
   );
     if(!isset( $decode_array[$term_in][$step]))
        {$term_out[0]=$term_in;}
     else
        {$term_out[0]= $decode_array[$term_in][$step];}
        
    if(!isset( $decode_array[$term_in][2]))
      {$term_out[1]=$term_in;}
    else
      {$term_out[1]= $decode_array[$term_in][2];}
     return $term_out; 
}




function GG_funx_translate_wetter_lang($term_in,$opt_language_index){

  $wetter_lang = array(
                       "0"=>array("Gewitter","Orage","Tempestad","Tempesta","Burze","vihar","Tempestade","عاصفة رعدية"),
                       "1"=>array("Gewitter","Orage","Tempestad","Tempesta","Gwa&#322;towne burze","vihar","Tempestade","عاصفة رعدية"), 
                       "2"=>array("Gewitter","Orage","Tempestad","Tempesta","Gwa&#322;towne burze","heves vihar","Tempestade","عاصفة رعدية"),
                       "3"=>array("Gewitter","Orage","Tempestad","Tempesta","Burze","heves vihar","Tempestade","عاصفة رعدية"),
                       "4"=>array("Gewitter","Orage","Tempestad","Tempesta","Burze","vihar","Tempestade","عاصفة رعدية"),
                       "5"=>array("Schneeregen","Neige Fondue","xxxAguanieve","Nevischio","Deszcz ze &#347;niegiem","havas es&#337;","Granizo","مطر متجمد"),
                       "6"=>array("Regen und Hagel","Pluie &eacute; Gr&ecirc;le ","lluvia y granizada","Pioggia e Grandine","Krupy &#347;nie&#380;ne","&oacute;nos es&#337;","Chuva forte","مطر وبرد"),
                       "7"=>array("Starker Schneeregen","Neige Fondue Forte","Aguanieve Fuerte","Nevischio violento","Obfite opady deszczu ze &#347;niegiem","havas es&#337;","Neve Forte","برد ثقيل"),
                       "8"=>array("Leichter Regen, Glatteisgefahr!","Pluie l&eacute;g&ecirc;r, verglas!","Lluvia d&eacute;bil, hielo liso!","Pioggia debole, Gelicidio!","Marzn&#261;ca m&#380;awka, &#347;lisko!","szemerk&eacute;l&#337; es&#337;","Garoa","الضباب والمخاطر الثلجية!"),
                       "9"=>array("Leichter Regen","Pluie l&eacute;g&ecirc;r","Lluvia d&eacute;bil","Pioggia debole","M&#380;awka","szemerk&eacute;l&#337; es&#337;","Chuva","مطر خفيف"),
                       "10"=>array("Regen, Glatteisgefahr!","Pluie verglas!","Lluvia, hielo liso!","Pioggia, Gelicidio!","Marzn&#261;cy deszcz, go&#322;oled&#378;!","es&#337;, t&uuml;k&ouml;rj&eacute;gvesz&eacute;ly!","Chuva","مطر"),
                       "11"=>array("Regen","Pluie","Lluvia","Pioggia","Umiarkowane opady deszczu","es&#337;","Chuva","مطر"),
                       "12"=>array("St&auml;rkerer Regen","Pluie mod&eacute;r&eacute;e","Lluvia moderada","Pioggia moderata","Obfite opady deszczu","er&#337;sebb es&#337;","Chuva moderada","مطر معتدل"),
                       "13"=>array("Leichter Schneefall","Neige l&eacute;g&ecirc;r","Nevada d&eacute;bil","Nevicata debole","Lekkie opady &#347;niegu","h&oacute;sz&aacute;lling&oacute;z&aacute;s","Neve Fraca","تصاقط الثلوج ضعيف"),
                       "14"=>array("Schneefall","Chute de neige","Nevada","Nevicata","Opady &#347;niegu","havaz&aacute;s","Neve","تصاقط الثلوج"),
                       "15"=>array("Rauhrreif","Givre","Cencellada blanca sin","Calaverna","Szron","d&eacute;r","Neve","صقيع"),
                       "16"=>array("Starker Schneefall","Neige lourde","Nieve pesada","Nevicata violenta","Obfite opady &#347;niegu","er&#337;sebb havaz&aacute;s","Neve pesada","قوي"),
                       "17"=>array("Gewitter","Orage","Tempestad","Tempesta","Burze","vihar","Tempestade","عاصفة رعدية"),
                       "18"=>array("Hagel","Gr&ecirc;le","Granizada","Grandine","Grad","j&eacute;ges&#337;","Dil&uacute;vio","عاصفة برد"),
                       "19"=>array("Smog","Smog","Smog","Smog","Py&#322;","szmog","Polui&ccedil;&atilde;o","ضباب ودخان"),
                       "20"=>array("Nebel","Brouillard","Neblina","Nebbia","Mg&#322;y","k&ouml;d","Sem neblina","ضباب"),
                       "21"=>array("Dunst","Brume s&egrave;che","Parcialmente nebulado","Foschia","Zamglenia","p&aacute;ra","Neblina","ضباب"),
                       "22"=>array("Smog","Smog","Smog","Smog","Dym","szmog","Polui&ccedil;&atilde;o","ضباب"),
                       "23"=>array("Windig","Venteux","Ventoso","Ventoso","Wietrznie","szeles","Ventos","عاصف"),
                       "24"=>array("Windig","Venteux","Ventoso","Ventoso","Wietrznie","szeles","Ventania","عاصف"),
                       "25"=>array("n/a","n/a","n/a","n/a","n/a","n/a","n/a","n/a"),
                       "26"=>array("Bedeckt","Couvert","Cubierto","Nuvolosita compatta","Zachmurzenie ca&#322;kowite","felh&#337;s","Coberto","مغطى"),
                       "27"=>array("Bew&ouml;lkt","Nuageux","Nublado","Nuvolosita diffuse","Pochmurno","er&#337;sen felh&#337;s","Nublado","غائم"),
                       "28"=>array("Bew&ouml;lkt","Nuageux","Nublado","Nuvolosita diffuse","Pochmurno","er&#337;sen felh&#337;s","Nublado","غائم"),
                       "29"=>array("Teilweise Bew&ouml;lkt","Partiellement nuageux","Parcialmente nublado","Nubi sparse","Zachmurzenie umiarkowane","r&eacute;szben felh&#337;s","Parcialmente nublado","جزئي"),
                       "30"=>array("Teilweise Bew&ouml;lkt","Partiellement nuageux","Parcialmente nublado","Nubi sparse","Zachmurzenie umiarkowane","r&eacute;szben felh&#337;s","Parcialmente nublado","جزئي"),
                       "31"=>array("Klar","Serein","Despejado","Sereno","Bezchmurne niebo","der&#369;s","Claro","واضح"),
                       "32"=>array("Sonnig","Ensoleill&eacute;","Soleado","Sereno","S&#322;onecznie","napos","Ensolarado","مشمس"),
                       "33"=>array("&Uuml;berwiegend klar","Pour la plupart serein","Mayormente despejado","Poco Nuvoloso","Zachmurzenie ma&#322;e","f&#337;leg der&#369;s","Parcialmente nublado","غائم جزئي"),
                       "34"=>array("Heiter","Serein","Mayormente soleado","Poco Nuvoloso","Zachmurzenie ma&#322;e","f&#337;leg napos","Principalmente ensolarado","مشمس في الغالب"),
                       "35"=>array("Gewitter","Orage","Tempestad","Tempesta","Burze","vihar","Tempestade","عاصفة رعدية"),
                       "36"=>array("Sonnig","Ensoleill&eacute;","Soleado","Sereno","Upalnie","napos","Ensolarado","مشمس"),
                       "37"=>array("Gewitterneigung","Probabilit&eacute; d'Orage","Riesgo de Tempesta","Tendeza di Temporale","Lokalne burze","zivatar","Probabilidade de tempestade","عواصف رعدية"),
                       "38"=>array("Gewitterneigung","Probabilit&eacute; d'Orage","Riesgo de Tempesta","Tendeza di Temporale","Rozproszone burze","zivatar","Probabilidade de tempestade","عواصف رعدية"),
                       "39"=>array("Sonnig mit Schauerneigung","Ensoleill&eacute; - probabilit&eacute d'averse; ","Soleado con probabilidades de lluvia","Pioggia e Schiarite","Przelotne opady deszczu","z&aacute;por","C&eacute;u nublado com chuva prov&aacute;vel","غائم مع هطول أمطار على الأرجح"),
                       "40"=>array("Starker Regen","Pluie forte","lluvia Fuerte","Pioggia violenta","Mocne opady deszczu","er&#337;sebb es&#337;","Chuva forte","مطر غزير"),
                       "41"=>array("Sonnig mit Schauerneigung","Ensoleill&eacute; - probabilit&eacute d'averse; ","Soleado con probabilidades de lluvia","Pioggia e Schiarite","Pogodnie z opadami &#347;niegu","havaz&aacute;s","C&eacute;u nublado com chuva prov&aacute;vel","غائم مع هطول أمطار على الأرجح"),
                       "42"=>array("Starker Schneefall","Neige lourde","Nieve pesada","Nevicata violenta","Obfite opady &#347;niegu","er&#337;s havaz&aacute;s","Neve pesada","ثلوج غزيرة"),
                       "43"=>array("Schneefall bei starkem Wind","Neige et Vent","Nieve y ventoso","Nevicata e Vento","Zawieje i zamiecie &#347;nie&#380;ne","havaz&aacute;s er&#337;s sz&eacute;llel","Neve e vento","ثلوج ورياح"),
                       "44"=>array("n/a","n/a","n/a","n/a","n/a","n/a","n/a","n/a"),
                       "45"=>array("Regenschauer in der Nacht","Averse la nuit","Chubasco de noche","Rovescio della notte","Nocne opady deszczu","&eacute;jszakai fut&oacute; z&aacute;por","Chuva &agrave; noite","زخات مطر في الليل"),
                       "46"=>array("Schneeschauer in der Nacht","Averse de neige la nuit","Nevada de noche","Breve e intensa nevicata della notte","Nocne opady &#347;niegu","&eacute;jszakai havaz&aacute;s","Nevada &agrave; noite","زخات ثلوج في الليل"),
                       "47"=>array("N&auml;chtliche Gewitter","Orage en nuit","Tempestad de noche","Tempesta della notte","Nocne burze","&eacute;jszakai zivatar","Tempestade da noite","ليلة عاصفة")
                       );                       
                       if(!isset($wetter_lang[$term_in][$opt_language_index]))
                        {$term_out=$term_in;}
                      else
                        {$term_out=$wetter_lang[$term_in][$opt_language_index];}
                      return $term_out;
}

function GG_funx_translate_uvindex($term_in,$opt_language_index){
     $uv_index_lang = array("0"=>array("Keine","Z&eacute;ro", "Nulo","Nulla","zerowy","nulla","nenhum","لا"),
	                        "1"=>array("Niedrig","Faible","Bajo","Basso","niski","gyenge","Baixo","منخفض"),
                          "2"=>array("Niedrig","Faible","Bajo","Basso","niski","gyenge","Baixo","منخفض"),
                          "3"=>array("Mittel","Mod&eacute;r&eacute;","Moderado","Medio","umiarkowany","k&ouml;zepes","Moderado","متوسط"),
                          "4"=>array("Mittel","Mod&eacute;r&eacute;","Moderado","Medio","umiarkowany","k&ouml;zepes","Moderado","متوسط"),
                          "5"=>array("Mittel","Mod&eacute;r&eacute;","Moderado","Medio","umiarkowany","k&ouml;zepes","Moderado","متوسط"),
                          "6"=>array("Hoch","Elev&eacute;","Alto","Alto","wysoki","magas","Alto","عاليه"),
                          "7"=>array("Hoch","Elev&eacute;","Alto","Alto","wysoki","magas","Alto","عاليه"),
                          "8"=>array("Sehr Hoch","Tr&ecirc;s Elev&eacute;","Muy Alto","Molto Alto","bardzo wysoki","nagyon magas","Muito Alto","عالية جدا"),
                          "9"=>array("Sehr Hoch","Tr&ecirc;s Elev&eacute;","Muy Alto","Molto Alto","bardzo wysoki","nagyon magas","Muito Alto","عالية جدا"),
                          "10"=>array("Sehr Hoch","Tr&ecirc;s Elev&eacute;","Muy Alto","Molto Alto","bardzo wysoki","nagyon magas","Muito Alto","عالية جدا"),
                          "10+"=>array("Extrem Hoch","Extr&ecirc;me Elev&eacute","Extremademente Alto","Estremo Alto","ekstremalnie wysoki","extr&eacute;m magas","Extremamente Alto","عالية جدا")
                          );
    if(!isset($uv_index_lang[$term_in][$opt_language_index]))
      {$term_out=$term_in;}
    else
      {$term_out=$uv_index_lang[$term_in][$opt_language_index];}
    return $term_out;
}

function GG_funx_translate_array($term_in,$language)
{
    $trans_array=array();
    $term_save=$term_in;
    $term_in="YYY".strtolower($term_in)."Y";
    if ($language=="ar"){
      $trans_array= array('الشروط الفعلية',	'صباحاً',	'و',	'',	'عاصفة ثلجية',	'تهب',	'الرياح',	'صافي',	'يصفى',	'كثافة سحابة',	'غائم',	'بلورات',	'نهاراً',	'ندى',	'رذاذ',	'باكرا',	'السبت',	'الشعور وكأنه',	'تفرقه',	'الثلوج',	'ضباب',	'تجمد',	'الجُمْعَة',	'منذ',	'حبيبات',	'الرياح',	'برد',	'ضباب',	'كثيف',	'ساعات',	'رطوبة',	'جليد',	'في',	'متوقع',	'متأخرا',	'طول اليوم',	'نهاراً',	'ضباب',	'مزيج',	'معتدل',	'الأحد',	'المرحلة القمرية',	'غالباً',	'قريب',	'التوقعات',	'ليلاً',	'من',	'أو',	'جزئي',	'غير مكتمل',	'الكريات',	'مساءً',	'هطول',	'الضغط',	'ممطر',	'الجمعة',	'رمل',	'خميس',	'متناثر',	'امطار م',	'زخات مطر',	'مطر متجمد',	'دخان',	'مثلج',	'عذراً.. لاتوجد بيانات متوفره حالياً!',	'رذاذ',	'رعد',	'هبوط',	'مشمس',	'قليله',	'مشمس',	'شروق الشمس في',	'غروب الشمس في',	'ارتفاع',	'الإربعا',	'أحيانا',	'شديده',	'عواصف',	'عواصف',	'الأثنين',	'وضوح',	'الثلثاء',	'غبار واسع الانتشار',	'هادئه',	'رياح',	'شتوي',	'مع',
 );
        }
    if ($language=="de"){
    $trans_array= array( 'Aktuell',	'vormittags',	'und',	'',	'Schneesturm',	'wehender',	'windstill',	'wolkenlos',	'Aufkl&auml;rung',	'Bew&ouml;lkungsdichte',	'bew&ouml;lkt',	'Kristalle',	'Tags&uuml;ber',	'Taupunkt',	'Nieselregen',	'vor Mitternacht',	'fallend',	'Gef&uuml;hlt',	'vereinzelt',	'Schneegest&ouml;ber',	'Nebel',	'frierender',	'Freitag',	'aus',	'K&ouml;rner',	'B&ouml;en',	'Hagel',	'Dunst',	'kr&auml;ftiger',	'Uhr',	'Feuchte',	'Eis',	'in',	'&ouml;rtliche',	'nach Mitternacht',	'Tagl&auml;nge',	'leichter',	'Nebel',	'Mix',	'm&auml;ssig',	'Montag',	'Mondphase',	'&uuml;berwiegend',	'in der N&auml;he',	'Aussichten',	'Nachts',	'von',	'oder',	'teilweise',	'l&uuml;ckenhaft',	'Pellets',	'nachmittags',	'Niederschlag',	'Druck',	'Regen',	'steigend',	'Sand',	'Samstag',	'vereinzelte',	'Schauer',	'Schauer',	'Schneeregen',	'Rauch',	'Schnee',	'Leider keine aktuellen Wetterdaten verf&uuml;gbar!',	'Spray',	'Sturm',	'starker',	'sonnig',	'Sonntag',	'sonnig',	'Sonnenaufgang um',	'Sonnenuntergang um',	'Gewitter',	'Donnerstag',	'zeitweise',	'Heute',	'Morgen',	'Gewitter',	'Dienstag',	'Sichtbarkeit',	'Mittwoch',	'verbreitet Staub',	'Wind',	'windig',	'winterlicher',	'mit',
 );
        }
    if ($language=="es"){
      $trans_array=array( 'Actualmente',	'la man&atilde;na',	'y',	'',	'ventisca',	'soplado',	'Calma',	'despejado',	'parcialmente despejado',	'Nube de densidad',	'nublado',	'cristales',	'De dia',	'Punto de rocío',	'llovizna',	'antes de medianoche',	'bajada',	'T.de sensaci&oacute;n',	'aislado;',	'r&aacute;fagas',	'niebla',	'congelaci&oacute;n',	'Viernes',	'-',	'granos',	'R&aacute;fagas',	'granizo',	'neblina',	'fuerte',	'h',	'Humedad',	'hielo',	'en',	'local',	'despu&eacute;s de medianoche',	'Duraci&oacute;n del dia',	'd&eacute;bil',	'niebla',	'mezcla',	'moderado',	'Lunes',	'Fase Lunar',	'prevalente',	'cerca',	'Perspectivas',	'De la noche',	'de',	'o',	'parcialmente',	'irregular',	'pellets',	'la tarde',	'Precipitaci&oacute;n',	'Presi&oacute;n',	'lluvia',	'subida',	'arena',	'Sabado',	'aislado',	'chubasco',	'chubasco',	'aguanieve',	'humo',	'nieve',	'Perd&oacute;n! Datos del tiempo no disponibles!',	'spray',	'tormenta',	'fuerte',	'soleado',	'Domingo',	'soleado',	'Amanecer',	'Ocaso',	'tormenta',	'Jueves',	'a veces',	'Hoy',	'Ma&ntilde;ana',	'Tempestad',	'Martes',	'Visibilidad',	'Miercoles',	'Nubes de polvo',	'Viento',	'ventoso',	'invernal',	'con',
 );
        }
    if ($language=="fr"){
      $trans_array=array('Actuelle',	'matin',	'et',	'',	'Blizzard',	'souffle',	'calme',	'serein',	'&Eacute;claircies',	'La densit&eacute; des nuages',	'nuageux',	'cristaux',	'Le jour',	'Point de ros&eacute;e',	'bruine',	'avant minuit',	'en baisse',	'T.Ressentie',	'&eacute;parses',	'averses de neige',	'brouillard',	'le gel',	'Vendredi',	'du',	'c&eacute;r&eacute;ales',	'Rafales',	'la gr&ecirc;le',	'brume',	'fort',	'h',	'Humidit&eacute;',	'la glace',	'en',	'locale',	'apr&egrave;s minuit',	'Dur&eacute;e du jour',	'l&eacute;ger',	'brouillard',	'm&eacute;lange',	'mod&eacute;r&eacute;e',	'Lundi',	'Phase de la lune',	'pour la plupart',	'&agrave; proximit&eacute;',	'Perspectives',	'Le nuit',	'des',	'ou',	'partiellement',	'in&eacute;gal',	'boulettes',	'&acute;apr&ecirc;s-midi',	'Pr&eacute;cipitation',	'Pression',	'pluie',	'en hausse',	'le sable',	'Samedi',	'rares',	'averse',	'averse',	'neige fondue',	'de fum&eacute;e',	'neige',	'D&Eacute;sol&Eacute;, donn&Eacute;es m&Eacute;t&Eacute;orologiques non disponibles',	'de pulv&eacute;risation',	'temp&egrave;te',	'fort',	'ensoleill&eacute;',	'Dimanche',	'ensoleill&eacute;',	'Lever du soleil &agrave;',	'Coucher du soleil',	'orage',	'Jeudi',	'&agrave; des moments',	'Aujourd hui',	'Demain',	'orages',	'Mardi',	'La visibilit&eacute;',	'Mercredi',	'la poussi&eleger;re r&eacute;pandue',	'Vent',	'venteux',	'hivernal',	'avec',
 );                                                                       
        }
     if ($language=="it"){
      $trans_array=array( 'Attuale',	'mattinera',	'e',	'',	'bufera di neve',	'soffiaggio',	'Senza vento',	'sereno',	'schiarisi',	'Nuvola densit&agrave;',	'nuvuloso',	'cristalli',	'Durante il giorno',	'Punto di rugiad',	'pioggerella',	'serale',	'discendente',	'T.percepita',	'locale;',	'folate',	'Nebbia',	'congelamento',	'Venerdi',	'da',	'grani',	'Raffiche',	'grandine',	'foschia',	'forte',	'h',	'Umidit&aacute;',	'ghiaccio',	'en',	'localizzata',	'notturna',	'Durata del giorno',	'debole',	'Nebbia',	'misto',	'moderato',	'Lunedi',	'Fase Lunar',	'predominante',	'vicino',	'Prospettive',	'Di notte',	'di',	'o',	'parzialemente',	'irregolare',	'pellet',	'pomeridiana',	'Precipitatione',	'Pressione',	'pioggia',	'crescente',	'sabbia',	'Sabato',	'localizzata',	'rovescio',	'rovescio',	'nevischio',	'fumo',	'neve',	'Spiacenti, no dati disponible!',	'spray',	'tempesta',	'forte',	'solare',	'Domenica',	'solare',	'Alba',	'Tramonto',	'temporale',	'Giovedi',	'talvolta',	'Oggi',	'Domani',	'Tempesta',	'Martedi',	'Visibilit&agrave;',	'Mercoledi',	'polvere diffusa',	'Vento',	'ventoso',	'invernale',	'con',
);
      }
    if ($language=="hu"){
      $trans_array= array('Aktu&aacute;lis adatok',	'd&eacute;lel&#337;tt',	'&eacute;s',	'',	'h&oacute;vihar',	'f&uacute;j&oacute;',	'sz&eacute;lcsend',	'der&#369;s',	'tisztul&aacute;s',	's&#369;r&#369;s&eacute;ge a felh&#337;',	'felh&#337;s',	'krist&aacute;lyok',	'Nappal',	'harmatpont',	'szit&aacute;l&aacute;s',	'&eacute;jf&eacute;l el&#337;tt',	'cs&ouml;kken&#337;',	'&eacute;rz&eacute;sre',	'n&eacute;h&aacute;ny',	'h&oacute;z&aacute;porok',	'k&ouml;d',	'fagyaszt&oacute;',	'p&eacute;ntek',	/*'-',*/'',	'szemerk&eacute;l&#337; es&#337;',	'sz&eacute;ll&ouml;k&eacute;sek',	'j&eacute;ges#337;',	'k&ouml;d',	'er&#337;s',	'h',	'relat&iacute;v p&aacute;ratartalom',	'j&eacute;g',	'h&aacute;travan',	'elszigetelt',	'&eacute;jf&eacute;l ut&aacute;n',	'a nap hossza',	'gyenge',	'k&ouml;d',	'vegyes',	'm&eacute;rs&eacute;kelt',	'h&eacute;tf&#337;',	'holdf&aacute;zis',	'f&#337;leg',	'k&ouml;zeli',	'El&#337;rejelz&eacute;s:',	'&eacute;jszaka',	'a',	'vagy',	'r&eacute;szben',	'egyenetlen',	'pellet',	'd&eacute;lut&aacute;n',	'a csapad&eacute;k val&oacute;sz&iacute;n&#369;s&eacute;ge:',	'nyom&aacute;s',	'es&#337;',	'emelked&#337;',	'homok',	'szombat',	'elsz&oacute;rt',	'zivatar',	'zivatar',	'&oacute;nos eső',	'f&uuml;st',	'h&oacute;',	'Eln&eacute;z&eacute;st! Nincs el&eacute;rhet&#337; aktu&aacute;lis id&#337;j&aacute;r&aacute;s-jelent&eacute;s!',	'permet',	'vihar',	'er&#337;s',	'napos',	'vas&aacute;rnap',	'napos ',	'napkelte',	'napnyugta',	'zivatar',	'csut&ouml;rt&ouml;k',	'alkalommal',	'Ma',	'holnap',	'viharok',	'kedd',	'l&aacute;t&oacute;t&aacute;vols&aacute;g',	'szerda',	'n&eacute;ha',	'l&eacute;gmozg&aacute;s',	'szeles',	't&eacute;lies',	'a', );
      }
    if ($language=="pl"){
      $trans_array= array('Obecnie',	'przed po&#322;udniem',	'i',	'',	'zamie&#263;',	'dmuchanie',	'bezwietrznie',	'bezchmurnie',	'przeja&#347;nienia',	'gesto&sacute;&cacute; chmury',	'pochmurno',	'kryszta&#322;&oacute;w',	'W dzie&#324',	'punktu rosy',	'm&#378;awka',	'przed p&#243;&#322;noc&#261;',	'spadki ci&#347;nienia ',	'odczuwalne',	'niewielkie',	'opady',	'mg&#322;y',	'zamra&#378;anie',	'pi&#261;tek',	'z',	'ziarna',	'podmuch',	'grad',	'mg&#322;a',	'silne',	'h',	'Wilgotno&#347;&#263; powietrza',	'l&oacute;d',	'za',	'lokalne',	'po p&#243;&#322;nocy',	'D&#322;ugo&#347;&#263; dnia',	'lekkie',	'mg&#322;y',	'mieszanka -',	'umiarkowany',	'poniedzia&#322;ek',	'Faza Ksi&#281;&#380;yca',	'przewa&#380;nie',	'w pobli&#378;u',	'Kolejne dni',	'W nocy',	'z',	'lub',	'cz&#281;&#347;ciowo',	'niejednolity',	'granulki',	'po po&#322;udniu',	'Prawdopodobie&#324;stwo opad&#243;w',	'Obecnie',	'opady deszczu',	'wzrosty ci&#347;nienia',	'piasek',	'sobota',	'rozproszone',	'przelotne opady deszczu',	'przelotne opady deszczu',	'deszcz ze &#347;niegiem',	'dym',	'opady &#347;niegu',	'Niestety aktualne dane pogodowe nie s&#261; dost&#281;pne!',	'sprayu',	'burza',	'silne',	's&#322;onecznie',	'niedziela',	's&#322;onecznie',	'wsch&#243;d S&#322;o&#324;ca o godz.',	'zach&#243;d S&#322;o&#324;ca o godz.',	'burza',	'czwartek ',	'czasami',	'dzisiaj',	'jutro',	'burze',	'wtorek',	'widoczno&sacute;&#263;',	'&#347;roda',	'powszechne py&#322;',	'Wiatr',	'wietrznie',	'zimowe',	'z',
 );
        }
     if ($language=="pt"){  
     $trans_array= array('Agora',	'Manh&atilde;',	'e',	'',	'nevasca',	'sopro',	'Calma',	'claro',	'claro',	'Densidade da nuvem',	'nublado',	'cristais',	'Dia',	'ponto de orvalh',	'garoa',	'antes da meia noite',	'queda',	'Sensa&ccedil;&atilde;o T&eacute;rmica',	'poucos',	'flurries',	'nevoeiro',	'congela&ccedil;&atilde;o',	'Sexta-feira',	'de',	'gr&atilde;os',	'Rajadas',	'granizo',	'neblina',	'forte',	'h',	'Umidade',	'gelo',	'en',	'isolados',	'depois da meia noite',	'Dura&ccedil;&atilde;o do dia',	'fraca',	'nevoeiro',	'mistura',	'moderado',	'Segunda-feira',	'Fases da lua',	'predominante',	'pr&oacute;ximo',	'Perspectivas',	'Noiche',	'de',	'ou',	'parcialmente',	'desigual',	'pellets',	'&agrave; tarde',	'Precipita&ccedil;&atilde;o',	'Press&atilde;o',	'chuva',	'aumento',	'areia',	'Sabado',	'disperso',	'chuva',	'chuvas',	'sleet',	'fuma&ccedil;a',	'neve',	'Sinto Muito!&nbsp;Dados meteorol&oacute;gicos n&atilde;o dispon&iacute;vel!',	'spray',	'tempestades   ',	'forte',	'ensolarado',	'Domingo',	'ensolarado',	'Amanhecer',	'P&ocirc;r do sol',	'Tempestade',	'Quinta-feira',	'&agrave;s vezes',	'Hoje',	'Manh&atilde',	'Tempestade',	'Ter&ccedil;a-feira',	'visibilidade',	'Quarta-feira',	'poeira generalizada',	'Vento',	'vento',	'de inverno',	'com',
 );
        }   
    $term_out=str_replace(
    array('YYYactual conditionsY',	'YYYamY',	'YYYandY',	'YYYatY',	'YYYblizzardY',	'YYYblowingY',	'YYYcalmY',	'YYYclear Y',	'YYYclearingY',	'YYYcloudcoverY',	'YYYcloudyY',	'YYYcrystalsY',	'YYYdayY',	'YYYdewpointY',	'YYYdrizzleY',	'YYYearlyY',	'YYYfallingY',	'YYYfeels likeY',	'YYYfewY',	'YYYflurriesY',	'YYYfogY',	'YYYfreezingY',	'YYYfridayY',	'YYYfromY',	'YYYgrainsY',	'YYYgustsY',	'YYYhailY',	'YYYhazeY',	'YYYheavyY',	'YYYhrsY',	'YYYhumidityY',	'YYYiceY',	'YYYinY',	'YYYisolatedY',	'YYYlateY',	'YYYlength of dayY',	'YYYlightY',	'YYYmistY',	'YYYmixY',	'YYYmoderateY',	'YYYmondayY',	'YYYmoonphaseY',	'YYYmostlyY',	'YYYnearbyY',	'YYYnext daysY',	'YYYnightY',	'YYYofY',	'YYYorY',	'YYYpartlyY',	'YYYpatchyY',	'YYYpelletsY',	'YYYpmY',	'YYYprecipitationY',	'YYYpressureY',	'YYYrainY',	'YYYrisingY',	'YYYsandY',	'YYYsaturdayY',	'YYYscatteredY',	'YYYshowerY',	'YYYshowersY',	'YYYsleetY',	'YYYsmokeY',	'YYYsnowY',	'YYYsorry! no actual weather data available!Y',	'YYYsprayY',	'YYYstormsY',	'YYYstrongY',	'YYYsunY',	'YYYsundayY',	'YYYsunnyY',	'YYYsunrise atY',	'YYYsunset atY',	'YYYthunderY',	'YYYthursdayY',	'YYYtimesY',	'YYYtodayY',	'YYYtomorrowY',	'YYYt-stormsY',	'YYYtuesdayY',	'YYYvisibilityY',	'YYYwednesdayY',	'YYYwidespread dustY',	'YYYwindY',	'YYYwindyY',	'YYYwintryY',	'YYYwithY',

     ),$trans_array,$term_in);
    if(!$term_out){$term_out=$term_save;}
    return $term_out;    
} 
function GG_funx_translate_array_mf($term_in)
{
    $trans_array=array(	 'xx>l&eacute;g&ecirc;re',	'xx>forte',		'nuageuse',	'venteuse', 'sereine',
    'nublado','ventoso' , 'despejado', 'aislado',
    );                                                                       
    $term_in=strtolower($term_in);
    $term_out=str_replace(
    array( 	'xx>l&eacute;ger',	'xx>fort',		'nuageux',	'venteux', 'serein',
    'nublada', 'ventosa,' ,'despejada', 'aislada',
    ),
    $trans_array		
    ,	
    $term_in);
    return $term_out;    
} 

function GG_funx_translate_windspeed($term_in,$unit,$opt_language){
      $corr=1;
      $term_out="";
      if ($opt_language=="ar"){
        if ($unit=="mph"){$corr=1.609344;}
        if ($term_in*$corr>10 and $term_in<=19){$term_out="الرياح خفيفه";}
        if ($term_in*$corr>19 and $term_in<=28){$term_out="رياح لطيفة";}
        if ($term_in*$corr>28 and $term_in<=37){$term_out="نسيم معتدل";}
        if ($term_in*$corr>37 and $term_in<=46){$term_out="نسيم بارد";}
        if ($term_in*$corr>46 and $term_in<=56){$term_out="رياح قويه";}
        if ($term_in*$corr>56 and $term_in<=65){$term_out="رياح شديدة";}
        if ($term_in*$corr>65 and $term_in<=74){$term_out="رياح ثابته";}
        if ($term_in*$corr>74 and $term_in<=83){$term_out="عاصفه";}
        if ($term_in*$corr>83 and $term_in<=102){$term_out="عاصفة ثقيلة";}
        if ($term_in*$corr>102 and $term_in<=120){$term_out="عاصفة عنيفه";}
        if ($term_in*$corr>120) {$term_out="إعصار";}
        return $term_out;
      }
      if ($opt_language=="de"){
        //echo "STOP".$corr;
        if ($unit=="mph"){$corr=1.609344;}
         if ($term_in*$corr>1 and $term_in*$corr<=10){$term_out="Geringer Wind";}
        if ($term_in*$corr>10 and $term_in*$corr<=19){$term_out="Leichter Wind";}
        if ($term_in*$corr>19 and $term_in*$corr<=28){$term_out="Schwacher Wind";}
        if ($term_in*$corr>28 and $term_in*$corr<=37){$term_out="M&auml;ssiger Wind";}
        if ($term_in*$corr>37 and $term_in*$corr<=46){$term_out="Frischer Wind";}
        if ($term_in*$corr>46 and $term_in*$corr<=56){$term_out="Starker Wind";}
        if ($term_in*$corr>56 and $term_in*$corr<=65){$term_out="Starker bis st&uuml;ischer Wind";}
        if ($term_in*$corr>65 and $term_in*$corr<=74){$term_out="St&uuml;rmischer Wind";}
        if ($term_in*$corr>74 and $term_in*$corr<=83){$term_out="Sturm";}
        if ($term_in*$corr>83 and $term_in*$corr<=102){$term_out="Schwerer Sturm";}
        if ($term_in*$corr>102 and $term_in*$corr<=120){$term_out="Orkanartiger Sturm";}
        if ($term_in*$corr>120) {$term_out="Orkan";}
        return $term_out;
      }
      if ($opt_language=="en"){
        if ($unit=="km/h"){$corr=1.609344;}
        if ($term_in/$corr>0.5 and $term_in/$corr<=4.5){$term_out="Light Air";}
        if ($term_in/$corr>4.6 and $term_in/$corr<=7.5){$term_out="Light Breeze";}
        if ($term_in/$corr>7.5 and $term_in/$corr<=12.1){$term_out="Gentle Breeze";}
        if ($term_in/$corr>12.1 and $term_in/$corr<=19){$term_out="Moderate Breeze";}
        if ($term_in/$corr>19 and $term_in/$corr<=24.7){$term_out="Fresh Breeze";}
        if ($term_in/$corr>24.7 and $term_in/$corr<=31.6){$term_out="Strong Breeze";}
        if ($term_in/$corr>31.6 and $term_in/$corr<=38.6){$term_out="Moderate Gale";}
        if ($term_in/$corr>38.6 and $term_in/$corr<=46.6){$term_out="Fresh Gale";}
        if ($term_in/$corr>46.6 and $term_in/$corr<=54.7){$term_out="Strong Gale";}
        if ($term_in/$corr>54.7 and $term_in/$corr<=63.9){$term_out="Whole Gale";}
        if ($term_in/$corr>63.9 and $term_in/$corr<=73.1){$term_out="Storm";}
        if ($term_in/$corr>73.1) {$term_out="Hurricane";}
        return $term_out;
      }
      if ($opt_language=="es"){
        if ($unit=="mph"){$corr=1.609344;}
        if ($term_in*$corr>10 and $term_in*$corr<=19){$term_out="Brisa muy d&eacute;bil";}
        if ($term_in*$corr>19 and $term_in*$corr<=28){$term_out="Brisa d&eacute;bil";}
        if ($term_in*$corr>28 and $term_in*$corr<=37){$term_out="Brisa moderada";}
        if ($term_in*$corr>37 and $term_in*$corr<=46){$term_out="Brisa fresca";}
        if ($term_in*$corr>46 and $term_in*$corr<=56){$term_out="Brisa fuerte";}
        if ($term_in*$corr>56 and $term_in*$corr<=65){$term_out="Viento fuerte";}
        if ($term_in*$corr>65 and $term_in*$corr<=74){$term_out="Viento duro";}
        if ($term_in*$corr>74 and $term_in*$corr<=83){$term_out="Muy duro";}
        if ($term_in*$corr>83 and $term_in*$corr<=102){$term_out="Temporal";}
        if ($term_in*$corr>102 and $term_in*$corr<=120){$term_out="Borrasca";}
        if ($term_in*$corr>120) {$term_out="Hurac&aacute;n";}
        return $term_out;
      }
       if ($opt_language=="fr"){
        if ($unit=="mph"){$corr=1.609344;}
        if ($term_in*$corr>6 and $term_in*$corr<=12){$term_out="L&eacute;ger brise";}
        if ($term_in*$corr>12 and $term_in*$corr<=19){$term_out="Petite brise";}
        if ($term_in*$corr>20 and $term_in*$corr<=29){$term_out="Jolie brise";}
        if ($term_in*$corr>29 and $term_in*$corr<=39){$term_out="Bonne brise";}
        if ($term_in*$corr>39 and $term_in*$corr<=50){$term_out="Vent frais";}
        if ($term_in*$corr>50 and $term_in*$corr<=62){$term_out="Grand vent frais";}
        if ($term_in*$corr>62 and $term_in*$corr<=74){$term_out="Coup de vent";}
        if ($term_in*$corr>75 and $term_in*$corr<=89){$term_out="Fort coup de vent";}
        if ($term_in*$corr>89 and $term_in*$corr<=103){$term_out="Temp&ecirc;te";}
        if ($term_in*$corr>103 and $term_in*$corr<=118){$term_out="Viloente temp&ecirc;te";}
        if ($term_in*$corr>118) {$term_out="Ouragan";}
        return $term_out;
      }
      if ($opt_language=="it"){
        if ($unit=="mph"){$corr=1.609344;}
        if ($term_in*$corr>10 and $term_in*$corr<=19){$term_out="Brezza leggera";}
        if ($term_in*$corr>19 and $term_in*$corr<=28){$term_out="Brezza tesa";}
        if ($term_in*$corr>28 and $term_in*$corr<=37){$term_out="Vento moderato";}
        if ($term_in*$corr>37 and $term_in*$corr<=46){$term_out="Vento teso";}
        if ($term_in*$corr>46 and $term_in*$corr<=56){$term_out="Vento fresco";}
        if ($term_in*$corr>56 and $term_in*$corr<=65){$term_out="Vento forte";}
        if ($term_in*$corr>65 and $term_in*$corr<=74){$term_out="Burrasca";}
        if ($term_in*$corr>74 and $term_in*$corr<=83){$term_out="Burrasca forte";}
        if ($term_in*$corr>83 and $term_in*$corr<=102){$term_out="Tempesta";}
        if ($term_in*$corr>102 and $term_in*$corr<=120){$term_out="Tempesta Violenta";}
        if ($term_in*$corr>120) {$term_out="Uragano";}
        return $term_out;
      }
      if ($opt_language=="hu"){
        if ($unit=="mph"){$corr=1.609344;}
        if ($term_in*$corr>=0 and $term_in*$corr<=1){$term_out="sz&eacute;lcsend";}
        if ($term_in*$corr>1 and $term_in*$corr<=6){$term_out="gyenge szell&#337;";}
        if ($term_in*$corr>6 and $term_in*$corr<=11){$term_out="enyhe sz&eacute;l";}
        if ($term_in*$corr>11 and $term_in*$corr<=19){$term_out="gyenge sz&eacute;l";}
        if ($term_in*$corr>19 and $term_in*$corr<=29){$term_out="m&eacute;rs&eacute;kelt sz&eacute;l";}
        if ($term_in*$corr>29 and $term_in*$corr<=39){$term_out="&eacute;l&eacute;nk sz&eacute;l";}
        if ($term_in*$corr>39 and $term_in*$corr<=49){$term_out="er&#337;s sz&eacute;l";}
        if ($term_in*$corr>49 and $term_in*$corr<=60){$term_out="viharos sz&eacute;l";}
        if ($term_in*$corr>60 and $term_in*$corr<=72){$term_out="&eacute;l&eacute;nk viharos sz&eacute;l";}
        if ($term_in*$corr>72 and $term_in*$corr<=85){$term_out="heves vihar";}
        if ($term_in*$corr>85 and $term_in*$corr<=100){$term_out="d&uuml;h&ouml;ng&#337;s vihar";}
        if ($term_in*$corr>100 and $term_in*$corr<=115){$term_out="heves sz&eacute;lv&eacute;sz";}
        if ($term_in*$corr>115 and $term_in*$corr<=120){$term_out=" ork&aacute;n";}
        if ($term_in*$corr>120) {$term_out="hurrik&aacute;n";}
        return $term_out;
      }
      
      if ($opt_language=="pl"){
        if ($unit=="mph"){$corr=1.609344;}
        if ($term_in*$corr>=1 and $term_in*$corr<6){$term_out="bardzo s&#322;aby";}
        if ($term_in*$corr>=6 and $term_in*$corr<12){$term_out="s&#322;aby";}
        if ($term_in*$corr>=12 and $term_in*$corr<20){$term_out="&#322;agodny";}
        if ($term_in*$corr>=20 and $term_in*$corr<29){$term_out="umiarkowany";}
        if ($term_in*$corr>=29 and $term_in*$corr<39){$term_out="do&#347;&#263; silny";}
        if ($term_in*$corr>=39 and $term_in*$corr<50){$term_out="silny";}
        if ($term_in*$corr>=50 and $term_in*$corr<62){$term_out="bardzo silny";}
        if ($term_in*$corr>=62 and $term_in*$corr<75){$term_out="gwa&#322;towny";}
        if ($term_in*$corr>=75 and $term_in*$corr<89){$term_out="wichura";}
        if ($term_in*$corr>=89 and $term_in*$corr<103){$term_out="silna wichura";}
        if ($term_in*$corr>=103 and $term_in*$corr<117){$term_out="gwa&#322;towna wichura";}
        if ($term_in*$corr>=117) {$term_out="huragan";}
        return $term_out;
      }
      if ($opt_language=="pt"){
        if ($unit=="mph"){$corr=1.609344;}
        if ($term_in*$corr>10 and $term_in*$corr<=19){$term_out="Brisa leve";}
        if ($term_in*$corr>19 and $term_in*$corr<=28){$term_out="Brisa";}
        if ($term_in*$corr>28 and $term_in*$corr<=37){$term_out="Brisa";}
        if ($term_in*$corr>37 and $term_in*$corr<=46){$term_out="Brisa calma";}
        if ($term_in*$corr>46 and $term_in*$corr<=56){$term_out="Brisa forte";}
        if ($term_in*$corr>56 and $term_in*$corr<=65){$term_out="Vento forte";}
        if ($term_in*$corr>65 and $term_in*$corr<=74){$term_out="Ventania";}
        if ($term_in*$corr>74 and $term_in*$corr<=83){$term_out="Ventania forte";}
        if ($term_in*$corr>83 and $term_in*$corr<=102){$term_out="Temporal";}
        if ($term_in*$corr>102 and $term_in*$corr<=120){$term_out="Tempestade";}
        if ($term_in*$corr>120) {$term_out="Furac&atilde;o";}
        return $term_out;
      }
      
}
function GG_funx_translate_pressure($term_in,$unit,$opt_language){
        $corr=1;
        $term_out="";
        if ($opt_language=="ar"){
          if ($unit ==  "in"){$corr=33.8637526;}
          if ($term_in*$corr<1003){$term_out="منخفضه";}
          if ($term_in*$corr>1020){$term_out="مرتفعه";}
        }
        if ($opt_language=="de"){
          if ($unit ==  "in"){$corr=33.8637526;}
          if ($term_in*$corr<1003){$term_out="Tief";}
          if ($term_in*$corr>1020){$term_out="Hoch";}
        }
        if ($opt_language=="en"){
          if ($unit ==  "mbar"){$corr=33.8637526;}
          if ($term_in/$corr<29.6){$term_out="Low ";}     //29.6
          if ($term_in/$corr>30.1){$term_out="High ";}
        }
        if ($opt_language=="es"){
          if ($unit ==  "in"){$corr=33.8637526;}
          if ($term_in*$corr<1003){$term_out="Baja ";}
          if ($term_in*$corr>1020){$term_out="Alta ";}
        }
        if ($opt_language=="fr"){
          if ($unit ==  "in"){$corr=33.8637526;}
          if ($term_in*$corr<1003){$term_out="Basse ";}
          if ($term_in*$corr>1020){$term_out="Haute ";}
        }
        if ($opt_language=="it"){
          if ($unit ==  "in"){$corr=33.8637526;}
          if ($term_in*$corr<1003){$term_out="Bassa ";}
          if ($term_in*$corr>1020){$term_out="Alta ";}
        }
        if ($opt_language=="hu"){
          if ($unit ==  "in"){$corr=33.8637526;}
          if ($term_in*$corr<1003){$term_out="alacsony ";}
          if ($term_in*$corr>1020){$term_out="magas ";}
        }
        if ($opt_language=="pl"){
          if ($unit ==  "in"){$corr=33.8637526;}
          if ($term_in*$corr<1003){$term_out="Niskie ";}
          if ($term_in*$corr>1020){$term_out="Wysokie ";}
        }
        if ($opt_language=="pt"){
          if ($unit ==  "in"){$corr=33.8637526;}
          if ($term_in*$corr<1003){$term_out="Bassa ";}
          if ($term_in*$corr>1020){$term_out="Alta ";}
        }
        if ($opt_language=="xx"){ //to transfer pressure from mb to in
          if ($unit ==  "in"){$corr=33.8637526;}
          $term_out=round($term_in/$corr,2);
        }        
        return $term_out;       
}

function GG_funx_translate_inch($term_in,$unit){
        $corr=1;
        $term_out="";
        if ($unit ==  "m"){$corr=25;}
        $term_out=round(1/5*$term_in*$corr,0)*5;
        return $term_out;       
}
function GG_funx_translate_speed($term_in,$unit){
        $corr=0.621371192;
        $term_out="";
        if ($unit ==  "mph"){
        $term_out=round($term_in*$corr,0);}
        if ($unit ==  "kmph"){
        $term_out=round($term_in/$corr,0);;
        }
        return $term_out;       
}
 
function GG_funx_translate_fahrenheit($term_in,$unit){
        $corr=1;
        $corr2=0;
        $term_out="";
        if ($unit ==  "m"){$corr=1.8;$corr2=32;}
        $term_out=round(($term_in-$corr2)/$corr,0);
        if ($unit ==  "m"){
        $term_out=$term_out."&deg;C";
        }
        else
        {
        $term_out=$term_out."&deg;F";
        }
        return $term_out;       
}

function GG_funx_translate_winddirections($term_in,$language){
  $term_in_save=$term_in;
  if (strlen($term_in)==1){$term_in=$term_in."X";}
  if (strlen($term_in)==2){$term_in=$term_in."X";}
  if ($language=="ar"){
  $term_out=str_replace(
  array('NXX','NNE','NEX','ENE','EXX','ESE','SEX','SSE','SXX','SSW','SWX','WSW','WXX','WNW','NWX','NNW','VAR'),	
  array('N','NNO','NO','ONO','O','OSO','SO','SSO','S','SSW','SW','WSW','W','WNW','NW','NNW','اتجاهات مختلفه'),	
  $term_in);
  return $term_out;
  }
  if ($language=="de"){
  $term_out=str_replace(
  array('NXX','NNE','NEX','ENE','EXX','ESE','SEX','SSE','SXX','SSW','SWX','WSW','WXX','WNW','NWX','NNW','VAR'),	
  array('N','NNO','NO','ONO','O','OSO','SO','SSO','S','SSW','SW','WSW','W','WNW','NW','NNW','verschiedenen Richtungen'),	
  $term_in);
  return $term_out;
  } 
  if ($language=="es"){
  $term_out=str_replace(
  array('NXX','NNE','NEX','ENE','EXX','ESE','SEX','SSE','SXX','SSW','SWX','WSW','WXX','WNW','NWX','NNW','VAR'),	
  array('N','NNE','NE','ENE','E','ESE','SE','SSE','S','SSO','SO','OSO','O','ONO','NO','NNO','VAR'),	
  $term_in);
  return $term_out;
  }
  if ($language=="fr"){
  $term_out=str_replace(
  array('NXX','NNE','NEX','ENE','EXX','ESE','SEX','SSE','SXX','SSW','SWX','WSW','WXX','WNW','NWX','NNW','VAR'),	
  array('N','NNE','NE','ENE','E','ESE','SE','SSE','S','SSO','SO','OSO','O','ONO','NO','NNO','VAR'),	
  $term_in);
  return $term_out;
  } 
  if ($language=="it"){
  $term_out=str_replace(
  array('NXX','NNE','NEX','ENE','EXX','ESE','SEX','SSE','SXX','SSW','SWX','WSW','WXX','WNW','NWX','NNW','VAR'),	
  array('N','NNE','NE','ENE','E','ESE','SE','SSE','S','SSO','SO','OSO','O','ONO','NO','NNO','VAR'),	
  $term_in);
  return $term_out;
  }
  if ($language=="hu"){
  $term_out=str_replace(
  array('NXX','NNE','NEX','ENE','EXX','ESE','SEX','SSE','SXX','SSW','SWX','WSW','WXX','WNW','NWX','NNW','VAR'),	
  array('&Eacute;-i','&Eacute;-&Eacute;K-i','&Eacute;K-i','K-&Eacute;K-i','K-i','K-DK-i','DK-i','D-DK-i','D-i','D-DNy-i','DNy-i','Ny-DNy-i','Ny-i','Ny-&Eacute;Ny-i','&Eacute;Ny-i','&Eacute;-&Eacute;Ny-i','v&aacute;ltoz&oacute; ir&aacute;ny&uacute;'),	
   $term_in);
  return $term_out;
  }
  if ($language=="pl"){
  $term_out=str_replace(
  array('NXX','NNE','NEX','ENE','EXX','ESE','SEX','SSE','SXX','SSW','SWX','WSW','WXX','WNW','NWX','NNW','VAR'),	
  array(' p&#243;&#322;nocy',' p&#243;&#322;nocy i p&#243;&#322;nocnego wschodu',' p&#243;&#322;nocnego wschodu','e wschodu i p&#243;&#322;nocnego wschodu','e wschodu','e wschodu i po&#322;udniowego wschodu',' po&#322;udniowego wschodu',' po&#322;udnia i po&#322;udniowego wschodu',' po&#322;udnia',' po&#322;udnia i po&#322;udniowego zachodu',' po&#322;udniowego zachodu',' zachodu i po&#322;udniowego zachodu',' zachodu',' zachodu i p&#243;&#322;nocnego zachodu',' p&#243;&#322;nocnego zachodu',' p&#243;&#322;nocy i p&#243;&#322;nocnego zachodu',' kierunk&#243;w zmiennych'),	
  $term_in);
  return $term_out;
  } 
  if ($language=="pt"){
  $term_out=str_replace(
  array('NXX','NNE','NEX','ENE','EXX','ESE','SEX','SSE','SXX','SSW','SWX','WSW','WXX','WNW','NWX','NNW','VAR'),	
  array('N','NNE','NE','ENE','E','ESE','SE','SSE','S','SSO','SO','OSO','O','ONO','NO','NNO','VAR'),	
  $term_in);
  
  //$term_out="HALLO";
  return $term_out;
  }
  if($term_out==""){
  $term_out=$term_in_save;
  return $term_out;}
       
}
function GG_funx_translate_winddirections_degrees($term_in){
$term_in=round($term_in/22.5,0);
//echo $term_in."<br /><br />";
  If($term_in==0 or $term_in==16 ){$term_out="N";}
  elseIf($term_in==1){$term_out="NNE";}
  elseIf($term_in==2){$term_out="NE";}
  elseIf($term_in==3){$term_out="ENE";}
  elseIf($term_in==4){$term_out="E";}
  elseIf($term_in==5){$term_out="ESE";}
  elseIf($term_in==6){$term_out="SE";}
  elseIf($term_in==7){$term_out="SSE";}
  elseIf($term_in==8){$term_out="S";}
  elseIf($term_in==9){$term_out="SSW";}
  elseIf($term_in==10){$term_out="SW";}
  elseIf($term_in==11){$term_out="WSW";}
  elseIf($term_in==12){$term_out="W";}
  elseIf($term_in==13){$term_out="WNW";}
  elseIf($term_in==14){$term_out="NW";}
  elseIf($term_in==15){$term_out="NNW";}
return $term_out;

}

function GG_funx_translate_capital($term_in)
{
    $trans_array=array(	
        'Georgetown','Andorra la Vella','Abu Dhabi','Kabul','Saint John&rsquo;s','The Valley','Tirana','Yerevan','Willemstad',
        'Luanda','Ross Dependency','Buenos Aires','Pago Pago','Vienna','Canberra','Oranjestad','Mariehamn','Baku','Stepanakert',
        'Sarajevo','Bridgetown','Dhaka','Brussels','Ouagadougou','Sofia','Manama','Bujumbura','Porto-Novo','Hamilton',
        'Bandar Seri Begawan','La Paz','Brasilia','Nassau','Thimphu','Bouvet Island','Gaborone','Minsk','Belmopan','Ottawa',
        'West Island','Kinshasa','Bangui','Brazzaville','Bern','Yamoussoukro','Avarua','Santiago','Yaounde','Beijing','Bogota',
        'San Jose','Havana','Praia','The Settlement','Nicosia','Nicosia','Prague','Berlin','Djibouti','Copenhagen','Roseau','Santo Domingo',
        'Algiers','Quito','Tallinn','Cairo','Asmara','Madrid','Addis Ababa','Helsinki','Suva','Stanley','Palikir','Torshavn',
        'Paris','Libreville','London','Saint George&rsquo;s','Tbilisi','Sokhumi','Tskhinvali','Cayenne','Saint Peter Port','Accra',
        'Gibraltar','Nuuk','Banjul','Conakry','Gustavia','Marigot','Basse-Terre','Malabo','Athens','South Georgia','Guatemala',
        'Hagatna','Bissau','Georgetown','Hong Kong','Heard Island','Tegucigalpa','Zagreb','Port-au-Prince','Budapest','Jakarta',
        'Dublin','Jerusalem','Douglas','New Delhi','British Indian Ocean Territory','Baghdad','Tehran','Reykjavik','Rome','Saint Helier',
        'Kingston','Amman','Tokyo','Nairobi','Bishkek','Phnom Penh','Tarawa','Moroni','Basseterre','Pyongyang','Seoul','Kuwait','George Town',
        'Astana','Vientiane','Beirut','Castries','Vaduz','Colombo','Monrovia','Maseru','Vilnius','Luxembourg','Riga','Tripoli',
        'Rabat','Monaco','Chisinau','Tiraspol','Podgorica','Antananarivo','Majuro','Skopje','Bamako','Naypyidaw','Ulaanbaatar',
        'Macau','Saipan','Fort-de-France','Nouakchott','Plymouth','Valletta','Port Louis','Male','Lilongwe','Mexico','Kuala Lumpur',
        'Maputo','Windhoek','Noumea','Niamey','Kingston','Abuja','Managua','Amsterdam','Oslo','Kathmandu','Yaren','Alofi',
        'Wellington','Muscat','Panama','Lima','Papeete','Clipperton Island','Port Moresby','Manila','Islamabad','Warsaw',
        'Saint-Pierre','Adamstown','San Juan','Lisbon','Melekeok','Asuncion','Doha','Saint-Denis','Bucharest','Belgrade',
        'Moscow','Kigali','Riyadh','Honiara','Victoria','Khartoum','Stockholm','Singapore','Jamestown','Ljubljana','Longyearbyen',
        'Bratislava','Freetown','San Marino','Dakar','Mogadishu','Hargeisa','Paramaribo','Sao Tome','San Salvador','Damascus',
        'Mbabane','Edinburgh','Grand Turk','N&rsquo;Djamena','Martin-de-Viviès','Lome','Bangkok','Dushanbe','Tokelau','Dili',
        'Ashgabat','Tunis','Nuku&rsquo;alofa','Ankara','Port-of-Spain','Funafuti','Taipei','Dar es Salaam','Kiev','Kampala',
        'Baker Island','Washington','Montevideo','Tashkent','Vatican City','Kingstown','Caracas','Road Town','Charlotte Amalie',
        'Hanoi','Port-Vila','Mata&rsquo;utu','Apia','Sanaa','Mamoudzou','Pretoria','Lusaka','Harare','London',

    );                                                                       
    $term_out=str_replace(array( 	
        'AC','AD','AE','AF','AG','AI','AL','AM','AN','AO','AQ','AR','AS','AT','AU','AW','AX','AZ','AZ','BA','BB','BD','BE','BF','BG','BH','BI','BJ','BM','BN','BO','BR','BS','BT','BV','BW','BY','BZ','CA','CC',
        'CD','CF','CG','CH','CI','CK','CL','CM','CN','CO','CR','CU','CV','CX','CY','CY','CZ','DE','DJ','DK','DM','DO','DZ','EC','EE','EG','ER','ES','ET','FI','FJ','FK','FM','FO','FR','GA','GB','GD','GE','GE',
        'GE','GF','GG','GH','GI','GL','GM','GN','GP','GP','GP','GQ','GR','GS','GT','GU','GW','GY','HK','HM','HN','HR','HT','HU','ID','IE','IL','IM','IN','IO','IQ','IR','IS','IT','JE','JM','JO','JP','KE','KG',
        'KH','KI','KM','KN','KP','KR','KW','KY','KZ','LA','LB','LC','LI','LK','LR','LS','LT','LU','LV','LY','MA','MC','MD','MD','ME','MG','MH','MK','ML','MM','MN','MO','MP','MQ','MR','MS','MT','MU','MV','MW',
        'MX','MY','MZ','NA','NC','NE','NF','NG','NI','NL','NO','NP','NR','NU','NZ','OM','PA','PE','PF','PF','PG','PH','PK','PL','PM','PN','PR','PT','PW','PY','QA','RE','RO','RS','RU','RW','SA','SB','SC','SD',
        'SE','SG','SH','SI','SJ','SK','SL','SM','SN','SO','SO','SR','ST','SV','SY','SZ','TA','TC','TD','TF','TG','TH','TJ','TK','TL','TM','TN','TO','TR','TT','TV','TW','TZ','UA','UG','UM','US','UY','UZ','VA',
        'VC','VE','VG','VI','VN','VU','WF','WS','YE','YT','ZA','ZM','ZW','UK',
    ),
    $trans_array		
    ,	
    $term_in);
    return $term_out;    
} 


function GG_funx_translate_country($term_in)
{$trans_array=array(	
        'AL','AK','AZ','AR','CA','CO','CT','DE','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC',
        'SD','TN','TX','UT','VT','VA','WA','WV','WI','WY',
    );                                                                       
    $term_out=str_replace(array( 	
        'alabama','alaska','arizona','arkansas','california','colorado','connecticut','delaware','florida','georgia','hawaii','idaho','illinois','indiana','iowa','kansas','kentucky','louisiana','maine',
        'maryland','massachusetts','michigan','minnesota','mississippi','missouri','montana','mebraska','mevada','mew hampshire','new jersey','new mexico','new york','north carolina','north dakota','ohio',
        'oklahoma','oregon','pennsylvania','rhode island','south carolina','south dakota','tennessee','texas','utah','vermont','virginia','washington','west virginia','wisconsin','wyoming',
    ),
    $trans_array		
    ,	
    $term_in);
    return $term_out;
} 

function GG_funx_translate_statename($term_in,$opt_language_index)
{

$term_in=strtolower($term_in);
$state_name=array(

'af'=>array('Afghanistan','Afghanistan','Afganist&aacute;n','Afghanistan','Afganistan','Afganiszt&aacute;n','Afeganist&atilde;o','أفغانستان'),
'al'=>array('Albanien','Albanie','Albania','Albania','Albania','Alb&aacute;nia','Alb&acirc;nia','ألبانيا'),
'dz'=>array('Algerien','Alg&eacute;rie','Argelia','Algeria','Algieria','Alg&eacute;ria','Arg&eacute;lia','الجزائر'),
'ad'=>array('Andorra','Andorre','Andorra','Andorra','Andora','Andorra','Andorra','أندورا'),
'ao'=>array('Angola','Angola','Angola','Angola','Angola','Angola','Angola','أنغولا'),
'ai'=>array('Anguilla','Anguilla','Anguila','Anguilla','Anguilla','Anguilla','Anguilla','أنغيلا'),
'aq'=>array('Antarktika','Antarctique','ntarctica','Antartide','Antarktyda','Antarktisz','Antarctica','القارة القطبية الجنوبيه'),
'ag'=>array('Antigua und Barbuda','Antigua-et-Barbuda','Antigua y Barbuda','Antigua e Barbuda','Antigua i Barbuda','Antigua &eacute;s Barbuda','Ant&iacute;gua e Barbuda','أنتيغوا وبربودا'),
'sa'=>array('Saudi-Arabien','Arabie saoudite','Arabia Saudita','Arabia Saudita','Arabia Saudyjska','Sza&uacute;d-Ar&aacute;bia','Ar&aacute;bia Saudita','المملكة العربية السعودية'),
'ar'=>array('Argentinien','Argentine','Argentina','Argentina','Argentyna','Argent&iacute;na','Argentina','الأرجنتين'),
'am'=>array('Armenien','Arm&eacute;nie','Armenia','Armenia','Armenia','&Ouml;rm&eacute;nyorsz&aacute;g','Arm&eacute;nia','أرمينيا'),
'aw'=>array('Aruba','Aruba','Aruba','Aruba','Aruba','Aruba','Aruba','أوربا'),
'au'=>array('Australien','Australie','Australia','Australia','Australia','Ausztr&aacute;lia','Austr&aacute;lia','استراليا'),
'at'=>array('&Ouml;sterreich','Autriche','Austria','Austria','Austria','Ausztria','&Aacute;ustria','النمسا'),
'az'=>array('Aserbaidschan','Azerba&iuml;djan','Azerbaiy&aacute;n','Azerbaigian','Azerbejd&#380;an','Azerbajdzs&aacute;n','Azerbaij&atilde;o','أذربيجان'),
'bs'=>array('Bahamas','Bahamas','Bahamas','Bahamas','Bahamy','Bahama-szigetek','Baamas','جزر البهاما'),
'bh'=>array('Bahrain','Bahre&iuml;n','Bar&eacute;in','Bahrain','Bahrajn','Bahrein','Bar&eacute;m','البحرين'),
'bd'=>array('Bangladesch','Bangladesh','Banglad&eacute;s','Bangladesh','Bangladesz','Banglades','Bangladeche','البنجلاديش'),
'bb'=>array('Barbados','Barbade','Barbados','Barbados','Barbados','Barbados','Barbados','بربادوس'),
'be'=>array('Belgien','Belgique','B&eacute;lgica','Belgio','Belgia','Belgium','B&eacute;lgica','بلجيكا'),
'bz'=>array('Belize','Belize','Belice','Belize','Belize','Belize','Belize','بليز'),
'bj'=>array('Benin','B&eacute;nin','Ben&iacute;n','Benin','Benin','Benin','Benim','بنين'),
'bm'=>array('Bermuda','Bermudes','Bermudas','Bermuda','Bermudy','Bermuda','Bermudas','برمودا'),
'bt'=>array('Bhutan','Bhoutan','But&aacute;n','Bhutan','Bhutan','Bhut&aacute;n','But&atilde;o','بوتان'),
'by'=>array('Wei&szlig;russland','Bi&eacute;lorussie','Bielorrusia','Bielorussia','Bia&#322;oru&#347;','Feh&eacute;roroszorsz&aacute;g','Bielorr&uacute;ssia','روسيا البيضاء'),
'mm'=>array('Burma','Birmanie','Birmania','Birmania','Birma','Mianmar','Myanmar','بورما'),
'bo'=>array('Bolivien','Bolivie','Bolivia','Bolivia','Boliwia','Bol&iacute;via','Bol&iacute;via','بوليفيا'),
'bq'=>array('Bonaire, Sint Eustatius und Saba','Bonaire, Saint-Eustache et Saba','onaire, Saint Eustatius and Saba','Isole BES','Bonaire, Sint Eustatius i Saba','Bonaire, Saint Eustatius and Saba','Bonaire, Saint Eustatius and Saba','بونير'),
'bw'=>array('Botswana','Botswana','Botsuana','Botswana','Botswana','Botswana','Botsuana','بوتسوانا'),
'ba'=>array('Bosnien und Herzegowina','Bosnie-Herz&eacute;govine','Bosnia y Herzegovina','Bosnia-Erzegovina','Bo&#347;nia i Hercegowina','Bosznia-Hercegovina','B&#243;snia e Herzegovina','البوسنة والهرسك'),
'br'=>array('Brasilien','Br&eacute;sil','Brasil','Brasile','Brazylia','Braz&iacute;lia','Brasil','البرازيل'),
'bn'=>array('Brunei Darussalam','Brunei','Brun&eacute;i','Brunei','Brunei','Brunei','Brunei','بروناي دار السلام'),
'io'=>array('Britisches Territorium im Indischen Ozean','Territoire britannique de loc&eacute;an Indien','Territorio Brit&aacute;nico del Oc&eacute;ano &Iacute;ndico','Territorio britannico delloceano Indiano','Brytyjskie Terytorium Oceanu Indyjskiego','Brit Indiai-&#243;ce&aacute;ni Ter&uuml;let','British Indian Ocean Territory','إقليم المحيط الهندي البريطاني'),
'vg'=>array('Britische Jungferninseln','&Icirc;les Vierges britanniques','Islas V&iacute;rgenes Brit&aacute;nicas','Isole Vergini britanniche','Brytyjskie Wyspy Dziewicze','Brit Virgin-szigetek','Ilhas Virgens Brit&acirc;nicas','جزر فيرجن البريطانية'),
'bf'=>array('Burkina Faso','Burkina Faso','Burkina Faso','Burkina Faso','Burkina Faso','Burkina Faso','Burquina Faso','بوركينا فاسو'),
'bi'=>array('Burundi','Burundi','Burundi','Burundi','Burundi','Burundi','Bur&uacute;ndi','بوروندي'),
'bg'=>array('Bulgarien','Bulgarie','Bulgaria','Bulgaria','Bu&#322;garia','Bulg&aacute;ria','Bulg&aacute;ria','بلغاريا'),
'cl'=>array('Chile','Chili','Chile','Cile','Chile','Chile','Chile','تشيلي'),
'cn'=>array('China, Volksrepublik','Chine','China','Cina','Chiny','K&iacute;na','China','الصين'),
'hr'=>array('Kroatien','Croatie','Croacia','Croazia','Chorwacja','Horv&aacute;torsz&aacute;g','Cro&aacute;cia','كرواتيا'),
'cw'=>array('Cura&ccedil;ao','Cura&ccedil;ao','ura&ccedil;ao','Cura&ccedil;ao','Cura&ccedil;ao','Cura&ccedil;ao','Cura&ccedil;ao','كوراساو'),
'cy'=>array('Zypern','Chypre (pays)','Chipre','Cipro','Cypr','Ciprus','Chipre','قبرص'),
'td'=>array('Tschad','Tchad','Chad','Ciad','Czad','Cs&aacute;d','Chade','تشاد'),
'me'=>array('Montenegro','Mont&eacute;n&eacute;gro','Montenegro','Montenegro','Czarnog&#243;ra','Montenegr&#243;','Montenegro','الجبل الأسود'),
'cz'=>array('Tschechische Republik','R&eacute;publique tch&egrave;que','Rep&uacute;blica Checa','Repubblica Ceca','Czechy','Csehorsz&aacute;g','Rep&uacute;blica Checa','الجمهورية التشيكية'),
'um'=>array('United States Minor Outlying Islands','&Icirc;les mineures &eacute;loign&eacute;es des &Eacute;tats-Unis','nited States Minor Outlying Islands','Isole minori degli Stati Uniti','Dalekie Wyspy Mniejsze Stan&#243;w Zjednoczonych','Amerikai Csendes-&#243;ce&aacute;ni szigetek','United States Minor Outlying Islands','الولايات المتحدة البعيدة الجزر الصغيرة'),
'dk'=>array('D&auml;nemark','Danemark','Dinamarca','Danimarca','Dania','D&aacute;nia','Dinamarca','الدنيمارك'),
'cd'=>array('Kongo','R&eacute;p. d&eacute;m. du Congo / (R&eacute;publique d&eacute;mocratique du Congo)','Rep. Dem. del Congo','Rep. Dem. del Congo','Demokratyczna Republika Konga','Kong&#243;i Demokratikus K&ouml;zt&aacute;rsas&aacute;g (Zaire)','Congo-Kinshasa','الكونغو'),
'dm'=>array('Dominica','Dominique','Dominica','Dominica','Dominika','Dominikai K&ouml;z&ouml;ss&eacute;g','Dom&iacute;nica','دومينيكا'),
'do'=>array('Dominikanische Republik','R&eacute;publique dominicaine','Rep&uacute;blica Dominicana','Repubblica Dominicana','Dominikana','Dominikai K&ouml;zt&aacute;rsas&aacute;g','Rep&uacute;blica Dominicana','جمهورية الدومينيكان'),
'dj'=>array('Dschibuti','Djibouti','Yibuti','Gibuti','D&#380;ibuti','Dzsibuti','Jibuti','جيبوتي'),
'eg'=>array('&Auml;gypten','&Eacute;gypte','Egipto','Egitto','Egipt','Egyiptom','Egipto','مصر'),
'ec'=>array('Ecuador','&Eacute;quateur','Ecuador','Ecuador','Ekwador','Ecuador','Equador','الاكوادور'),
'er'=>array('Eritrea','&Eacute;rythr&eacute;e','Eritrea','Eritrea','Erytrea','Eritrea','Eritreia','اريتريا'),
'ee'=>array('Estland','Estonie','Estonia','Estonia','Estonia','&Eacute;sztorsz&aacute;g','Est&#243;nia','استونيا'),
'et'=>array('&Auml;thiopien','&Eacute;thiopie','Etiop&iacute;a','Etiopia','Etiopia','Eti&#243;pia','Eti&#243;pia','أثيوبيا'),
'fk'=>array('Falklandinseln','&Icirc;les Malouines','Islas Malvinas','Isole Falkland','Falklandy','Falkland-szigetek','Falkland Islands','جزر فوكلاند'),
'fj'=>array('Fidschi','Fidji','Fiyi','Figi','Fid&#380;i','Fidzsi','Fiji','فيجي'),
'ph'=>array('Philippinen','Philippines','Filipinas','Filippine','Filipiny','F&uuml;l&ouml;p-szigetek','Filipinas','الفلبين'),
'fi'=>array('Finnland','Finlande','Finlandia','Finlandia','Finlandia','Finnorsz&aacute;g','Finl&acirc;ndia','فنلندا'),
'fr'=>array('Frankreich','France','Francia','Francia','Francja','Franciaorsz&aacute;g','Fran&ccedil;a','فرنسا'),
'tf'=>array('Franz&ouml;sische S&uuml;d- und Antarktisgebiete','Terres australes et antarctiques fran&ccedil;aises','Territorios Australes Franceses','Terre Australi e Antartiche Francesi','Francuskie Terytoria Po&#322;udniowe i Antarktyczne','Francia D&eacute;li &eacute;s Antarktiszi Ter&uuml;letek','French Southern Territories','الأراضي الفرنسية الجنوبية والقارة القطبية الجنوبية'),
'ga'=>array('Gabun','Gabon','Gab&#243;n','Gabon','Gabon','Gabon','Gab&atilde;o','الغابون'),
'gm'=>array('Gambia','Gambie','Gambia','Gambia','Gambia','Gambia','G&acirc;mbia','غامبيا'),
'gs'=>array('S&uuml;dgeorgien und die S&uuml;dlichen Sandwichinseln','G&eacute;orgie du Sud-et-les &Icirc;les Sandwich du Sud','Islas Georgias del Sur y Sandwich del Sur','Georgia del Sud e isole Sandwich','Georgia Po&#322;udniowa i Sandwich Po&#322;udniowy','D&eacute;li-Georgia &eacute;s D&eacute;li-Sandwich-szigetek','South Georgia and the South Sandwich Islands','جورجيا الجنوبية وجزر ساندويتش الجنوبية'),
'gh'=>array('Ghana','Ghana','Ghana','Ghana','Ghana','Gh&aacute;na','Gana','غانا'),
'gi'=>array('Gibraltar','Gibraltar','Gibraltar','Gibilterra','Gibraltar','Gibralt&aacute;r','Gibraltar','جبل طارق'),
'gr'=>array('Griechenland','Gr&egrave;ce','Grecia','Grecia','Grecja','G&ouml;r&ouml;gorsz&aacute;g','Gr&eacute;cia','يونان'),
'gd'=>array('Grenada','Grenade (pays)','Granada','Grenada','Grenada','Grenada','Granada','غرينادا'),
'gl'=>array('Gr&ouml;nland','Groenland','Groenlandia','Groenlandia','Grenlandia','Gr&ouml;nland','Greenland','غرينلاند'),
'ge'=>array('Georgien','G&eacute;orgie (pays)','Georgia','Georgia','Gruzja','Gr&uacute;zia','Ge&#243;rgia','جورجيا'),
'gu'=>array('Guam','Guam','Guam','Guam','Guam','Guam','Guam','غوام'),
'gg'=>array('Guernsey','Guernesey','Guernsey','Guernsey','Guernsey','Guernsey','Guernsey','غيرنسي'),
'gy'=>array('Guyana','Guyana','Guyana','Guyana','Gujana','Guyana','Guiana','غيانا'),
'gf'=>array('Franz&ouml;sisch-Guayana','Guyane','Guayana Francesa','Guyana Francese','Gujana Francuska','Francia Guyana','French Guiana','غيانا الفرنسية'),
'gp'=>array('Guadeloupe','Guadeloupe','Guadalupe','Guadalupa','Gwadelupa','Guadeloupe','Guadalupe','جوادلوب'),
'gt'=>array('Guatemala','Guatemala','Guatemala','Guatemala','Gwatemala','Guatemala','Guatemala','غواتيمالا'),
'gn'=>array('Guinea','Guin&eacute;e','Guinea','Guinea','Gwinea','Guinea','Guin&eacute;','غينيا'),
'gw'=>array('Guinea-Bissau','Guin&eacute;e-Bissau','Guinea-Bissau','Guinea-Bissau','Gwinea Bissau','Bissau-Guinea','Guin&eacute;-Bissau','غينيا بيساو'),
'gq'=>array('&Auml;quatorialguinea','Guin&eacute;e &eacute;quatoriale','Guinea Ecuatorial','Guinea Equatoriale','Gwinea R&#243;wnikowa','Egyenl&iacute;tői-Guinea','Guin&eacute; Equatorial','غينيا الاستوائية'),
'ht'=>array('Haiti','Ha&iuml;ti','Hait&iacute;','Haiti','Haiti','Haiti','Haiti','هايتي'),
'es'=>array('Spanien','Espagne','pain','Spagna','Hiszpania','Spanyolorsz&aacute;g','Espanha','إسبانيا'),
'nl'=>array('Niederlande','Pays-Bas','Pa&iacute;ses Bajos','Paesi Bassi','Holandia','Hollandia','Pa&iacute;ses Baixos','هولندا'),
'hn'=>array('Honduras','Honduras','Honduras','Honduras','Honduras','Honduras','Honduras','هندوراس'),
'hk'=>array('Hongkong','Hong Kong','Hong Kong','Hong Kong','Hongkong','Hongkong','Hong Kong','هونغ كونغ'),
'in'=>array('Indien','Inde','India','India','Indie','India','&Iacute;ndia','الهند'),
'id'=>array('Indonesien','Indon&eacute;sie','Indonesia','Indonesia','Indonezja','Indon&eacute;zia','Indon&eacute;sia','أندونيسيا'),
'iq'=>array('Irak','Irak','Irak','Iraq','Irak','Irak','Iraque','العراق'),
'ir'=>array('Iran','Iran','Ir&aacute;n','Iran','Iran','Ir&aacute;n','Ir&atilde;o','إيران'),
'ie'=>array('Irland','Irlande (pays)','Irlanda','Irlanda','Irlandia','&Iacute;rorsz&aacute;g','Irlanda','إيرلندا'),
'is'=>array('Island','Islande','Islandia','Islanda','Islandia','Izland','Isl&acirc;ndia','أيسلندا'),
'il'=>array('Israel','Isra&euml;l','Israel','Israele','Izrael','Izrael','Israel','فلسطين'),
'jm'=>array('Jamaika','Jama&iuml;que','Jamaica','Giamaica','Jamajka','Jamaica','Jamaica','جامايكا'),
'jp'=>array('Japan','Japon','Jap&#243;n','Giappone','Japonia','Jap&aacute;n','Jap&atilde;o','اليابان'),
'ye'=>array('Jemen','emen','Yemen','Yemen','Jemen','Jemen','I&eacute;men','اليمن'),
'je'=>array('Jersey','Jersey','Jersey','Jersey','Jersey','Jersey','Jersey','جيرسي'),
'jo'=>array('Jordanien','Jordanie','Jordania','Giordania','Jordania','Jord&aacute;nia','Jord&acirc;nia','الأردن'),
'ky'=>array('Kaimaninseln','&Icirc;les Ca&iuml;mans','Islas Caim&aacute;n','Isole Cayman','Kajmany','Kajm&aacute;n-szigetek','Ilhas Cayman','جزر كايمان'),
'kh'=>array('Kambodscha','Cambodge','Camboya','Cambogia','Kambod&#380;a','Kambodzsa','Camboja','كمبوديا'),
'cm'=>array('Kamerun','Cameroun','Camer&uacute;n','Camerun','Kamerun','Kamerun','Camar&otilde;es','الكاميرون'),
'ca'=>array('Kanada','Canada','Canad&aacute;','Canada','Kanada','Kanada','Canad&aacute;','كندا'),
'qa'=>array('Katar','Qatar','Catar','Qatar','Katar','Katar','Catar','قطر'),
'kz'=>array('Kasachstan','Kazakhstan','Kazajist&aacute;n','Kazakistan','Kazachstan','Kazahszt&aacute;n','Cazaquist&atilde;o','كازاخستان'),
'ke'=>array('Kenia','Kenya','Kenia','Kenya','Kenia','Kenya','Qu&eacute;nia','كينيا'),
'kg'=>array('Kirgisistan','Kirghizistan','Kirguist&aacute;n','Kirghizistan','Kirgistan','Kirgiziszt&aacute;n','Quirguizist&atilde;o','قيرغيزستان'),
'ki'=>array('Kiribati','Kiribati','Kiribati','Kiribati','Kiribati','Kiribati','Quirib&aacute;ti','كيريباتي'),
'co'=>array('Kolumbien','Colombie','Colombia','Colombia','Kolumbia','Kolumbia','Col&ocirc;mbia','كولومبيا'),
'km'=>array('Komoren','Comores','Comoras','Comore','Komory','Comore-szigetek','Comores','جزر القمر'),
'cg'=>array('Kongo','Congo-Brazzaville / (Congo)','Rep&uacute;blica del Congo','Repubblica del Congo','Kongo','Kong&#243;i K&ouml;zt&aacute;rsas&aacute;g (Kong&#243;)','Congo-Brazzaville','الكونغو'),
'kr'=>array('S&uuml;dkorea','Cor&eacute;e du Sud','Corea del Sur','Corea del Sud','Korea Po&#322;udniowa','D&eacute;l-Korea (Koreai K&ouml;zt&aacute;rsas&aacute;g)','Coreia do Sul','كوريا الجنوبية'),
'kp'=>array('Nordkorea','Cor&eacute;e du Nord','Corea del Norte','Corea del Nord','Korea P&#243;&#322;nocna','&Eacute;szak-Korea (Koreai NDK)','Coreia do Norte','كوريا الشمالية'),
'cr'=>array('Costa Rica','Costa Rica','Costa Rica','Costa Rica','Kostaryka','Costa Rica','Costa Rica','كوستا ريكا'),
'cu'=>array('Kuba','Cuba','Cuba','Cuba','Kuba','Kuba','Cuba','كوبا'),
'kw'=>array('Kuwait','Kowe&iuml;t','Kuwait','Kuwait','Kuwejt','Kuvait','Kuwait','الكويت'),
'la'=>array('Laos, Demokratische Volksrepublik','Laos','ao Peoples Democratic Republic','Laos','Laos','Laosz','Laos','لاوس'),
'ls'=>array('Lesotho','Lesotho','Lesoto','Lesotho','Lesotho','Lesotho','Lesoto','ليسوتو'),
'lb'=>array('Libanon','Liban','L&iacute;bano','Libano','Liban','Libanon','L&iacute;bano','لبنان'),
'lr'=>array('Liberia','Liberia','Liberia','Liberia','Liberia','Lib&eacute;ria','Lib&eacute;ria','ليبيريا'),
'ly'=>array('Libyen','Libye','Libia','Libia','Libia','L&iacute;bia','L&iacute;bia','ليبيا'),
'li'=>array('Liechtenstein','Liechtenstein','Liechtenstein','Liechtenstein','Liechtenstein','Liechtenstein','Listenstaine','Liechtenstein'),
'lt'=>array('Litauen','Lituanie','Lituania','Lituania','Litwa','Litv&aacute;nia','Litu&acirc;nia','ليتوانيا'),
'lu'=>array('Luxemburg','Luxembourg (pays)','Luxemburgo','Lussemburgo','Luksemburg','Luxemburg','Luxemburgo','لوكسمبورغ'),
'mk'=>array('Mazedonien','Mac&eacute;doine (pays)','Rep&uacute;blica de Macedonia','Macedonia','Macedonia','Maced&#243;nia','Maced&#243;nia','مقدونيا'),
'mg'=>array('Madagaskar','Madagascar','Madagascar','Madagascar','Madagaskar','Madagaszk&aacute;r','Madag&aacute;scar','مدغشقر'),
'yt'=>array('Mayotte','Mayotte','Mayotte','Mayotte','Majotta','Mayotte','Mayotte','مايوت'),
'mo'=>array('Macao','Macao','Macao','Macao','Makau','Maka&#243;','Macau','ماكاو'),
'mw'=>array('Malawi','Malawi','Malaui','Malawi','Malawi','Malawi','Mal&aacute;vi','ملاوي'),
'mv'=>array('Malediven','Maldives','Maldivas','Maldive','Malediwy','Mald&iacute;v-szigetek','Maldivas','جزر المالديف'),
'my'=>array('Malaysia','Malaisie','Malasia','Malesia','Malezja','Malajzia','Mal&aacute;sia','ماليزيا'),
'ml'=>array('Mali','Mali','Mal&iacute;','Mali','Mali','Mali','Mali','مالي'),
'mt'=>array('Malta','Malte','Malta','Malta','Malta','M&aacute;lta','Malta','مالطا'),
'mp'=>array('N&ouml;rdliche Marianen','&Icirc;les Mariannes du Nord','Islas Marianas del Norte','Isole Marianne Settentrionali','Mariany P&#243;&#322;nocne','&Eacute;szaki-Mariana-szigetek','Northern Mariana Islands','جزر ماريانا الشمالية'),
'ma'=>array('Marokko','Maroc','Marruecos','Marocco','Maroko','Marokk&#243;','Marrocos','المغرب'),
'mq'=>array('Martinique','Martinique','Martinica','Martinica','Martynika','Martinique','Martinica','مارتينيك'),
'mr'=>array('Mauretanien','Mauritanie','Mauritania','Mauritania','Mauretania','Maurit&aacute;nia','Maurit&acirc;nia','موريتانيا'),
'mu'=>array('Mauritius','Maurice (pays)','Mauricio','Mauritius','Mauritius','Mauritius','Maur&iacute;cia','موريشيوس'),
'mx'=>array('Mexiko','Mexique','M&eacute;xico','Messico','Meksyk','Mexik&#243;','M&eacute;xico','المكسيك'),
'fm'=>array('Mikronesien','Micron&eacute;sie (pays)','Micronesia','Micronesia','Mikronezja','Mikron&eacute;zia','Micron&eacute;sia','ميكرونيزيا'),
'mc'=>array('Monaco','Monaco','M&#243;naco','Monaco','Monako','Monaco','M&#243;naco','موناكو'),
'mn'=>array('Mongolei','Mongolie','Mongolia','Mongolia','Mongolia','Mong&#243;lia','Mong&#243;lia','منغوليا'),
'ms'=>array('Montserrat','Montserrat','Montserrat','Montserrat','Montserrat','Montserrat (Egyes&uuml;lt Kir&aacute;lys&aacute;g)','Montserrat','مونتسيرات'),
'mz'=>array('Mosambik','Mozambique','Mozambique','Mozambico','Mozambik','Mozambik','Mo&ccedil;ambique','موزامبيق'),
'md'=>array('Moldawien','Moldavie','Moldavia','Moldavia','Mo&#322;dawia','Moldova','Mold&aacute;via','مولدافيا'),
'na'=>array('Namibia','Namibie','amibia','Namibia','Namibia','Nam&iacute;bia','Nam&iacute;bia','ناميبيا'),
'nr'=>array('Nauru','Nauru','Nauru','Nauru','Nauru','Nauru','Nauru','ناورو'),
'np'=>array('Nepal','N&eacute;pal','Nepal','Nepal','Nepal','Nep&aacute;l','Nepal','نيبال'),
'de'=>array('Deutschland','Allemagne','Alemania','Germania','Niemcy','N&eacute;metorsz&aacute;g','Alemanha','ألمانيا'),
'ne'=>array('Niger','Niger','N&iacute;ger','Niger','Niger','Niger','N&iacute;ger','النيجر'),
'ng'=>array('Nigeria','Nigeria','igeria','Nigeria','Nigeria','Nig&eacute;ria','Nig&eacute;ria','نيجيريا'),
'ni'=>array('Nicaragua','Nicaragua','icaragua','Nicaragua','Nikaragua','Nicaragua','Nicar&aacute;gua','نيكاراغوا'),
'nu'=>array('Niue','Niue','Niue','Niue','Niue','Niue','Niue','نيوي'),
'nf'=>array('Norfolkinsel','Norfolk','Norfolk','Isola Norfolk','Norfolk','Norfolk-sziget','Norfolk Island','نورفولك'),
'no'=>array('Norwegen','Norv&egrave;ge','Noruega','Norvegia','Norwegia','Norv&eacute;gia','Noruega','النرويج'),
'nc'=>array('Neukaledonien','Nouvelle-Cal&eacute;donie','Nueva Caledonia','Nuova Caledonia','Nowa Kaledonia','&Uacute;j-Kaled&#243;nia','New Caledonia','كاليدونيا الجديدة'),
'nz'=>array('Neuseeland','Nouvelle-Z&eacute;lande','Nueva Zelanda','Nuova Zelanda','Nowa Zelandia','&Uacute;j-Z&eacute;land','Nova Zel&acirc;ndia','نيوزيلندا'),
'om'=>array('Oman','Oman','Om&aacute;n','Oman','Oman','Om&aacute;n','Om&atilde;','عمان'),
'pk'=>array('Pakistan','Pakistan','Pakist&aacute;n','Pakistan','Pakistan','Pakiszt&aacute;n','Paquist&atilde;o','باكستان'),
'pw'=>array('Palau','Palaos','Palaos','Palau','Palau','Palau','Palau','بالاو'),
'ps'=>array('Pal&auml;stinensische Autonomiegebiete','Palestine','Autoridad Nacional Palestina','Autorit&agrave; Nazionale Palestinese','Palestyna','Palesztina','Palestinian Territory','فلسطين'),
'pa'=>array('Panama','Panam&aacute;','Panam&aacute;','Panam&aacute;','Panama','Panama','Panam&aacute;','بنما'),
'pg'=>array('Papua-Neuguinea','Papouasie-Nouvelle-Guin&eacute;e','Pap&uacute;a Nueva Guinea','Papua Nuova Guinea','Papua-Nowa Gwinea','P&aacute;pua &Uacute;j-Guinea','Papua-Nova Guin&eacute;','بابوا غينيا الجديدة'),
'py'=>array('Paraguay','Paraguay','Paraguay','Paraguay','Paragwaj','Paraguay','Paraguai','باراغواي'),
'pe'=>array('Peru','P&eacute;rou','eru','Per&ugrave;','Peru','Peru','Peru','بيرو'),
'pn'=>array('Pitcairninseln','&Icirc;les Pitcairn','Islas Pitcairn','Isole Pitcairn','Pitcairn','Pitcairn-szigetek','Pitcairn','بيتكيرن'),
'pf'=>array('Franz&ouml;sisch-Polynesien','Polyn&eacute;sie fran&ccedil;aise','Polinesia Francesa','Polinesia francese','Polinezja Francuska','Francia Polin&eacute;zia','Polin&eacute;sia Francesa','بولينيزيا الفرنسية'),
'pl'=>array('Polen','Pologne','Polonia','Polonia','Polska','Lengyelorsz&aacute;g','Pol&#243;nia','بولندا'),
'pr'=>array('Puerto Rico','Porto Rico','Puerto Rico','Porto Rico','Portoryko','Puerto Rico','Porto Rico','بورتوريكو'),
'pt'=>array('Portugal','Portugal','Portugal','Portogallo','Portugalia','Portug&aacute;lia','Portugal','البرتغال'),
'za'=>array('S&uuml;dafrika','Afrique du Sud','Sud&aacute;frica','Sudafrica','Republika Po&#322;udniowej Afryki','D&eacute;l-afrikai K&ouml;zt&aacute;rsas&aacute;g','&Aacute;frica do Sul','جنوب أفريقيا'),
'cv'=>array('Kap Verde','Cap-Vert','Cabo Verde','Capo Verde','Republika Zielonego Przyl&#261;dka','Z&ouml;ld-foki K&ouml;zt&aacute;rsas&aacute;g','Cabo Verde','كابو فيردي'),
'cf'=>array('Zentralafrikanische Republik','R&eacute;publique centrafricaine','Rep&uacute;blica Centroafricana','Repubblica Centrafricana','Republika &#346;rodkowoafryka&#324;ska','K&ouml;z&eacute;p-Afrika','Rep&uacute;blica Centro-Africana','جمهورية أفريقيا الوسطى'),
're'=>array('R&eacute;union','La R&eacute;union','Reuni&#243;n','Riunione','Reunion','R&eacute;union R&eacute;union','Reunion','ريونيون'),
'ru'=>array('Russische F&ouml;deration','Russie','Rusia','Russia','Rosja','Oroszorsz&aacute;g','R&uacute;ssia','روسيا'),
'ro'=>array('Rum&auml;nien','Roumanie','Rumania','Romania','Rumunia','Rom&aacute;nia','Rom&eacute;nia','رومانيا'),
'rw'=>array('Ruanda','Rwanda','Ruanda','Ruanda','Rwanda','Ruanda','Ruanda','رواندا'),
'eh'=>array('Westsahara','ahara occidental','Rep&uacute;blica &Aacute;rabe Saharaui Democr&aacute;tica','Sahara Occidentale','Sahara Zachodnia','Nyugat-Szahara','Western Sahara','الصحراء الغربية'),
'kn'=>array('St. Kitts und Nevis','Saint-Christophe-et-Ni&eacute;v&egrave;s','San Crist&#243;bal y Nieves','Saint Kitts e Nevis','Saint Kitts i Nevis','Saint Kitts &eacute;s Nevis','S&atilde;o Crist&#243;v&atilde;o e Neves','شارع كيتس نيفيس اوند'),
'lc'=>array('St. Lucia','Sainte-Lucie','Santa Luc&iacute;a','Santa Lucia','Saint Lucia','Saint Lucia','Santa L&uacute;cia','سانتا لوسيا'),
'vc'=>array('St. Vincent und die Grenadinen','Saint-Vincent-et-les-Grenadines','San Vicente y las Granadinas','Saint Vincent e Grenadine','Saint Vincent i Grenadyny','Saint Vincent &eacute;s a Grenadine-szigetek','S&atilde;o Vicente e Granadinas','سانت فنسنت وغرينادين'),
'bl'=>array('Saint-Barth&eacute;lemy','Saint-Barth&eacute;lemy','San Bartolom&eacute;','Saint-Barth&eacute;lemy','Saint-Barth&eacute;lemy','Saint Barth&eacute;lemy','Saint-Barth&eacute;lemy','سانت بارتيليمي'),
'mf'=>array('Saint-Martin','Saint-Martin (Antilles fran&ccedil;aises)','San Mart&iacute;n','Saint-Martin','Saint-Martin','Saint Martin','S&atilde;o Martinho','Saint Pierre and Miquelon'),
'pm'=>array('Saint-Pierre und Miquelon','Saint-Pierre-et-Miquelon','San Pedro y Miquel&#243;n','Saint-Pierre e Miquelon','Saint-Pierre i Miquelon','Saint-Pierre &eacute;s Miquelon','Saint-Pierre e Miquelon','El Salvador'),
'sv'=>array('El Salvador','Salvador','El Salvador','El Salvador','Salwador','El Salvador','Salvador','Samoa'),
'ws'=>array('Samoa','Samoa','Samoa','Samoa','Samoa','Szamoa','Samoa','ساموا'),
'as'=>array('Amerikanisch-Samoa','Samoa am&eacute;ricaines','merican Samoa','Samoa americane','Samoa Ameryka&#324;skie','Amerikai Szamoa','American Samoa','ميريكان ساموا'),
'sm'=>array('San Marino','Saint-Marin','San Marino','San Marino','San Marino','San Marino','S&atilde;o Marinho','سان مارينو'),
'sn'=>array('Senegal','S&eacute;n&eacute;gal','Senegal','Senegal','Senegal','Szeneg&aacute;l','Senegal','السنغال'),
'rs'=>array('Serbien','Serbie','Serbia','Serbia','Serbia','Szerbia','S&eacute;rvia','صربيا'),
'sc'=>array('Seychellen','Seychelles','Seychelles','Seychelles','Seszele','Seychelle-szigetek','Seicheles','سيشيل'),
'sl'=>array('Sierra Leone','Sierra Leone','Sierra Leona','Sierra Leone','Sierra Leone','Sierra Leone','Serra Leoa','Sierra Leone'),
'sg'=>array('Singapur','Singapour','Singapur','Singapore','Singapur','Szingap&uacute;r','Singapura','سنغافورة'),
'sx'=>array('Sint Maarten','Saint-Martin','int Maarten','int Maarten','Sint Maarten','Sint Maarten','Sint Maarten','سانت مارتن'),
'so'=>array('Somalia','Somalie','Somalia','Somalia','Somalia','Szom&aacute;lia','Som&aacute;lia','الصومال'),
'lk'=>array('Sri Lanka','Sri Lanka','Sri Lanka','Sri Lanka','Sri Lanka','Sr&iacute; Lanka','Sri Lanca','سري لانكا'),
'us'=>array('Vereinigte Staaten von Amerika','&Eacute;tats-Unis','ri Lanka','Stati Uniti dAmerica','Stany Zjednoczone','Amerikai Egyes&uuml;lt &Aacute;llamok','Estados Unidos','الولايات المتحدة الأمريكية'),
'sz'=>array('Swasiland','Swaziland','Suazilandia','Swaziland','Suazi','Szv&aacute;zif&ouml;ld','Suazil&acirc;ndia','سوازيلاند'),
'sd'=>array('Sudan','Soudan','Sud&aacute;n','Sudan','Sudan','Szud&aacute;n','Sud&atilde;o','سودان'),
'sr'=>array('Suriname','Suriname','Surinam','Suriname','Surinam','Suriname','Suriname','سورينام'),
'sj'=>array('Svalbard und Jan Mayen','Svalbard et &icirc;le Jan Mayen','Svalbard y Jan Mayen','Svalbard e Jan Mayen','Svalbard i Jan Mayen','Svalbard (Spitzberg&aacute;k) &eacute;s Jan Mayen','Svalbard','ذ سفالبارد جان ماين'),
'sy'=>array('Syrien, Arabische Republik','Syrie','Siria','Siria','Syria','Sz&iacute;ria','S&iacute;ria','سوريا'),
'ch'=>array('Schweiz','Suisse','Suiza','Svizzera','Szwajcaria','Sv&aacute;jc','Su&iacute;&ccedil;a','سويسرا'),
'se'=>array('Schweden','Su&egrave;de','Suecia','Svezia','Szwecja','Sv&eacute;dorsz&aacute;g','Su&eacute;cia','السويد'),
'sk'=>array('Slowakei','Slovaquie','Eslovaquia','Slovacchia','S&#322;owacja','Szlov&aacute;kia','Eslov&aacute;quia','سلوفاكيا'),
'si'=>array('Slowenien','Slov&eacute;nie','Eslovenia','Slovenia','S&#322;owenia','Szlov&eacute;nia','Eslov&eacute;nia','سلوفينيا'),
'tj'=>array('Tadschikistan','Tadjikistan','Tayikist&aacute;n','Tagikistan','Tad&#380;ykistan','T&aacute;dzsikiszt&aacute;n','Tajiquist&atilde;o','طاجيكستان'),
'th'=>array('Thailand','Tha&iuml;lande','Tailandia','Thailandia','Tajlandia','Thaif&ouml;ld','Tail&acirc;ndia','تايلاند'),
'tw'=>array('Taiwan','Ta&iuml;wan / (R&eacute;publique de Chine (Ta&iuml;wan))','Taiw&aacute;n','Taiwan','Tajwan','Tajvan','Taiwan','تايوان'),
'tz'=>array('Tansania, Vereinigte Republik','Tanzanie','Tanzania','Tanzania','Tanzania','Tanz&aacute;nia','Tanz&acirc;nia','تنزانيا'),
'tl'=>array('Westtimor','Timor oriental','Timor Oriental','Timor Est','Timor Wschodni','Kelet-Timor','Timor Leste','تيمور الشرقية'),
'tg'=>array('Togo','Togo','Togo','Togo','Togo','Togo','Togo','توغو'),
'tk'=>array('Tokelau','Tokelau','Tokelau','Tokelau','Tokelau','Tokelau-szigetek','Tokelau','توكيلاو'),
'to'=>array('Tonga','Tonga','onga','Tonga','Tonga','Tonga','Tonga','تونغا'),
'tt'=>array('Trinidad und Tobago','Trinit&eacute;-et-Tobago','Trinidad y Tobago','Trinidad e Tobago','Trynidad i Tobago','Trinidad &eacute;s Tobago','Trindade e Tobago','ترينيداد وتوباغو'),
'tn'=>array('Tunesien','Tunisie','T&uacute;nez','Tunisia','Tunezja','Tun&eacute;zia','Tun&iacute;sia','تونس'),
'tr'=>array('T&uuml;rkei','Turquie','Turqu&iacute;a','Turchia','Turcja','T&ouml;r&ouml;korsz&aacute;g','Turquia','تركيا'),
'tm'=>array('Turkmenistan','Turkm&eacute;nistan','Turkmenist&aacute;n','Turkmenistan','Turkmenistan','T&uuml;rkmeniszt&aacute;n','Turquemenist&atilde;o','تركمانستان'),
'tc'=>array('Turks- und Caicosinseln','&Icirc;les Turques-et-Ca&iuml;ques','Islas Turcas y Caicos','Turks e Caicos','Turks i Caicos','Turks- &eacute;s Caicos-szigetek','Turks e Caicos','الاتراك'),
'tv'=>array('Tuvalu','Tuvalu','Tuvalu','Tuvalu','Tuvalu','Tuvalu','Tuvalu','توفالو'),
'ug'=>array('Uganda','Ouganda','Uganda','Uganda','Uganda','Uganda','Uganda','أوغندا'),
'ua'=>array('Ukraine','Ukraine','kraine','Ucraina','Ukraina','Ukrajna','Ucr&acirc;nia','أوكرانيا'),
'uy'=>array('Uruguay','Uruguay','Uruguay','Uruguay','Urugwaj','Uruguay','Uruguai','أوروغواي'),
'uz'=>array('Usbekistan','Ouzb&eacute;kistan','Uzbekist&aacute;n','Uzbekistan','Uzbekistan','&Uuml;zbegiszt&aacute;n','Usbequist&atilde;o','أوزبكستان'),
'vu'=>array('Vanuatu','Vanuatu','Vanuatu','Vanuatu','Vanuatu','Vanuatu','Vanuatu','فانواتو'),
'wf'=>array('Wallis und Futuna','Wallis et Futuna','Wallis y Futuna','Wallis e Futuna','Wallis i Futuna','Wallis &eacute;s Futuna','Wallis e Futuna','اليس اوند فوتونا'),
'va'=>array('Vatikanstadt','Saint-Si&egrave;ge (&Eacute;tat de la Cit&eacute; du Vatican)','Ciudad del Vaticano','Citt&agrave; del Vaticano','Watykan','Vatik&aacute;n','Vaticano','مدينة الفاتيكان'),
've'=>array('Venezuela','enezuela','Venezuela','Venezuela','Wenezuela','Venezuela','Venezuela','فنزويلا'),
'gb'=>array('Gro&szlig;britannien','Royaume-Uni','Reino Unido','Regno Unito','Wielka Brytania','Egyes&uuml;lt Kir&aacute;lys&aacute;g','Reino Unido','المملكة المتحدة'),
'vn'=>array('Vietnam','iet Nam','Vietnam','Vietnam','Wietnam','Vietnam','Vietname','فيتنام'),
'ci'=>array('Elfenbeink&uuml;ste','C&ocirc;te dIvoire','Costa de Marfil','Costa dAvorio','Wybrze&#380;e Ko&#347;ci S&#322;oniowej','Elef&aacute;ntcsontpart','Costa do Marfim','ساحل العاج'),
'bv'=>array('Bouvetinsel','&Icirc;le Bouvet','Isla Bouvet','Isola Bouvet','Wyspa Bouveta','Bouvet-sziget','Bouvet Island','إيسلا بوفيت'),
'cx'=>array('Weihnachtsinsel','&Icirc;le Christmas','Isla de Navidad','Isola di Natale','Wyspa Bo&#380;ego Narodzenia','Kar&aacute;csony-sziget','Christmas Island','جزيرة كريسماس'),
'im'=>array('Insel Man','&Icirc;le de Man','Isla de Man','Isola di Man','Wyspa Man','Isle of Man','Ilha de Man','جزيرة مان'),
'sh'=>array('St. Helena','Sainte-H&eacute;l&egrave;ne, Ascension et Tristan da Cunha','Santa Helena, A. y T.','SantElena','Wyspa &#346;wi&#281;tej Heleny, Wyspa Wniebowst&#261;pienia i Tristan da Cunha','Szent Ilona','Saint Helena, Ascension and Tristan da Cunha','سانت هيلانة'),
'ax'=>array('&Aring;land','&Aring;land','&Aring;land','sole &Aring;land','Wyspy Alandzkie','&Aring;land','&Aring;land Islands','آلاند'),
'ck'=>array('Cookinseln','&Icirc;les Cook','Islas Cook','Isole Cook','Wyspy Cooka','Cook-szigetek','Cook Islands','جزر كوك'),
'vi'=>array('Amerikanische Jungferninseln','&Icirc;les Vierges des &Eacute;tats-Unis','Islas V&iacute;rgenes de los Estados Unidos','Isole Vergini americane','Wyspy Dziewicze Stan&#243;w Zjednoczonych','Amerikai Virgin-szigetek','Virgin Islands, U.S.','U. S. جزر فيرجن'),
'hm'=>array('Heard und McDonaldinseln','&Icirc;les Heard-et-MacDonald','Islas Heard y McDonald','Isole Heard e McDonald','Wyspy Heard i McDonalda','Heard-sziget &eacute;s McDonald-szigetek','Heard Island and McDonald Islands','جزر ماكدونالد'),
'cc'=>array('Kokosinseln','&Icirc;les Cocos','Islas Cocos','Isole Cocos e Keeling','Wyspy Kokosowe','K&#243;kusz (Keeling)-szigetek','Cocos Islands','جزر كوكوس'),
'mh'=>array('Marshallinseln','Marshall (pays)','Islas Marshall','Isole Marshall','Wyspy Marshalla','Marshall-szigetek','Ilhas Marshall','جزر مارشال'),
'fo'=>array('F&auml;r&ouml;er','&Icirc;les F&eacute;ro&eacute;','Islas Feroe','Isole F&aelig;r &Oslash;er','Wyspy Owcze','Fer&ouml;er','Faeroe Islands','جزر فارو'),
'sb'=>array('Salomonen','Salomon','Islas Salom&#243;n','Isole Salomone','Wyspy Salomona','Salamon-szigetek','Ilhas Salom&atilde;o','جزر سليمان'),
'st'=>array('S&atilde;o Tom&eacute; und Pr&iacute;ncipe','Sao Tom&eacute;-et-Principe','ao Tome and Principe','S&atilde;o Tom&eacute; e Pr&iacute;ncipe','Wyspy &#346;wi&#281;tego Tomasza i Ksi&#261;&#380;&#281;ca','S&atilde;o Tom&eacute; &eacute;s Pr&iacute;ncipe','S&atilde;o Tom&eacute; e Pr&iacute;ncipe','ساو تومي'),
'hu'=>array('Ungarn','Hongrie','Hungr&iacute;a','Ungheria','W&#281;gry','Magyarorsz&aacute;g','Hungria','هنغاريا'),
'it'=>array('Italien','Italie','Italia','Italia','W&#322;ochy','Olaszorsz&aacute;g','It&aacute;lia','إيطاليا'),
'zm'=>array('Sambia','ambia','Zambia','Zambia','Zambia','Zambia','Z&acirc;mbia','زامبيا'),
'zw'=>array('Simbabwe','imbabwe','Zimbabue','Zimbabwe','Zimbabwe','Zimbabwe','Zimbabu&eacute;','زيمبابوي'),
'ae'=>array('Vereinigte Arabische Emirate','&Eacute;mirats arabes unis','Emiratos &Aacute;rabes Unidos','Emirati Arabi Uniti','Zjednoczone Emiraty Arabskie','Egyes&uuml;lt Arab Em&iacute;rs&eacute;gek','Emiratos &Aacute;rabes Unidos','الامارات العربية المتحدة'),
'lv'=>array('Lettland','Lettonie','Letonia','Lettonia','&#321;otwa','Lettorsz&aacute;g','Let&#243;nia','لاتفيا'),
);


if(!isset($state_name[$term_in][$opt_language_index]))
      {$term_out=$term_in;}
    else
      {$term_out=trim($state_name[$term_in][$opt_language_index]);}
    return $term_out;
}

function GG_funx_unscramble_day_night($gg_weather){
 
  $pos=strpos($gg_weather[0][1][1]," PM");
  if($pos> 0){$add_hours=12;}
  $pos=strpos($gg_weather[0][1][1],":");  
  $pos_1=$pos-2;
  $hours=substr($gg_weather[0][1][1],$pos_1,2);
  $minutes=substr($gg_weather[0][1][1],$pos+1,2);
  $time=$hours+$minutes/60+$add_hours;
  if($time<=5.5 or $time>=18.5){
    $gg_weather[0][19][7]="night";}
  else{
    $gg_weather[0][19][7]="day";}  
}
?>