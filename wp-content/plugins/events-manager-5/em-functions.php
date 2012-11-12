<?php

if(!function_exists('em_paginate')){ //overridable e.g. in you mu-plugins folder.
/**
 * Takes a few params and determins a pagination link structure
 * @param string $link
 * @param int $total
 * @param int $limit
 * @param int $page
 * @param int $pagesToShow
 * @return string
 */
function em_paginate($link, $total, $limit, $page=1, $pagesToShow=10){
	if($limit > 0){
		$url_parts = explode('?', $link);
		$base_link = $url_parts[0];
    	//Get querystring for first page without page
    	$query_arr = array();
    	parse_str($url_parts[1], $query_arr);
    	unset($query_arr['page']); unset($query_arr['pno']);
    	$base_querystring = build_query($query_arr);
    	if( !empty($base_querystring) ) $base_querystring = '?'.$base_querystring;
    	//calculate
		$maxPages = ceil($total/$limit); //Total number of pages
		$startPage = ($page <= $pagesToShow) ? 1 : $pagesToShow * (floor($page/$pagesToShow)) ; //Which page to start the pagination links from (in case we're on say page 12 and $pagesToShow is 10 pages)
		$placeholder = urlencode('%PAGE%');
		$link = str_replace('%PAGE%', $placeholder, $link); //To avoid url encoded/non encoded placeholders
	    //Add the back and first buttons
		    $string = ($page>1 && $startPage != 1) ? '<a class="prev page-numbers" href="'.str_replace($placeholder,1,$link).'" title="1">&lt;&lt;</a> ' : '';
		    if($page == 2){
		    	$string .= ' <a class="prev page-numbers" href="'.$base_link.$base_querystring.'" title="2">&lt;</a> ';
		    }elseif($page > 2){
		    	$string .= ' <a class="prev page-numbers" href="'.str_replace($placeholder,$page-1,$link).'" title="'.($page-1).'">&lt;</a> ';
		    }
		//Loop each page and create a link or just a bold number if its the current page
		    for ($i = $startPage ; $i < $startPage+$pagesToShow && $i <= $maxPages ; $i++){
	            if($i == $page){
	                $string .= ' <strong><span class="page-numbers current">'.$i.'</span></strong>';
	            }elseif($i=='1'){
	                $string .= ' <a class="page-numbers" href="'.$base_link.$base_querystring.'" title="'.$i.'">'.$i.'</a> ';
	            }else{
	                $string .= ' <a class="page-numbers" href="'.str_replace($placeholder,$i,$link).'" title="'.$i.'">'.$i.'</a> ';
	            }
		    }
		//Add the forward and last buttons
		    $string .= ($page < $maxPages) ? ' <a class="next page-numbers" href="'.str_replace($placeholder,$page+1,$link).'" title="'.($page+1).'">&gt;</a> ' :' ' ;
		    $string .= ($i-1 < $maxPages) ? ' <a class="next page-numbers" href="'.str_replace($placeholder,$maxPages,$link).'" title="'.$maxPages.'">&gt;&gt;</a> ' : ' ';
		//Return the string
		    return apply_filters('em_paginate', $string);
	}
}
}

/**
 * Creates a wp-admin style navigation.
 * @param string $link
 * @param int $total
 * @param int $limit
 * @param int $page
 * @param int $pagesToShow
 * @return string
 * @uses paginate_links()
 * @uses add_query_arg()
 */
function em_admin_paginate($total, $limit, $page=1, $vars=false, $base = false, $format = ''){
	$return = '<div class="tablenav-pages">';
	$base = !empty($base) ? $base:add_query_arg( 'pno', '%#%' );
	$events_nav = paginate_links( array(
		'base' => $base,
		'format' => $format,
		'total' => ceil($total / $limit),
		'current' => $page,
		'add_args' => $vars
	));
	$return .= sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'dbem') . ' </span>%s',
		number_format_i18n( ( $page - 1 ) * $limit + 1 ),
		number_format_i18n( min( $page * $limit, $total ) ),
		number_format_i18n( $total ),
		$events_nav
	);
	$return .= '</div>';
	return apply_filters('em_admin_paginate',$return,$total,$limit,$page,$vars);
}

/**
 * Takes a url and appends GET params (supplied as an assoc array), it automatically detects if you already have a querystring there
 * @param string $url
 * @param array $params
 * @param bool $html
 * @param bool $encode
 * @return string
 */
function em_add_get_params($url, $params=array(), $html=true, $encode=true){
	//Splig the url up to get the params and the page location
	$url_parts = explode('?', $url);
	$url = $url_parts[0];
	$url_params_dirty = array();
	if(count($url_parts) > 1){
		$url_params_dirty = $url_parts[1];
		//get the get params as an array
		if( !is_array($url_params_dirty) ){
			if( strstr($url_params_dirty, '&amp;') !== false ){
				$url_params_dirty = explode('&amp;', $url_params_dirty);
			}else{
				$url_params_dirty = explode('&', $url_params_dirty);
			}
		}
		//split further into associative array
		$url_params = array();
		foreach($url_params_dirty as $url_param){
			if( !empty($url_param) ){
				$url_param = explode('=', $url_param);
				if(count($url_param) > 1){
					$url_params[$url_param[0]] = $url_param[1];
				}
			}
		}
		//Merge it together
		$params = array_merge($url_params, $params);
	}
	//Now build the array back up.
	$count = 0;
	foreach($params as $key=>$value){
		if( $value !== null ){
			$value = ($encode) ? urlencode($value):$value;
			if( $count == 0 ){
				$url .= "?{$key}=".$value;
			}else{
				$url .= ($html) ? "&amp;{$key}=".$value:"&{$key}=".$value;
			}
			$count++;
		}
	}
	return $url;
}

/**
 * Get a array of countries, translated. Keys are 2 character country iso codes. If you supply a string or array that will be the first value in the array (if array, the array key is the first key in the returned array)
 * @param mixed $add_blank
 * @return array
 */
function em_get_countries($add_blank = false){
	global $em_countries_array;
	if( !is_array($em_countries_array) ){
		$em_countries_array_i18n = array();
		$em_countries_array_i18n['cs'] = array ('AF' => 'Afghanistan', 'AL' => 'Albánie',  'DZ' => 'Alžírsko', 'AS' => 'Americká Samoa',     'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarctica',  'AG' => 'Antigua a Barbuda',   'AR' => 'Argentina',   'AM' => 'Arménie',      'AW' => 'Aruba', 'AU' => 'Austrálie',  'AT' => 'Rakousko',   'AZ' => 'Ázerbájdžán',   'BS' => 'Bahamy',  'BH' => 'Bahrajn', 'BD' => 'Bangladéš',   'BB' => 'Barbados', 'BY' => 'Belorusko',    'BE' => 'Belgie',   'BZ' => 'Belize', 'BJ' => 'Benin', 'BT' => 'Bhútán',  'BO' => 'Bolívie',  'BA' => 'Bosna a Hercegovina',     'BW' => 'Botswana', 'BR' => 'Brazílie',  'VG' => 'Britské Panenské ostrovy',    'BN' => 'Brunej',            'BG' => 'Bulharsko',  'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'Pobreží slonoviny',                 'KH' => 'Kambodža',   'CM' => 'Kamerun',  'CA' => 'Kanada', 'CV' => 'Kapverdy',            'KY'=>'Kajmanské ostrovy',  'CF' => 'Stredoafrická republika',           'TD' => 'Cad',    'CL' => 'Chile', 'CN' => 'Cína',  'CO' => 'Kolumbie',  'KM' => 'Komory',          'CR' => 'Kostarika',  'HR' => 'Chorvatsko',   'CU' => 'Kuba', 'CY' => 'Kypr',   'CZ' => 'Ceská republika',       'KP' => 'Severní Korea',   'CD' => 'Democratic Republic of the Congo', 'DK' => 'Dánsko', 'DJ' => 'Džibuti', 'DM' => 'Dominika', 'DO' => 'Dominikánská republika', 'EC' => 'Ekvádor', 'EG' => 'Egypt', 'SV' => 'Salvador', 'XE' => 'England', 'GQ' => 'Rovníková Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonsko', 'ET' => 'Etiopie', 'FJ' => 'Fidži', 'FI' => 'Finsko', 'FR' => 'Francie', 'PF' => 'Francouzská Polynésie', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Gruzie', 'DE' => 'Nemecko', 'GH' => 'Ghana', 'GR' => 'Recko', 'GL' => 'Grónsko', 'GD' => 'Grenada', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guiné-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Madarsko', 'IS' => 'Island', 'IN' => 'Indie', 'ID' => 'Indonésie', 'IR' => 'Írán', 'IQ' => 'Irák', 'IE' => 'Irsko', 'IL' => 'Izrael', 'IT' => 'Itálie', 'JM' => 'Jamajka', 'JP' => 'Japonsko', 'JO' => 'Jordánsko', 'KZ' => 'Kazachstán', 'KE' => 'Kena', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuvajt', 'KG' => 'Kyrgyzstán', 'LA' => 'Laos', 'LV' => 'Lotyšsko', 'LB' => 'Libanon', 'LS' => 'Lesotho', 'LR' => 'Libérie', 'LY' => 'Libye', 'LI' => 'Lichtenštejnsko', 'LT' => 'Litva', 'LU' => 'Lucembursko', 'MO' => 'Macao', 'MK' => 'Makedonie', 'MG' => 'Madagaskar', 'MW' => 'Malawi', 'MY' => 'Malajsie', 'MV' => 'Maledivy', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshallovy ostrovy', 'MQ' => 'Martinik', 'MU' => 'Mauricius', 'MR' => 'Mauritánie', 'MX' => 'Mexiko', 'FM' => 'Micronésie', 'MD' => 'Moldávie', 'MC' => 'Monako', 'MN' => 'Mongolsko', 'ME' => 'Cerná Hora', 'MA' => 'Maroko', 'MZ' => 'Mozambik', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namibie', 'NR' => 'Nauru', 'NP' => 'Nepál', 'NL' => 'Holandsko', 'AN' => 'Nizozemské Antily', 'NC' => 'Nová Kaledonie', 'NZ' => 'Nový Zéland', 'NI' => 'Nikaragua', 'NE' => 'Niger', 'NG' => 'Nigérie', 'XI' => 'Northern Ireland', 'MP' => 'Severní Mariany', 'NO' => 'Norsko', 'OM' => 'Omán', 'PK' => 'Pákistán', 'PW' => 'Palau', 'PS' => 'Palestina', 'PA' => 'Panama', 'PG' => 'Papua-Nová Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Filipíny', 'PL' => 'Polsko', 'PT' => 'Portugalsko', 'PR' => 'Portoriko', 'QA' => 'Katar', 'CG' => 'Republic of the Congo', 'RO' => 'Rumunsko', 'RU' => 'Rusko', 'RW' => 'Rwanda', 'ST' => 'Svatý Tomáš a Princuv ostrov', 'KN' => 'Svatý Kryštof a Nevis', 'LC' => 'Svatá Lucie', 'VC' => 'Svatý Vincenc a Grenadiny', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Saúdská Arábie', 'XS' => 'Skotsko', 'SN' => 'Senegal', 'RS' => 'Srbsko', 'SC' => 'Seychely', 'SL' => 'Sierra Leone', 'SG' => 'Singapur', 'SK' => 'Slovensko', 'SI' => 'Slovinsko', 'SB' => 'Šalamounovy ostrovy', 'SO' => 'Somálsko', 'ZA' => 'Jihoafrická republika', 'KR' => 'Jižní Korea', 'ES' => 'Španělsko', 'LK' => 'Srí Lanka', 'SD' => 'Súdán', 'SR' => 'Surinam', 'SZ' => 'Svazijsko', 'SE' => 'Švédsko', 'CH' => 'Švýcarsko', 'SY' => 'Sýrie', 'TW' => 'Tchaj-wan', 'TJ' => 'Tádžikistán', 'TZ' => 'Tanzanie', 'TH' => 'Thajsko', 'TL' => 'Východní Timor', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad a Tobago', 'TN' => 'Tunisko', 'TR' => 'Turecko', 'TM' => 'Turkmenistán', 'TV' => 'Tuvalu', 'VI' => 'Americké Panenské ostrovy', 'UG' => 'Uganda', 'UA' => 'Ukrajina', 'AE' => 'Spojené arabské emiráty', 'GB' => 'Spojené království', 'US' => 'Spojené státy', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistán', 'VU' => 'Nové Hebridy', 'VA' => 'Vatikán', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Jemen', 'ZM' => 'Zambie', 'ZW' => 'Zimbabwe' );
		$em_countries_array_i18n['da'] = array ('AF' => 'Afghanistan', 'AL' => 'Albanien', 'DZ' => 'Algeriet', 'AS' => 'Amerikansk Samoa',   'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarktis',   'AG' => 'Antigua og Barbuda',  'AR' => 'Argentina',   'AM' => 'Armenien',     'AW' => 'Aruba', 'AU' => 'Australien', 'AT' => 'Østrig',     'AZ' => 'Aserbajdsjan',  'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh',  'BB' => 'Barbados', 'BY' => 'Belarus',      'BE' => 'Belgien',  'BZ' => 'Belize', 'BJ' => 'Benin', 'BT' => 'Bhutan',  'BO' => 'Bolivia',  'BA' => 'Bosnien-Hercegovina',     'BW' => 'Botswana', 'BR' => 'Brasilien', 'VG' => 'De Britiske Jomfruøer',       'BN' => 'Brunei',            'BG' => 'Bulgarien',  'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'Côte d\'Ivoire',                    'KH' => 'Cambodja',   'CM' => 'Cameroun', 'CA' => 'Canada', 'CV' => 'Kap Verde',           'KY'=>'Caymanøerne',        'CF' => 'Den Centralafrikanske Republik',    'TD' => 'Tchad',  'CL' => 'Chile', 'CN' => 'Kina',  'CO' => 'Colombia',  'KM' => 'Comorerne',       'CR' => 'Costa Rica', 'HR' => 'Kroatien',     'CU' => 'Cuba', 'CY' => 'Cypern', 'CZ' => 'Tjekkiet',              'KP' => 'Nordkorea',       'CD' => 'Congo', 'DK' => 'Danmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Den Dominikanske Republik', 'EC' => 'Ecuador', 'EG' => 'Egypten', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Ækvatorialguinea', 'ER' => 'Eritrea', 'EE' => 'Estland', 'ET' => 'Etiopien', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'Frankrig', 'PF' => 'Fransk Polynesien', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgien', 'DE' => 'Tyskland', 'GH' => 'Ghana', 'GR' => 'Grækenland', 'GL' => 'Grønland (Kalaallit Nunaat)', 'GD' => 'Grenada', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hongkong', 'HU' => 'Ungarn', 'IS' => 'Island', 'IN' => 'Indien', 'ID' => 'Indonesien', 'IR' => 'Iran', 'IQ' => 'Irak', 'IE' => 'Irland', 'IL' => 'Israel', 'IT' => 'Italien', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JO' => 'Jordan', 'KZ' => 'Kasakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Kirgisistan', 'LA' => 'Laos', 'LV' => 'Letland', 'LB' => 'Libanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyen', 'LI' => 'Liechtenstein', 'LT' => 'Litauen', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Makedonien', 'MG' => 'Madagaskar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldiverne', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshalløerne', 'MQ' => 'Martinique', 'MU' => 'Mauritius', 'MR' => 'Mauretanien', 'MX' => 'Mexico', 'FM' => 'Mikronesien', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongoliet', 'ME' => 'Montenegro', 'MA' => 'Marokko', 'MZ' => 'Mozambique', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Nederlandene', 'AN' => 'De Nederlandske Antiller', 'NC' => 'Ny Kaledonien', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Nordmarianerne', 'NO' => 'Norge', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papua Ny Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Filippinerne', 'PL' => 'Polen', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'CG' => 'Republic of the Congo', 'RO' => 'Rumænien', 'RU' => 'Rusland', 'RW' => 'Rwanda', 'ST' => 'São Tomé og Príncipe', 'KN' => 'Saint Kitts og Nevis', 'LC' => 'Saint Lucia', 'VC' => 'Saint Vincent og Grenadinerne', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Saudi-Arabien', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychellerne', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakiet', 'SI' => 'Slovenien', 'SB' => 'Salomonøerne', 'SO' => 'Somalia', 'ZA' => 'Sydafrika', 'KR' => 'Sydkorea', 'ES' => 'Spanien', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Surinam', 'SZ' => 'Swaziland', 'SE' => 'Sverige', 'CH' => 'Schweiz', 'SY' => 'Syrien', 'TW' => 'Taiwan', 'TJ' => 'Tadsjikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Østtimor', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad og Tobago', 'TN' => 'Tunesien', 'TR' => 'Tyrkiet', 'TM' => 'Turkmenistan', 'TV' => 'Tuvalu', 'VI' => 'De Amerikanske Jomfruøer', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'De Forenede Arabiske Emirater', 'GB' => 'Det Forenede Kongerige', 'US' => 'USA', 'UY' => 'Uruguay', 'UZ' => 'Usbekistan', 'VU' => 'Vanuatu', 'VA' => 'Vatikanstaten', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe' );
		$em_countries_array_i18n['es'] = array ('AF' => 'Afganistán',  'AL' => 'Albania',  'DZ' => 'Argelia',  'AS' => 'Samoa Americana',    'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antártida',   'AG' => 'Antigua y Barbuda',   'AR' => 'Argentina',   'AM' => 'Armenia',      'AW' => 'Aruba', 'AU' => 'Australia',  'AT' => 'Austria',    'AZ' => 'Azerbaiyán',    'BS' => 'Bahamas', 'BH' => 'Bahráin', 'BD' => 'Bangladesh',  'BB' => 'Barbados', 'BY' => 'Bielorrusia',  'BE' => 'Bélgica',  'BZ' => 'Belice', 'BJ' => 'Benin', 'BT' => 'Bután',   'BO' => 'Bolivia',  'BA' => 'Bosnia y Hercegovina',    'BW' => 'Botsuana', 'BR' => 'Brasil',    'VG' => 'Islas Vírgenes Británicas',   'BN' => 'Brunéi',            'BG' => 'Bulgaria',   'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'Costa de Marfil',                   'KH' => 'Camboya',    'CM' => 'Camerún',  'CA' => 'Canadá', 'CV' => 'Cabo Verde',          'KY'=>'Islas Caimán',       'CF' => 'República Centroafricana',          'TD' => 'Chad',   'CL' => 'Chile', 'CN' => 'China', 'CO' => 'Colombia',  'KM' => 'Comoras',         'CR' => 'Costa Rica', 'HR' => 'Croacia',      'CU' => 'Cuba', 'CY' => 'Chipre', 'CZ' => 'República Checa',       'KP' => 'Corea del Norte',                      'CD' => 'República Democrática del Congo', 'DK' => 'Dinamarca', 'DJ' => 'Yibuti', 'DM' => 'Dominica', 'DO' => 'República Dominicana', 'EC' => 'Ecuador', 'EG' => 'Egipto', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Guinea Ecuatorial', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Etiopía', 'FJ' => 'Fiyi', 'FI' => 'Finlandia', 'FR' => 'Francia', 'PF' => 'Polinesia Francesa', 'GA' => 'Gabón', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Alemania', 'GH' => 'Ghana', 'GR' => 'Grecia', 'GL' => 'Groenlandia', 'GD' => 'Granada', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haití', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungría', 'IS' => 'Islandia', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Irán', 'IQ' => 'Iraq', 'IE' => 'Irlanda', 'IL' => 'Israel', 'IT' => 'Italia', 'JM' => 'Jamaica', 'JP' => 'Japón', 'JO' => 'Jordania', 'KZ' => 'Kazajistán', 'KE' => 'Kenia', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Kirguizistán', 'LA' => 'Laos', 'LV' => 'Letonia', 'LB' => 'Líbano', 'LS' => 'Lesoto', 'LR' => 'Liberia', 'LY' => 'Libia', 'LI' => 'Liechtenstein', 'LT' => 'Lituania', 'LU' => 'Luxemburgo', 'MO' => 'Macao', 'MK' => 'Macedonia', 'MG' => 'Madagascar', 'MW' => 'Malaui', 'MY' => 'Malasia', 'MV' => 'Maldivas', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Islas Marshall', 'MQ' => 'Martinica', 'MU' => 'Mauricio', 'MR' => 'Mauritania', 'MX' => 'México', 'FM' => 'Micronesia', 'MD' => 'Moldavia', 'MC' => 'Mónaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MA' => 'Marruecos', 'MZ' => 'Mozambique', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Países Bajos', 'AN' => 'Antillas Neerlandesas', 'NC' => 'Nueva Caledonia', 'NZ' => 'Nueva Zelanda', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Islas Marianas del Norte', 'NO' => 'Noruega', 'OM' => 'Omán', 'PK' => 'Pakistán', 'PW' => 'Palaos', 'PS' => 'Palestine', 'PA' => 'Panamá', 'PG' => 'Papúa-Nueva Guinea', 'PY' => 'Paraguay', 'PE' => 'Perú', 'PH' => 'Filipinas', 'PL' => 'Polonia', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'CG' => 'Republic of the Congo', 'RO' => 'Rumania', 'RU' => 'Russia', 'RW' => 'Ruanda', 'ST' => 'Santo Tomé y Príncipe', 'KN' => 'San Cristóbal y Nieves', 'LC' => 'Santa Lucía', 'VC' => 'San Vicente y las Granadinas', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Arabia Saudí', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leona', 'SG' => 'Singapur', 'SK' => 'Eslovaquia', 'SI' => 'Eslovenia', 'SB' => 'Islas Salomón', 'SO' => 'Somalia', 'ZA' => 'Sudáfrica', 'KR' => 'Corea del Sur', 'ES' => 'España', 'LK' => 'Sri Lanka', 'SD' => 'Sudán', 'SR' => 'Surinam', 'SZ' => 'Suazilandia', 'SE' => 'Suecia', 'CH' => 'Suiza', 'SY' => 'Siria', 'TW' => 'Taiwán', 'TJ' => 'Tayikistán', 'TZ' => 'Tanzania', 'TH' => 'Tailandia', 'TL' => 'Timor Oriental', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad y Tobago', 'TN' => 'Túnez', 'TR' => 'Turquía', 'TM' => 'Turkmenistán', 'TV' => 'Tuvalu', 'VI' => 'Islas Vírgenes Americanas', 'UG' => 'Uganda', 'UA' => 'Ucrania', 'AE' => 'Emiratos Árabes Unidos', 'GB' => 'Reino Unido', 'US' => 'Estados Unidos', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistán', 'VU' => 'Vanuatu', 'VA' => 'El Vaticano', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabue' );
		$em_countries_array_i18n['fr'] = array ('AF' => 'Afghanistan', 'AL' => 'Albanie',  'DZ' => 'Algérie',  'AS' => 'Samoa américaines',  'AD' => 'Andorre', 'AO' => 'Angola', 'AQ' => 'Antarctique', 'AG' => 'Antigua-et-Barbuda',  'AR' => 'Argentine',   'AM' => 'Arménie',      'AW' => 'Aruba', 'AU' => 'Australie',  'AT' => 'Autriche',   'AZ' => 'Azerbaïdjan',   'BS' => 'Bahamas', 'BH' => 'Bahreïn', 'BD' => 'Bangladesh',  'BB' => 'Barbade',  'BY' => 'Biélorussie',  'BE' => 'Belgique', 'BZ' => 'Belize', 'BJ' => 'Bénin', 'BT' => 'Bhoutan', 'BO' => 'Bolivie',  'BA' => 'Bosnie-Herzégovine',      'BW' => 'Botswana', 'BR' => 'Brésil',    'VG' => 'Iles Vierges britanniques',   'BN' => 'Brunei',            'BG' => 'Bulgarie',   'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'C&ocirc;te D\'Ivoire',              'KH' => 'Cambodge',   'CM' => 'Cameroun', 'CA' => 'Canada', 'CV' => 'Cap-Vert',            'KY'=>'Iles Cayman',        'CF' => 'République centrafricaine',         'TD' => 'Tchad',  'CL' => 'Chili', 'CN' => 'Chine', 'CO' => 'Colombie',  'KM' => 'Comores',         'CR' => 'Costa Rica', 'HR' => 'Croatie',      'CU' => 'Cuba', 'CY' => 'Chypre', 'CZ' => 'République tchèque',    'KP' => 'Corée du Nord',                        'CD' => 'République démocratique du Congo', 'DK' => 'Danemark', 'DJ' => 'Djibouti', 'DM' => 'Dominique', 'DO' => 'République dominicaine', 'EC' => 'Équateur', 'EG' => 'Égypte', 'SV' => 'Salvador', 'XE' => 'England', 'GQ' => 'Guinée équatoriale', 'ER' => 'Érythrée', 'EE' => 'Estonie', 'ET' => 'Éthiopie', 'FJ' => 'Iles Fidji', 'FI' => 'Finlande', 'FR' => 'France', 'PF' => 'Polynésie française', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Géorgie', 'DE' => 'Allemagne', 'GH' => 'Ghana', 'GR' => 'Grèce', 'GL' => 'Groenland', 'GD' => 'Grenade', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinée', 'GW' => 'Guinée-Bissao', 'GY' => 'Guyana', 'HT' => 'Haïti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hongrie', 'IS' => 'Islande', 'IN' => 'Inde', 'ID' => 'Indonésie', 'IR' => 'Iran', 'IQ' => 'Iraq', 'IE' => 'Irlande', 'IL' => 'Israël', 'IT' => 'Italie', 'JM' => 'Jamaïque', 'JP' => 'Japon', 'JO' => 'Jordanie', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Koweït', 'KG' => 'Kirghizistan', 'LA' => 'Laos', 'LV' => 'Lettonie', 'LB' => 'Liban', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libye', 'LI' => 'Liechtenstein', 'LT' => 'Lituanie', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macédoine', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaisie', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malte', 'MH' => 'Iles Marshall', 'MQ' => 'Martinique', 'MU' => 'Maurice', 'MR' => 'Mauritanie', 'MX' => 'Mexique', 'FM' => 'Micronésie', 'MD' => 'Moldavie', 'MC' => 'Monaco', 'MN' => 'Mongolie', 'ME' => 'Montenegro', 'MA' => 'Maroc', 'MZ' => 'Mozambique', 'MM' => 'le Myanmar', 'NA' => 'Namibie', 'NR' => 'Nauru', 'NP' => 'Népal', 'NL' => 'Pays-Bas', 'AN' => 'Antilles néerlandaises', 'NC' => 'Nouvelle-Calédonie', 'NZ' => 'Nouvelle-Zélande', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Mariannes du Nord', 'NO' => 'Norvège', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papouasie-Nouvelle-Guinée', 'PY' => 'Paraguay', 'PE' => 'Pérou', 'PH' => 'Philippines', 'PL' => 'Pologne', 'PT' => 'Portugal', 'PR' => 'Porto Rico', 'QA' => 'Qatar', 'CG' => 'Republic of the Congo', 'RO' => 'Roumanie', 'RU' => 'Russie', 'RW' => 'Rwanda', 'ST' => 'Sao Tomé-et-Principe', 'KN' => 'Saint-Christophe-et-Niévès', 'LC' => 'Sainte-Lucie', 'VC' => 'Saint-Vincent-et-les-Grenadines', 'WS' => 'Samoa', 'SM' => 'Saint-Marin', 'SA' => 'Arabie saoudite', 'XS' => 'Scotland', 'SN' => 'Sénégal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapour', 'SK' => 'Slovaquie', 'SI' => 'Slovénie', 'SB' => 'Iles Salomon', 'SO' => 'Somalie', 'ZA' => 'Afrique du Sud', 'KR' => 'Corée du Sud', 'ES' => 'Espagne', 'LK' => 'Sri Lanka', 'SD' => 'Soudan', 'SR' => 'Suriname', 'SZ' => 'Swaziland', 'SE' => 'Suède', 'CH' => 'Suisse', 'SY' => 'Syrie', 'TW' => 'Taïwan', 'TJ' => 'Tadjikistan', 'TZ' => 'Tanzanie', 'TH' => 'Thaïlande', 'TL' => 'Timor Oriental', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinité-et-Tobago', 'TN' => 'Tunisie', 'TR' => 'Turquie', 'TM' => 'Turkménistan', 'TV' => 'Tuvalu', 'VI' => 'Iles Vierges américaines', 'UG' => 'Ouganda', 'UA' => 'Ukraine', 'AE' => 'Émirats arabes unis', 'GB' => 'Royaume-Uni', 'US' => 'États-Unis', 'UY' => 'Uruguay', 'UZ' => 'Ouzbékistan', 'VU' => 'Vanuatu', 'VA' => 'Saint-Siège', 'VE' => 'Venezuela', 'VN' => 'Viêt Nam', 'XW' => 'Wales', 'YE' => 'Yémen', 'ZM' => 'Zambie', 'ZW' => 'Zimbabwe' );
		$em_countries_array_i18n['de'] = array ('AF' => 'Afghanistan', 'AL' => 'Albanien', 'DZ' => 'Algerien', 'AS' => 'Amerikanisch-Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarktis',   'AG' => 'Antigua und Barbuda', 'AR' => 'Argentinien', 'AM' => 'Armenien',     'AW' => 'Aruba', 'AU' => 'Australien', 'AT' => 'Österreich', 'AZ' => 'Aserbaidschan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesch', 'BB' => 'Barbados', 'BY' => 'Belarus',      'BE' => 'Belgien',  'BZ' => 'Belize', 'BJ' => 'Benin', 'BT' => 'Bhutan',  'BO' => 'Bolivien', 'BA' => 'Bosnien und Herzegowina', 'BW' => 'Botsuana', 'BR' => 'Brasilien', 'VG' => 'Britische Jungferninseln',    'BN' => 'Brunei Darussalam', 'BG' => 'Bulgarien',  'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'C&ocirc;te D\'Ivoire',              'KH' => 'Kambodscha', 'CM' => 'Kamerun',  'CA' => 'Kanada', 'CV' => 'Kap Verde',           'KY'=>'Kaimaninseln',       'CF' => 'Zentralafrikanische Republik',      'TD' => 'Tschad', 'CL' => 'Chile', 'CN' => 'China', 'CO' => 'Kolumbien', 'KM' => 'Komoren',         'CR' => 'Costa Rica', 'HR' => 'Kroatien',     'CU' => 'Kuba', 'CY' => 'Zypern', 'CZ' => 'Tschechische Republik', 'KP' => 'Demokratische Volksrepublik Korea',    'CD' => 'Demokratische Republik Kongo', 'DK' => 'Dänemark', 'DJ' => 'Dschibuti', 'DM' => 'Dominica', 'DO' => 'Dominikanische Republik', 'EC' => 'Ecuador', 'EG' => 'Ägypten', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Äquatorialguinea', 'ER' => 'Eritrea', 'EE' => 'Estland', 'ET' => 'Äthiopien', 'FJ' => 'Fidschi', 'FI' => 'Finnland', 'FR' => 'Frankreich', 'PF' => 'Französisch-Polynesien', 'GA' => 'Gabun', 'GM' => 'Gambia', 'GE' => 'Georgien', 'DE' => 'Deutschland', 'GH' => 'Ghana', 'GR' => 'Griechenland', 'GL' => 'Grönland', 'GD' => 'Grenada', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'HongKong', 'HU' => 'Ungarn', 'IS' => 'Island', 'IN' => 'Indien', 'ID' => 'Indonesien', 'IR' => 'Iran', 'IQ' => 'Irak', 'IE' => 'Irland', 'IL' => 'Israel', 'IT' => 'Italien', 'JM' => 'Jamaika', 'JP' => 'Japan', 'JO' => 'Jordanien', 'KZ' => 'Kasachstan', 'KE' => 'Kenia', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Kirgisistan', 'LA' => 'Laos', 'LV' => 'Lettland', 'LB' => 'Libanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyen', 'LI' => 'Liechtenstein', 'LT' => 'Litauen', 'LU' => 'Luxemburg', 'MO' => 'Macau', 'MK' => 'Mazedonien', 'MG' => 'Madagaskar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Malediven', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshallinseln', 'MQ' => 'Martinique', 'MU' => 'Mauritius', 'MR' => 'Mauretanien', 'MX' => 'Mexiko', 'FM' => 'Mikronesien', 'MD' => 'Republik Moldau', 'MC' => 'Monaco', 'MN' => 'Mongolei', 'ME' => 'Montenegro', 'MA' => 'Marokko', 'MZ' => 'Mosambik', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Niederlande', 'AN' => 'Niederländische Antillen', 'NC' => 'Neukaledonien', 'NZ' => 'Neuseeland', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Nördliche Marianen', 'NO' => 'Norwegen', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papua-Neuguinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippinen', 'PL' => 'Polen', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Katar', 'CG' => 'Kongo', 'RO' => 'Rumänien', 'RU' => 'Russische Föderation', 'RW' => 'Ruanda', 'ST' => 'São Tomé und Príncipe', 'KN' => 'St. Kitts und Nevis', 'LC' => 'St. Lucia', 'VC' => 'St. Vincent und die Grenadinen', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Saudi-Arabien', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychellen', 'SL' => 'Sierra Leone', 'SG' => 'Singapur', 'SK' => 'Slowakei', 'SI' => 'Slowenien', 'SB' => 'Salomonen', 'SO' => 'Somalia', 'ZA' => 'Südafrika', 'KR' => 'Republik Korea', 'ES' => 'Spanien', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SZ' => 'Swasiland', 'SE' => 'Schweden', 'CH' => 'Schweiz', 'SY' => 'Syrien', 'TW' => 'Taiwan', 'TJ' => 'Tadschikistan', 'TZ' => 'Tansania', 'TH' => 'Thailand', 'TL' => 'Osttimor', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad und Tobago', 'TN' => 'Tunesien', 'TR' => 'Türkei', 'TM' => 'Turkmenistan', 'TV' => 'Tuvalu', 'VI' => 'Amerikanische Jungferninseln', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'Vereinigte Arabische Emirate', 'GB' => 'Vereinigtes Königreich', 'US' => 'Vereinigte Staaten', 'UY' => 'Uruguay', 'UZ' => 'Usbekistan', 'VU' => 'Vanuatu', 'VA' => 'Vatikanstadt', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Jemen', 'ZM' => 'Sambia', 'ZW' => 'Simbabwe' );
		$em_countries_array_i18n['hu'] = array ('AF' => 'Afganisztán', 'AL' => 'Albánia',  'DZ' => 'Algéria',  'AS' => 'Amerikai Szamoa',    'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarktisz',  'AG' => 'Antigua és Barbuda',  'AR' => 'Argentína',   'AM' => 'Örményország', 'AW' => 'Aruba', 'AU' => 'Ausztrália', 'AT' => 'Ausztria',   'AZ' => 'Azerbajdzsán',  'BS' => 'Bahamas', 'BH' => 'Bahrein', 'BD' => 'Banglades',   'BB' => 'Barbados', 'BY' => 'Belorusszia',  'BE' => 'Belgium',  'BZ' => 'Belize', 'BJ' => 'Benin', 'BT' => 'Bhután',  'BO' => 'Bolívia',  'BA' => 'Bosznia-Hercegovina',     'BW' => 'Botswana', 'BR' => 'Brazília',  'VG' => 'Brit Virgin-szigetek',        'BN' => 'Brunei Darussalam', 'BG' => 'Bulgária',   'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'Côte d\'Ivoire (Elefántcsontpart)', 'KH' => 'Kambodzsa',  'CM' => 'Kamerun',  'CA' => 'Kanada', 'CV' => 'Zöld-foki-szigetek',  'KY'=>'Kajmán-szigetek',    'CF' => 'Közép-Afrikai Köztársaság',         'TD' => 'Csád',   'CL' => 'Chile', 'CN' => 'Kína',  'CO' => 'Kolumbia',  'KM' => 'Comore-szigetek', 'CR' => 'Costa Rica', 'HR' => 'Horvátország', 'CU' => 'Kuba', 'CY' => 'Ciprus', 'CZ' => 'Cseh Köztársaság',      'KP' => 'Koreai Népi Demokratikus Köztársaság', 'CD' => 'Kongói Demokratikus Köztársaság', 'DK' => 'Dánia', 'DJ' => 'Dzsibuti', 'DM' => 'Dominika', 'DO' => 'Dominikai Köztársaság', 'EC' => 'Ecuador', 'EG' => 'Egyiptom', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Egyenlítõi Guinea', 'ER' => 'Eritrea', 'EE' => 'Észtország', 'ET' => 'Etiópia', 'FJ' => 'Fidzsi', 'FI' => 'Finnország', 'FR' => 'Franciaország', 'PF' => 'Francia Polinézia', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'GrLzia', 'DE' => 'Németország', 'GH' => 'Ghána', 'GR' => 'Görögország', 'GL' => 'Grönland', 'GD' => 'Grenada', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Bissau-Guinea', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Magyarország', 'IS' => 'Izland', 'IN' => 'India', 'ID' => 'Indonézia', 'IR' => 'Irán', 'IQ' => 'Irak', 'IE' => 'Írország', 'IL' => 'Izrael', 'IT' => 'Olaszország', 'JM' => 'Jamaica', 'JP' => 'Japán', 'JO' => 'Jordánia', 'KZ' => 'Kazahsztán', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuvait', 'KG' => 'Kirgizisztán', 'LA' => 'Lao Népi Demokratikus Köztársaság', 'LV' => 'Litvánia', 'LB' => 'Libanon', 'LS' => 'Lesotho', 'LR' => 'Libéria', 'LY' => 'Líbia', 'LI' => 'Liechtenstein', 'LT' => 'Litvánia', 'LU' => 'Luxemburg', 'MO' => 'Makaó', 'MK' => 'Macedónia', 'MG' => 'Madagaszkár', 'MW' => 'Malawi', 'MY' => 'Malajzia', 'MV' => 'Maldív-szigetek', 'ML' => 'Mali', 'MT' => 'Málta', 'MH' => 'Marshall-szigetek', 'MQ' => 'Martinique', 'MU' => 'Mauritius', 'MR' => 'Mauritánia', 'MX' => 'Mexikó', 'FM' => 'Mikronéziai Szövetségi Államok', 'MD' => 'Moldva', 'MC' => 'Monaco', 'MN' => 'Mongólia', 'ME' => 'Montenegro', 'MA' => 'Marokkó', 'MZ' => 'Mozambik', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namíbia', 'NR' => 'Nauru', 'NP' => 'Nepál', 'NL' => 'Hollandia', 'AN' => 'Holland Antillák', 'NC' => 'Új-Kaledónia', 'NZ' => 'Új-Zéland', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigéria', 'XI' => 'Northern Ireland', 'MP' => 'Észak-Mariana-szigetek', 'NO' => 'Norvégia', 'OM' => 'Omán', 'PK' => 'Pakisztán', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Pápua Új-Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Fülöp-szigetek', 'PL' => 'Lengyelország', 'PT' => 'Portugália', 'PR' => 'Puerto Rico', 'QA' => 'Katar', 'CG' => 'Kongó', 'RO' => 'Románia', 'RU' => 'Orosz Föderáció', 'RW' => 'Ruanda', 'ST' => 'Sao Tome és Principe', 'KN' => 'Saint Kitts és Nevis', 'LC' => 'Szent Lucia', 'VC' => 'Saint Vincent és Grenadines', 'WS' => 'Szamoa', 'SM' => 'San Marino', 'SA' => 'SzaLd-Arábia', 'XS' => 'Scotland', 'SN' => 'Szenegál', 'RS' => 'Serbia', 'SC' => 'Seychelle-szigetek', 'SL' => 'Sierra Leone', 'SG' => 'SzingapLr', 'SK' => 'Szlovákia', 'SI' => 'Szlovénia', 'SB' => 'Salamon-szigetek', 'SO' => 'Szomália', 'ZA' => 'Dél-Afrika', 'KR' => 'Koreai Köztársaság', 'ES' => 'Spanyolország', 'LK' => 'Srí Lanka', 'SD' => 'Szudán', 'SR' => 'Suriname', 'SZ' => 'Szváziföld', 'SE' => 'Svédország', 'CH' => 'Svájc', 'SY' => 'Szíriai Arab Köztársaság', 'TW' => 'Tajvan', 'TJ' => 'Tádzsikisztán', 'TZ' => 'Tanzániai Egyesült Köztársaság', 'TH' => 'Thaiföld', 'TL' => 'Kelet-Timor', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad és Tobago', 'TN' => 'Tunézia', 'TR' => 'Törökország', 'TM' => 'Türkmenisztán', 'TV' => 'Tuvalu', 'VI' => 'Amerikai Virgin-szigetek', 'UG' => 'Uganda', 'UA' => 'Ukrajna', 'AE' => 'Egyesült Arab Emirátusok', 'GB' => 'Egyesült Királyság', 'US' => 'Egyesült Államok', 'UY' => 'Uruguay', 'UZ' => 'Üzbegisztán', 'VU' => 'Vanuatu', 'VA' => 'Vatikán', 'VE' => 'Venezuela', 'VN' => 'Vietnám', 'XW' => 'Wales', 'YE' => 'Jemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe' );
		$em_countries_array_i18n['it'] = array ('AF' => 'Afghanistan', 'AL' => 'Albania',  'DZ' => 'Algeria',  'AS' => 'Samoa americane',    'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antartide',   'AG' => 'Antigua e Barbuda',   'AR' => 'Argentina',   'AM' => 'Armenia',      'AW' => 'Aruba', 'AU' => 'Australia',  'AT' => 'Austria',    'AZ' => 'Azerbaigian',   'BS' => 'Bahamas', 'BH' => 'Bahrein', 'BD' => 'Bangladesh',  'BB' => 'Barbados', 'BY' => 'Bielorussia',  'BE' => 'Belgio',   'BZ' => 'Belize', 'BJ' => 'Benin', 'BT' => 'Bhutan',  'BO' => 'Bolivia',  'BA' => 'Bosnia-Erzegovina',       'BW' => 'Botswana', 'BR' => 'Brasile',   'VG' => 'Isole Vergini britanniche',   'BN' => 'Brunei',            'BG' => 'Bulgaria',   'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'Costa d\'Avorio',                   'KH' => 'Cambogia',   'CM' => 'Camerun',  'CA' => 'Canada', 'CV' => 'Capo Verde',          'KY'=>'Isole Cayman',       'CF' => 'Repubblica Centrafricana',          'TD' => 'Ciad',   'CL' => 'Cile',  'CN' => 'Cina',  'CO' => 'Colombia',  'KM' => 'Comore',          'CR' => 'Costa Rica', 'HR' => 'Croazia',      'CU' => 'Cuba', 'CY' => 'Cipro',  'CZ' => 'Repubblica ceca',       'KP' => 'Corea del Nord', 'CD' => 'Congo', 'DK' => 'Danimarca', 'DJ' => 'Gibuti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egitto', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Guinea Equatoriale', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Etiopia', 'FJ' => 'Figi', 'FI' => 'Finlandia', 'FR' => 'Francia', 'PF' => 'Polinesia francese', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germania', 'GH' => 'Ghana', 'GR' => 'Grecia', 'GL' => 'Groenlandia', 'GD' => 'Grenada', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Ungheria', 'IS' => 'Islanda', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran', 'IQ' => 'Iraq', 'IE' => 'Irlanda', 'IL' => 'Israele', 'IT' => 'Italia', 'JM' => 'Giamaica', 'JP' => 'Giappone', 'JO' => 'Giordania', 'KZ' => 'Kazakistan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Kirghizistan', 'LA' => 'Laos', 'LV' => 'Lettonia', 'LB' => 'Libano', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libia', 'LI' => 'Liechtenstein', 'LT' => 'Lituania', 'LU' => 'Lussemburgo', 'MO' => 'Macao', 'MK' => 'Macedonia', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malesia', 'MV' => 'Maldive', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Isole Marshall', 'MQ' => 'Martinica', 'MU' => 'Maurizio', 'MR' => 'Mauritania', 'MX' => 'Messico', 'FM' => 'Micronesia', 'MD' => 'Moldavia', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MA' => 'Marocco', 'MZ' => 'Mozambico', 'MM' => 'Myanmar(Birmania)', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Paesi Bassi', 'AN' => 'Antille olandesi', 'NC' => 'Nuova Caledonia', 'NZ' => 'Nuova Zelanda', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Marianne settentrionali', 'NO' => 'Norvegia', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papua Nuova Guinea', 'PY' => 'Paraguay', 'PE' => 'Perù', 'PH' => 'Filippine', 'PL' => 'Polonia', 'PT' => 'Portogallo', 'PR' => 'Portorico', 'QA' => 'Qatar', 'CG' => 'Republic of the Congo', 'RO' => 'Romania', 'RU' => 'Russia', 'RW' => 'Ruanda', 'ST' => 'São Tomé e Príncipe', 'KN' => 'Saint Christopher e Nevis', 'LC' => 'Saint Lucia', 'VC' => 'Saint Vincent e Grenadine', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Arabia Saudita', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seicelle', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovacchia', 'SI' => 'Slovenia', 'SB' => 'Isole Salomone', 'SO' => 'Somalia', 'ZA' => 'Sudafrica', 'KR' => 'Corea del Sud', 'ES' => 'Spagna', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SZ' => 'Swaziland', 'SE' => 'Svezia', 'CH' => 'Svizzera', 'SY' => 'Siria', 'TW' => 'Taiwan', 'TJ' => 'Tagikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailandia', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad e Tobago', 'TN' => 'Tunisia', 'TR' => 'Turchia', 'TM' => 'Turkmenistan', 'TV' => 'Tuvalu', 'VI' => 'Isole Vergini americane', 'UG' => 'Uganda', 'UA' => 'Ucraina', 'AE' => 'Emirati arabi uniti', 'GB' => 'Regno Unito', 'US' => 'Stati Uniti', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VA' => 'Vaticano', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe' );
		$em_countries_array_i18n['nl'] = array ('AF' => 'Afghanistan', 'AL' => 'Albanië',  'DZ' => 'Algerije', 'AS' => 'Amerikaans-Samoa',   'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarctica',  'AG' => 'Antigua en Barbuda',  'AR' => 'Argentinië',  'AM' => 'Armenië',      'AW' => 'Aruba', 'AU' => 'Australië',  'AT' => 'Oostenrijk', 'AZ' => 'Azerbeidzjan',  'BS' => 'Bahamas', 'BH' => 'Bahrein', 'BD' => 'Bangladesh',  'BB' => 'Barbados', 'BY' => 'Belarus',      'BE' => 'België',   'BZ' => 'Belize', 'BJ' => 'Benin', 'BT' => 'Bhutan',  'BO' => 'Bolivia',  'BA' => 'Bosnië en Herzegovina',   'BW' => 'Botswana', 'BR' => 'Brazilië',  'VG' => 'Britse Maagdeneilanden',      'BN' => 'Brunei',            'BG' => 'Bulgarije',  'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'Ivoorkust',                         'KH' => 'Cambodja',   'CM' => 'Kameroen', 'CA' => 'Canada', 'CV' => 'Kaapverdië',          'KY'=>'Caymaneilanden',     'CF' => 'Centraal-Afrikaanse Republiek',     'TD' => 'Tsjaad', 'CL' => 'Chili', 'CN' => 'China', 'CO' => 'Colombia',  'KM' => 'Comoren',         'CR' => 'Costa Rica', 'HR' => 'Kroatië',      'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Tsjechië',              'KP' => 'Noord-Korea', 'CD' => 'Democratische Republiek Congo', 'DK' => 'Denemarken', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominicaanse Republiek', 'EC' => 'Ecuador', 'EG' => 'Egypte', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Equatoriaal-Guinea', 'ER' => 'Eritrea', 'EE' => 'Estland', 'ET' => 'Ethiopië', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'Frankrijk', 'PF' => 'Frans-Polynesië', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgië', 'DE' => 'Duitsland', 'GH' => 'Ghana', 'GR' => 'Griekenland', 'GL' => 'Groenland', 'GD' => 'Grenada', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinee', 'GW' => 'Guinee-Bissau', 'GY' => 'Guyana', 'HT' => 'Haïti', 'HN' => 'Honduras', 'HK' => 'HongKong', 'HU' => 'Hongarije', 'IS' => 'IJsland', 'IN' => 'India', 'ID' => 'Indonesië', 'IR' => 'Iran', 'IQ' => 'Irak', 'IE' => 'Ierland', 'IL' => 'Israël', 'IT' => 'Italië', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JO' => 'Jordanië', 'KZ' => 'Kazachstan', 'KE' => 'Kenia', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Koeweit', 'KG' => 'Kirgizië', 'LA' => 'Laos', 'LV' => 'Letland', 'LB' => 'Libanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libië', 'LI' => 'Liechtenstein', 'LT' => 'Litouwen', 'LU' => 'Luxemburg', 'MO' => 'Macau', 'MK' => 'Macedonië', 'MG' => 'Madagaskar', 'MW' => 'Malawi', 'MY' => 'Maleisië', 'MV' => 'Maldiven', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshalleilanden', 'MQ' => 'Martinique', 'MU' => 'Mauritius', 'MR' => 'Mauritanië', 'MX' => 'Mexico', 'FM' => 'Micronesia', 'MD' => 'Moldavië', 'MC' => 'Monaco', 'MN' => 'Mongolië', 'ME' => 'Montenegro', 'MA' => 'Marokko', 'MZ' => 'Mozambique', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namibië', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Nederland', 'AN' => 'Nederlandse Antillen', 'NC' => 'Nieuw-Caledonië', 'NZ' => 'Nieuw-Zeeland', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Noordelijke Marianen', 'NO' => 'Noorwegen', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papoea-Nieuw-Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Filipijnen', 'PL' => 'Polen', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'CG' => 'Republic of the Congo', 'RO' => 'Roemenië', 'RU' => 'Rusland', 'RW' => 'Rwanda', 'ST' => 'Sao Tomé en Principe', 'KN' => 'Saint Kitts en Nevis', 'LC' => 'Saint Lucia', 'VC' => 'Saint Vincent en de Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Saudi-Arabië', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychellen', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slowakije', 'SI' => 'Slovenië', 'SB' => 'Salomonseilanden', 'SO' => 'Somalië', 'ZA' => 'Zuid-Afrika', 'KR' => 'Zuid-Korea', 'ES' => 'Spanje', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SZ' => 'Swaziland', 'SE' => 'Zweden', 'CH' => 'Zwitserland', 'SY' => 'Syrië', 'TW' => 'Taiwan', 'TJ' => 'Tadzjikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad en Tobago', 'TN' => 'Tunesië', 'TR' => 'Turkije', 'TM' => 'Turkmenistan', 'TV' => 'Tuvalu', 'VI' => 'Amerikaanse Maagdeneilanden', 'UG' => 'Uganda', 'UA' => 'Oekraïne', 'AE' => 'Verenigde Arabische Emiraten', 'GB' => 'Verenigd Koninkrijk', 'US' => 'Verenigde Staten', 'UY' => 'Uruguay', 'UZ' => 'Oezbekistan', 'VU' => 'Vanuatu', 'VA' => 'Vaticaanstad', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Jemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe' );
		$em_countries_array_i18n['pt'] = array ('AF' => 'Afeganistão', 'AL' => 'Albânia',  'DZ' => 'Argélia',  'AS' => 'Samoa Americana',    'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antárctida',  'AG' => 'Antígua e Barbuda',   'AR' => 'Argentina',   'AM' => 'Arménia',      'AW' => 'Aruba', 'AU' => 'Austrália',  'AT' => 'Áustria',    'AZ' => 'Azerbaijão',    'BS' => 'Baamas',  'BH' => 'Barém',   'BD' => 'Bangladeche', 'BB' => 'Barbados', 'BY' => 'Bielorrússia', 'BE' => 'Bélgica',  'BZ' => 'Belize', 'BJ' => 'Benim', 'BT' => 'Butão',   'BO' => 'Bolívia',  'BA' => 'Bósnia e Herzegovina',    'BW' => 'Botsuana', 'BR' => 'Brasil',    'VG' => 'Ilhas Virgens Britânicas',    'BN' => 'Brunei',            'BG' => 'Bulgária',   'BF' => 'Burquina Faso', 'BI' => 'Burúndi', 'CI' => 'Costa do Marfim',                   'KH' => 'Camboja',    'CM' => 'Camarões', 'CA' => 'Canadá', 'CV' => 'Cabo Verde',          'KY'=>'Ilhas Caimão',       'CF' => 'República Centro-Africana',         'TD' => 'Chade',  'CL' => 'Chile', 'CN' => 'China', 'CO' => 'Colômbia',  'KM' => 'Comores',         'CR' => 'Costa Rica', 'HR' => 'Croácia',      'CU' => 'Cuba', 'CY' => 'Chipre', 'CZ' => 'República Checa',       'KP' => 'Coreia do Norte', 'CD' => 'Congo-Kinshasa', 'DK' => 'Dinamarca', 'DJ' => 'Jibuti', 'DM' => 'Domínica', 'DO' => 'República Dominicana', 'EC' => 'Equador', 'EG' => 'Egipto', 'SV' => 'Salvador', 'XE' => 'England', 'GQ' => 'Guiné Equatorial', 'ER' => 'Eritreia', 'EE' => 'Estónia', 'ET' => 'Etiópia', 'FJ' => 'Fiji', 'FI' => 'Finlândia', 'FR' => 'França', 'PF' => 'Polinésia Francesa', 'GA' => 'Gabão', 'GM' => 'Gambia', 'GE' => 'Geórgia', 'DE' => 'Alemanha', 'GH' => 'Gana', 'GR' => 'Grécia', 'GL' => 'Gronelândia', 'GD' => 'Granada', 'GU' => 'Guame', 'GT' => 'Guatemala', 'GN' => 'Guiné', 'GW' => 'Guiné-Bissau', 'GY' => 'Guiana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungria', 'IS' => 'Islândia', 'IN' => 'Índia', 'ID' => 'Indonésia', 'IR' => 'Irão', 'IQ' => 'Iraque', 'IE' => 'Irlanda', 'IL' => 'Israel', 'IT' => 'Itália', 'JM' => 'Jamaica', 'JP' => 'Japão', 'JO' => 'Jordânia', 'KZ' => 'Cazaquistão', 'KE' => 'Quénia', 'KI' => 'Quiribáti', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Quirguizistão', 'LA' => 'Laos', 'LV' => 'Letónia', 'LB' => 'Líbano', 'LS' => 'Lesoto', 'LR' => 'Libéria', 'LY' => 'Líbia', 'LI' => 'Listenstaine', 'LT' => 'Lituânia', 'LU' => 'Luxemburgo', 'MO' => 'Macau', 'MK' => 'Macedónia', 'MG' => 'Madagáscar', 'MW' => 'Malávi', 'MY' => 'Malásia', 'MV' => 'Maldivas', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Ilhas Marshall', 'MQ' => 'Martinica', 'MU' => 'Maurícia', 'MR' => 'Mauritânia', 'MX' => 'México', 'FM' => 'Micronésia', 'MD' => 'Moldávia', 'MC' => 'Mónaco', 'MN' => 'Mongólia', 'ME' => 'Montenegro', 'MA' => 'Marrocos', 'MZ' => 'Moçambique', 'MM' => 'Birmânia', 'NA' => 'Namíbia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Países Baixos', 'AN' => 'Antilhas Neerlandesas', 'NC' => 'Nova Caledónia', 'NZ' => 'Nova Zelândia', 'NI' => 'Nicarágua', 'NE' => 'Níger', 'NG' => 'Nigéria', 'XI' => 'Northern Ireland', 'MP' => 'Marianas do Norte', 'NO' => 'Noruega', 'OM' => 'Omã', 'PK' => 'Paquistão', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panamá', 'PG' => 'Papua-Nova Guiné', 'PY' => 'Paraguai', 'PE' => 'Peru', 'PH' => 'Filipinas', 'PL' => 'Polónia', 'PT' => 'Portugal', 'PR' => 'Porto Rico', 'QA' => 'Catar', 'CG' => 'Republic of the Congo', 'RO' => 'Roménia', 'RU' => 'Rússia', 'RW' => 'Ruanda', 'ST' => 'São Tomé e Príncipe', 'KN' => 'São Cristóvão e Neves', 'LC' => 'Santa Lúcia', 'VC' => 'São Vicente e Granadinas', 'WS' => 'Samoa', 'SM' => 'São Marinho', 'SA' => 'Arábia Saudita', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seicheles', 'SL' => 'Serra Leoa', 'SG' => 'Singapura', 'SK' => 'Eslováquia', 'SI' => 'Eslovénia', 'SB' => 'Ilhas Salomão', 'SO' => 'Somália', 'ZA' => 'África do Sul', 'KR' => 'Coreia do Sul', 'ES' => 'Espanha', 'LK' => 'Sri Lanca', 'SD' => 'Sudão', 'SR' => 'Suriname', 'SZ' => 'Suazilândia', 'SE' => 'Suécia', 'CH' => 'Suíça', 'SY' => 'Síria', 'TW' => 'Taiwan', 'TJ' => 'Tajiquistão', 'TZ' => 'Tanzânia', 'TH' => 'Tailândia', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trindade e Tobago', 'TN' => 'Tunísia', 'TR' => 'Turquia', 'TM' => 'Turquemenistão', 'TV' => 'Tuvalu', 'VI' => 'Ilhas Virgens Americanas', 'UG' => 'Uganda', 'UA' => 'Ucrânia', 'AE' => 'Emiratos Árabes Unidos', 'GB' => 'Reino Unido', 'US' => 'Estados Unidos', 'UY' => 'Uruguai', 'UZ' => 'Usbequistão', 'VU' => 'Vanuatu', 'VA' => 'Vaticano', 'VE' => 'Venezuela', 'VN' => 'Vietname', 'XW' => 'Wales', 'YE' => 'Iémen', 'ZM' => 'Zâmbia', 'ZW' => 'Zimbabué' );
		$em_countries_array_i18n['ru'] = array ('AF' => 'Афганистан',  'AL' => 'Албания',  'DZ' => 'Алжир',    'AS' => 'Самоа (США)',        'AD' => 'Андорра', 'AO' => 'Ангола', 'AQ' => 'Антарктика',  'AG' => 'Антигуа и Барбуда',   'AR' => 'Аргентина',   'AM' => 'Армения',      'AW' => 'Аруба', 'AU' => 'Австралия',  'AT' => 'Австрия',    'AZ' => 'Азербайджан',   'BS' => 'Багамы',  'BH' => 'Бахрейн', 'BD' => 'Бангладеш',   'BB' => 'Барбадос', 'BY' => 'Беларусь',     'BE' => 'Бельгия',  'BZ' => 'Белиз',  'BJ' => 'Бенин', 'BT' => 'Бутан',   'BO' => 'Боливия',  'BA' => 'Босния-Герцеговина',      'BW' => 'Ботсвана', 'BR' => 'Бразилия',  'VG' => 'Вирджинские острова (Брит.)', 'BN' => 'Бруней',            'BG' => 'Болгария',   'BF' => 'Буркина-Фасо',  'BI' => 'Бурунди', 'CI' => 'Берег Слоновой Кости',              'KH' => 'Камбоджа',   'CM' => 'Камерун',  'CA' => 'Канада', 'CV' => 'Кабо-Верде',          'KY'=>'Каймановы острова',  'CF' => 'Центрально-Африканская Республика', 'TD' => 'Чад',    'CL' => 'Чили',  'CN' => 'Китай', 'CO' => 'Колумбия',  'KM' => 'Коморос',         'CR' => 'Коста Рикa', 'HR' => 'Хорватия',     'CU' => 'Куба', 'CY' => 'Кипр',   'CZ' => 'Чешская Республика',    'KP' => 'Северная Корея', 'CD' => 'Демократическая Республика Конго', 'DK' => 'Дания', 'DJ' => 'Джибути', 'DM' => 'Доминика', 'DO' => '	Доминиканская Республика', 'EC' => 'Эквадор', 'EG' => 'Египет', 'SV' => 'Сальвадор', 'XE' => 'Англия', 'GQ' => 'Экваториальная Гвинея', 'ER' => 'Эритрея', 'EE' => 'Эстония', 'ET' => 'Эфиопия', 'FJ' => 'Фиджи', 'FI' => 'Финляндия', 'FR' => 'Франция', 'PF' => '	Полинезия (Фр.)', 'GA' => 'Габон', 'GM' => 'Гамбия', 'GE' => 'Грузия', 'DE' => 'Германия', 'GH' => 'Гана', 'GR' => 'Греция', 'GL' => 'Гренландия', 'GD' => 'Гренада', 'GU' => 'Гуам (США)', 'GT' => 'Гватемала', 'GN' => 'Гвинея', 'GW' => 'Гвинея-Бисау', 'GY' => 'Гайана', 'HT' => 'Гаити', 'HN' => 'Гондурас', 'HK' => 'Гонг Конг', 'HU' => 'Венгрия', 'IS' => 'Исландия', 'IN' => 'Индия', 'ID' => 'Индонезия', 'IR' => 'Иран', 'IQ' => 'Ирак', 'IE' => 'Ирландия', 'IL' => 'Израйль', 'IT' => 'Италия', 'JM' => 'Ямайка', 'JP' => 'Япония', 'JO' => 'Иордания', 'KZ' => 'Казахстан', 'KE' => 'Кения', 'KI' => 'Кирибати', 'KV' => 'Косово', 'KW' => 'Кувейт', 'KG' => 'Киргизия', 'LA' => 'Лаос', 'LV' => 'Латвия', 'LB' => 'Ливан', 'LS' => 'Лесото', 'LR' => 'Либерия', 'LY' => 'Либия', 'LI' => 'Лихтейнштейн', 'LT' => 'Литва', 'LU' => 'Люксембург', 'MO' => 'Макау', 'MK' => 'Македония', 'MG' => 'Мадагаскар', 'MW' => 'Малави', 'MY' => 'Малайзия', 'MV' => 'Мальдивы', 'ML' => 'Мали', 'MT' => 'Мальта', 'MH' => 'Маршальские острова', 'MQ' => 'Мартиника (Фр.)', 'MU' => 'Мавритий', 'MR' => 'Мавритания', 'MX' => 'Мексика', 'FM' => 'Микронезия', 'MD' => 'Молдова', 'MC' => 'Монако', 'MN' => 'Монголия', 'ME' => 'Черногория', 'MA' => 'Морокко', 'MZ' => 'Мозамбик', 'MM' => 'Бирма (Мьянма)', 'NA' => 'Намибия', 'NR' => 'Науру', 'NP' => 'Непал', 'NL' => 'Нидерланды', 'AN' => 'Антиллы (Нидерланды)', 'NC' => 'Новая Каледония (Фр.)', 'NZ' => 'Новая Зеландия', 'NI' => 'Никарагуаa', 'NE' => 'Нигер', 'NG' => 'Нигерия', 'XI' => 'Северной Ирландии', 'MP' => 'Северные Марианские острова', 'NO' => 'Норвегия', 'OM' => 'Оман', 'PK' => 'Пакистан', 'PW' => 'Палау', 'PS' => 'Палестина', 'PA' => 'Панама', 'PG' => 'Папуа Новая Гвинея', 'PY' => 'Парагвай', 'PE' => 'Перу', 'PH' => 'Филиппины', 'PL' => 'Польша', 'PT' => 'Португалия', 'PR' => 'Пуэрто-Рико', 'QA' => 'Катар', 'CG' => 'Конго', 'RO' => 'Румыния', 'RU' => 'Россия', 'RW' => 'Руанда', 'ST' => 'Сан-Томе и Принсипи', 'KN' => 'Сент-Киттс Нэвис Ангуилла', 'LC' => 'Санта Лючия', 'VC' => 'Сент-Висент и Гренадины', 'WS' => 'Самоа', 'SM' => 'Сан-Марино', 'SA' => 'Саудовская Аравия', 'XS' => 'Шотландия', 'SN' => 'Сенегал', 'RS' => 'Сербии', 'SC' => 'Сейшеллы', 'SL' => 'Сьерра-Леоне', 'SG' => 'Сингапур', 'SK' => 'Словакия', 'SI' => 'Словения', 'SB' => 'Соломоновы острова', 'SO' => 'Сомали', 'ZA' => 'Южная Африка', 'KR' => 'Южная Корея', 'ES' => 'Испания', 'LK' => 'Шри Ланка', 'SD' => 'Судан', 'SR' => 'Суринам', 'SZ' => 'Свазиленд', 'SE' => 'Швеция', 'CH' => 'Швейцария', 'SY' => 'Сирия', 'TW' => 'Тайвань', 'TJ' => 'Таджикистан', 'TZ' => 'Танзания', 'TH' => 'Таиланд', 'TL' => 'Восточный Тимор', 'TG' => 'Того', 'TO' => 'Тонга', 'TT' => 'Тринидад и Тобаго', 'TN' => 'Тунис', 'TR' => 'Турция', 'TM' => 'Туркменистан', 'TV' => 'Тувалу', 'VI' => 'Вирджинские острова (США)', 'UG' => 'Уганда', 'UA' => 'Украина', 'AE' => 'Объединенные Арабские Эмираты', 'GB' => 'Великобритания', 'US' => 'США', 'UY' => 'Uruguay', 'UZ' => 'Узбекистан', 'VU' => 'Вануату', 'VA' => 'Ватикан', 'VE' => 'Венесуэла', 'VN' => 'Вьетнам', 'XW' => 'Уэльс', 'YE' => 'Йемен', 'ZM' => 'Замбия', 'ZW' => 'Зимбабве' );
		$em_countries_array_i18n['sv'] = array ('AF' => 'Afghanistan', 'AL' => 'Albanien', 'DZ' => 'Algeriet', 'AS' => 'Amerikanska Samoa',  'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarktis',   'AG' => 'Antigua och Barbuda', 'AR' => 'Argentina',   'AM' => 'Armenien',     'AW' => 'Aruba', 'AU' => 'Australien', 'AT' => 'Österrike',  'AZ' => 'Azerbajdzjan',  'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh',  'BB' => 'Barbados', 'BY' => 'Vitryssland',  'BE' => 'Belgien',  'BZ' => 'Belize', 'BJ' => 'Benin', 'BT' => 'Bhutan',  'BO' => 'Bolivia',  'BA' => 'Bosnien och Hercegovina', 'BW' => 'Botswana', 'BR' => 'Brasilien', 'VG' => 'Brittiska Jungfruöarna',      'BN' => 'Brunei',            'BG' => 'Bulgarien',  'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'Elfenbenskusten',                   'KH' => 'Kambodja',   'CM' => 'Kamerun',  'CA' => 'Kanada', 'CV' => 'Kap Verde',           'KY'=>'Caymanöarna',        'CF' => 'Centralafrikanska republiken',      'TD' => 'Tchad',  'CL' => 'Chile', 'CN' => 'Kina',  'CO' => 'Colombia',  'KM' => 'Komorerna',       'CR' => 'Costa Rica', 'HR' => 'Kroatien',     'CU' => 'Kuba', 'CY' => 'Cypern', 'CZ' => 'Tjeckien',              'KP' => 'Nordkorea',  'CD' => 'Demokratiska republiken Kongo', 'DK' => 'Danmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominikanska republiken', 'EC' => 'Ecuador', 'EG' => 'Egypten', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Ekvatorialguinea', 'ER' => 'Eritrea', 'EE' => 'Estland', 'ET' => 'Etiopien', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'Frankrike', 'PF' => 'Franska Polynesien', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgien', 'DE' => 'Tyskland', 'GH' => 'Ghana', 'GR' => 'Grekland', 'GL' => 'Grönland', 'GD' => 'Grenada', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Ungern', 'IS' => 'Island', 'IN' => 'Indien', 'ID' => 'Indonesien', 'IR' => 'Iran', 'IQ' => 'Irak', 'IE' => 'Irland', 'IL' => 'Israel', 'IT' => 'Italien', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JO' => 'Jordanien', 'KZ' => 'Kazakstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Kirgizistan', 'LA' => 'Laos', 'LV' => 'Lettland', 'LB' => 'Libanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyen', 'LI' => 'Liechtenstein', 'LT' => 'Litauen', 'LU' => 'Luxemburg', 'MO' => 'Macao', 'MK' => 'Makedonien', 'MG' => 'Madagaskar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldiverna', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshallöarna', 'MQ' => 'Martinique', 'MU' => 'Mauritius', 'MR' => 'Mauretanien', 'MX' => 'Mexiko', 'FM' => 'Mikronesien', 'MD' => 'Moldavien', 'MC' => 'Monaco', 'MN' => 'Mongoliet', 'ME' => 'Montenegro', 'MA' => 'Marocko', 'MZ' => 'Moçambique', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Nederländerna', 'AN' => 'Nederländska Antillerna', 'NC' => 'Nya Kaledonien', 'NZ' => 'Nya Zeeland', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Nordmarianerna', 'NO' => 'Norge', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papua Nya Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Filippinerna', 'PL' => 'Polen', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'CG' => 'Kongo', 'RO' => 'Rumänien', 'RU' => 'Ryssland', 'RW' => 'Rwanda', 'ST' => 'São Tomé och Príncipe', 'KN' => '	Saint Christopher och Nevis', 'LC' => 'Saint Lucia', 'VC' => 'Saint Vincent och Grenadinerna', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Saudiarabien', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychellerna', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakien', 'SI' => 'Slovenien', 'SB' => 'Salomonöarna', 'SO' => 'Somalia', 'ZA' => 'Sydafrika', 'KR' => 'Sydkorea', 'ES' => 'Spanien', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Surinam', 'SZ' => 'Swaziland', 'SE' => 'Sverige', 'CH' => 'Schweiz', 'SY' => 'Syrien', 'TW' => 'Taiwan', 'TJ' => 'Tadzjikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad och Tobago', 'TN' => 'Tunisien', 'TR' => 'Turkiet', 'TM' => 'Turkmenistan', 'TV' => 'Tuvalu', 'VI' => 'Amerikanska Jungfruöarna', 'UG' => 'Uganda', 'UA' => 'Ukraina', 'AE' => 'Förenade Arabemiraten', 'GB' => 'Förenade kungariket', 'US' => 'Förenta staterna', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VA' => '	Heliga stolen', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe' );
		$em_countries_array_i18n['en'] = array ('AF' => 'Afghanistan', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarctica', 'AG' => 'Antigua and Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AW' => 'Aruba', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia and Herzegovina', 'BW' => 'Botswana', 'BR' => 'Brazil', 'VG' => 'British Virgin Islands', 'BN' => 'Brunei', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'CI' => 'C&ocirc;te D\'Ivoire', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY'=>'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CR' => 'Costa Rica', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'KP' => 'Democratic People\'s Republic of Korea', 'CD' => 'Democratic Republic of the Congo', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'PF' => 'French Polynesia', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Laos', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyan Arab Jamahiriya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Mauritania', 'MU' => 'Mauritius', 'MR' => 'Mauritius', 'MX' => 'Mexico', 'FM' => 'Micronesia', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'CG' => 'Republic of the Congo', 'RO' => 'Romania', 'RU' => 'Russia', 'RW' => 'Rwanda', 'ST' => 'S&agrave;o Tom&eacute; And Pr&iacute;ncipe', 'KN' => 'Saint Kitts and Nevis', 'LC' => 'Saint Lucia', 'VC' => 'Saint Vincent and the Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Saudi Arabia', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'KR' => 'South Korea', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syria', 'TW' => 'Taiwan', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad and Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TV' => 'Tuvalu', 'VI' => 'US Virgin Islands', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VA' => 'Vatican', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe' );
		$lang = substr(get_locale(), 0, 2);
		if( array_key_exists($lang, $em_countries_array_i18n) ){
			$em_countries_array = $em_countries_array_i18n[$lang];
		}else{
			$em_countries_array = $em_countries_array_i18n['en'];
		}
	}
	if($add_blank !== false){
		if(is_array($add_blank)){
			$em_countries_array = $add_blank + $em_countries_array;
		}else{
		    $em_countries_array = array(0 => $add_blank) + $em_countries_array;
		}
	}
	return apply_filters('em_get_countries', $em_countries_array);
}

/**
 * Returns an array of scopes available to events manager. Hooking into this function's em_get_scopes filter will allow you to add scope options to the event pages.
 */
function em_get_scopes(){
	$scopes = array(
		'all' => __('All events','dbem'),
		'future' => __('Future events','dbem'),
		'past' => __('Past events','dbem'),
		'today' => __('Today\'s events','dbem'),
		'tomorrow' => __('Tomorrow\'s events','dbem'),
		'month' => __('Events this month','dbem'),
		'next-month' => __('Events next month','dbem'),
		'1-months'  => __('Events current and next month','dbem'),
		'2-months'  => __('Events within 2 months','dbem'),
		'3-months'  => __('Events within 3 months','dbem'),
		'6-months'  => __('Events within 6 months','dbem'),
		'12-months' => __('Events within 12 months','dbem')
	);
	return apply_filters('em_get_scopes',$scopes);
}

function em_get_currencies(){
	$currencies = new stdClass();
	$currencies->names = array('EUR' => 'EUR - Euros','USD' => 'USD - U.S. Dollars','GBP' => 'GBP - British Pounds','CAD' => 'CAD - Canadian Dollars','AUD' => 'AUD - Australian Dollars','BRL' => 'BRL - Brazilian Reais','CZK' => 'CZK - Czech Koruny','DKK' => 'DKK - Danish Kroner','HKD' => 'HKD - Hong Kong Dollars','HUF' => 'HUF - Hungarian Forints','ILS' => 'ILS - Israeli New Shekels','JPY' => 'JPY - Japanese Yen','MYR' => 'MYR - Malaysian Ringgit','MXN' => 'MXN - Mexican Pesos','TWD' => 'TWD - New Taiwan Dollars','NZD' => 'NZD - New Zealand Dollars','NOK' => 'NOK - Norwegian Kroner','PHP' => 'PHP - Philippine Pesos','PLN' => 'PLN - Polish Zlotys','SGD' => 'SGD - Singapore Dollars','SEK' => 'SEK - Swedish Kronor','CHF' => 'CHF - Swiss Francs','THB' => 'THB - Thai Baht','TRY' => 'TRY - Turkish Liras');
	$currencies->symbols = array( 'EUR' => '&euro;','USD' => '$','GBP' => '&pound;','CAD' => '$','AUD' => '$','BRL' => 'R$','DKK' => 'kr','HKD' => '$','HUF' => 'Ft','JPY' => '&#165;','MYR' => 'RM','MXN' => '$','TWD' => '$','NZD' => '$','NOK' => 'kr','PHP' => 'Php','SGD' => '$','SEK' => 'kr','CHF' => 'CHF','TRY' => 'TL');
	$currencies->true_symbols = array( 'EUR' => '€','USD' => '$','GBP' => '£','CAD' => '$','AUD' => '$','BRL' => 'R$','DKK' => 'kr','HKD' => '$','HUF' => 'Ft','JPY' => '¥','MYR' => 'RM','MXN' => '$','TWD' => '$','NZD' => '$','NOK' => 'kr','PHP' => 'Php','SGD' => '$','SEK' => 'kr','CHF' => 'CHF','TRY' => 'TL');
	return apply_filters('em_get_currencies',$currencies);
}

function em_get_currency_formatted($price, $currency=false, $format=false){
	$formatted_price = '';
	if(!$format) $format = get_option('dbem_bookings_currency_format','@#');
	if(!$currency) $currency = get_option('dbem_bookings_currency');
	$formatted_price = str_replace('@', em_get_currency_symbol(true,$currency), $format);
	$formatted_price = str_replace('#', number_format( $price, 2, get_option('dbem_bookings_currency_decimal_point','.'), get_option('dbem_bookings_currency_thousands_sep',',') ), $formatted_price);
	return apply_filters('em_get_currency_formatted', $formatted_price, $price, $currency, $format);
}

function em_get_currency_symbol($true_symbol = false, $currency = false){
	if( !$currency ) $currency = get_option('dbem_bookings_currency');
	if($true_symbol){
		return em_get_currencies()->true_symbols[$currency];
	}
	return apply_filters('em_get_currency_symbol', em_get_currencies()->symbols[$currency]);
}

function em_get_currency_name($currency = false){
	if( !$currency ) $currency = get_option('dbem_bookings_currency');
	return apply_filters('em_get_currency_name', em_get_currencies()->names[$currency]);
}

function em_get_hour_format(){
	return get_option('dbem_time_24h') ? "H:i":"h:i A";
}

function em_get_days_names(){
	return array (1 => __ ( 'Mon' ), 2 => __ ( 'Tue' ), 3 => __ ( 'Wed' ), 4 => __ ( 'Thu' ), 5 => __ ( 'Fri' ), 6 => __ ( 'Sat' ), 0 => __ ( 'Sun' ) );
}

/**
 * Works like check_admin_referrer(), but also in public mode. If in admin mode, it triggers an error like in check_admin_referrer(), if outside admin it just exits with an error.
 * @param string $action
 */
function em_verify_nonce($action, $nonce_name='_wpnonce'){
	if( is_admin() ){
		if( !wp_verify_nonce($_REQUEST[$nonce_name], $action) ) check_admin_referer('trigger_error');
	}else{
		if( !wp_verify_nonce($_REQUEST[$nonce_name], $action) ) exit( __('Trying to perform an illegal action.','dbem') );
	}
}

/**
 * Gets all WP users
 * @return array
 */
function em_get_wp_users( $args = array(), $extra_users = array() ) {
	global $wpdb;
	$users = get_users($args);
	$indexed_users = array();
	foreach($users as $user){
		$indexed_users[$user->ID] = $user->display_name;
	}
 	return $extra_users + $indexed_users;
}

function em_get_attributes($lattributes = false){
	//We also get a list of attribute names and create a ddm list (since placeholders are fixed)
	$formats =
		get_option ( 'dbem_placeholders_custom' ).
		get_option ( 'dbem_location_placeholders_custom' ).
		get_option ( 'dbem_full_calendar_event_format' ).
		get_option ( 'dbem_rss_description_format' ).
		get_option ( 'dbem_rss_title_format' ).
		get_option ( 'dbem_map_text_format' ).
		get_option ( 'dbem_location_baloon_format' ).
		get_option ( 'dbem_location_event_list_item_format' ).
		get_option ( 'dbem_location_page_title_format' ).
		get_option ( 'dbem_event_list_item_format' ).
		get_option ( 'dbem_event_page_title_format' ).
		get_option ( 'dbem_single_event_format' ).
		get_option ( 'dbem_single_location_format' );
	//We now have one long string of formats, get all the attribute placeholders
	if( $lattributes ){
		preg_match_all('/#_LATT\{([^}]+)\}(\{([^}]+)\})?/', $formats, $matches);
	}else{
		preg_match_all('/#_ATT\{([^}]+)\}(\{([^}]+)\})?/', $formats, $matches);
	}
	//Now grab all the unique attributes we can use in our event.
	$attributes = array('names'=>array(), 'values'=>array());
	foreach($matches[1] as $key => $attribute) {
		if( !in_array($attribute, $attributes['names']) ){
			$attributes['names'][] = $attribute ;
			$attributes['values'][$attribute] = array();
		}
		//check if there's ddm values
		if( !empty($matches[3][$key]) ){
		    $new_values = explode('|',$matches[3][$key]);
		    if( count($new_values) > count($attributes['values'][$attribute]) ){
		    	foreach($new_values as $key => $value){
		    	    $new_values[$key] = trim($value);
		    	}
				$attributes['values'][$attribute] = apply_filters('em_get_attributes_'.$attribute, $new_values, $attribute, $matches);
		    }
		}
	}
	return apply_filters('em_get_attributes', $attributes, $matches);
}

/**
 * Copied straight from wp-login.php, only change atm is a function renaming.
 * Handles registering a new user.
 *
 * @param array associative array of user values to insert
 * @return int|WP_Error Either user's ID or error on failure.
 */
function em_register_new_user( $user_data ) {
	$user_data = apply_filters('em_register_new_user_pre',$user_data);
	$errors = new WP_Error();
	if( !empty($user_data['user_name']) ){
		$name = explode(' ', $user_data['user_name']);
		$user_data['first_name'] = array_shift($name);
		$user_data['last_name'] = implode(' ',$name);
	}
	$sanitized_user_login = sanitize_user( $user_data['user_login'] );
	$user_data['user_login'] = $sanitized_user_login;
	$user_email = apply_filters( 'user_registration_email', $user_data['user_email'] );

	// Check the username
	if ( $sanitized_user_login == '' ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.', 'dbem') );
	} elseif ( ! validate_username( $user_data['user_login'] ) ) {
		$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.', 'dbem') );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered, please choose another one.' ) );
	}

	// Check the e-mail address
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.', 'dbem') );
	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.', 'dbem') );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.' ) );
	}

	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

	ob_start(); //prevent any errors going out here, e.g. with RPR
	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );
	ob_clean();

	if ( $errors->get_error_code() ) return $errors;

	if(empty($user_data['user_pass'])){
		$user_data['user_pass'] =  wp_generate_password( 12, false);
	}

	$user_id = wp_insert_user( $user_data );
	if( is_numeric($user_id) && !empty($user_data['dbem_phone']) ){
		update_user_meta($user_id, 'dbem_phone', $user_data['dbem_phone']);
	}

	if ( ! $user_id ) {
		$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'dbem'), get_option( 'admin_email' ) ) );
		return $errors;
	}

	update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.

	em_new_user_notification( $user_id, $user_data['user_pass'] );

	return apply_filters('em_register_new_user',$user_id);
}

/**
 * Notify the blog admin of a new user, normally via email.
 *
 * @since 2.0
 *
 * @param int $user_id User ID
 * @param string $plaintext_pass Optional. The user's plaintext password
 */
function em_new_user_notification($user_id, $plaintext_pass = '') {
	global $LoginWithAjax;

	//if you want you can disable this email from going out, and will still consider registration as successful.
	if( get_option('dbem_email_disable_registration') ){ return true;  }

	//Copied out of /wp-includes/pluggable.php
	$user = new WP_User($user_id);

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message  = sprintf(__('New user registration on your blog %s:', 'dbem'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s', 'dbem'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s', 'dbem'), $user_email) . "\r\n";
	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration', 'dbem'), $blogname), $message);

	if ( empty($plaintext_pass) )
		return;

	//
	ob_start();
	em_locate_template('emails/new-user.php', true);
	$message = ob_get_clean();
	$message  = str_replace(array('%password%','%username%'), array($plaintext_pass, $user_login), $message);
	
	global $EM_Mailer;
	return $EM_Mailer->send(sprintf(__('[%s] Your username and password', 'dbem'), $blogname), $message, $user_email);
}

/*
 * UI Helpers
 * previously dbem_UI_helpers.php functions
 */

function em_option_items($array, $saved_value) {
	$output = "";
	foreach($array as $key => $item) {
		$selected ='';
		if ($key == $saved_value)
			$selected = "selected='selected'";
		$output .= "<option value='".esc_attr($key)."' $selected >".esc_html($item)."</option>\n";

	}
	echo $output;
}

function em_checkbox_items($name, $array, $saved_values, $horizontal = true) {
	$output = "";
	foreach($array as $key => $item) {
		$checked = "";
		if (in_array($key, $saved_values))
			$checked = "checked='checked'";
		$output .=  "<input type='checkbox' name='".esc_attr($name)."' value='".esc_attr($key)."' $checked /> ".esc_html($item)."&nbsp; ";
		if(!$horizontal)
			$output .= "<br/>\n";
	}
	echo $output;

}
function em_options_input_text($title, $name, $description ='', $default='') {
	?>
	<tr valign="top" id='<?php echo esc_attr($name);?>_row'>
		<th scope="row"><?php echo esc_html($title); ?></th>
	    <td>
			<input name="<?php echo esc_attr($name) ?>" type="text" id="<?php echo esc_attr($title) ?>" style="width: 95%" value="<?php echo esc_attr(get_option($name, $default), ENT_QUOTES); ?>" size="45" /><br />
			<em><?php echo $description; ?></em>
		</td>
	</tr>
	<?php
}
function em_options_input_password($title, $name, $description ='') {
	?>
	<tr valign="top" id='<?php echo esc_attr($name);?>_row'>
		<th scope="row"><?php echo esc_html($title); ?></th>
	    <td>
			<input name="<?php echo esc_attr($name) ?>" type="password" id="<?php echo esc_attr($title) ?>" style="width: 95%" value="<?php echo esc_attr(get_option($name)); ?>" size="45" /><br />
			<em><?php echo $description; ?></em>
		</td>
	</tr>
	<?php
}

function em_options_textarea($title, $name, $description ='') {
	?>
	<tr valign="top" id='<?php echo esc_attr($name);?>_row'>
		<th scope="row"><?php echo esc_html($title); ?></th>
			<td>
				<textarea name="<?php echo esc_attr($name) ?>" id="<?php echo esc_attr($name) ?>" rows="6" cols="60"><?php echo esc_attr(get_option($name), ENT_QUOTES);?></textarea><br/>
				<em><?php echo $description; ?></em>
			</td>
		</tr>
	<?php
}

function em_options_radio($name, $options, $title='') {
		$option = get_option($name);
		?>
	   	<tr valign="top" id='<?php echo esc_attr($name);?>_row'>
	   		<?php if( !empty($title) ): ?>
	   		<th scope="row"><?php  echo esc_html($title); ?></th>
	   		<td>
	   		<?php else: ?>
	   		<td colspan="2">
	   		<?php endif; ?>
	   			<table>
	   			<?php foreach($options as $value => $text): ?>
	   				<tr>
	   					<td><input id="<?php echo esc_attr($name) ?>_<?php echo esc_attr($value); ?>" name="<?php echo esc_attr($name) ?>" type="radio" value="<?php echo esc_attr($value); ?>" <?php if($option == $value) echo "checked='checked'"; ?> /></td>
	   					<td><?php echo $text ?></td>
	   				</tr>
				<?php endforeach; ?>
				</table>
			</td>
	   	</tr>
<?php
}

function em_options_radio_binary($title, $name, $description='', $option_names = '') {
	if( empty($option_names) ) $option_names = array(0 => __('No','dbem'), 1 => __('Yes','dbem'));
	if( substr($name, 0, 7) == 'dbem_ms' ){
		$list_events_page = get_site_option($name);
	}else{
		$list_events_page = get_option($name);
	}
	?>
   	<tr valign="top" id='<?php echo $name;?>_row'>
   		<th scope="row"><?php echo esc_html($title); ?></th>
   		<td>
   			<?php echo $option_names[1]; ?> <input id="<?php echo esc_attr($name) ?>_yes" name="<?php echo esc_attr($name) ?>" type="radio" value="1" <?php if($list_events_page) echo "checked='checked'"; ?> />&nbsp;&nbsp;&nbsp;
			<?php echo $option_names[0]; ?> <input  id="<?php echo esc_attr($name) ?>_no" name="<?php echo esc_attr($name) ?>" type="radio" value="0" <?php if(!$list_events_page) echo "checked='checked'"; ?> />
			<br/><em><?php echo $description; ?></em>
		</td>
   	</tr>
	<?php
}

function em_options_select($title, $name, $list, $description='', $default='') {
	$option_value = get_option($name, $default);
	if( $name == 'dbem_events_page' && !is_object(get_page($option_value)) ){
		$option_value = 0; //Special value
	}
	?>
   	<tr valign="top" id='<?php echo esc_attr($name);?>_row'>
   		<th scope="row"><?php echo esc_html($title); ?></th>
   		<td>
			<select name="<?php echo esc_attr($name); ?>" >
				<?php foreach($list as $key => $value) : ?>
 				<option value='<?php echo esc_attr($key) ?>' <?php echo ("$key" == $option_value) ? "selected='selected' " : ''; ?>>
 					<?php echo esc_html($value); ?>
 				</option>
				<?php endforeach; ?>
			</select> <br/>
			<em><?php echo $description; ?></em>
		</td>
   	</tr>
	<?php
}
// got from http://davidwalsh.name/php-email-encode-prevent-spam
function em_ascii_encode($e){
	$output = '';
    for ($i = 0; $i < strlen($e); $i++) { $output .= '&#'.ord($e[$i]).';'; }
    return $output;
}


if( !function_exists('get_current_blog_id') ){
	/**
	 * Substitutes the original function in 3.1 onwards, for backwards compatability (only created if not previously defined)
	 * @return int
	 */
	function get_current_blog_id(){ return 1; } //for < 3.1
}

if( !function_exists( 'is_main_query' ) ){
	/**
	 * Substitutes the original function in 3.3 onwards, for backwards compatability (only created if not previously defined)
	 * @return bool
	 */
	function is_main_query(){ global $wp_query; return $wp_query->in_the_loop == true; }
}

function em_get_thumbnail_url($image_url, $width, $height){
	return plugins_url('includes/thumbnails/timthumb.php', __FILE__).'?src='.$image_url.'&amp;h='. $height .'&amp;w='. $width;
}

/**
 * Depreciated
 * @return unknown
 */
function em_get_date_format(){
	return get_option('dbem_date_format');
}
?>