<?php 

	header("Content-type: text/css");

	$id = $_GET['widget_id'];
	$id = clean($id);
	$skin = $_GET['skin'];
	$skin = clean($skin);
	if(!empty($skin)){	
		$skin .= '.css';
		$css = file_get_contents('./skins/' . $skin );
		$widget_skin = preg_replace('/%ID%/',$id, $css);
		echo $widget_skin;
	}

?>
<?php
function clean($str = '', $html = false) {
	if (empty($str)) return;

	if (is_array($str)) {
		foreach($str as $key => $value) $str[$key] = clean($value, $html);
	} else {
		if (get_magic_quotes_gpc()) $str = stripslashes($str);

		if (is_array($html)) $str = strip_tags($str, implode('', $html));
		elseif (preg_match('|<([a-z]+)>|i', $html)) $str = strip_tags($str, $html);
		elseif ($html !== true) $str = strip_tags($str);

		$str = trim($str);
		$str = str_replace(".", "", $str);
		$str = str_replace("/", "", $str);
	}

	return $str;
}
?>