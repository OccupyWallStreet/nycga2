<?php
/**
* Plugin Name: BuddyPress Global Unified search
* Plugin URI:http://buddydev.com/plugins/bp-global-unified-search/
* Version: 1.0
* Author: Brajesh Singh
* Author URI: http://buddydev.com/members/sbrajesh
* License: GPL
* Last Updated: September 16, 2011
* Description: Global Unified search for BuddyPress 1.5
* Requires BuddyPress 1.5 (alpha,beta,gamma,RC,trunk or stable release when available)
*
*/
//setyupr site/search as the root component for pre 1.5 compatibility

//let us build it using a singleton class to avoid namespace collision in future
class BPUnifiedsearch{
    private static $instance;
    private function __construct(){
        //all hooks here
        add_action('bp_setup_root_components',array(&$this,'setup_search_component'));
        add_action('bp_setup_globals',array(&$this,'setup_globals'));//setup globals
        add_action('bp_actions',array(&$this,'screen_handler'),3);//catch the search page screen
        add_filter('bp_core_exclude_pages',array(&$this,'remove_from_nav'));//remove search page from page navigation
        //title for search page
        add_filter('bp_modify_page_title',array(&$this,'get_title'),5,4);
        
    }
    
    //factory method
    public static function get_instance(){
        //if the instance is not set, let us create it
        if(!isset(self::$instance))
                self::$instance=new self();
        
        return self::$instance;
    }
    
    //add root component
    function setup_search_component(){
        bp_core_add_root_component( 'search' );//
    }
    
    function setup_globals(){
        global $bp;
        if(!defined('BP_SEARCH_SLUG'))
            define('BP_SEARCH_SLUG','search');//though we know it is set, just a safety bit
            
            if(!property_exists($bp, 'search')){
                $bp->search = new stdClass();
            }
            
            $bp->search->slug = BP_SEARCH_SLUG;
            $bp->search->has_directory = true;
            $bp->search->id = 'search';
            $bp->search->name = __('Search Page', 'cc');
            $bp->search->root_slug = isset($bp->pages->search->slug) ? $bp->pages->search->slug : $bp->search->slug;
       }
     
    function screen_handler(){
        global $bp;
        if(bp_is_current_component($bp->search->slug))//really? yup, we don't care about others vars set or not
            bp_core_load_template(apply_filters('bp_unified_search_template','search-single'),true);//use this filter to change the main template file of search which lists the search
    }

    //remove from nav
    function remove_from_nav($excluded){
        global $bp;//var_dump($bp);
        if(property_exists($bp, 'pages') && property_exists($bp->pages, 'search') && property_exists($bp->pages->search, 'id')){
            $excluded[] = $bp->pages->search->id;
        }
        return $excluded;
    }
 //search page title
 

    function get_title($title,$cur_title, $sep, $seplocation){
       global $bp;

        if(bp_is_current_component($bp->search->slug))
            $title=get_the_title($bp->pages->search->id). " $sep ";
        
     return $title;

    }
    
}

/**
* I will leave the functions.php code intact, as many website is using it and if I overhaul that code, we may have issues for the existing users :) still decision lies with you my comrades:) Let me know if you want to get rid of functions.php and want me to include the code here(Yes, in that case, you won't have to temper with your template functions at all)
*
*/
?>