<?php
    /**
     * 
     * @author marcus
     *
     */
    class EM_Notices implements Iterator {
        var $notices = array('errors'=>array(), 'infos'=>array(), 'alerts'=>array(), 'confirms'=>array());
        
        function __construct(){
        	session_start();
        	//Grab from session
        	if( !empty($_SESSION['events-manager']['notices']) && is_serialized($_SESSION['events-manager']['notices']) ){
        		$this->notices = unserialize($_SESSION['events-manager']['notices']);
        	}
        	//Flush notices that weren't made to stay cross-requests, we can do this if initialized immediately.
        	foreach($this->notices as $notice_type => $notices){
        		foreach ($notices as $key => $notice){
        			if( empty($notice['static']) ){
        				unset($this->notices[$notice_type][$key]);
        			}else{
        				unset($this->notices[$notice_type][$key]['static']); //so it gets removed next request
        			}
        		}
        	}
            add_action('shutdown', array(&$this,'destruct'));
            add_filter('wp_redirect', array(&$this,'destruct'), 1,1);
        }
        
        function destruct($redirect = false){
        	//die('destructing');
        	$_SESSION['events-manager']['notices'] = serialize($this->notices);
        	return $redirect;
        }
        
        function __toString(){
            $string = false;
            if(count($this->notices['errors']) > 0){
                $string .= "<div class='em-warning em-warning-errors error'>{$this->get_errors()}</div>";
            }
            if(count($this->notices['alerts']) > 0){
                $string .= "<div class='em-warning em-warning-alerts updated'>{$this->get_alerts()}</div>";
            }
            if(count($this->notices['infos']) > 0){
                $string .= "<div class='em-warning em-warning-infos updated'>{$this->get_infos()}</div>";
            }
            if(count($this->notices['confirms']) > 0){
                $string .= "<div class='em-warning em-warning-confirms updated'>{$this->get_confirms()}</div>";
            }
            return ($string !== false) ? "<div class='statusnotice'>".$string."</div>" : '';
        }
        
        /* General */
        function add($string, $type, $static = false){
        	if( is_array($string) ){
        		$result = true;
        		foreach($string as $string_item){
        			if( $this->add($string_item, $type, $static) === false ){ $result = false; }
        		}
        		return $result;
        	}
            if($string != ''){
                if( isset($this->notices[$type]) ){
                	$notice_key = 0;
                	foreach( $this->notices[$type] as $notice_key => $notice ){
                		if($string == $notice['string']){
                			return $notice_key;
                		}
                	}
                    $i = $notice_key+1;
                    $this->notices[$type][$i]['string'] = $string;
                    if( $static ){
                    	$this->notices[$type][$i]['static'] = true;
                    }
                    return $i;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
        function remove($key, $type){
            if( isset($this->notices[$type]) ){
                unset($this->notices[$type][$key]);
                return true;
            }else{
                return false;
            }
        }
        function remove_all(){
        	$this->notices = array('errors'=>array(), 'infos'=>array(), 'alerts'=>array(), 'confirms'=>array());
        }
        function get($type){
            if( isset($this->notices[$type]) ){
        		$string = '';
                foreach ($this->notices[$type] as $key => $error){
                    $class = substr($type, 0, (strlen($type)-1));
                    $string .= "<p>{$error['string']}</p>";
                    /* Disabled for now, pending review due to issues in the_content firing x times e.g. in SFC
                    if( empty($error['static']) || $error['static'] !== true){
                        $this->remove($key, $type);
                    }
                    */
                }
                return $string;
            }
            return false;
        }
        function count($type){
       		if( isset($this->notices[$type]) ){
        		return count($this->notices[$type]);
            }
            return 0;
        }
        
        /* Errors */
        function add_error($string, $static=false){
            return $this->add($string, 'errors', $static);
        }
        function remove_error($key){
            return $this->remove($key, 'errors');
        }
        function get_errors(){
            return $this->get('errors');
        }
        function count_errors(){
            return $this->count('errors');
        }

        /* Alerts */
        function add_alert($string, $static=false){
            return $this->add($string, 'alerts', $static);
        }
        function remove_alert($key){
            return $this->remove($key, 'alerts');
        }
        function get_alerts(){
            return $this->get('alerts');
        }
        function count_alerts(){
            return $this->count('alerts');
        }
        
        /* Info */
        function add_info($string, $static=false){
            return $this->add($string, 'infos', $static);
        }
        function remove_info($key){
            return $this->remove($key, 'infos');
        }
        function get_infos(){
            return $this->get('infos');
        }
        function count_infos(){
            return $this->count('infos');
        }
        
        /* Confirms */
        function add_confirm($string, $static=false){
        	return $this->add($string, 'confirms', $static);
        }
        function remove_confirm($key){
            return $this->remove($key, 'confirms');
        }
        function get_confirms(){
            return $this->get('confirms');
        }  
        function count_confirms(){
            return $this->count('confirms');
        }  

		//Iterator Implementation
	    function rewind(){
	        reset($this->bookings);
	    }  
	    function current(){
	        $var = current($this->bookings);
	        return $var;
	    }  
	    function key(){
	        $var = key($this->bookings);
	        return $var;
	    }  
	    function next(){
	        $var = next($this->bookings);
	        return $var;
	    }  
	    function valid(){
	        $key = key($this->bookings);
	        $var = ($key !== NULL && $key !== FALSE);
	        return $var;
	    }        
        
    }
    function em_notices_init(){
	    global $EM_Notices;
	    $EM_Notices = new EM_Notices();	
    }
    add_action('plugins_loaded', 'em_notices_init');
?>