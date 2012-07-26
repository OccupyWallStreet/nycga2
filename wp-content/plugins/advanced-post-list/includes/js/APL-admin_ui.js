jQuery(document).ready(function($)
    {
        // postTax: is the Post Type and Taxonomy structure
        var post_types = apl_admin_ui_settings.post_types;
        // postTax_parent_selector: show which Post Types are heirarchical
        var postTax_parent_selector = apl_admin_ui_settings.postTax_parent_selector;
        
        for (var post_type_name in post_types)
        {
            //var taxonomies = postTax[post_type_name].taxonomies;
            
            $("#slctParentSelector-" + post_type_name).multiselect({
                noneSelectedText: "Select Parent",
                selectedText: "# of # pages selected",
                selectedList: 2,
                height: 192,
                click: function(event, ui)
                {
                    if (ui.value === "0")
                    {
                        for (var post_type in post_types)
                        {
                            var parentIDs =  $("#slctParentSelector-" + post_type).val();
                            if (parentIDs == null)
                            {
                                parentIDs = new Array();
                            }
                            var tmp_array = new Array();
                            var a3 = parentIDs.length;
                            if (ui.checked == true)
                            {
                                tmp_array = parentIDs;
                                tmp_array[a3] = "0";
                                $("#slctParentSelector-" + post_type).val(tmp_array);
                            }
                            else
                            {
                                var i = 0;
                                for (var parent_index in parentIDs)
                                {
                                    //TODO sloppy, FIX this
                                    if (parentIDs[parent_index] === "0")
                                    {
                                        //do nothing or skip
                                    }
                                    else
                                    {
                                        tmp_array[i] = parentIDs[parent_index];
                                        i++
                                    }
                                }
                                $("#slctParentSelector-" + post_type).val(tmp_array);
                            }
                            $("#slctParentSelector-" + post_type).multiselect('refresh');
                        }
                    }
                }
            });
//            $("#slctChkCurrent-" + post_type_name).click(function()
//            {
//                var checked = $(this).is(':checked');
//                for (var post_type in postTax)
//                {
//                    $("#slctChkCurrent-" + post_type).attr('checked', checked);
//                    $("#slctParentSelector-" + post_type).multiselect('refresh');
//                }
//                
//            });
            // Disable option if the post type is non-hierarchical (Pages)
            if (postTax_parent_selector[post_type_name]['hierarchical'] == false)
            {
                $("#slctParentSelector-" + post_type_name).multiselect("disable");
            }
            
            //$( "#chkReq-" + post_type_name ).button();
            //$( "#chkIncld-" + post_type_name ).button();
            
            $("#tabs-" + post_type_name + '-cats').tabs();
            $('#tabs-' + post_type_name + '-cats').children().each(function(index, domEle)
            {
                var div_size = $(this).parent().height();

                if (index != 0)
                {
                    var tab_size =  $(this).parent().children().first().height();
                    var change = div_size - tab_size - 32 + (2 * (tab_size/23));
                    $(domEle).height(change);
                }
            });
            $("#tabs-" + post_type_name + '-tags').tabs();
            $('#tabs-' + post_type_name + '-tags').children().each(function(index, domEle)
            {
                var div_size = $(this).parent().height();

                if (index != 0)
                {
                    var tab_size =  $(this).parent().children().first().height();
                    var change = div_size - tab_size - 32 + (2 * (tab_size/23));
                    $(domEle).height(change);
                }
            });

            
        }
        
        
        $("#divCustomPostTaxonomyContent").accordion();
        $("#btnSavePreset").button();
        $("#btnSaveSettings").button();
        $("#btnExport").button();
        $("#btnImport").button();
        $("#btnRestorePreset").button();

    });


