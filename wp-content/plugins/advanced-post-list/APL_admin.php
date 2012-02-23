<?php
if (!function_exists('add_action'))
{
  echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
  exit;
}
//TODO fit these varibles in javascript
$save_preset_nonce = wp_create_nonce('APL_handler_save_preset');
$delete_preset_nonce = wp_create_nonce('APL_handler_delete_preset');
$restore_preset_nonce = wp_create_nonce('APL_handler_restore_preset');
$export_nonce = wp_create_nonce('APL_handler_export');
$import_nonce = wp_create_nonce('APL_import');


$catList = get_categories('hide_empty=0');
$tagList = get_tags('hide_empty=0');

//TODO set as default to override future settings
$presetDbObj = new APLPresetDbObj('default');
$presetDb = $presetDbObj->_preset_db;
//$atmp = json_encode((array) $presetDb);
//echo $atmp;
$adminOptions = $this->APL_options_load();
/*var presetArr = '<?php echo json_encode($presetDb); ?>';
 *var presetArr = '<?php echo $presetDb; ?>'; 
 */


?>

<link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url(__FILE__); ?>APL_admin.css" />
<link rel="stylesheet" type="text/css" href="APL_admin.css" />


<script language="javascript" type='text/javascript'>
  
  jQuery(document).ready(function($)
  {
    
    var savePresetNonce = '<?php echo $save_preset_nonce; ?>';
    var deletePresetNonce = '<?php echo $delete_preset_nonce; ?>';
    var restorePresetNonce = '<?php echo $restore_preset_nonce; ?>';
    var exportNonce = '<?php echo $export_nonce; ?>';
    var importNonce = '<?php echo $import_nonce; ?>';
    var saveSettingsNonce  = '<?php echo wp_create_nonce('APL_handler_save_settings'); ?>';
    
    var catList = <?php echo json_encode($catList); ?>;
    var tagList = <?php echo json_encode($tagList); ?>;
	
    
    
    var presetArr = <?php echo json_encode((array) $presetDbObj->_preset_db); ?>;
    function getCatString()
    {
      var catString = '';
      var pageCount = 0;
      var l = catList.length;
      for(var i=0; i<l; i++)
      {
        if($('#chkCat' + catList[i]['term_id']).is(':checked'))
				{
          catString += catList[i]['term_id'] + ",";
          pageCount++;
        }
      }
      return catString;
    }
    
    function getTagString()
    {
      var tagString = '';
      var pageCount = 0;
      var l = tagList.length;
      for(var i=0; i<l; i++)
      {
        if($('#chkTag' + tagList[i]['slug']).is(':checked'))
				{
          tagString += tagList[i]['slug'] + ",";
          pageCount++;
        }
      }
      return tagString;
    }
    
    function setCatValues(str)
    {
      
      var l = catList.length;
      for(var i=0; i<l; i++)
      {
        $('#chkCat' + catList[i]['term_id']).attr('checked', false);
      }
      
      var arrCats = str.split(",");
      var l = arrCats.length;
      for(var i=0; i<l; i++)
      {
        $('#chkCat' + arrCats[i]).attr('checked', true);
      }
    }
    
    function setTagValues(str)
    {
      var l = tagList.length;		   
      for(var i=0; i<l; i++)
      {
        $('#chkTag' + tagList[i]['slug']).attr('checked', false);
      }
      
      var arrCats = str.split(",");
      var l = arrCats.length;
      for(var i=0; i<l; i++)
      {
        $('#chkTag' + arrCats[i]).attr('checked', true);
      }
    }
    
    function setPHPOutput(preset_name)
    {
      //$('#presetPHP').html('PHP code: <code>if(function_exists("kalinsPost_show"){kalinsPost_show("' + data.preset_name + '");}</code>');
      
      //$('#presetPHP').html('PHP code: <code><<b>?php</b> if(function_exists("APL_display")){APL_display("' + preset_name + '");} <b>?</b>></code>');
      $('#presetPHP').html('PHP code: <code><<b>?php</b> if (method_exists($advanced_post_list, "APL_display")){echo $advanced_post_list->APL_display("' + preset_name + '");} <b>?</b>></code>');
    }
    //build the file table - we build it all in javascript so we can 
    // simply rebuild it whenever an entry is added through ajax
    function buildPresetTable()
    {
      
      function tc(str)
      {
        return "<td style='border:solid 1px' align='center'>" + str + "</td>";
      }
      
      var tableHTML = "<table style='border:solid 1px' width='725' border='1' cellspacing='1' cellpadding='3'><tr><th scope='col'>#</th><th scope='col'>Preset Name</th><th scope='col'>Load</th><th scope='col'>Download</th><th scope='col'>Delete</th><th scope='col'>Shortcode</th></tr>";
      
      var count = 0;
      for(i in presetArr)
      {
        var shortcode = '[post_list name="' + i + '"]';
        tableHTML += "<tr>" + tc(count) + tc(i) + tc("<button name='btnLoad_" + count + "' id='btnLoad_" + count + "'>Load</button>") + tc("<button name = 'btnDownload_" + count + "' id = 'btnDownload_" + count + "'>Download</button>") + tc("<button name='btnDelete_" + count + "' id='btnDelete_" + count + "'>Delete</button>") + tc(shortcode) + "</tr>";
        count++;
      }
      
      tableHTML += "</table>";
      
      $('#presetListDiv').html(tableHTML);
      
      count = 0;
      for(j in presetArr)
      {
        
        $('#btnDelete_' + count).attr('presetname', j);
        
        $('#btnDelete_' + count).click(function()
				{
          if(confirm("Are you sure you want to delete " + $(this).attr('presetname') + "?"))
					{							
            deletePreset($(this).attr('presetname'));
          }
        });
        
        $('#btnDownload_' + count).attr('presetname', j);
        
        $('#btnDownload_' + count).click(function()
				{
          
          var name = $(this).attr('presetname');
          var url = "<?php echo APL_URL; ?>includes/export.php?presetname=" + name;
          //alert(url);
          
          window.location = url;
        });
        
        $('#btnLoad_' + count).attr('presetname', j);
        
        $('#btnLoad_' + count).click(function()
				{				
          loadPreset($(this).attr('presetname'));
        });
        
        count++;
      }	
    }
    ////////////////////////////////////////////////////////////////////////////
    //// AJAX FUNCTIONS ////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    function deletePreset(id)
    {
      //alert("deleting: " + id);
      
      var data = { 
        action: 'APL_handler_delete_preset',
        _ajax_nonce : deletePresetNonce
      }
      
      data.preset_name = id;
      
      $('#createStatus').html("Deleting preset...");
      
      // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
      jQuery.post(ajaxurl, data, function(response) 
      {
        //alert(response);
        
        var startPosition = response.indexOf("{");
        var responseObjString = response.substr(startPosition, response.lastIndexOf("}") - startPosition + 1);
        
        var newFileData = JSON.parse(responseObjString);
        
        /*if(newFileData.status == "success"){
                    $('#createStatus').html("Preset deleted successfully.");
                }else{
                    $('#createStatus').html(response);
                }*/
        
        presetArr = newFileData;//.preset_arr;
        
        buildPresetTable();
        
        $('#createStatus').html("Preset deleted successfully.");
        
      });
    }
    function downloadPreset(id)
    {
      
    }
    function loadPreset(id)
    {
      
      var newValues = presetArr[id];
      
      setCatValues(newValues["_catsSelected"]);
      setTagValues(newValues["_tagsSelected"]);
      
      $('#txtNumberposts').val(newValues["_listAmount"]);
      $('#txtBeforeList').val(newValues["_before"]);
      $('#txtContent').val(newValues["_content"]);
      $('#txtAfterList').val(newValues["_after"]);
      
      $('#cboPost_type option[value=' + newValues["_postType"] + ']').attr('selected','selected'); 
      $('#cboOrderby option[value=' + newValues["_listOrderBy"] + ']').attr('selected','selected');
      $('#cboOrder option[value=' + newValues["_listOrder"] + ']').attr('selected','selected');
      
      $('#cboParent option[value=' + newValues["_postParent"] + ']').attr('selected','selected');
      
      if(newValues["_postExcludeCurrent"] == 'true')
			{//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
        $('#chkExcludeCurrent').attr('checked', true);
      }
			else
			{
        $('#chkExcludeCurrent').attr('checked', false);
      }
      
      if(newValues["_catsInclude"] == 'true'
			){//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
        $('#chkIncludeCats').attr('checked', true);
      }
			else
			{
        $('#chkIncludeCats').attr('checked', false);
      }
      
      if(newValues["_tagsInclude"] == 'true'
			){//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
        $('#chkIncludeTags').attr('checked', true);
      }
			else
			{
        $('#chkIncludeTags').attr('checked', false);
      }
      
      if(newValues["_catsRequired"] == 'true')
			{//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
        $('#chkRequireAllCats').attr('checked', true);
      }
			else
			{
        $('#chkRequireAllCats').attr('checked', false);
      }
      
      if(newValues["_tagsRequired"] == 'true')
			{//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
        $('#chkRequireAllTags').attr('checked', true);
      }
			else
			{
        $('#chkRequireAllTags').attr('checked', false);
      }
      
      $('#txtPresetName').val(id);
      
      $('#previewDiv').html("");
      
      //$('#presetPHP').html('PHP code: <code>kalinsPost_show("' + id + '");</code>');
      
      setPHPOutput(id);
      
      setNoneHide();
    }
    
    ////////////////////////////////////////////////////////////////////////////
    //// AJAX BUTTONS //////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    
    $('#btnSavePreset').click(function()
    {
      var data = { action: 'APL_handler_save_preset',
        _ajax_nonce : savePresetNonce
      }
      
      data.preset_name = $("#txtPresetName").val();
      
      if(data.preset_name == "")
	  {
        $('#createStatus').html("Error: Please type a name for your preset, or press 'load' on any of the presets below to edit.");
        return;
      }
      
      if(presetArr[data.preset_name])
      {				   
        if(!confirm("Are you sure you want to overwrite the preset " + data.preset_name))
		{
          $('#createStatus').html("<br/>");
          return;
        }
      }
      
      data.categories = getCatString();
      data.tags = getTagString();
      
      data.requireAllCats = $("#chkRequireAllCats").is(':checked');
      data.requireAllTags = $("#chkRequireAllTags").is(':checked');
      
      data.includeCats = $("#chkIncludeCats").is(':checked');
      data.includeTags = $("#chkIncludeTags").is(':checked');
      
      if(data.requireAllCats && data.categories.split(",").length < 3 && !data.includeCats)
			{//check if there's only one or zeor cats selected (plus the empty one at the end)
        $('#createStatus').html("Error: If you select 'Require all selected categories' you must also select at least two categories.");
        alert("Error: If you select 'Require all selected categories' you must also select at least two categories.");
        return;
      }
      
      if(data.requireAllTags && data.tags.split(",").length < 3 && !data.includeTags)
			{//check if there's only one or zeor cats selected (plus the empty one at the end)
        $('#createStatus').html("Error: If you select 'Require all selected tags' you must also select at least two tags.");
        alert("Error: If you select 'Require all selected tags' you must also select at least two tags.");
        return;
      }
      
      data.post_type = $("#cboPost_type").val();
      
      data.numberposts = $("#txtNumberposts").val();
      data.before = $("#txtBeforeList").val();
      data.content = $("#txtContent").val();
      data.after = $("#txtAfterList").val();
      
      data.orderby = $("#cboOrderby").val();
      data.order = $("#cboOrder").val();
      
      data.excludeCurrent = $("#chkExcludeCurrent").is(':checked');
      
      
      
      //data.doCleanup = $("#chkDoCleanup").is(':checked');
      
      if($('#parentSelector').css('display')=='none')
      {
        data.post_parent = "None";
      }
      else
      {
        data.post_parent = $("#cboParent").val();
      }
      
      //$('#presetPHP').html('PHP code: <code>kalinsPost_show("' + data.preset_name + '");</code>');
      
      setPHPOutput(data.preset_name);
      
      $('#createStatus').html("Saving to preset...");
      
      // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
      jQuery.post(ajaxurl, data, function(response) 
			{
        
        //alert(response);
        
        var startPosition = response.indexOf("{");
        var responseObjString = response.substr(startPosition, response.lastIndexOf("}") - startPosition + 1);
        
        //alert(responseObjString);
        
        var newFileData = JSON.parse(responseObjString);
        
        presetArr = newFileData.preset_arr;
        buildPresetTable();
        $('#createStatus').html("Preset successfully saved.");
        
        if($("#chkShowPreview").is(':checked'))
				{
          $('#previewDiv').html(newFileData.previewOutput);
        }
				else
				{
          $('#previewDiv').html("");
        }
        
        //$('#previewDiv').html("Hello floopy doop");
        
      });
    });
    
    function optionsHeader(output)
    {
        $('#optionsHeader').html('<b>' + output + '</b>');
        $('#optionsHeader').fadeOut(5000, function(){
            $('#optionsHeader').show();
            $('#optionsHeader').html('<h3 style="margin: 0px;">General Settings</h3>');
        });
        
    }
    
    $('#frmSettings').submit(function()
    {
      var data = {
        action : 'APL_handler_save_settings',
        _ajax_nonce : saveSettingsNonce
      }
      
      //Delete Database Yes/No
      var rdoDeleteDb = document.frmSettings.rdoDeleteDb;
      for (var i = 0; i < rdoDeleteDb.length; i++)
      {
        if (rdoDeleteDb[i].checked)
        {
          data.deleteDb = rdoDeleteDb[i].value;
        }
      }
      
      
      jQuery.post(ajaxurl, data, function(dataRtn)
      {
        var a1 = dataRtn;
        optionsHeader('Options Saved');
      });
      //Cancels default action of form after applying jQuery.get/post
      // 
      return false;
    });
    $('#txtExportFileName').change(function()
    {
      //$in = $(this);
      //var a1 = $in.next().html($in.val());
      
      var iChars = "<>:\"/\\|?*";
      
      for (var i = 0; i < document.frmExport.txtExportFileName.value.length; i++) 
      {
        if (iChars.indexOf(document.frmExport.txtExportFileName.value.charAt(i)) != -1) 
        {
          alert ("Cannot use illegal characters (< > : \" / \\ | ? *).\nPlease rename your filename. ");
          return false;
        }
        
      }
    });
    
    $('#frmExport').submit(function(){
      var data = { 
        action : 'APL_handler_export',
        _ajax_nonce : exportNonce
      };
      
      //CHECK USER SIDE
      data.filename = $.trim($('#txtExportFileName').val());
      $('#btnExport').val('Please wait...');
      
      //Check for illegal filename characters
      // -Note: by default, any illegal character is replaced by another
      var iChars = "<>:\"/\\|?*";
      for (var i = 0; i < document.frmExport.txtExportFileName.value.length; i++) 
      {
        if (iChars.indexOf(document.frmExport.txtExportFileName.value.charAt(i)) != -1) 
        {
          alert ("Cannot use illegal characters (< > : \" / \\ | ? *).\nPlease rename your filename. ");
          $('#btnExport').val('Export');
          return false;
        }
        
      }
      if (data.filename == "")
      {
        alert("A filename doesn't exist.\nPlease enter a filename before exporting.");
        $('#btnExport').val('Export');
        return false;
      }
      
      jQuery.get(ajaxurl, data, function(dataRtn)
      {
        //CONVERT RETURN/RESPONSE DATA (dataRtn)
        //SYNTAX .substr(start, length)
        //SYNTAX .substring(start, end)
        dataRtn = $.trim(dataRtn);
        var dataRtn = JSON.parse(dataRtn.substring(dataRtn.indexOf("{"), dataRtn.lastIndexOf("}") + 1));
        
        //CHECK SERVER SIDE
        //ERROR CHECKING
        if (dataRtn._error != '')
        {
          //Display error to user
          alert(dataRtn._error);
          $('#btnExport').val('Export');
          return false;
        }
        //FINAL
        else
        {
          
          //SETUP AN IFRAME TO DOWNLOAD DATA THROUGH export.php
          var url = "<?php echo APL_URL; ?>includes/export.php?_ajax_nonce=" + data._ajax_nonce + "&action =" + data.action + "&filename=" + dataRtn.filename;
          var elemIF = document.createElement("iframe");
          elemIF.src = url;
          elemIF.style.display = "none";
          document.body.appendChild(elemIF);
          $('#btnExport').val('Export');
          optionsHeader('Exporting Data Successful');
        }
      });
      //Cancels default action of form after applying jQuery.get/post
      // 
      return false;
    });
    
    
//    $('#frmImport').submit(function(e)
//    {
//      var data = {
//        action : 'APL_handler_import',
//        _ajax_nonce : importNonce,
//        //test : e
//      };
//      
//      //var a1 = $('#fileImportDir').val();
//      //var a3 = document.frmImport.fileImportDir;
//      
//      var a4 = $('#frmImport').serialize();
//      
//      //data.a1 = a1;
//      //data.a2 = a2;
//      //data.a3 = a3;
//      data.a4 = a4;
//      
//      alert(a4);
//      
//      jQuery.post(ajaxurl, data, function(dataRtn)
//      {
//        //CONVERT RETURN/RESPONSE DATA (dataRtn)
//        //SYNTAX .substr(start, length)
//        //SYNTAX .substring(start, end)
//        dataRtn = $.trim(dataRtn);
//        var dataRtn = JSON.parse(dataRtn.substring(dataRtn.indexOf("{"), dataRtn.lastIndexOf("}") + 1));
//        
//        //CHECK SERVER SIDE
//        //ERROR CHECKING
//        if (dataRtn._error != '')
//        {
//          //Display error to user
//          alert(dataRtn._error);
//          $('#btnImport').val('Import');
//          return false;
//        }
//        //FINAL
//        else
//        {
//          
//          //SETUP AN IFRAME TO DOWNLOAD DATA THROUGH export.php
//          var url = "<?php// echo APL_URL; ?>includes/import.php?_ajax_nonce=" + data._ajax_nonce + "&action =" + data.action + "&filename=" + dataRtn.filename;
//          var elemIF = document.createElement("iframe");
//          elemIF.src = url;
//          elemIF.style.display = "none";
//          document.body.appendChild(elemIF);
//          $('#btnImport').val('Import');
//        }
//      });
//    return false;
//      
//    });

    $('#frmImport').submit(function()
    {
      var wp_nonce = importNonce;
      
      //Get the (radio) Import Type
      var rdoImport = document.frmImport.importType;
      for (var i = 0; i < rdoImport.length; i++)
      {
        if (rdoImport[i].checked)
        {
          var importType = rdoImport[i].value;
        }
      }
      
      
      //Temp IFrame
      var elemIF = document.createElement("iframe");
      elemIF.name = "uploadTarget";
      elemIF.style.display = "none";
      var url = "<?php echo APL_URL; ?>includes/import.php?wp_nonce=" + wp_nonce;
      elemIF.src = url;
      
      document.frmImport.target = "uploadTarget";
      document.frmImport.action = "<?php echo plugin_dir_url(__FILE__); ?>includes/import.php?wp_nonce=" + wp_nonce;
      
      document.frmImport.appendChild(elemIF);
      
      //$('#btnImport').val('Import');
      var fileDir = $('#fileImportDir').val();
      if ($('#fileImportDir').val() == '' && importType == 'file')
      {
          return false;
      }
      if ($('#fileImportDir').val() != '')
      {
        var ext = $('#fileImportDir').val().split('.').pop().toLowerCase();
        if($.inArray(ext, ['json']) == -1) 
        {
            alert('Invalid file type. Please choose a JSON file to upload.');
            return false;
        }
      }
      optionsHeader('Importing Data Successful');
      switch (importType)
      {
        
        case 'file':
          return true;
          break;
        case 'kalin':
          return true;
          break;
        default:
          alert('There is no import type selected!');
          return false;
        
      }
    });
    $('#btnRestorePreset').click(function()
		{
      //alert(data.post_type);
      var data = { 
        action: 'APL_handler_restore_preset',
        _ajax_nonce : restorePresetNonce
      }
      
      if(confirm("Are you sure you want to restore all default presets? This will remove any changes you've made to the default presets, but will not delete your custom presets."))
      {
        
        //$('#createStatus').html("Restoring presets...");
        $('#previewDiv').html("");
		
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) 
		{
          
          var startPosition = response.indexOf("{");
          var responseObjString = response.substr(startPosition, response.lastIndexOf("}") - startPosition + 1);
          
          //alert(responseObjString);
          var newFileData = JSON.parse(responseObjString);
          
          presetArr = newFileData;//.preset_arr;
          buildPresetTable();
          //$('#createStatus').html("Preset successfully added.");
          optionsHeader('Restoring Default Presets Successful');
          
        });
      }
    });
    
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////// 
    $('#cboPost_type').change(function() 
		{
      setNoneHide();
    });
    
    function setNoneHide(){
      
      var postTypeVal = $("#cboPost_type").val() ;
      
      if(postTypeVal == "none")
			{
        $('.noneHide').hide();
        $('.noneShow').show();
        $('#createStatus').html("In 'None' mode, the content field will be displayed only once and all shortcodes will refer to the current page.");
      }
			else
			{
        $('.noneHide').show();
        $('.noneShow').hide();
        $('#createStatus').html("&nbsp;");
      }
      
      if(postTypeVal != "none" && postTypeVal != "post" && postTypeVal != "attachment")
			{//if it's a page or custom type, show parent selector
        $('#parentSelector').show();
      }
			else
			{
        $('#parentSelector').hide();
      }
    }
    
    buildPresetTable();
    
    //$('#outputSpan').hide();
    $('#parentSelector').hide();
    
  });
  
</script>


<div style="max-width: 1024px; min-width: 750px; margin-top: 23px">
  <div style="float: left;" >
    <h2>Advanced Post List - Settings</h2>
  </div>
  <div style="float: right; margin-right: 25px;" >
    <h3>Plugin Page (<a href="http://advanced-post-list.wikiforum.net/" title="Community Support Forum" target="_new">Community</a> / <a href="http://code.google.com/p/wordpress-advanced-post-list/" title="Code Google Developer's Page" target="_new">Developement</a> / <a href="http://kalinbooks.com/post-list-wordpress-plugin/" title="Default Plugin Page" target="_new">Wordpress</a>)</h3>
  </div>
</div>


<br style="clear:left"/>
<hr/>
<br/>

<p>Post type:
  <select id="cboPost_type" name="cboPost_type" style="width:100px;">

    
    <?php
    $post_types = get_post_types('', 'names');
    //loop to create each option value. this will grab post, page, 
    // attachment and any custom post types
    foreach ($post_types as $post_type)
    {
      if ($post_type != "revision" && $post_type != "nav_menu_item")
      {
        echo "<option value='$post_type'>" . $post_type . "</option>";
      }
    }
    ?>
    <option value="any">any</option>
    <option value="none">none</option>
    
  </select>

  <span class="noneHide">
    &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;

Show count: <input type="text" size='5' name='txtNumberposts' id='txtNumberposts' value='5' />

    &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
<?php 
//Order by: <select id="cboOrderby" style="width:110px;">
//      <option value="post_date">post date</option>
//      <option value="author">author ID</option>
//      <option value="ID">post ID</option>
//      <option value="menu_order">menu_order</option>
//      <option value="modified">modified date</option>
//      <option value="parent">parent</option>
//      <option value="rand">random</option>
//      <option value="title">title</option>
//    </select>
//    Order by: <select id="cboOrderby" style="width:110px;">
//      <option value="post_date">post date</option>
//      <option value="author">author ID</option>
//      <option value="category">category ID</option>
//      <option value="date">date</option>
//      <option value="ID">post ID</option>
//      <option value="menu_order">menu_order</option>
//      <option value="mime_type">mime_type</option>
//      <option value="modified">modified date</option>
//      <option value="post_name">name</option>
//      <option value="parent">parent</option>
//      <option value="rand">random</option>
//      <option value="status">status</option>
//      <option value="title">title</option>
//      <option value="type">type</option>
//    </select>
?>
   
    Order by: <select id="cboOrderby" style="width:110px;">
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

</p>
<div id="parentSelector"><p>
    Parent: <select id="cboParent" style="width:110px;">

      <option value="">-Any or None-</option>
      <option value="current">-Current Page-</option>

      <?php
      //$posts = get_posts('numberposts=-1&post_type=any&orderby=title');      
      //FIX Checking for children is being misread as attachments being 'page' children.
      //Just display the pages without checking for children. Since some pages 
      // may not have any children yet.
      
      $pages = get_posts('numberposts=-1&post_type=page&orderby=title');
      foreach ($pages as $page)
      {
        echo "<option value='" . $page->ID . "'>" . $page->post_title . "</option>";
      }
//      foreach ($pages as $page)
//      {
//        //if this returns some children, show the option
//        if (get_children($page->ID))
//        {
//          echo "<option value='" . $page->ID . "'>" . $page->post_title . "</option>";
//        }
//      }
      ?>

    </select>


  </p></div>

<div style="overflow:scroll; overflow-x:hidden; height:150px; width:239px; float:left; border:ridge; margin-right:20px; padding-left:10px;" class="noneHide">
  <h3 align="center">Categories</h3>

  <input type=checkbox id="chkIncludeCats" name="chkIncludeCats" ></ input> Include current post categories <br />
  <input type=checkbox id="chkRequireAllCats" name="chkRequireAllCats" ></ input> Require all selected categories
  <hr />

  <?php
  $l = count($catList);
  for ($i = 0; $i < $l; $i++)
  {//build our list of cats
    $pageID = $catList[$i]->term_id;
    echo('<input type=checkbox id="chkCat' . $pageID . '" name="chkCat' . $pageID . '" ></ input> ' . $catList[$i]->name . '<br />');
  }
  ?>

</div>

<div style="overflow:scroll; overflow-x:hidden; height:150px; width:239px; border:ridge; padding-left:10px; float:left; margin-right:20px"  class="noneHide">
  <h3 align='center'>Tags</h3>
  <input type=checkbox id="chkIncludeTags" name="chkIncludeTags" ></ input> Include current post tags <br />
  <input type=checkbox id="chkRequireAllTags" name="chkRequireAllTags" ></ input> Require all selected tags
  <hr />
  <?php
  $l = count($tagList);
  for ($i = 0; $i < $l; $i++)
  {//build our list of cats
    $pageID = $tagList[$i]->slug; //so retarded that categories run off IDs and tags run off slugs
    echo('<input type=checkbox id="chkTag' . $pageID . '" name="chkTag' . $pageID . '" ></ input> ' . $tagList[$i]->name . '<br />');
  }
  ?>
</div>

<div style="overflow:auto; height:150px; width:160px; border:ridge; padding-left:10px;" class="noneHide">

  <p>Nothing selected means show everything, including tags or categories not yet created. Checking all will include everything, but will exclude all future categories or tags.</p>

</div>

  <br/><br/>
<div id="containerPresetStyler" style="min-width: 720px; max-width: 1024px; margin-right: 25px;" >


<div id="before" class="noneHide" style="clear:both; white-space:nowrap;" >
<label for="txtBeforeList" style="float:left; width: 125px;" >Before list HTML:</label>
<textarea name="txtBeforeList" id="txtBeforeList" style="float:none; min-width:570px; max-width: 874px; min-height:70px;"></textarea>
</div>

<div id="content" style="clear: both; white-space:nowrap;" >
<label for="txtContent" style="float:left; width:125px;" >List item content:</label>
<textarea name='txtContent' id='txtContent' style="float: none; min-width:570px; max-width: 874px; min-height: 70px;" ></textarea>
</div>

<div id="after" class="noneHide" style="clear: both; white-space:nowrap;" >
<label for="txtAfterList" style="float:left; width:125px;" >After list HTML:</label>
<textarea name='txtAfterList' id='txtAfterList'  style="float: none; min-width:570px; max-width: 874px; min-height: 70px;" ></textarea>
</div>
</div>

  <input type=checkbox id="chkExcludeCurrent" name="chkExcludeCurrent" class="noneHide" checked="yes"></input><label for="chkExcludeCurrent" class="noneHide" >Exclude current post from results</label>
<p>
  <button id="btnSavePreset">Save to Preset</button>&nbsp;&nbsp;:&nbsp;&nbsp;<input type="text" size='30' name='txtPresetName' id='txtPresetName' >&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox id="chkShowPreview" name="chkShowPreview" checked="yes"><label for="chkShowPreview" >show preview</label>
</p>

<p><span id="createStatus">&nbsp;</span></p>


<div style="width:700px; padding:10px">
  <div id="previewDiv">
    Preview will appear here when saved
  </div>  
</div>





<div id="presetListDiv">
</div>

<div id="presetListDiv" onload="parent.download" ></div>


<div id="presetPHP">
  PHP code - click load on any preset to generate PHP code for use in your theme
</div>

<br/>
<hr/>
<div id="containerOptions">
<div id="optionsHeader" align="center"><h3 style="margin: 0px;">General Options</h3></div>
<div id="options1">
<h4 style="margin-bottom: 1px;" >Settings</h4>
	<form id="frmSettings" name="frmSettings" enctype="text/plain" method="get" style="margin: 0px;" >
  	<table width="316" border="0 " style="">
  <tr>
    <td width="84"><input id="rdoDeleteDb" name="rdoDeleteDb" type="radio" value="true" <?php
  if ($adminOptions["delete_core_db"] == "true")
  {
    echo "checked";
  }
  ?> /> Yes <input id="rdoDeleteDb" name="rdoDeleteDb" type="radio" value="false" <?php
  if ($adminOptions["delete_core_db"] == "false")
  {
    echo "checked";
  }
  ?> /> No 
  </td>
    <td width="216" >Delete database upon deactivation.</td>
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


<?php /*?>
 * ORIGINAL IMPORT FORM
<form id="frmImport" name="fimport" action="<?php echo plugin_dir_url(__FILE__); ?>includes/import.php" method="post" enctype="multipart/form-data" target="uploadframe" style="margin-top: 5px;">
  
  <input id="rdoImportType" type="radio" name="importType" value="file" checked /><input name="importFileDir" type="file" id="importFileDir" style="width: 250px;" />
  
  <br/>
  <input id="rdoImportType" type="radio" name="importType" value="kalin" />Kalin's Post List Database
  
  <br/>
  <input type="radio" style="visibility:hidden;" /><input id="btnImport" name="btnImport" type="submit" value="Import" />
  
  <iframe id="uploadframe" name="uploadframe" src="<?php echo plugin_dir_url(__FILE__); ?>includes/import.php" width="8" height="8" scrolling="no" frameborder="0" ></iframe>
</form><?php */?>

</div>
<div id="options3" align="center" >
<h4 align="left" style="margin-bottom: 1px;">Restore Plugin Data</h4>
<p style="margin: 0px;">Note: Restoring the plugin's default preset  table will only overwrite/add the initial set of presets, and will not delete other presets of a different name.
</p>

  <button id="btnRestorePreset" style="margin: 10px;" >Restore Preset Defaults</button>

</div>

</div>
<br style="clear:left" />
<br />

<hr/><br/>
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

