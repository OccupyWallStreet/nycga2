<?php

require_once '../../../../wp-load.php';
require_once '/Class/APLCore.php';
require_once 'Class/APLPresetDbObj.php';
require_once 'Class/APLPresetObj.php';
wp_verify_nonce($_REQUEST['wp_nonce'],
                'APL_import');

function APL_import_file()
{
    $presetDbObj = new APLPresetDbObj('default');
    $file_presetDb = json_decode(file_get_contents($_FILES['fileImportDir']['tmp_name']));

    foreach ($file_presetDb as $presetDb_key => $presetDb_value)
    {
        if ($presetDb_key == '_preset_db')
        {
            foreach ($file_presetDb->_preset_db as $preset_name => $preset_value)
            {

                $tmp_presetObj = new APLPresetObj();
                if (isset($preset_value->_catsSelected) &&
                        isset($preset_value->_tagsSelected) &&
                        isset($preset_value->_catsInclude) &&
                        isset($preset_value->_tagsInclude) &&
                        isset($preset_value->_catsRequired) &&
                        isset($preset_value->_tagsRequired) &&
                        isset($preset_value->_postType))
                {

                    $tmp_presetObj = APL_convert_preset_020_030($preset_value);
                }
                else
                {

                    $tmp_presetObj = $preset_value;
                }
                $presetDbObj->_preset_db->$preset_name = $tmp_presetObj;
            }
        }
        else
        {
            $presetDbObj->$presetDb_key = $presetDb_value;
        }
    }

    $presetDbObj->options_save_db();
}

function APL_convert_preset_020_030($old_presetObj)
{
    $rtnPresetObj = new APLPresetObj();

    if ($old_presetObj->_postParent === 'current')
    {
        $rtnPresetObj->_postParent[0] = "-1";
    }
    else if ($old_presetObj->_postParent !== 'None' && $old_presetObj->_postParent !== '')
    {
        $rtnPresetObj->_postParent[0] = $old_presetObj->_postParent;
    }

    if ($old_presetObj->_catsSelected !== '')
    {

        $rtnPresetObj->_postTax->post->taxonomies->category->require_taxonomy = false; //NEW
        $rtnPresetObj->_postTax->post->taxonomies->category->require_terms = true;
        if ($old_presetObj->_catsRequired === 'false')
        {
            $rtnPresetObj->_postTax->post->taxonomies->category->require_terms = false;
        }
        $rtnPresetObj->_postTax->post->taxonomies->category->include_terms = true;
        if ($old_presetObj->_catsInclude === 'false')
        {
            $rtnPresetObj->_postTax->post->taxonomies->category->include_terms = false;
        }
        $terms = explode(',',
                         $old_presetObj->_catsSelected);
        $i = 0;
        foreach ($terms as $term)
        {
            $rtnPresetObj->_postTax->post->taxonomies->category->terms[$i] = intval($term);
            $i++;
        }
    }
    if ($old_presetObj->_tagsSelected !== '')
    {

        $rtnPresetObj->_postTax->post->taxonomies->post_tag->require_taxonomy = false; //NEW
        $rtnPresetObj->_postTax->post->taxonomies->post_tag->require_terms = true;
        if ($old_presetObj->_tagsRequired === 'false')
        {
            $rtnPresetObj->_postTax->post->taxonomies->post_tag->require_terms = false;
        }
        $rtnPresetObj->_postTax->post->taxonomies->post_tag->include_terms = true;
        if ($old_presetObj->_tagsInclude === 'false')
        {
            $rtnPresetObj->_postTax->post->taxonomies->post_tag->include_terms = false;
        }
        $terms = explode(',',
                         $old_presetObj->_tagsSelected);
        $i = 0;
        foreach ($terms as $term)
        {
            $rtnPresetObj->_postTax->post->taxonomies->post_tag->terms[$i] = intval($term);
            $i++;
        }
    }

    $rtnPresetObj->_listAmount = intval($old_presetObj->_listAmount);

    $rtnPresetObj->_listOrderBy = $old_presetObj->_listOrderBy;
    $rtnPresetObj->_listOrder = $old_presetObj->_listOrder;

    $rtnPresetObj->_postStatus = 'publish';

    $rtnPresetObj->_postExcludeCurrent = true;
    if ($old_presetObj->_postExcludeCurrent === 'false')
    {
        $rtnPresetObj->_postExcludeCurrent = false;
    }

    $rtnPresetObj->_before = $old_presetObj->_before;
    $rtnPresetObj->_content = $old_presetObj->_content;
    $rtnPresetObj->_after = $old_presetObj->_after;


    return $rtnPresetObj;
}

function import_kalin()
{
    $presetDbObj = new APLPresetDbObj('default');
    $tmp_array = get_option('kalinsPost_admin_options');
    $tmp_preset = json_decode($tmp_array['preset_arr']);
    foreach ($tmp_preset as $key => $value)
    {
        $presetDbObj->_preset_db->$key = convert_kalin($value);
        $presetDbObj->_preset_db->$key = APL_convert_preset_020_030($presetDbObj->_preset_db->$key);
    }
    $presetDbObj->options_save_db();
}

function convert_kalin($tmp_preset)
{
    $presetObj = new APLPresetObj();
    $presetObj->_catsSelected = $tmp_preset->categories; //replace these lines with dynamic loop like in restore_preset()?
    $presetObj->_tagsSelected = $tmp_preset->tags;
    $presetObj->_postType = $tmp_preset->post_type;
    $presetObj->_listOrderBy = $tmp_preset->orderby;
    $presetObj->_listOrder = $tmp_preset->order;
    $presetObj->_listAmount = $tmp_preset->numberposts;
    $presetObj->_before = $tmp_preset->before;
    $presetObj->_content = $tmp_preset->content;
    $presetObj->_after = $tmp_preset->after;
    $presetObj->_postExcludeCurrent = $tmp_preset->excludeCurrent;

    if (isset($tmp_preset->post_parent))
    {
        $presetObj->_postParent = $tmp_preset->post_parent;
    }

    $presetObj->_catsInclude = $tmp_preset->includeCats;
    $presetObj->_tagsInclude = $tmp_preset->includeTags;

    if (isset($tmp_preset->requireAllCats))
    {
        $presetObj->_catsRequired = $tmp_preset->requireAllCats;
    }
    if (isset($tmp_preset->requireAllTags))
    {
        $presetObj->_tagsRequired = $tmp_preset->requireAllTags;
    }

    return $presetObj;
}

//FILE REFERENCE
//$filename = $_GET["name"];
//$presetDbObj = new APLPresetDbObj('default');
////file name - APL.(date&time).json
//$_FILES['importFileDir']['name'];
////type of data - application/octet-stream
//$_FILES['importFileDir']['type'];
////temp file path - C:\xampp\tmp\php3C4.tmp
//$_FILES['importFileDir']['tmp_name'];
////error - 0
//$_FILES['importFileDir']['error'];
////size of the file in bytes
//$_FILES['importFileDir']['size'];

if (isset($_FILES['fileImportDir']))
{
    switch ($_POST['importType'])
    {
        case 'file':
            APL_import_file();
//            $presetDbObj = new APLPresetDbObj('default');
//            $content = file_get_contents($_FILES['fileImportDir']['tmp_name']);
//
//            $tmp_array = json_decode($content);
//
//            //Copy import array to Db array
//            foreach ($tmp_array as $key1 => $value1)
//            {
//                if ($key1 == '_preset_db')
//                {
//                    foreach ($tmp_array->_preset_db as $key2 => $value2)
//                    {
//                        $presetDbObj->_preset_db->$key2 = $value2;
//                    }
//                }
//                else
//                {
//                    $presetDbObj->$key1 = $value1;
//                }
//            }
//
//            //$presetDbObj->options_save_db();
            break;
        case 'kalin':
            import_kalin();
//            $presetDbObj = new APLPresetDbObj('default');
//            $tmp_array = get_option('kalinsPost_admin_options');
//            $tmp_preset = json_decode($tmp_array['preset_arr']);
//            foreach ($tmp_preset as $key => $value)
//            {
//                $presetDbObj->_preset_db->$key = convert_kalin($value);
//            }
//            $presetDbObj->options_save_db();
            break;
        default:
    }
//$rezultat = urlencode($rezultat);
//echo '<body onload="parent.buildPresetTable()"></body>';
//
//categories to _catsSelected
//tags to tagsSelected
//post_type to _postType
//orderby to _listOrderBy
//order to _listOrder
//numberposts to _listAmount
//before to _before
//content to _content
//after to _after
//excludeCurrent to _postExcludeCurrent
//includeCats to _catsInclude
//includeTags to _tagsInclude
//
///"_before":"<style>\n.k_ul{width: 320px;text-align:center;list-style-type:none;}\n.k_li{width: 100px; height:65px; float: left; padding:3px;}\n.k_a{border:1px solid #f00;display:block;text-decoration:none;font-weight:bold;width:100%; height:65px}\n.k_a:hover{border:1px solid #00f;background:#00f;color:#fff;}\n.k_a:active{background:#f00;color:#fff;}\n<\/style><ul class=\"k_ul\">",
//"_content":"<li class=\"k_li\"><a class=\"k_a\" href=\"[post_permalink]\">[post_title]<\/a><\/li>",
//"_after":"<\/ul>",
//"_catsSelected":"",
//"_tagsSelected":"",
//"_catsInclude":"false",
//"_tagsInclude":"false",
//"_catsRequired":"false",
//"_tagsRequired":"false",
//"_listOrder":"DESC",
//"_listOrderBy":"post_date",
//"_listAmount":"5",
//"_postType":"page",
//"_postParent":"",
//"_postExcludeCurrent":"true"
//
//"categories":"",
//"tags":"",
//"post_type":"page",
//"orderby":"menu_order",
//"order":"ASC",
//"numberposts":"5",
//"before":"<p><hr\/>",
//"content":"<a href=\"[post_permalink]\">[post_title]<\/a> by [post_author] - [post_date]<br\/>[post_content]<hr\/>",
//"after":"<\/p>",
//"excludeCurrent":"true",
//"includeCats":"false",
//"includeTags":"false"},
//
//
//
    //echo '<div id="presetListDiv" onload="buildPresetTable" ></div>';
    //header('Content-type: application/json');
    //header('Content-Disposition: attachment; filename="Works.json"');
    //echo 'Works!!!';
}
?>
