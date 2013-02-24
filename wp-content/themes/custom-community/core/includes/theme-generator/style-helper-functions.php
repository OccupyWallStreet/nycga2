<?php
/**
* This function removes comments, spaces and line breaks
* @param string $buffer
* @return stripped $buffer
*/
function compress($buffer) {
    // cut out the comments
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    // remove the spaces, line breaks, etc.
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '   '), '', $buffer);
    return $buffer;
}