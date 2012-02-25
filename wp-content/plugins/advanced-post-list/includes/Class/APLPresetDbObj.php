<?php

/**
 * <p><b>Desc:</b> Database Object to contain Preset Objects to be
 *                  saved to the database.</p>
 * 
 * @package APLPresetDbObj
 * @since 0.1.0
 * 
 */
class APLPresetDbObj
{

    /**
     * @var string
     * @since 0.1.0
     */
    var $_preset_db_name;

    /**
     * @var array(APLPresetObj())
     * @since 0.1.0
     */
    var $_preset_db;

    /**
     * @var string
     * @since 0.1.0
     */
    var $_delete;

    /**
     * <p><b>Desc:</b> Loads Preset Database Option data. If no data
     *                  exists with the same name, then a new database
     *                  options is created and saved to the developer's
     *                  WordPress database.</p> 
     * @param string $db_name 
     * 
     * @since 0.1.0
     * @uses $this->option_load_db()
     * @uses $this->set_to_defaults()
     * @uses $this->options_save_db();
     * 
     * @tutorial
     * <ol>
     * <li value="1">Store Preset_Database-Name to 
     *               $this->Preset_Database-Name.</li>
     * <li value="2">Load 'Preset DbOptions' values, if any then 
     *               skip <b>Steps 3-4</b>.</li>
     * <li value="3">Set (this) values to default values.</li>
     * <li value="4">Save 'Preset DbOptions.</li>
     * </ol>
     */
    function __construct($db_name)
    {
        //Step 1
        $this->_preset_db_name = 'APL_preset_db-' . $db_name;
        //Step 2
        $this->option_load_db();

        //If data doesn't exist in options, then make one
        if (empty($this->_preset_db) && empty($this->_delete))
        {
            //Step 3
            $this->set_to_defaults();
            //Step 4
            $this->options_save_db();
            //$this->option_load_db();
        }
    }

    /**
     * <p><b>Desc:</b> Loads and stores database values to 
     *                 (this) class values.</p>  
     * @param none
     * 
     * @since 0.1.0
     *  
     * @tutorial
     * <ol>
     * <li value="1">Get 'Preset DbOptions with the value stored in 
     *              the class varible _preset_db_name.</li>
     * <li value="2">Store database varible to class values (_preset_db & _delete).</li>
     * </ol>
     */
    function option_load_db()
    {
        //Step 1
        $DBOptions = get_option($this->_preset_db_name);
        //Step 2
        $this->_preset_db = $DBOptions->_preset_db;
        $this->_delete = $DBOptions->_delete;
    }

    /**
     * <p><b>Desc:</b> Saves (this) class object to the developer's 
     *                 WordPress database.</p>  
     * @param none
     * 
     * @since 0.1.0
     *  
     * @tutorial
     * <ol>
     * <li value="1">Save (this) class object to database.</li>
     * </ol>
     */
    function options_save_db()
    {
        //Step 1
        update_option($this->_preset_db_name, $this);
    }

//  function options_save_db($newOptions)
//  {
//    
//    //$this->_option_db_name = 'APL_option_db_'.$db_name;
//    //$this->update_options_db();
//    if (isset($newOptions))
//    {
//      $this->_preset_db = $newOptions->_preset_db;
//      update_option($newOptions->_preset_db_name, $newOptions);
//    }
//    
//  }
    /**
     * <p><b>Desc:</b> Deletes the 'Preset Database Options' that is 
     *                 stored in the devoloper's WordPress database.</p>  
     * @param none
     * 
     * @since 0.1.0
     *  
     * @tutorial
     * <ol>
     * <li value="1">Delete Options with the same Preset Db Options name.</li>
     * </ol>
     */
    function options_remove_db()
    {
        //Step 1
        delete_option($this->_preset_db_name);
    }

    /**
     * <p><b>Desc:</b> </p>  
     * @param none 
     * @return string - returns a JSON string of the Standard Class
     * 
     * @since 0.1.0
     *   
     * @tutorial
     * <ol>
     * <li value="1">Set delete to 'true'.</li>
     * <li value="2">Create a temp Preset (stdclass). Hardcoded as a JSON string.</li>
     * <li value="3">JSON Decode and store in (this) class _preset_db value.</li>
     * <li value="4"></li>
     * <li value="5"></li>
     * <li value="6"></li>
     * <li value="7"></li>
     * <li value="8"></li>
     * <li value="9"></li>
     * </ol>
     */
    function set_to_defaults()
    {
        
        $this->_preset_db = new stdClass();
        //$this->_preset_db = array();
        //Step 1
        $this->_delete = 'true';
        //Step 2
        $tmpPreset = (string) '{"pageContentDivided_5":{
                                                    "_before":"<p><hr\/>",
                                                    "_content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_content]<hr\/>",
                                                    "_after":"<\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"post",
                                                    "_postParent":"None",
                                                    "_postExcludeCurrent":"true"
                                                   },
                            "postExcerptDivided_5":{
                                                    "_before":"<p><hr\/>",
                                                    "_content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_excerpt]<hr\/>",
                                                    "_after":"<\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"post",
                                                    "_postParent":"None",
                                                    "_postExcludeCurrent":"true"
                                                   },
                         "simpleAttachmentList_10":{
                                                    "_before":"<ul>",
                                                    "_content":"<li><a href=\"[post_permalink]\">[post_title]<\/a><\/li>",
                                                    "_after":"<\/ul>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"post",
                                                    "_postParent":"None",
                                                    "_postExcludeCurrent":"true"
                                                   },
                                        "images_5":{
                                                    "_before":"<hr \/>",
                                                    "_content":"<p><a href=\"[post_permalink]\"><img src=\"[guid]\" \/><\/a><\/p>",
                                                    "_after":"<hr \/>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"post",
                                                    "_postParent":"None",
                                                    "_postExcludeCurrent":"true"
                                                   },
                                "pageDropdown_100":{
                                                    "_before":"<p><select id=\"postList_dropdown\" style=\"width:200px; margin-right:20px\">",
                                                    "_content":"<option value=\"[post_permalink]\">[post_title]<\/option>",
                                                    "_after":"<\/ select> <input type=\"button\" id=\"postList_goBtn\" value=\"GO!\" onClick=\"javascript:window.location=document.getElementById(\'postList_dropdown\').value\" \/><\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"post",
                                                    "_postParent":"None",
                                                    "_postExcludeCurrent":"true"
                                                   },
                                "simplePostList_5":{
                                                    "_before":"<p>",
                                                    "_content":"<a href=\"[post_permalink]\">[post_title]<\/a>[final_end], ",
                                                    "_after":"<\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"post",
                                                    "_postParent":"None",
                                                    "_postExcludeCurrent":"true"
                                                   },
                               "footerPageList_10":{
                                                     "_before":"<p align=\"center\">",
                                                    "_content":"<a href=\"[post_permalink]\">[post_title]<\/a>[final_end] | ",
                                                    "_after":"<\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"page",
                                                    "_postParent":"",
                                                    "_postExcludeCurrent":"true"
                                                   },
                          "everythingNumbered_200":{
                                                    "_before":"<p>All my pages and posts (roll over for titles):<br\/>",
                                                    "_content":"<a href=\"[post_permalink]\" title=\"[post_title]\">[item_number]<\/a>[final_end], ",
                                                    "_after":"<\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"page",
                                                    "_postParent":"",
                                                    "_postExcludeCurrent":"true"
                                                   },
                                "everythingID_200":{
                                                    "_before":"<p>All my pages and posts (roll over for titles):<br\/>",
                                                    "_content":"<a href=\"[post_permalink]\" title=\"[post_title]\">[ID]<\/a>[final_end], ",
                                                    "_after":"<\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"page",
                                                    "_postParent":"",
                                                    "_postExcludeCurrent":"true"
                                                   },
                                  "relatedPosts_5":{
                                                    "_before":"<p>Related posts: ",
                                                    "_content":"<a href=\"[post_permalink]\" title=\"[post_excerpt]\">[post_title]<\/a>[final_end], ",
                                                    "_after":"<\/p>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"page",
                                                    "_postParent":"",
                                                    "_postExcludeCurrent":"true"
                                                   },
                                        "CSSTable":{
                                                    "_before":"<style>\n.k_ul{width: 320px;text-align:center;list-style-type:none;}\n.k_li{width: 100px; height:65px; float: left; padding:3px;}\n.k_a{border:1px solid #f00;display:block;text-decoration:none;font-weight:bold;width:100%; height:65px}\n.k_a:hover{border:1px solid #00f;background:#00f;color:#fff;}\n.k_a:active{background:#f00;color:#fff;}\n<\/style><ul class=\"k_ul\">",
                                                    "_content":"<li class=\"k_li\"><a class=\"k_a\" href=\"[post_permalink]\">[post_title]<\/a><\/li>",
                                                    "_after":"<\/ul>",
                                                    "_catsSelected":"",
                                                    "_tagsSelected":"",
                                                    "_catsInclude":"false",
                                                    "_tagsInclude":"false",
                                                    "_catsRequired":"false",
                                                    "_tagsRequired":"false",
                                                    "_listOrder":"DESC",
                                                    "_listOrderBy":"post_date",
                                                    "_listAmount":"5",
                                                    "_postType":"page",
                                                    "_postParent":"",
                                                    "_postExcludeCurrent":"true"
                                                   }
                                             }';
        //Step 3
        $this->_preset_db = json_decode($tmpPreset);
    }

}

/*
 * //Load Options if any
  foreach ($name as $key)
  {
      $preset_options[$name[$key]] = get_option('APL_preset_' . $name);
      //TODO create other options for creating groups of post
      //      list settings.
  }
  if (!isset($preset_options))
  {
      install();
  }
 */
?>
