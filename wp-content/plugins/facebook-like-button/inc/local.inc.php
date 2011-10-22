<?php

/*
File Name: local.inc.php
Descrption: Get facebook locals
By: Anty (mail@anty.at)

*/

// available fonts
$fblikes_fonts = array("Default"        => "",
                       "Arial"          => "arial",
                       "Lucida Grande"  => "lucida grande",
                       "Segoe Ui"       => "segoe ui",
                       "Tahoma"         => "tahoma",
                       "Trebuchet MS"   => "trebuchet ms",
                       "Verdana"        => "verdana",
                      );
					  
					  
/**
 * Retrieves all locales from http://www.facebook.com/translations/FacebookLocales.xml and returns an array with the data
 *
 * @return array of locales with english names
 */
function fblikes_get_locales() {
    include_once(ABSPATH .'wp-includes/class-snoopy.php');
    
    $locales = array();

    $snoopy = new Snoopy;
    if($snoopy->fetch("http://www.facebook.com/translations/FacebookLocales.xml")) {
        $facebookLocales = $snoopy->results;
	
	preg_match_all('/<locale>\s*<englishName>([^<]+)<\/englishName>\s*<codes>\s*<code>\s*<standard>.+?<representation>([^<]+)<\/representation>/s', utf8_decode($facebookLocales), $localesArray, PREG_PATTERN_ORDER);

        foreach ($localesArray[1] as $i => $englishName) {
            $locales[$localesArray[2][$i]] = $englishName;
        }
    }

    if ($locales == array()) {
        // something went wrong, fall back to default locale "en_US"
	$locales['default'] = "Default";
    }

    return $locales;
}

/**
 * Returns the locale string for usage in an URL
 * Translates default-value to an empty string
 *
 * @return locale string like "&locale=en_US"
 */
function fblikes_get_url_locale() {
    $localeString = "";
    $locale = get_option("fblikes_locale");
    if ($locale != "default") {
        $localeString .= "&locale=". urlencode($locale);
    }

    return $localeString;
}

/**
 * Returns the locale string for usage in the FBXML markup
 * Translates default-value to an empty string
 *
 * @return locale string like ' locale="en_US"'
 */
function fblikes_get_js_locale() {
    $localeString = "";
    $locale = get_option("fblikes_locale");
    if ($locale != "default") {
        $localeString .= " locale=\"" . htmlentities($locale) ."\"";
    }

    return $localeString;
}

/**
 * Returns the locale string
 * Translates default-value to en_US
 *
 * @return locale string like "en_US"
 */
function fblikes_get_locale() {
    $locale = get_option("fblikes_locale");
    if ($locale == "default") {
        $locale = "en_US";
    }
    return $locale;
}


?>