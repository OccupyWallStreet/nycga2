<?php

$olt_checklist_version  = '1.1.4';                     // Version number of this copy
$olt_checklist_callback = create_function('$base_url', // Version specific init callback
    'include_once("'.addslashes(dirname(__FILE__)).'/olt-checklist.php");'. // addslashes to please the Windows god...
    'if(is_admin()) {'.
    'global $pagenow;'.
    'if($pagenow == "widgets.php"):'.
    '$js_url  = $base_url . "/olt-checklist.js";'.
    '$css_url = $base_url . "/olt-checklist.css";'.
    '  wp_enqueue_script("olt-checklist",$js_url,array("jquery","jquery-ui-tabs"));'.
    '  wp_enqueue_style("olt-checklist",$css_url);'.
    'endif;'.
    '}'
);

if(!function_exists('enqueue_olt_checklist_loader')){
	/**
     * enqueue_olt_checklist_loader function.
     * 
     * @access public
     * @param mixed $base_url
     * @return void
     */
    function enqueue_olt_checklist_loader($base_url){
        global $olt_checklist, $olt_checklist_version, $olt_checklist_callback;
                
        if(!isset($olt_checklist))
            $olt_checklist = array();
        
        if(substr($base_url, -1) == '/')
            $base_url = substr($base_url, 0, -1);
        
        $olt_checklist[] = array(
            $olt_checklist_version,
            $olt_checklist_callback,
            $base_url
        );
    }
}

if(!function_exists('load_olt_checklist')){
	/**
     * load_olt_checklist function.
     * 
     * @access public
     * @return void
     */
    function load_olt_checklist(){
        global $olt_checklist;
                
        $version  = $olt_checklist[0][0];
        $callback = $olt_checklist[0][1];
        $base_url = $olt_checklist[0][2];
        
        for($i=1;$i<count($olt_checklist);$i++){
            if(is_newer($olt_checklist[$i][0], $version)){
                $version  = $olt_checklist[$i][0];
                $callback = $olt_checklist[$i][1];
                $base_url = $olt_checklist[$i][2];
            }
        }
        
        $callback($base_url);
    }
    
}

add_action('init', 'load_olt_checklist');

if(!function_exists('is_newer')){
	/**
     * is_newer function.
     * 
     * @access public
     * @param mixed $v1. (default: null)
     * @param mixed $v2. (default: null)
     * @return void
     */
    function is_newer($v1 = null, $v2 = null){
        // return true if v1 > v2, false otherwise
                
        if(!is_string($v1) || $v1 == '')
            return false;
        
        if(!is_string($v2) || $v2 == '')
            return true;
        
        $v1_e = explode('.', $v1);
        $v2_e = explode('.', $v2);
        
        for($i=0;;$i++){
            // same prefix, but v2 is longer (or they tied) => v1 <= v2
            // e.g. 1.0 vs 1.0.1
            if($i >= count($v1_e))
                return false;
            
            // same prefix, but v1 is longer (or they tied) => v1 >= v2
            // e.g. 1.0.1 vs 1.0
            if($i >= count($v2_e))
                return true;
            
            $diff = intval($v1_e[$i]) - intval($v2_e[$i]);
            
            if($diff < 0){
                // v1 < v2
                // e.g. 1.1 vs 1.2
                return false;
            }elseif($diff > 0){
                // v1 > v2
                // e.g. 1.2 vs 1.1
                return true;
            }
        }
    }
}

?>