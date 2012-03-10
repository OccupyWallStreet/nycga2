<?php
/**
 * bpModPHP4Helper is a little abstract layer for some oop features between php4 and php5
 * Inspiration (and some code too) from cakephp Object, Overloadable and Overloadable2 classes
 *
 * This file only include bpModPHP4Helper.php4 or bpModPHP4Helper.php5 depending on php version.
 */
include_once __FILE__ . (int)PHP_VERSION;

?>
