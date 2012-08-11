<?php
/*
 * PicasaRSS
 *
 * This class can be used to fetch images from Picasa via an RSS feed.
 *
 * Original idea from here:
 * http://www.ploetner.it/dennis/smoothgallery-picasa.html
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


class PicasaRss {
 
	private $images;
	private $thumbSize;
 
	function __construct($rss) {
		$xmlstr = file_get_contents($rss);
		# XXX: $entry->children('http://search.yahoo.com/mrss/') doesn't seem
		# to work so we remove the namespace alltogether
		$xmlstr = str_replace('media:', '', $xmlstr);
		$xml = new SimpleXMLElement($xmlstr);

		$this->images = array();
		foreach ($xml->entry as $entry) {
			$this->images[] = new PicasaImg($entry);
		}
	}
 
	function get() {
		return $this->images;
	}
 
}
 
class PicasaImg {
 
	public $title;
	public $url;
	public $thumb;

	function __construct($entry) {
		$this->url = $entry->content['src'];
		$this->title = $entry->title;

		# XXX: for some images there may be not all thumbnails available
		$this->thumb = array();
		foreach ($entry->group->thumbnail as $thumb) {
			$this->thumb[] = $thumb['url'];
		}
	}
 
}


/**
 * Returns an array with the images that can be used by the generate_markup()
 * function.
 */
function get_picasa_images($url) {
	if (empty($url)) return array();

	$picasa = new PicasaRss($url);
	$images = array(); $i = 0;
	foreach ($picasa->get() as $image) {
		$images[$i]->url = $image->url;
		$images[$i]->title = $image->title;
		$images[$i]->alt = $image->thumb;
		$i++;
	}

	return $images;
}

# TEST
#$picasa = new PicasaRss('http://picasaweb.google.com/data/feed/base/user/kaicompagner/albumid/5275886784482388481');
#var_dump($picasa->get());
 
?>
