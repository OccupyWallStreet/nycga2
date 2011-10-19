<?php

/*
Add OpenGraph Support
*/

add_filter('language_attributes', 'OpenGraph');
        function OpenGraph($attr) {
                $attr .= "\n xmlns:og='http://opengraphprotocol.org/schema/'"; 

                return $attr;
 }
 
 
 add_filter('language_attributes', 'FGraph');
        function FGraph($attr) {
                $attr .= "\n xmlns:fb='http://www.facebook.com/2008/fbml'"; 

                return $attr;
 }

?>