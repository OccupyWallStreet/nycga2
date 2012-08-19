<?php
////////////////////////////////////////////////////////////////////////////////
// APLCore Class uses this file in method APL_admin_head, if any other
//  method, function, or file uses this php file. Then it will exit this file.
//
////////////////////////////////////////////////////////////////////////////////

if (!method_exists('APLCore', 'APL_admin_head'))
{
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}
wp_enqueue_script('jquery-ui-dialog');

require_once APL_DIR . 'includes/APL-admin_dialogs.php';

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

    $rtnString .= '<h3 style=""><a href="#" style="">' . $post_type_object->labels->singular_name . '</a></h3>';
    $rtnString .= '<div style="padding: 1px;">';
    //GET HEADER
    $rtnString .= APL_post_tax_get_left_header($post_type_name);
    $rtnString .= APL_post_tax_get_right_header();
    $rtnString .= '<div style="clear:both; height: 328px;">';
    $rtnString .= APL_post_tax_get_taxonomy_section($post_type_name,
                                                    $post_type_object->taxonomies);
    $rtnString .= '</div>';

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

    $rtnString .= '<div style="float: left; padding: 1px;">';
    $rtnString .= '<select id="slctParentSelector-' . $post_type_name . '" name="parent_select-' . $post_type_name . '" multiple="multiple">';

    $page_settings = array('post_type_name' => $post_type_name);
    $rtnString .= APL_get_page_heirarchy($page_settings);

    $rtnString .= '</select>';
    $rtnString .= '</div>';

    return $rtnString;
}
function APL_post_tax_get_right_header()
{
    $rtnString = '';

    $rtnString .= '<div style="float: right">';
    $rtnString .= '<span id="info10" class="info10 ui-icon ui-icon-info info-icon"  style="float:right" ></span>';
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

    $htmlCatTabs .= '<div id="tabs-' . $post_type_name . '-cats" style="clear: both; float: left; width: 48.5%; height: 320px; max-height: 320px;">';
    $htmlTagTabs .= '<div id="tabs-' . $post_type_name . '-tags" style="float: left; width: 48.5%; height: 320px; max-height: 320px;">';

    $htmlCatTabs .= '<ul style="font-size: 80%;">';
    $htmlTagTabs .= '<ul style="font-size: 80%;">';

    //$post_type_object = get_post_type_object($post_type_name);
    foreach ($taxonomy_names as $taxonomy_name)
    {
        $taxonomy_object = get_taxonomy($taxonomy_name);

        if ($taxonomy_object->hierarchical == true)
        {

            //// TAB SECTION
            $htmlCatTabs .= '<li><a href="#tab-' . $post_type_name . '-' . $taxonomy_name . '">' . $taxonomy_object->labels->singular_name . '</a></li>';

            //// CONTENT SECTION
            $htmlCatTabsContent .= '<div id="tab-' . $post_type_name . '-' . $taxonomy_name . '" style="overflow:scroll; overflow-x:hidden; border:ridge;">';

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
            $htmlTagTabsContent .= '<div id="tab-' . $post_type_name . '-' . $taxonomy_name . '" style="overflow:scroll; overflow-x:hidden; border:ridge;">';
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
    
    if (!empty($terms))
    {
        $rtnString .= '<input type=checkbox id="chkTerm-' . $post_type_name . '-' . $taxonomy_name . '-' . 0 . '" name="chkTerm-' . $post_type_name . '-' . $taxonomy_name . '-' . 0 . '" />Any/All<br />';
        foreach ($terms as $value_tag)
        {
            $rtnString .= '<input type=checkbox id="chkTerm-' . $post_type_name . '-' . $taxonomy_name . '-' . $value_tag->term_id . '" name="chkTerm-' . $post_type_name . '-' . $taxonomy_name . '-' . $value_tag->term_id . '" />' . $value_tag->name . '<br />';
        }
    }
    

    return $rtnString;
}
//TODO Change to use APLCore::APL_get_posts() instead of get_posts()
function APL_get_page_heirarchy($page_settings = array('post_type_name' => 'post'),
                                $parent = 0,
                                $depth = -1)
{
    $rtnString = '';
    $dashes = '';
    if ($depth == -1)
    {
        $rtnString .= '<option id="slctChkCurrent-' . $page_settings['post_type_name'] . '" value="' . 0 . '"><b>Current Page</b></option>';
    }
    $depth++;
    for ($i = 0;
            $i < $depth;
            $i++)
    {
        $dashes .= '-';
    }

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
            $rtnString .= '<option value="' . $page->ID . '">' . $dashes . $page->post_title . '</option>';
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
    if ($depth == -1)
    {
        $rtnString .= '<input type=checkbox id="chkTerm-' . $post_tax_settings['post_type_name'] . '-' . $post_tax_settings['taxonomy_name'] . '-' . 0 . '" name="chkTerm-' . $post_tax_settings['post_type_name'] . '-' . $post_tax_settings['taxonomy_name'] . '-' . 0 . '" />Any/All<br />';
    }
    $depth++;
    for ($i = 0;
            $i < $depth;
            $i++)
    {
        $dashes .= '-';
    }
    $terms = get_categories($argTerms);

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
////////////////////////////////////////////////////////////////////////////////
// END OF PHP                                                                 //
////////////////////////////////////////////////////////////////////////////////
?>
<div style="max-width: 800px; min-width: 640px; margin-top: 0px">
    <div style="float: left;" >
        <h2>Advanced Post List - Settings</h2>
    </div>
    <div style="float: right; margin-right: 25px;" >
        <h3>Plugin Page (<a href="http://ekojr.com/advanced-post-list/" title="Author's site" target="_new">Plugin Page</a> / <a href="http://wordpress.org/extend/plugins/advanced-post-list/" title="Default Plugin Page" target="_new">Wordpress</a>)</h3>
    </div>
</div>
<br style="clear:left"/>
<hr/>
<br/>
<div id="divCustomPostTaxonomyContent" style="font-size: 100%; width: 640px">
    <?php
        APL_post_tax_content();
    ?>
</div>
<br/>
<div style="width: 640px; margin-bottom: 6px; margin-top: 3px;">
    <div style="float: right;">
        <a id="info11" style="font-size: 75%;">Filter Settings Info<span class="ui-icon ui-icon-info info-icon" style="float:right"></span></a>
    </div>
    <div style="clear: both; height: 26px; margin: 3px 0px;" >
        <div style="float: left;" >
            <label for="cboPostStatus">Post Status:</label>
            <select id="cboPostVisibility" multiple="multiple" style="width:128px;">
                <option value="public" selected="selected" >Public</option>
                <option value="private">Private</option>
            </select>
            <select id="cboPostStatus" multiple="multiple" style="width:128px;">
                <option value="publish" selected="selected" >Published</option>
                <option value="future">Future</option>
                <option value="pending">Pending Review</option>
                <option value="draft">Draft</option>
                <option value="auto-draft">Auto-Save</option>
                <option value="inherit">Inherit</option>
                <option value="trash">Trash</option>
            </select>
        </div>
        <div style="float: right;">
            <label for="txtDisplayAmount">List Amount:</label>
            <input type="text" size='5' id='txtDisplayAmount' value='5' />
        </div>
    </div>
    <div style="clear: both; height: 26px; margin: 3px 0px;" >
        <div style="float: left;" >
            <label for="slctAuthorOperator">Author Filter:</label>
            <select id="slctAuthorOperator" style="">
                <option value="none" selected="selected">-None-</option>
                <option value="include">From</option>
                <option value="exclude">Remove</option>
            </select>
            <select id="cboAuthorIDs" multiple="multiple">
                <?php
                //// ALL USERS ////
                //$author_roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');
                
                $author_roles = array('administrator', 'editor', 'author', 'contributor');
                foreach ($author_roles as $author_role)
                {
                    $author_args = array(
                        'orderby' => 'display_name',
                        'order' => 'DESC',
                        'role' => $author_role
                    );

                    $authors = get_users($author_args);
                    if (!empty($authors))
                    {
                        echo '<optgroup label="' . ucfirst($author_role) . '">';
                        foreach ($authors as $author)
                        {
                            echo '<option value="' . $author->ID . '" >' . $author->display_name . '</option>';
                        }
                        echo '</optgroup>';
                    }
                }
                ?>
            </select>
        </div>
        <div style="float: right;">
            <label for="slctOrderBy">Order by: </label>
            <select id="slctOrderBy" style="width:2px;">
                <option value="date">Date</option>
                <option value="modified">Modified Date</option>
                <option value="title">Title</option>
                <option value="ID">ID</option>
                <option value="author">Author ID</option>
                <option value="parent">Parent</option>
                <option value="menu_order">Menu Order</option>
                <option value="rand">Random</option>
                <option value="comment_count">Comments</option>
            </select>

            <select id="slctOrder" style="width:110px;">
                <option value="DESC">Descending</option>
                <option value="ASC">Ascending</option>
            </select>
        </div>
    </div>
    <div style="clear: both; height: 26px; margin: 3px 0px;" >
        <div style="float: left;" >
            <label for="slctUserPerm" >Perm:</label>
            <select id="slctUserPerm">
                <option value="readable" selected="selected">Readable</option>
                <option value="editable" >Editable</option>
            </select>
        </div>
        <div style="float: right;">
            <input type=checkbox id="chkIgnoreSticky" name="chkIgnoreSticky" />
            <label for="chkIgnoreSticky">Ignore Sticky Posts</label>
            <input type=checkbox id="chkExcldCurrent" name="chkExcldCurrent" checked="yes" />
            <label for="chkExcldCurrent" >Exclude Current Post</label>
        </div>
    </div>
    <div style="clear: both; height: 26px; margin: 3px 0px;" >
        <div style="float: left;" >
            <label for="txtExcldPosts">Exclude Posts by ID:</label>
            <input type="text" size='15' id='txtExcldPosts' value='' />
        </div>
        <div style="float: right;">
            <input type=checkbox id="chkExcldDuplicates" name="chkExcldDuplicates" />
            <label for="chkExcldDuplicates" >Exclude Duplicates from Current Post.</label>
        </div>
    </div>
   
</div>




<div id="divPresetStyler" style="min-width: 640px; max-width: 1024px; margin-right: 25px; margin-top: 3px; margin-bottom: 3px;" >
    <div style="float: left; width: 640px">
        <div style="float: right;">
            <a id="info12" style="font-size: 75%;">Style Content Info<span class="ui-icon ui-icon-info info-icon" style="float:right"></span></a>
        </div>
    </div>
    
    <div id="divExitMsg" style="clear:both; white-space:nowrap;" >
        <label for="txtExitMsg" style="float:left; width: 96px;">Exit Message:</label>
        <textarea name="txtExitMsg" id="txtExitMsg" style="float:inherit; min-width:544px; max-width: 874px; min-height:70px;"></textarea>
    </div>

    <div id="divBefore" class="noneHide" style="clear:both; white-space:nowrap;" >
        <label for="txtBeforeList" style="float:left; width: 96px;" >Before list:</label>
        <textarea name="txtBeforeList" id="txtBeforeList" style="float:inherit; min-width:544px; max-width: 874px; min-height:70px;"></textarea>
    </div>

    <div id="divContent" style="clear: both; white-space:nowrap;" >
        <div style="float:left; width: 96px;">
            <label for="txtContent" style="float:left; width:96px;" >List content:</label>
            <a id="info13" style="float:left; max-width: 96px; font-size: 75%;">Shortcodes<span class="ui-icon ui-icon-info info-icon" style="float:right"></span></a>
        </div>
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
    <label for="chkShowPreview" >Show preview</label>&nbsp;(Results may vary <a id="info14" >Details</a>)
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
    <div id="optionL" >
        <h4 style="margin-bottom: 1px;" >Settings</h4>
        <div class="optionL-row">
            <div style="float:left">
                Delete database upon deactivation.
            </div>
            <div style="float:right">
                <input type="radio" id="rdoDeleteDbTRUE" name="rdoDeleteDb" value="true" 
                    <?php
                    //$a1 = $adminOptions["delete_core_db"];
                    if ($adminOptions["delete_core_db"] === TRUE || !isset($adminOptions["delete_core_db"]))
                    {
                        echo "checked";
                    }
                    ?>
                /> Yes <input type="radio" id="rdoDeleteDbFALSE" name="rdoDeleteDb" value="false" 
                    <?php
                    if ($adminOptions["delete_core_db"] === FALSE)
                    {
                        echo "checked";
                    }
                    ?>
                /> No
            </div>
        </div>
        <div class="optionL-row">
            <div style="float:left">
                Admin JQuery UI Theme
            </div>
            <div style="float:right">
                <select id="slctUITheme" name="slctUITheme">
                    <?php
                    
                    $theme_array_values = array("ui-lightness","ui-darkness",
                        "smoothness","start","redmond","sunny","overcast",
                        "le-frog","flick","pepper-grinder","eggplant","dark-hive",
                        "cupertino","south-street","blitzer","humanity",
                        "hot-sneaks","excite-bike","vader","dot-luv","mint-choc",
                        "black-tie","trontastic","swanky-purse");
                    $theme_array_names = array('UI Lightness','UI Darkness',
                        'Smoothness','Start','Redmond','Sunny','Overcast',
                        'Le Frog','Flick','Pepper Grinder','Eggplant','Dark Hive',
                        'Cupertino','South Street','Blitzer','Humanity',
                        'Hot Sneaks','Excite Bike','Vader','Dot Luv','Mint Choc',
                        'Black Tie','Trontastic','Swanky Purse');
                    foreach ($theme_array_values as $index => $value)
                    {
                        if ($value === $adminOptions['jquery_ui_theme'])
                        {
                            echo '<option value="' . $value . '" selected="selected">' . $theme_array_names[$index] . '</option>';
                        }
                        else
                        {
                            echo '<option value="' . $value . '">' . $theme_array_names[$index] . '</option>';
                        }
                    }
                    
                    ?>
                    
                </select>
            </div>
        </div>
        <div class="optionL-row">
            <div style="float:left">
                Enable Default Exit Message:
            </div>
            <div style="float:right">
                <input type="radio" id="rdoDefaultExitMsgTRUE" name="rdoDefaultExitMsg" value="true" 
                    <?php
                    //$a1 = $adminOptions["delete_core_db"];
                    if ($adminOptions["default_exit"] === TRUE)
                    {
                        echo "checked";
                    }
                    ?>
                /> Yes <input type="radio" id="rdoDefaultExitMsgFALSE" name="rdoDefaultExitMsg" value="false" 
                    <?php
                    if ($adminOptions["default_exit"] === FALSE || !isset($adminOptions["default_exit"]))
                    {
                        echo "checked";
                    }
                    ?>
                /> No
            </div>
        </div>
        <div class="optionL-row" style="height: auto;">
            <textarea name="txtDefaultExitMsg" id="txtDefaultExitMsg" style="float:inherit; width: 100%; max-width: 100%; min-width: 100%; min-height:128px; max-height: 256px;"><?php echo $adminOptions["default_exit_msg"]; ?></textarea>
        </div>
        <div class="optionL-row">
            <button id="btnSaveSettings" style="margin: 10px;">Save Settings</button>
        </div>
        
        
    </div>
    <div id="optionR" >
        <div id="options1" >
            <h4 style="margin-bottom: 1px;" >Export Preset Table</h4>
            <form id="frmExport" name="frmExport" method="get" enctype="multipart/form-data" style="margin: 0px;">
                <input type="radio" style="visibility:hidden;" /> Filename: <input id="txtExportFileName" name="txtExportFileName" type="text" style="width: 250px;" value="APL.<?php echo date('Y-m-d-Hi'); ?>" />
                <br />
                <input type="radio" style="visibility:hidden;" /><input id="btnExport" name="btnExport" type="submit" value="Export" />
            </form>
            <h4 style="margin-top: 10px; margin-bottom: 1px;" >Import Data<em> - Beta Mode: <a href="http://ekojr.com/apl_news/back-up-feature-broken/" title="Release Notes" target="_new">More details</a></em></h4>
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
        <div id="options2" align="center" >
            <h4 align="left" style="margin-bottom: 1px;">Restore Plugin Data</h4>
            <p style="margin: 0px;">Note: Restoring the plugin's default preset  table will only overwrite/add the initial set of presets, and will not delete other presets of a different name.
            </p>

            <button id="btnRestorePreset" style="margin: 10px;" >Restore Preset Defaults</button>

        </div>
    </div>
</div>
<br style="clear: left;" />
<hr />
<br />
<div style="">
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
    

</div>



