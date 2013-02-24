<?php
function diamond_arr_to_str($arg) {
	$ret = '';	
	if (!$arg || $arg == '')
		return $ret;	
	foreach($arg AS $a)
		$ret.=$a;
	return $ret;
}

function get_format_txt($code) {
	if ($code && $code != '' && mb_substr($code, 0, 8) == 'encrypt:') 
		return base64_decode(mb_substr($code, 8));
	return $code;
}

function get_format_code($txt) {
	if ($txt && $txt != '') 
		return 'encrypt:' . base64_encode($txt);
	return $txt;
}
?>
