<?php
 /* Diamond option based cache */
 
class Diamond_Cache {
	
	private $cache_file;
	
	
	function Diamond_Cache() {
		$this->cache_file = dirname(__FILE__). '/cache_';
		
	}
	
	function check_group($group) {
		
		$old_cache = maybe_unserialize(file_get_contents($this->cache_file.$group));
		
		$new_cache = array ();
		
		if ($old_cache)
			foreach ($old_cache AS $c) {				
				if (time() - $c['time'] < $c['expire']) {
					$new_cache[$c['key']] = $c;
				}
		}
    	file_put_contents($this->cache_file.$group, maybe_serialize($new_cache));
		return $new_cache;
	}
	
	function getSettings($group, $key) {
		$settings = get_option('diamond_cache_settings');
		if (!$settings[$group])
			$settings[$group] = array ();
		return $settings[$group][$key];
	}
	
	function addSettings($group, $key, $value) {
		$settings = get_option('diamond_cache_settings');
		$settings[$group][$key] = $value;
		update_option('diamond_cache_settings', $settings);		
	}
	
	function get($key, $group) {
		$cache = $this->check_group($group) ;
		if (!$cache[$key]) 
			return false;
		return $cache[$key]['content'];
	}
	
	function add($key, $group, $value, $expire = -1) {
		$cache = $this->check_group($group) ;
		if ($expire == -1)	{
			$expire = $this->getSettings($group, 'expire');			
			
			if (!$expire)
				$expire = 120;
		}
		if ($expire == 0)
			return;
		$cache[$key]  = array ( 'key' => $key, 'content' => $value, 'expire' => $expire, 'time' => time());
    	file_put_contents($this->cache_file.$group, maybe_serialize($cache));
	}	
}

$DiamondCache = new Diamond_Cache();

?>