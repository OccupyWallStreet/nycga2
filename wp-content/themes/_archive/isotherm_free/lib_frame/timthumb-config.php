<?php
/**
 * TimThumb by Ben Gillbanks and Mark Maunder
 * Based on work done by Tim McDaniels and Darren Hoyt
 * http://code.google.com/p/timthumb/
 * 
 * GNU General Public License, version 2
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Examples and documentation available on the project homepage
 * http://www.binarymoon.co.uk/projects/timthumb/
 */

/*
        -----TimThumb CONFIGURATION-----
        You can either edit the configuration variables manually here, or you can 
        create a file called timthumb-config.php and define variables you want
        to customize in there. It will automatically be loaded by timthumb.
        This will save you having to re-edit these variables everytime you download
        a new version of timthumb.

*/

define ('FILE_CACHE_DIRECTORY', '../custom/cache');             // Directory where images are cached. Left blank it will use the system temporary directory (which is better for security)

if(! isset($ALLOWED_SITES)){
        $ALLOWED_SITES = array (
                        'flickr.com',
                        'picasa.com',
                        'img.youtube.com',
                        'upload.wikimedia.org',
                        'photobucket.com',
                        'imgur.com',
                        'imageshack.us',
                        'tinypic.com',
						'bizzthemes.com'
        );
}