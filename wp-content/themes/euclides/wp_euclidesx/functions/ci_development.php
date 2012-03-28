<?php 
/**
 * Does a print_r() on the passed array, surrounding it in <pre></pre> tags.
 * 
 * @access private
 * @param array $arr
 * @return void
 */
function _ci_pre_r($arr)
{
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}


?>