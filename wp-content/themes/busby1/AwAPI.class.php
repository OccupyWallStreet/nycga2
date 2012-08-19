<?php
/**
* FeedBurner Awareness PHP 5 API wrapper class AwAPI.class.php
*
* The FeedBurner Awareness PHP API wrapper class AwAPI.class.php 
* provides an easy and simple was to use the FeedBurner Awareness API 
* in your PHP projects.
* 
* @license     	GNU General Public License (GPL)   
* @copyright  	Copyright (C) 2010 Goce Bonev, DevMD (http://devmd.com)             
* @author      	Goce Bonev <info@devmd.com>
* @link        	http://devmd.com/awapi
* @version		0.1
* @access 		public 
*/	

/* 
=======================================================================

LICENSE
-----------------------------------------------------------------------
FeedBurner Awareness PHP API wrapper class AwAPI.class.php
Copyright (C) 2010 Goce Bonev, DevMD (http://devmd.com)
	
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License (GPL)
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

To read the license please visit http://www.gnu.org/copyleft/gpl.html

=======================================================================
*/

	
	if(substr(phpversion(), 0, 1) < 5){die("Class AwAPI (AwAPI.class.php) requires PHP 5 or higher.");}
	
	class AwAPI{
		/**
		* Feedburner Awareness API Service URL
		*/
		const API = 'https://feedburner.google.com/api/awareness/1.0/';
		
		/**
		* FeedBurner Feed URI
		* @var string
		* @access private 
		*/
		private $uri;
		
		/**
		* Error message
		* @var string
		* @access public 
		*/
		public $err;
		
		/**
		* Error code number
		* @var number
		* @access public 
		*/
		public $errcode;
		
		/**
		* Use exceptions or return false
		* @var boolean
		* @access public 
		*/
		public $exceptions;
		
		/** 
		* Create an instance, optionally setting a starting point 
		* @param string $uri the URI of the FeedBurner feed
		* @param boolean $exceptions weather to use exceptions or just return false
		* @access public 
		*/ 
		function __construct($uri, $exceptions = true){
			$this -> uri = $uri;
			$this -> exceptions = $exceptions;
		}
		function __destruct(){}
		
		/** 
		* Current basic feed circulation, hits and reach
		* @access public 
		* @return mixed 
		*/ 
		public function FeedData(){
			$raw = $this -> call('GetFeedData');
			$result = Array();
			if($raw !== false && is_array($raw['ENTRY'][0]) && count($raw['ENTRY'][0]) && is_array($raw['FEED'][0]) && count($raw['FEED'][0]) > 0){
				# Make nice array
				foreach($raw['FEED'][0] as $i => $v){$result[strtolower($i)] = $v;}
				foreach($raw['ENTRY'][0] as $i => $v){$result[strtolower($i)] = $v;}
				return $result;
			}
			else{
				$this -> error('No data available!');
				return false;
			}
		}
	
		/** 
		* Get feed circulation, hits and reach history
		* @param string $startDate date in format YYYY-MM-DD
		* @param string $endDate date in format YYYY-MM-DD, if left blank set to today, optional
		* @access public 
		* @return mixed 
		*/ 
		public function FeedDataHistory($startDate, $endDate = ''){
			if(empty($startDate)){$this -> error('No start date'); return false;}
			if(empty($endDate)){$endDate = date('Y-m-d', time());}
			
			$raw = $this -> call('GetFeedData', array('dates' => $startDate.','.$endDate));
			
			if($raw !== false && is_array($raw['ENTRY']) && count($raw['ENTRY']) > 0){
				# Make nice array
				$result = Array();
				foreach($raw['ENTRY'] as $entry){
					foreach($entry as $i => $v){
						$result[$entry['DATE']][strtolower($i)] = $v;
					}
				}
				return $result;
			}
			else{
				$this -> error('No data available!');
				return false;
			}
		}		
		
		/** 
		* Note: to get item level stats the feed must be a FeedBurner Stats feed with the "track item views" feature enabled.
		* Current Feed Items Circulation, Hits And Reach
		* @param string $itemurl url of the feed item, optional
		* @access public 
		* @return mixed 
		*/ 
		public function ItemData($itemurl = ''){
			if(empty($itemurl)){$raw = $this -> call('GetItemData');}
			else{$raw = $this -> call('GetItemData', array('itemurl' => $itemurl));}
			
			$result = Array();
			if($raw !== false && is_array($raw['ITEM']) && count($raw['ITEM']) > 0){
				# Make nice array
				foreach($raw['ITEM'] as $n => $item){
					foreach($item as $i => $v){
						$result[$n][strtolower($i)] = $v;
					}
				}
				if(!empty($itemurl)){return $result[0];}
				return $result;
			}
			else{
				$this -> error('No data available!');
				return false;
			}
		}	
		
		/** 
		* Feed Item Circulation, Hits And Reach History
		* Note: to get item level stats the feed must be a FeedBurner Stats feed with the "track item views" feature enabled.
		* @param string $startDate date in format YYYY-MM-DD
		* @param string $endDate date in format YYYY-MM-DD, if left blank set to today, optional
		* @param string $itemurl url of the feed item, optional
		* @access public 
		* @return mixed 
		*/ 
		public function ItemDataHistory($startDate, $endDate = '', $itemurl = ''){
			if(empty($startDate)){$this -> error('No start date'); return false;}
			if(empty($endDate)){$endDate = date('Y-m-d', time());}
			
			if(empty($itemurl)){$raw = $this -> call('GetItemData', array('dates' => $startDate.','.$endDate));}
			else{$raw = $this -> call('GetItemData', array('itemurl' => $itemurl, 'dates' => $startDate.','.$endDate));}
			
			$result = Array();
			if(!empty($itemurl)){
				if($raw !== false && is_array($raw['ITEM']) && count($raw['ITEM']) > 0 && is_array($raw['ENTRY']) && count($raw['ENTRY']) > 0){
					# Make nice array
					$offset = (int)(count($raw['ENTRY']) - count($raw['ITEM']));
					foreach($raw['ITEM'] as $n => $item){
						foreach($item as $i => $v){
							$result[$n][strtolower($i)] = $v;
							$result[$n]['date'] = $raw['ENTRY'][($n+$offset)]['DATE'];
						}
					}
					return $result;
				}
				else{
					$this -> error('No data available!');
					return false;
				}
			}
			else{
				if(is_array($raw) && count($raw) > 0){
					# Make nice array
					foreach($raw as $el){
						if($el['tag'] == 'ENTRY' && is_array($el['attributes'])){
							$cdate = $el['attributes']['DATE'];
							$result[$cdate]['circulation'] = $el['attributes']['CIRCULATION'];
							$result[$cdate]['hits'] = $el['attributes']['HITS'];
							$result[$cdate]['reach'] = $el['attributes']['REACH'];
						}
						elseif($el['tag'] == 'ITEM' && is_array($el['attributes'])){
							$result[$cdate]['items'][] = array('title' => $el['attributes']['TITLE'], 'url' => $el['attributes']['URL'], 'itemviews' => $el['attributes']['ITEMVIEWS'], 'clickthroughs' => $el['attributes']['CLICKTHROUGHS']);
						}
					}
					return $result;
				}
				else{
					$this -> error('No data available!');
					return false;
				}
			}
		}	
		
		/** 
		* Get feed / item resyndication data
		* @param string $itemurl url of the feed item, optional
		* @access public 
		* @return mixed 
		*/ 
		public function ResyndicationData($itemurl = ''){
			if(empty($itemurl)){$raw = $this -> call('GetResyndicationData');}
			else{$raw = $this -> call('GetResyndicationData', array('itemurl' => $itemurl));}
			
			$result = Array();
			if(is_array($raw) && count($raw) > 0){
					# Make nice array
					$c = -1;
					foreach($raw as $el){
						if($el['tag'] == 'ITEM' && is_array($el['attributes'])){
							$c++;
							$result[$c] = array('title' => $el['attributes']['TITLE'], 'url' => $el['attributes']['URL'], 'itemviews' => $el['attributes']['ITEMVIEWS'], 'clickthroughs' => $el['attributes']['CLICKTHROUGHS']);
						}
						elseif($el['tag'] == 'REFERRER' && is_array($el['attributes'])){
							$result[$c]['items'][] = array('url' => $el['attributes']['URL'], 'itemviews' => $el['attributes']['ITEMVIEWS'], 'clickthroughs' => $el['attributes']['CLICKTHROUGHS']);
						}
					}
				if(empty($itemurl)){return $result;}
				else{return $result[0];}
			}
			else{
				$this -> error('No data available!');
				return false;
			}
		}	

		/** 
		* Make an API request
		* @param string $method FeedBurner Awareness API method
		* @param mixed $extra params to the reqest string
		* @access private 
		* @return mixed 
		*/ 		
		private function call($method, $extra = ''){
			# Add extra arguments
			if(is_array($extra) && count($extra) > 0){
				foreach($extra as $i => $v){
					$extra_url[] = urlencode($i).'='.urlencode($v);
				}
				$url = self::API.$method.'?uri='.$this -> uri.'&'.implode('&', $extra_url);
			}
			else{
				$url = self::API.$method.'?uri='.$this -> uri;
			}
			# echo $url;
			# Connect to API and get response via cURL
			$options = array(
				CURLOPT_URL 			=> $url,
				CURLOPT_RETURNTRANSFER 	=> true,
				CURLOPT_HEADER         	=> false,
				//CURLOPT_FOLLOWLOCATION 	=> true,
				CURLOPT_ENCODING       	=> 'UTF-8',
				CURLOPT_AUTOREFERER    	=> true,
				CURLOPT_CONNECTTIMEOUT 	=> 10,
				CURLOPT_TIMEOUT        	=> 10,
				CURLOPT_MAXREDIRS      	=> 5,
				CURLOPT_SSL_VERIFYHOST 	=> 0, 
				CURLOPT_SSL_VERIFYPEER 	=> false,  
				CURLOPT_VERBOSE        	=> 1   
			);
			
			$ch = curl_init();
			curl_setopt_array($ch, $options);
			$content = curl_exec($ch);
			$err = curl_errno($ch);
			$errmsg = curl_error($ch) ;
			$header = curl_getinfo($ch); 
			curl_close($ch);
			
			# Parse returned XML
			$parser = xml_parser_create('UTF-8');
			if(xml_parse_into_struct($parser, $content, $arr) == 0){
				$this -> error('Can not parse XML!');
				return false;
			}
			xml_parser_free($parser);
			
			//d($arr);
			
			# Check for errors
			if($arr[0]['attributes']['STAT'] == 'fail'){
				$this -> errcode = $arr[1]['attributes']['CODE'];
				$this -> error($arr[1]['attributes']['MSG']);
				return false;
			}		
				
			# Excetions
			if(($method == 'GetItemData' && preg_match('|&dates=|', $url) && !preg_match('|&itemurl=|', $url)) || ($method == 'GetResyndicationData')){
				return $arr;
			}
			
			# Put everyhing into a nice array;
			$result = array();
			if(is_array($arr) && count($arr) > 0){
				foreach($arr as $el){
					if(is_array($el['attributes']) && count($el['attributes']) > 0){
						$result[$el['tag']][] = $el['attributes'];
					}
				}
			}
			return $result;
		}
		
		/** 
		* Set error / throw exception 
		* @param string $what the error message
		* @access private 
		*/ 
		private function error($what){
			$this -> err =  $what;
			if($this -> exceptions){throw new Exception($this -> err);}
		}
	}