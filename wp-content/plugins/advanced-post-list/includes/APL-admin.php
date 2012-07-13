<?php
if (!function_exists('add_action'))
{
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}
require_once APL_DIR . 'includes/APL-admin_dialogs.php';


$catList = get_categories('hide_empty=0');
$tagList = get_tags('hide_empty=0');

$adminOptions = $this->APL_options_load();

function APL_post_tax_content()
{
    $htmlContent = '';
    $post_type_names = get_post_types('',
                                      'names');

    $skip_post_types = array('attachment', 'revision', 'nav_menu_item');
    foreach ($skip_post_types as $value)
    {
        unset($post_type_names[$value]);
    }

    foreach ($post_type_names as $post_type_name)
    {
        $htmlContent .= APL_post_tax_get_accordion_content($post_type_name);
    }

    echo $htmlContent;
}

function APL_post_tax_get_accordion_content($post_type_name)
{
    $rtnString = '';

    $post_type_object = get_post_type_object($post_type_name);

    $post_type_object->taxonomies = APL_get_taxonomy_object_names($post_type_name);

    $skip_taxonomies = array('post_format');
    foreach ($skip_taxonomies as $value)
    {
        unset($post_type_object->taxonomies[$value]);
    }

    $rtnString .= '<h3 style="font-size: 10em"><a href="#" style="font-size: 1em">' . $post_type_object->labels->singular_name . '</a></h3>';
    $rtnString .= '<div>';
    //GET HEADER
    $rtnString .= APL_post_tax_get_left_header($post_type_name);
    $rtnString .= APL_post_tax_get_right_header();
    $rtnString .= APL_post_tax_get_taxonomy_section($post_type_name,
                                                    $post_type_object->taxonomies);

    $rtnString .= '</div>';
    return $rtnString;
}

function APL_get_taxonomy_object_names($post_type_name)
{
    $rtnTaxonomyArray = array();

    $taxonomy_names = get_taxonomies('',
                                     'names');
    foreach ($taxonomy_names as $taxonomy_name)
    {
        $taxonomy_object = get_taxonomy($taxonomy_name);
        foreach ($taxonomy_object->object_type as $object_type_name)
        {
            if ($object_type_name === $post_type_name)
            {
                $rtnTaxonomyArray[$taxonomy_name] = $taxonomy_name;
            }
        }
    }

    return $rtnTaxonomyArray;
}

function APL_post_tax_get_left_header($post_type_name)
{
    $rtnString = '';

    $rtnString .= '<div style="font-size: 10em; float: left">';
    $rtnString .= '<select id="slctParentSelector-' . $post_type_name . '" name="parent_select-' . $post_type_name . '" multiple="multiple">';

    $page_settings = array('post_type_name' => $post_type_name);
    $rtnString .= APL_get_page_heirarchy($page_settings);

//        $args = array(
//            'numberposts' => -1,
//            'orderby' => 'title',
//            'post_type' => $post_type_name
//        );
//        $pages = get_posts($args);
//        
//        //TODO CREATE A BETTER FORMAT FOR SHOWING THE HEIRARCHY
//        foreach ($pages as $page)
//        {
//            $rtnString .= "<option value='" . $page->ID . "'>" . $page->post_title . "</option>";
//        }
    //foreach
    $rtnString .= '</select>';
    $rtnString .= '</div>';

    return $rtnString;
}
function APL_post_tax_get_right_header()
{
    $rtnString = '';

    $rtnString .= '<div style="font-size: 10em; float: right">';
    $rtnString .= '<span class="ui-icon ui-icon-info info-icon"  style="float:right" onclick="$( \'#d10\' ).dialog();"></span>';
    $rtnString .= '</div>';
    return $rtnString;
    
}


function APL_post_tax_get_taxonomy_section($post_type_name,
                                           $taxonomy_names)
{
    $rtnString = '';

    $htmlCatTabs = '';
    $htmlCatTabsContent = '';
    $htmlTagTabs = '';
    $htmlTagTabsContent = '';

    $htmlCatTabs .= '<div id="tabs-' . $post_type_name . '-cats" style="clear: both; float: left; height: 320px; max-height: 320px; width: 49%">';
    $htmlTagTabs .= '<div id="tabs-' . $post_type_name . '-tags" style="float: left; height: 320px; max-height: 320px; width: 49%">';

    $htmlCatTabs .= '<ul style="font-size: 9em;">';
    $htmlTagTabs .= '<ul style="font-size: 9em;">';

    //$post_type_object = get_post_type_object($post_type_name);
    foreach ($taxonomy_names as $taxonomy_name)
    {
        $taxonomy_object = get_taxonomy($taxonomy_name);

        if ($taxonomy_object->hierarchical == true)
        {

            //// TAB SECTION
            $htmlCatTabs .= '<li><a href="#tab-' . $post_type_name . '-' . $taxonomy_name . '">' . $taxonomy_object->labels->singular_name . '</a></li>';

            //// CONTENT SECTION
            $htmlCatTabsContent .= '<div id="tab-' . $post_type_name . '-' . $taxonomy_name . '" style="overflow:scroll; overflow-x:hidden; border:ridge; font-size: 12em;">';

            //// ADD A LIST OF OTHER POST TYPES THE TAXONOMY TERMS BELONG TO
            //$htmlCatTabsContent .= APL_add_other_post_types($post_type_name, $taxonomy_name, $taxonomy_object->other_post_types);
            $htmlCatTabsContent .= '<input type=checkbox id="chkReqTaxonomy-' . $post_type_name . '-' . $taxonomy_name . '" name="chkReqTaxonomy-' . $post_type_name . '-' . $taxonomy_name . '" /><b>Require Taxonomy</b> within post_type.<br />';
            $htmlCatTabsContent .= '<input type=checkbox id="chkReqTerms-' . $post_type_name . '-' . $taxonomy_name . '" name="chkReqTerms-' . $post_type_name . '-' . $taxonomy_name . '" /><b>Require Terms</b> selected.<br />';
            $htmlCatTabsContent .= '<input type=checkbox id="chkIncldTerms-' . $post_type_name . '-' . $taxonomy_name . '" name="chkIncldTerms-' . $post_type_name . '-' . $taxonomy_name . '" /><b>Include Terms</b> according to current page.<br />';
            $htmlCatTabsContent .= '<hr />';

            //// ADD TERMS
            $post_tax_settings = array('post_type_name' => $post_type_name, 'taxonomy_name' => $taxonomy_name);
            //$htmlCatTabsContent .= APL_add_terms($post_type_name, $taxonomy_name);
            $htmlCatTabsContent .= APL_get_cat_hierchy($post_tax_settings);

            $htmlCatTabsContent .= '</div>';
        }
        else
        {
            //htmlTagTabs
            //htmlTagTabsContent
            //// TAB SECTION
            $htmlTagTabs .= '<li><a href="#tab-' . $post_type_name . '-' . $taxonomy_name . '">' . $taxonomy_object->labels->singular_name . '</a></li>';

            //// CONTENT SECTION
            $htmlTagTabsContent .= '<div id="tab-' . $post_type_name . '-' . $taxonomy_name . '" style="overflow:scroll; overflow-x:hidden; border:ridge; font-size: 12em;">';
            //// ADD A LIST OF OTHER PST TYPES THE TAG BELONGS TO
            //$htmlTagTabsContent .= APL_add_other_post_types($post_type_name, $taxonomy_name, $taxonomy_object->other_post_types);
            $htmlTagTabsContent .= '<input type=checkbox id="chkReqTaxonomy-' . $post_type_name . '-' . $taxonomy_name . '" name="chkReqTaxonomy-' . $post_type_name . '-' . $taxonomy_name . '" /><b>Require Taxonomy</b> within post_type.<br />';
            $htmlTagTabsContent .= '<input type=checkbox id="chkReqTerms-' . $post_type_name . '-' . $taxonomy_name . '" name="chkReqTerms-' . $post_type_name . '-' . $taxonomy_name . '" /><b>Require Terms</b> selected.<br />';
            $htmlTagTabsContent .= '<input type=checkbox id="chkIncldTerms-' . $post_type_name . '-' . $taxonomy_name . '" name="chkIncldTerms-' . $post_type_name . '-' . $taxonomy_name . '" /><b>Include Terms</b> according to current page.<br />';
            $htmlTagTabsContent .= '<hr />';



            //// ADD TERMS
            $htmlTagTabsContent .= APL_add_terms($post_type_name,
                                                 $taxonomy_name);

            $htmlTagTabsContent .= '</div>';
        }
    }
    $htmlCatTabs .= '</ul>';
    $htmlTagTabs .= '</ul>';

    $htmlCatTabsContent .= '</div>';
    $htmlTagTabsContent .= '</div>';

    //Add Categories section
    $rtnString .= $htmlCatTabs . $htmlCatTabsContent;
    //Add Tags section
    $rtnString .= $htmlTagTabs . $htmlTagTabsContent;

    return $rtnString;
}

function APL_add_terms($post_type_name,
                       $taxonomy_name)
{
    $rtnString = '';
    $argTerms = array('hide_empty' => 0, 'taxonomy' => $taxonomy_name);
    $terms = get_categories($argTerms);

    foreach ($terms as $value_tag)
    {

        $rtnString .= '<input type=checkbox id="chkTerm-' . $post_type_name . '-' . $taxonomy_name . '-' . $value_tag->term_id . '" name="chkTerm-' . $post_type_name . '-' . $taxonomy_name . '-' . $value_tag->term_id . '" />' . $value_tag->name . '<br />';
    }

    return $rtnString;
}

function APL_get_page_heirarchy($page_settings = array('post_type_name' => 'post'),
                                $parent = 0,
                                $depth = -1)
{
    $rtnString = '';
    $dashes = '';
    if ($depth == -1)
    {
        $rtnString .= '<option id="slctChkCurrent-' . $page_settings['post_type_name'] . '" value="' . -1 . '"><b>Current Page</b></option>';
    }
    $depth++;
    for ($i = 0;
            $i < $depth;
            $i++)
    {
        $dashes .= '-';
    }
//        $args = array(
//            'numberposts' => -1,
//            'orderby' => 'title',
//            'post_type' => $post_type_name
//        );  
    $a01 = $page_settings['post_type_name'];
    $argPages = array('numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'post_type' => $page_settings['post_type_name']);
    $pages = get_posts($argPages);

    foreach ($pages as $page)
    {
        
        if ($page->post_parent == $parent)
        {

            $id = $page->ID;
            $rtnString .= "<option value='" . $page->ID . "'>" . $dashes . $page->post_title . "</option>";
            $rtnString .= APL_get_page_heirarchy($page_settings,
                                                 $id,
                                                 $depth);
        }
    }

    return $rtnString;
}

function APL_get_cat_hierchy($post_tax_settings = array('post_type_name' => 'post', 'taxonomy_name' => 'category'),
                             $parent = 0,
                             $depth = -1)
{
    $argTerms = array('hide_empty' => 0, 'taxonomy' => $post_tax_settings['taxonomy_name']);

    $rtnString = '';
    $dashes = '';
    $depth++;
    for ($i = 0;
            $i < $depth;
            $i++)
    {
        $dashes .= '-';
    }
    $terms = get_categories($argTerms);
    $rtn = new stdClass;

    foreach ($terms as $term)
    {
        if ($term->parent == $parent)
        {
            $id = $term->term_id;
            $rtnString .= '<input type=checkbox id="chkTerm-' . $post_tax_settings['post_type_name'] . '-' . $post_tax_settings['taxonomy_name'] . '-' . $term->term_id . '" name="chkTerm-' . $post_tax_settings['post_type_name'] . '-' . $post_tax_settings['taxonomy_name'] . '-' . $term->term_id . '" />' . $dashes . $term->name . '<br />';

            $rtnString .= APL_get_cat_hierchy($post_tax_settings,
                                              $id,
                                              $depth);
        }
    }

    return $rtnString;
}
?>
<div style="max-width: 800px; min-width: 640px; margin-top: 0px">
    <div style="float: left;" >
        <h2>Advanced Post List - Settings</h2>
    </div>
    <div style="float: right; margin-right: 25px;" >
        <h3>Plugin Page (<a href="http://advanced-post-list.wikiforum.net/" title="Community Support Forum" target="_new">Plugin Page</a> / <a href="http://wordpress.org/extend/plugins/advanced-post-list/" title="Default Plugin Page" target="_new">Wordpress</a>)</h3>
    </div>
</div>
<br style="clear:left"/>
<hr/>
<br/>
<div id="divCustomPostTaxonomyContent" style="font-size: 1px; width: 640px">
    <?php
        APL_post_tax_content();
    ?>
</div>
<br/>
<div style="width: 640px; margin-bottom: 6px; height: 26px; margin-top: 3px;">
    <div style="float: left">
        <label for="txtNumberPosts">Show count:</label>
        <input type="text" size='5' name='txtNumberPosts' id='txtNumberPosts' value='5' />
    </div>
    <div style="float: right">
        <label for="cboOrderBy">Order by: </label>
        <select id="cboOrderby" style="width:110px;">
            <option value="date">Date</option>
            <option value="modified">Modified Date</option>
            <option value="title">Title</option>
            <option value="ID">ID</option>
            <option value="author">Author ID</option>
            <option value="parent">Parent</option>
            <option value="menu_order">Menu Order</option>
            <option value="rand">Random</option>
            <option value="comment_count">Comment Count</option>
        </select>

        <select id="cboOrder" style="width:110px;">
            <option value="DESC">Descending</option>
            <option value="ASC">Ascending</option>
        </select>
    </div>
    
    <div style="clear: both; float: left">
        <label for="cboPostStatus">Post Status:</label>
        <select id="cboPostStatus" style="width:110px;">
            <option value="publish">Publish</option>
            <option value="pending">Pending</option>
            <option value="draft">Draft</option>
            <option value="auto-draft">Auto-Draft</option>
            <option value="future">Future</option>
            <option value="private">Private</option>
            <option value="inherit">Inherit</option>
            <option value="trash">Trash</option>
            <option value="any">Any</option>
        </select>
        
    </div>
    <div style="float: right; margin-top: 5px">
        <!--<input type=checkbox id="chkIgnoreStickyPosts" name="chkIgnoreStickyPosts" checked="yes" />
        <label for="chkIgnoreStickyPosts">Ignore Sticky Posts</label>-->
        <input type=checkbox id="chkExcludeCurrent" name="chkExcludeCurrent" class="noneHide" checked="yes" />
        <label for="chkExcludeCurrent" class="noneHide" >Exclude Current Post</label>
    </div>


</div>
<div id="divPresetStyler" style="min-width: 640px; max-width: 1024px; margin-right: 25px; margin-top: 3px; margin-bottom: 3px;" >


    <div id="divBefore" class="noneHide" style="clear:both; white-space:nowrap;" >
        <label for="txtBeforeList" style="float:left; width: 96px;" >Before list:</label>
        <textarea name="txtBeforeList" id="txtBeforeList" style="float:inherit; min-width:544px; max-width: 874px; min-height:70px;"></textarea>
    </div>

    <div id="divContent" style="clear: both; white-space:nowrap;" >
        <label for="txtContent" style="float:left; width:96px;" >List content:</label>
        <textarea name='txtContent' id='txtContent' style="float: inherit; min-width:544px; max-width: 874px; min-height: 70px;" ></textarea>
    </div>

    <div id="divAfter" class="noneHide" style="clear: both; white-space:nowrap;" >
        <label for="txtAfterList" style="float:left; width:96px;" >After list:</label>
        <textarea name='txtAfterList' id='txtAfterList'  style="float: inherit; min-width:544px; max-width: 874px; min-height: 70px;" ></textarea>
    </div>
</div>
<div id="divSavePreset" style="min-width: 640px; max-width: 1024px; margin-right: 25px; height: 26px; margin-top: 3px; margin-bottom: 3px;">
    <label for="txtPresetName">Preset Name:</label>
    <input id="txtPresetName" type="text" size='30' name='txtPresetName' />
    <button id="btnSavePreset">Save Preset</button>
    &nbsp;&nbsp;|&nbsp;&nbsp;
    <input id="chkShowPreview" type=checkbox name="chkShowPreview" checked="checked" />
    <label for="chkShowPreview" >Show preview</label>
</div>
<!--<p><span id="createStatus">&nbsp;</span></p>-->
<div style="width:700px; padding:10px">
    <div id="divPreview">
        Preview will appear here when saved
    </div>  
</div>


<div id="presetListDiv" style="clear: both">
</div>


<div id="presetPHP">
    PHP code - click load on any preset to generate PHP code for use in your theme
</div>

<br />
<hr />
<br />
<div id="containerOptions">
    <div id="optionsHeader" align="center"><h3 style="margin: 0px;">General Options</h3></div>
    <div id="options1">
        <h4 style="margin-bottom: 1px;" >Settings</h4>
        <form id="frmSettings" name="frmSettings" enctype="text/plain" method="get" style="margin: 0px;" >
            <table width="316" border="0 " style="">
                <tr>
                    <td width="216" >Delete database upon deactivation.</td>
                    <td width="84"><input id="rdoDeleteDb" name="rdoDeleteDb" type="radio" value="true" 
                        <?php
                        $a1 = $adminOptions["delete_core_db"];
                        if ($adminOptions["delete_core_db"] == "true")
                        {
                            echo "checked";
                        }
                        ?>
                    /> Yes <input id="rdoDeleteDb" name="rdoDeleteDb" type="radio" value="false" <?php
                                          if ($adminOptions["delete_core_db"] == "false")
                                          {
                                              echo "checked";
                                          }
?> /> No 
                    </td>
                    
                    
                        
                </tr>
                <tr>
                    <td>Admin JQuery UI Theme</td>
                    <td>
                        <select id="slctUITheme" name="slctUITheme">
                            <option value="ui-lightness">UI Lightness</option>
                            <option value="ui-darkness">UI Darkness</option>
                            <option value="smoothness">Smoothness</option>
                            <option value="start">Start</option>
                            <option value="redmond">Redmond</option>
                            <option value="sunny">Sunny</option>
                            <option value="overcast">Overcast</option>
                            <option value="le-frog">Le Frog</option>
                            <option value="flick">Flick</option>
                            <option value="pepper-grinder">Pepper Grinder</option>
                            <option value="eggplant">Eggplant</option>
                            <option value="dark-hive">Dark Hive</option>
                            <option value="cupertino">Cupertino</option>
                            <option value="south-street">South Street</option>
                            <option value="blitzer">Blitzer</option>
                            <option value="humanity">Humanity</option>
                            <option value="hot-sneaks">Hot Sneaks</option>
                            <option value="excite-bike">Excite Bike</option>
                            <option value="vader">Vader</option>
                            <option value="dot-luv">Dot Luv</option>
                            <option value="mint-choc">Mint Choc</option>
                            <option value="black-tie">Black Tie</option>
                            <option value="trontastic">Trontastic</option>
                            <option value="swanky-purse">Swanky Purse</option>
                        </select>
                    </td>
                    
                </tr>
            </table>
            <div align="center" style="margin-top: 10px;">
                <input id="btnSaveSettings" name="btnSaveSettings" type="submit" value="Save" />

            </div>
        </form>
    </div>
    <div id="options2" >
        <h4 style="margin-bottom: 1px;" >Export Preset Table</h4>
        <form id="frmExport" name="frmExport" method="get" enctype="multipart/form-data" style="margin: 0px;">
            <input type="radio" style="visibility:hidden;" /> Filename: <input id="txtExportFileName" name="txtExportFileName" type="text" style="width: 250px;" value="APL.<?php echo date('Y-m-d-Hi'); ?>" />
            <br />
            <input type="radio" style="visibility:hidden;" /><input id="btnExport" name="btnExport" type="submit" value="Export" />
        </form>
        <h4 style="margin-top: 10px; margin-bottom: 1px;" >Import Data<em> - Beta Mode: <a href="http://advanced-post-list.wikiforum.net/t4-beta-import-data-release-info" title="Release Notes" target="_new">More details</a></em></h4>
        <form id="frmImport" name="frmImport" method="post" enctype="multipart/form-data" style="margin-top: 5px;">
            <input id="rdoImportType" type="radio" name="importType" value="file" checked /> Upload: <input id="fileImportDir" name="fileImportDir" type="file"  style="width: 225px;" />
            <br/>
            <input id="rdoImportType" type="radio" name="importType" value="kalin" /> Kalin's Post List Database
            <br/>
            <input type="radio" style="visibility:hidden;" /><input id="btnImport" name="btnImport" type="submit" value="Import" />

        </form>


        <?php /* ?>
         * ORIGINAL IMPORT FORM
          <form id="frmImport" name="fimport" action="<?php echo plugin_dir_url(__FILE__); ?>includes/import.php" method="post" enctype="multipart/form-data" target="uploadframe" style="margin-top: 5px;">

          <input id="rdoImportType" type="radio" name="importType" value="file" checked /><input name="importFileDir" type="file" id="importFileDir" style="width: 250px;" />

          <br/>
          <input id="rdoImportType" type="radio" name="importType" value="kalin" />Kalin's Post List Database

          <br/>
          <input type="radio" style="visibility:hidden;" /><input id="btnImport" name="btnImport" type="submit" value="Import" />

          <iframe id="uploadframe" name="uploadframe" src="<?php echo plugin_dir_url(__FILE__); ?>includes/import.php" width="8" height="8" scrolling="no" frameborder="0" ></iframe>
          </form><?php */ ?>

    </div>
    <div id="options3" align="center" >
        <h4 align="left" style="margin-bottom: 1px;">Restore Plugin Data</h4>
        <p style="margin: 0px;">Note: Restoring the plugin's default preset  table will only overwrite/add the initial set of presets, and will not delete other presets of a different name.
        </p>

        <button id="btnRestorePreset" style="margin: 10px;" >Restore Preset Defaults</button>

    </div>

</div>
<br style="clear:left" />
<hr />
<br />
<p>
    <b>Shortcodes:</b> Use these codes inside the list item content (will throw errors if placed in before or after HTML fields)<br />
</p>
<ul style="width: 720px">

    <li><b>[ID]</b> - the ID number of the page/post</li>
    <li><b>[post_author]</b> - author of the page/post</li>
    <li><b>[post_permalink]</b> - the page permalink</li>
    <li><b>[post_date format="m-d-Y"]</b> - date page/post was created <b>*</b></li>
    <li><b>[post_date_gmt format="m-d-Y"]</b> - date page/post was created in gmt time <b>*</b></li>
    <li><b>[post_title]</b> - page/post title</li>
    <li><b>[post_content]</b> - page/post content</li>
    <li><b>[post_excerpt length="250"]</b> - page/post excerpt (note the optional character 'length' parameter)</li>
    <li><b>[post_name]</b> - page/post slug name</li>
    <li><b>[post_modified format="m-d-Y"]</b> - date page/post was last modified <b>*</b></li>
    <li><b>[post_modified_gmt format="m-d-Y"]</b> - date page/post was last modified in gmt time <b>*</b></li>
    <li><b>[guid]</b> - original URL of the page/post (post_permalink is probably better)</li>
    <li><b>[comment_count]</b> - number of comments posted for this post/page</li>

    <li><b>[item_number offset="1" increment="1"]</b> - the list index for each page/post. Offset parameter sets start position. Increment sets the number you want to increase on each loop.</li>
    <li><b>[final_end]</b> - on the final list item, everything after this shortcode will be excluded. This will allow you to have commas (or anything else) after each item except the last one.</li>
    <li><b>[post_pdf]</b> - URL to the page/post's PDF file. (Requires Kalin's PDF Creation Station plugin. See help menu for more info.)</li>

    <li><b>[post_meta name="custom_field_name"]</b> - page/post custom field value. Correct 'name' parameter required</li>
    <li><b>[post_tags delimeter=", " links="true"]</b> - post tags list. Optional 'delimiter' parameter sets separator text. Use optional 'links' parameter to turn off links to tag pages</li>
    <li><b>[post_categories delimeter=", " links="true"]</b> - post categories list. Parameters work like tag shortcode.</li>
    <li><b>[post_parent link="true"]</b> - post parent. Use optional 'link' parameter to turn off link</li>
    <li><b>[post_comments before="" after=""]</b> - post comments. Parameters represent text/HTML that will be inserted before and after comment list but will not be displayed if there are no comments. PHP coders: <a href="http://kalinbooks.com/2011/customize-comments-pdf-creation-station">learn how to customize comment display (kalinbooks site).</a></li>
    <li><b>[post_thumb]</b> - URL to the page/post's featured image (requires theme support)</li>
    <li><b>[php_function name="function_name" param=""]</b> - call a user-defined custom function. Refer to <a href="http://kalinbooks.com/2011/custom-php-functions/">this blog post (kalinbooks site)</a> for instructions.</li>
</ul>
<p style="width: 720px"><b>*</b> Time shortcodes have an optional format parameter. Format your dates using these possible tokens: m=month, M=text month, F=full text month, d=day, D=short text Day Y=4 digit year, y=2 digit year, H=hour, i=minute, s=seconds. More tokens listed here: <a href="http://php.net/manual/en/function.date.php" target="_blank">http://php.net/manual/en/function.date.php.</a> </p>
<p>Note: these shortcodes only work in the List item content box on this page.</p>
<hr/>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----"><input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></form>

