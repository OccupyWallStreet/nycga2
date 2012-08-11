<?php
/*
 * Flickr
 *
 * This method helps us to fetch images from Flickr.
 */

#
# WordPress SmoothGallery plugin
# Copyright (C) 2009 Christian Schenk
# 
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
#


/**
 * Returns an array with the images that can be used by the generate_markup()
 * function.
 */
function get_flickr_images($username = NULL, $photoset = NULL) {
	if (FLICKR_APIKEY == '' or FLICKR_SECRET == '') return array();
	if (empty($username)) return array();

	require_once('phpFlickr/phpFlickr.php');
	$flickr = new phpFlickr(FLICKR_APIKEY, FLICKR_SECRET);

	$person = $flickr->people_findByUsername($username);
	# user may supply userId if username doesn't work
	if (empty($person)) $person['id'] = $username;

	// get the friendly URL of the user's photos
	$photos_url = $flickr->urls_getUserPhotos($person['id']);

	$photos = array();
	if ($photoset == NULL) {
		$photos = $flickr->people_getPublicPhotos($person['id']);
		$photos = (array) $photos['photos']['photo'];
	} else {
		$photos = $flickr->photosets_getPhotos($photoset);
		$photos = (array) $photos['photoset']['photo'];
	}
	if (empty($photos) or !is_array($photos)) return array();

	$images = array(); $i = 0;
	foreach ($photos as $photo) {
		$images[$i]->url = $flickr->buildPhotoURL($photo);
		$images[$i]->title = $photo[title];

		$altImages = array();
		foreach (array('square', 'thumbnail', 'small', 'medium', 'large') as $size) {
			$altImages[] = $flickr->buildPhotoURL($photo, $size);
		}
		$images[$i]->alt = $altImages;

		$i++;
	}

	return $images;
}
 
?>
