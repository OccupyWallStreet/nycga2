jQuery(document).ready(function($){
       
    if( 'undefined' == typeof( apl_admin_settings ) )
    {
        return;
    }
        
    var plugin_url = apl_admin_settings.plugin_url;
    
    var savePresetNonce = apl_admin_settings.savePresetNonce;
    var deletePresetNonce =  apl_admin_settings.deletePresetNonce;
    var restorePresetNonce = apl_admin_settings.restorePresetNonce;
    var exportNonce = apl_admin_settings.exportNonce;
    var importNonce = apl_admin_settings.importNonce;
    var saveSettingsNonce = apl_admin_settings.saveSettingsNonce;
    
    var presetObj = apl_admin_settings.presetDb;
    presetObj = JSON.parse(presetObj);
    
    var postTax = apl_admin_settings.postTax;
    //postTax = JSON.parse(postTax);
    
    var taxTerms = apl_admin_settings.taxTerms;
    
  
    
    
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
        for(var i in presetObj)
        {
            var shortcode = '[post_list name="' + i + '"]';
            tableHTML += "<tr>" + tc(count) + tc(i) + tc("<button name='btnLoad_" + count + "' id='btnLoad_" + count + "'>Load</button>") + tc("<button name = 'btnDownload_" + count + "' id = 'btnDownload_" + count + "'>Download</button>") + tc("<button name='btnDelete_" + count + "' id='btnDelete_" + count + "'>Delete</button>") + tc(shortcode) + "</tr>";
            count++;
        }
      
        tableHTML += "</table>";
      
        $('#presetListDiv').html(tableHTML);
      
        count = 0;
        for(j in presetObj)
        {
        
            $('#btnDelete_' + count).attr('presetname', j);
            $('#btnDelete_' + count).click(function()
            {
                if(confirm("Are you sure you want to delete " + $(this).attr('presetname') + "?"))
                {							
                    deletePreset($(this).attr('presetname'));
                }
            });
            $('#btnDelete_' + count).button();
        
            $('#btnDownload_' + count).attr('presetname', j);
            $('#btnDownload_' + count).click(function()
            {
                //FIX - REPLACE PHP CODE
                var name = $(this).attr('presetname');
                var url = plugin_url + "includes/export.php?presetname=" + name;
                //alert(url);
          
                window.location = url;
            });
            $('#btnDownload_' + count).button();
        
            $('#btnLoad_' + count).attr('presetname', j);
            $('#btnLoad_' + count).click(function()
            {				
                loadPreset($(this).attr('presetname'));
            });
            $('#btnLoad_' + count).button();
        
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
        
            presetObj = newFileData;//.preset_arr;
        
            buildPresetTable();
        
            $('#createStatus').html("Preset deleted successfully.");
        
        });
    }
    function downloadPreset(id)
    {
      
    }
    function loadPreset(id)
    {
      
        var newValues = presetObj[id];
      
        set_postTax(newValues._postTax);
        set_parent(newValues._postParent);
        
        $('#txtNumberPosts').val(newValues["_listAmount"]);
        
        
        $('#cboOrderBy option[value=' + newValues["_listOrderBy"] + ']').attr('selected','selected');
        $('#cboOrder option[value=' + newValues["_listOrder"] + ']').attr('selected','selected');
        
        $('#cboPostStatus option[value=' + newValues["_postStatus"] + ']').attr('selected','selected');
        
        $('#chkExcludeCurrent').attr('checked', newValues["_postExcludeCurrent"]);
        
        $('#txtBeforeList').val(newValues["_before"]);
        $('#txtContent').val(newValues["_content"]);
        $('#txtAfterList').val(newValues["_after"]);
      
      
        $('#txtPresetName').val(id);
      
        $('#divPreview').html("");
      
      
        setPHPOutput(id);
      
        setNoneHide();
    }
    
    function set_postTax(post_tax)
    {
        
        reset_postTax();
        for (var post_type_name in post_tax)
        {
            
            for (var taxonomy_name in post_tax[post_type_name]['taxonomies'])
            {
                $("#chkReqTaxonomy-" + post_type_name + "-" + taxonomy_name).attr('checked', post_tax[post_type_name]['taxonomies'][taxonomy_name]['require_taxonomy']);
                $("#chkReqTerms-" + post_type_name + "-" + taxonomy_name).attr('checked', post_tax[post_type_name]['taxonomies'][taxonomy_name]['require_terms']);
                $("#chkIncldTerms-" + post_type_name + "-" + taxonomy_name).attr('checked', post_tax[post_type_name]['taxonomies'][taxonomy_name]['include_terms']);
                
                for (var term in post_tax[post_type_name]['taxonomies'][taxonomy_name]['terms'])
                {
                    $("#chkTerm-" + post_type_name + "-" + taxonomy_name + '-' + post_tax[post_type_name]['taxonomies'][taxonomy_name]['terms'][term]).attr('checked',true)
                }
                
            }
        }
    }
    function reset_postTax()
    {
        
        for (var post_type_name in postTax)
        {
            for (var taxonomy_name in postTax[post_type_name].taxonomies)
            {
                $("#chkReqTaxonomy-" + post_type_name + "-" + taxonomy_name).attr('checked', false);
                $("#chkReqTerms-" + post_type_name + "-" + taxonomy_name).attr('checked', false);
                $("#chkIncldTerms-" + post_type_name + "-" + taxonomy_name).attr('checked', false);
                
//                var a1 = taxTerms[taxonomy_name];
//                var a2 = taxTerms[taxonomy_name]['terms'];
                var terms = taxTerms[taxonomy_name].terms;
                
//                for (var i = 0; i < taxTerms[taxonomy_name].terms; i++)
//                {
//                    $("#chkTerm-" + post_type_name + "-" + taxonomy_name + '-' + term).attr('checked',false)
//                }
                for (var term in terms)
                {
                    
                    $("#chkTerm-" + post_type_name + "-" + taxonomy_name + '-' + terms[term]).attr('checked',false)
                    
                }
                
            }
            
        }
        
    }
    function set_parent(parentArr)
    {
        reset_parent();
        //parentArr.toArray();
        parentArr = jQuery.makeArray(parentArr); 
        
        for (var post_type_name in postTax)
        {
            
            $("#slctParentSelector-" + post_type_name).val(parentArr);
            $("#slctParentSelector-" + post_type_name).multiselect('refresh');
            //$("#slctParentSelector-" + post_type_name).each(object, callback);
            
        }
        
    }
    function reset_parent()
    {
        
        for (var post_type_name in postTax)
        {
            $("#slctParentSelector-" + post_type_name).val([]);
            $("#slctParentSelector-" + post_type_name).multiselect('refresh');
            
        }
        
    }
    
    
    
    ////////////////////////////////////////////////////////////////////////////
    //// AJAX BUTTONS //////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    $('#btnSavePreset').click(function()
    {
        if (!save_preset_precheck())
        {
            save_preset();
        }
    });
    //// RETURN TRUE IF AN EVENT OCCURED
    function save_preset_precheck()
    {
        if (!check_required())
        {
            
            if (!check_preset_name())
            {
                return false;
            }
            return true;
        }
        return true;
        
    }
    function check_required()
    {
        //var event_occured = false;
        for (var post_type_name in postTax)
        {
            var require_taxonomy = new Array();
            for (var taxonomy_name in postTax[post_type_name].taxonomies)
            {
                require_taxonomy[taxonomy_name] = new Array();
                require_taxonomy[taxonomy_name]['require'] = $("#chkReqTaxonomy-" + post_type_name + "-" + taxonomy_name).is(':checked');
                var require_terms = $("#chkReqTerms-" + post_type_name + "-" + taxonomy_name).is(':checked');
                var include_terms = $("#chkIncldTerms-" + post_type_name + "-" + taxonomy_name).is(':checked');
                require_taxonomy[taxonomy_name]['count'] = 0;
                
                var terms = taxTerms[taxonomy_name].terms;
                for (var term in terms)
                {
                    if ($("#chkTerm-" + post_type_name + "-" + taxonomy_name + '-' + terms[term]).is(':checked'))
                    {
                        require_taxonomy[taxonomy_name]['count']++;
                    }
                }


                if (require_taxonomy[taxonomy_name]['count'] < 2 && require_terms == true && include_terms == false)
                {
                    $( "#d03" ).dialog({
                        resizable: false,
                        height:192,
                        modal: true,
                        buttons: {
                            "Ok": function() 
                            {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                    return true;
                }
                else if (require_taxonomy[taxonomy_name]['count'] < 2 && require_taxonomy[taxonomy_name]['require'] == true && include_terms == false)
                {
                    $( "#d04" ).dialog({
                        resizable: false,
                        height:224,
                        modal: true,
                        buttons: {
                            "Ok": function() 
                            {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                    return true;
                }

            }
            
            for(var taxonomy01 in require_taxonomy)
            {
                if (require_taxonomy[taxonomy01]['require'] == true)
                {
                    var other_taxonomy_used = false;
                    for(var taxonomy02 in require_taxonomy)
                    {
                        if (require_taxonomy[taxonomy02]['count'] > 0 && taxonomy01 != taxonomy02)
                        {
                            other_taxonomy_used = true;
                        }
                    }
                    if (other_taxonomy_used == false)
                    {

                        $( "#d05" ).dialog(
                        {
                            resizable: false,
                            height:224,
                            modal: true,
                            buttons: 
                            {
                                "Ok": function() 
                                {
                                    $( this ).dialog( "close" );
                                }
                            }
                        });
                        return true;
                                
                    }
                }
                
            }

        }
        return false;
    }
    function check_preset_name()
    {
        var preset_name = $("#txtPresetName").val();
        if(presetObj[preset_name])
        {				   
            $( "#d01" ).dialog({
                resizable: false,
                height:192,
                modal: true,
                buttons: {
                    "Save Preset": function() 
                    {
                        $( this ).dialog( "close" );
                        save_preset();
                    },
                    "Cancel": function() 
                    {
                        $( this ).dialog( "close" );
                        
                    }
                }
            });
            return true;
        }
        else if(preset_name == "")
        {
            $( "#d02" ).dialog({
                resizable: false,
                height:192,
                modal: true,
                buttons: {
                    "Ok": function() 
                    {
                        $( this ).dialog( "close" );
                        
                    }
                }
            });
            return true;
        }
        return false;
        
    }
    
    function save_preset()
    {
        var data = {
            action: 'APL_handler_save_preset',
            _ajax_nonce : savePresetNonce
        }
        //css style bug fix
        var btn_height = $('#btnSavePreset').height() + 2;
        var btn_width = $('#btnSavePreset').width() + 2;
        $('#btnSavePreset').html("Saving...");
        $('#btnSavePreset').height(btn_height);
        $('#btnSavePreset').width(btn_width);
        
        data.presetName = $("#txtPresetName").val();
        
        
        data.postParent = JSON.stringify(get_parent());
        data.postTax = JSON.stringify(get_postTax());
        
        data.numberPosts = $("#txtNumberPosts").val();
        data.orderBy = $("#cboOrderby").val();
        data.order = $("#cboOrder").val();
        
        data.postStatus = $("#cboPostStatus").val();
        //data.ignoreStickyPosts = $("#chkIgnoreStickyPosts").is(':checked');
        data.excludeCurrent = $("#chkExcludeCurrent").is(':checked');
        
        data.before = $("#txtBeforeList").val();
        data.content = $("#txtContent").val();
        data.after = $("#txtAfterList").val();
        
        
        setPHPOutput(data.presetName);
      
        
      
        //since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) 
        {

            //alert(response);

            var startPosition = response.indexOf("{");
            var responseObjString = response.substr(startPosition, response.lastIndexOf("}") - startPosition + 1);

            //alert(responseObjString);

            var newFileData = JSON.parse(responseObjString);

            presetObj = newFileData.preset_arr;
            buildPresetTable();
            $('#btnSavePreset').html("Save Preset");

            if($("#chkShowPreview").is(':checked'))
            {
                $('#divPreview').html(newFileData.previewOutput);
            }
            else
            {
                $('#divPreview').html("");
            }


        });
    }
    
    function get_postTax()
    {
        var rtnObject = new Object();
        var tmp_post_types = new Object();
        for (var post_type_name in postTax)
        {
            var tmp_taxonomies = new Object();
            var post_type_used = false;
            for (var taxonomy_name in postTax[post_type_name].taxonomies)
            {
                var tmp_terms = new Array();
                var i = 0;
                var terms = taxTerms[taxonomy_name].terms;
                for (var term in terms)
                {
                    if ($("#chkTerm-" + post_type_name + "-" + taxonomy_name + '-' + terms[term]).is(':checked'))
                    {
                        tmp_terms[i] = terms[term];
                        i++;
                    }
                }
                if (i > 0 || $("#chkIncldTerms-" + post_type_name + "-" + taxonomy_name).is(':checked'))
                {
                    tmp_taxonomies[taxonomy_name] = new Object();
                    tmp_taxonomies[taxonomy_name].require_taxonomy = $("#chkReqTaxonomy-" + post_type_name + "-" + taxonomy_name).is(':checked');
                    tmp_taxonomies[taxonomy_name].require_terms = $("#chkReqTerms-" + post_type_name + "-" + taxonomy_name).is(':checked');
                    tmp_taxonomies[taxonomy_name].include_terms = $("#chkIncldTerms-" + post_type_name + "-" + taxonomy_name).is(':checked');
                    tmp_taxonomies[taxonomy_name].terms = tmp_terms;

                    post_type_used = true;
                }
            }
            if (post_type_used)
            {
                tmp_post_types[post_type_name] = new Object();
                
                tmp_post_types[post_type_name].taxonomies = tmp_taxonomies;
                
            }
            
        }
        rtnObject = tmp_post_types;
        return rtnObject;
    }
    function get_parent()
    {
        
        var parentIDs = new Array();
        var rtnArray = new Array();
        var unique = function(origArr) 
        {  
            var newArr = [],  
            origLen = origArr.length,  
            found,  
            x, y;  
  
            for ( x = 0; x < origLen; x++ ) 
            {  
                found = undefined;  
                for ( y = 0; y < newArr.length; y++ ) 
                {  
                    if ( origArr[x] === newArr[y] ) 
                    {  
                        found = true;  
                        break;  
                    }  
                }  
                if ( !found) newArr.push( origArr[x] );  
            }  
            return newArr;  
        };
        
        var i = 0;
        for (var post_type_name in postTax)
        {
            parentIDs =  $("#slctParentSelector-" + post_type_name).val();
            
            if (parentIDs !== null)
            {
                for (var j = 0; j < parentIDs.length; j++, i++)
                {
                    rtnArray[i] = parentIDs[j];
                }
            }
        }
        //TODO make array unique
        rtnArray = unique(rtnArray);
        return rtnArray;
    }
    
    
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
        save_settings();
        ////Cancels default action of form after applying jQuery.get/post
        // 
        return false;
    });
    function save_settings()
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
        data.theme = $('#slctUITheme').val();
        
        jQuery.post(ajaxurl, data, function(response)
        {
            var startPosition = response.indexOf("{");
            var responseObjString = response.substr(startPosition, response.lastIndexOf("}") - startPosition + 1);
            var newFileData = JSON.parse(responseObjString);
            
            
            optionsHeader('Options Saved');
            loadjscssfile('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/themes/' + newFileData.theme + '/jquery-ui.css', 'css');
        });
        
    }
    function loadjscssfile(filename, filetype)
    {
        //if filename is a external JavaScript file
        if (filetype === "js")
        { 
            var fileref=document.createElement('script');
            fileref.setAttribute("type","text/javascript");
            fileref.setAttribute("src", filename);
        }
        //if filename is an external CSS file
        else if (filetype === "css")
        { 
            var fileref=document.createElement("link");
            fileref.setAttribute("rel", "stylesheet");
            fileref.setAttribute("type", "text/css");
            fileref.setAttribute("href", filename);
        }
        if (typeof fileref !== "undefined")
        {
            document.getElementsByTagName("head")[0].appendChild(fileref);
        }
            
    }
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
        return true;
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
            var dataR = JSON.parse(dataRtn.substring(dataRtn.indexOf("{"), dataRtn.lastIndexOf("}") + 1));
        
            //CHECK SERVER SIDE
            //ERROR CHECKING
            if (dataR._error != '')
            {
                //Display error to user
                alert(dataR._error);
                $('#btnExport').val('Export');
                return false;
            }
            //FINAL
            else
            {
          
                //SETUP AN IFRAME TO DOWNLOAD DATA THROUGH export.php
                //TODO FIX - NEED TO REMOVE PHP CODE. INSERT IT INTO THE AJAX HANDLER IN CORE
                //var url = "<?php echo APL_URL; ?>includes/export.php?_ajax_nonce=" + data._ajax_nonce + "&action =" + data.action + "&filename=" + dataR.filename;
                var url = dataR.export_url + "?_ajax_nonce=" + data._ajax_nonce + "&action =" + data.action + "&filename=" + dataR.filename;
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
        var url = plugin_url + "includes/import.php?wp_nonce=" + wp_nonce;
        elemIF.src = url;
      
        document.frmImport.target = "uploadTarget";
        document.frmImport.action = plugin_url + "includes/import.php?wp_nonce=" + wp_nonce;
      
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
            $('#divPreview').html("");
		
            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function(response) 
            {
          
                var startPosition = response.indexOf("{");
                var responseObjString = response.substr(startPosition, response.lastIndexOf("}") - startPosition + 1);
          
                //alert(responseObjString);
                var newFileData = JSON.parse(responseObjString);
          
                presetObj = newFileData;//.preset_arr;
                buildPresetTable();
                //$('#createStatus').html("Preset successfully added.");
                optionsHeader('Restoring Default Presets Successful');
          
            });
        }
    });
    
    
    $('#postTypeHeader01').click(function(){
        $('#postTypeContent01').slideToggle('slow')
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
  





