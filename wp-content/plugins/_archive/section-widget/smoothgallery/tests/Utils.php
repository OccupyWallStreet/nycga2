<?php

#
# WordPress SmoothGallery plugin
# Copyright (C) 2008-2009 Christian Schenk
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

require_once('PHPUnit/Framework.php');
require_once('../utils.php');
 
class Utils extends PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function get_smoothgallery_parameter() {
		$meta = "h:500\nw=333";
		$this->assertEquals('500', get_smoothgallery_parameter($meta, array('h')));
		$this->assertEquals('333', get_smoothgallery_parameter($meta, array('w')));

		$meta = "height=500\nwidth=333";
		$this->assertEquals('500', get_smoothgallery_parameter($meta, array('h', 'height')));
		$this->assertEquals('500', get_smoothgallery_parameter($meta, array('h', 'HeIgHt')));
		$this->assertEquals('333', get_smoothgallery_parameter($meta, array('w', 'wiDTh')));
		$meta = "h=500\nw:333";
		$this->assertEquals('500', get_smoothgallery_parameter($meta, array('height', 'h')));
		$this->assertEquals('333', get_smoothgallery_parameter($meta, array('wIdtH', 'w')));

		$meta = "h= 500  \nw : 333";
		$this->assertEquals('500', get_smoothgallery_parameter($meta, array('h')));
		$this->assertEquals('333', get_smoothgallery_parameter($meta, array('w')));
		$meta = "h = 500 \nw   :    333  ";
		$this->assertEquals('500', get_smoothgallery_parameter($meta, array('h')));
		$this->assertEquals('333', get_smoothgallery_parameter($meta, array('w')));

		$values = array(null, 'hurz');
		foreach ($values as $value1) {
			foreach ($values as $value2) {
				$this->assertFalse(get_smoothgallery_parameter($value1, array($value2)));
			}
		}
	}


	/**
	 * @test
	 */
	public function fix_quoting() {
		$this->assertEquals('"test"', fix_quoting('\\"test\\"'));
		$this->assertEquals('"test"', fix_quoting('"test"'));
		$this->assertEquals('"test"', fix_quoting("\\'test\\'"));
		$this->assertEquals('"test"', fix_quoting("'test'"));

		#$this->assertEquals('"test"', fix_quoting('\\"test is "test"\\"'));
	}
 

	/**
	 * @test
	 */
	public function find_alternative_image() {
		$images = array('image-450x150', 'picture-600x450');
		$this->assertEquals('image-450x150', find_alternative_image($images, '450x150'));
		$this->assertEquals('picture-600x450', find_alternative_image($images, '600x450'));
		$this->assertEquals('image-450x150', find_alternative_image($images, '450x600'));
		$this->assertEquals('image-450x150', find_alternative_image($images, '600x150'));
		$this->assertEquals('picture-600x450', find_alternative_image($images, '600x151'));

		$this->assertFalse(find_alternative_image($images, 42));

		$this->assertFalse(find_alternative_image(NULL, NULL));
		$this->assertFalse(find_alternative_image(42, NULL));
		$this->assertFalse(find_alternative_image(42, 12));
		$this->assertFalse(find_alternative_image(array(), 12));
	}

	
	/**
	 * @test
	 */
	public function generate_smoothgallery_query_string() {
		$atts = array('t1' => 12, 't2' => 21, 't3' => 42);
		$defaults = array('t1' => 12, 't2' => 21, 't3' => 42);
		$this->assertEquals('t1=12&amp;t2=21&amp;t3=42', generate_smoothgallery_query_string($atts, array()));
		$this->assertEquals('', generate_smoothgallery_query_string($atts, $defaults));

		$atts['t2'] = 22;
		$this->assertEquals('t2=22', generate_smoothgallery_query_string($atts, $defaults));
		$atts['t3'] = 43;
		$this->assertEquals('t2=22&amp;t3=43', generate_smoothgallery_query_string($atts, $defaults));

		# empty() returns true on "0" (string) -> nasty feature
		$atts['t1'] = 0;
		$this->assertEquals('t1=0&amp;t2=22&amp;t3=43', generate_smoothgallery_query_string($atts, $defaults));

		$this->assertEquals('', generate_smoothgallery_query_string($atts, $blah));
		$this->assertEquals('', generate_smoothgallery_query_string(array(), $defaults));
		$this->assertEquals('', generate_smoothgallery_query_string(array(), array()));

		$atts = array('url' => 'http://example.com/', 't1' => 42);
		$defaults = array('t1' => 42, 'url' => NULL);
		$this->assertEquals('url=http%3A%2F%2Fexample.com%2F', generate_smoothgallery_query_string($atts, $defaults, array('url')));
		$atts['t1'] = 12;
		$this->assertEquals('url=http%3A%2F%2Fexample.com%2F&amp;t1=12', generate_smoothgallery_query_string($atts, $defaults, array('url')));
	}


	/**
	 * @test
	 */
	public function removeElementFromArray() {
		$array = array('test1' => 12, 'test2' => 21, 'test3' => 42);
		removeElementFromArray('test2', $array);
		$this->assertTrue(count($array) == 2);
		$this->assertFalse(isset($array['test2']));
		$this->assertEquals(12, $array['test1']);
		$this->assertEquals(42, $array['test3']);

		$array = array('test1', 'test2', 'test3');
		removeElementFromArray('test2', $array);
		$this->assertTrue(count($array) == 2);
		$this->assertFalse(isset($array['test2']));
	}
}

?>
