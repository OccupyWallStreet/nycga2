ProjectManager.isLoading = function(id) {
	document.getElementById(id).style.display = 'inline';
	document.getElementById(id).innerHTML="<img src='"+ProjectManagerAjaxL10n.pluginUrl+"/admin/icons/loading.gif' />";
}
ProjectManager.doneLoading = function(id) {
	document.getElementById(id).style.display = 'none';
}


ProjectManager.saveProjectLink = function(formfield_id) {
	projectId = document.getElementById("form_field_project_" + formfield_id).value;
	var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
	ajax.execute = 1;
	ajax.method = 'POST';
	ajax.setVar( "action", "projectmanager_save_project_link" );
	ajax.setVar( "project_id", projectId );
	ajax.setVar( "formfield_id", formfield_id );
	ajax.onError = function() { alert('Ajax error on saving dataset order'); };
	ajax.onCompletion = function() { return true; };
	ajax.runAJAX();

	tb_remove();
}

ProjectManager.getCategoryDropdown = function (projectId, formfield_id) {
	var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
	ajax.execute = 1;
	ajax.method = 'POST';
	ajax.setVar( "action", "projectmanager_get_cat_dropdown" );
	ajax.setVar( "project_id", projectId  );
	ajax.setVar( "formfield_id", formfield_id );
	ajax.onError = function() { alert('Ajax error on saving dataset order'); };
	ajax.onCompletion = function() { return true; };
	ajax.runAJAX();
}

ProjectManager.addWPUser = function() {
	var user_id = document.getElementById('wp_user_id').value;
	var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
	ajax.execute = 1;
	ajax.method = 'POST';
	ajax.setVar( "action", "projectmanager_insert_wp_user" );
	ajax.setVar( "wp_user_id", user_id  );
	ajax.onError = function() { alert('Ajax error on saving dataset order'); };
	ajax.onCompletion = function() { return true; };
	ajax.runAJAX();

	tb_remove();
}


ProjectManager.AJAXdeleteFile = function(file, dataset_id, formfield_id, formfield_type) {		
	var check = window.confirm(ProjectManagerAjaxL10n.delFile + " " + ProjectManager.basename(file) + '?');
	
	if ( check == true ) {
		var delfile = document.getElementById("delfile" + formfield_id + "_" + dataset_id);
		var fileimage = document.getElementById("fileimage" + formfield_id + "_" + dataset_id);
		delfile.style.display = "none";
		fileimage.style.display = "none";
		
		var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
		ajax.execute = 1;
		ajax.method = 'POST';
		ajax.setVar( "action", "projectmanager_ajax_delete_file" );
		ajax.setVar( "file", file );
		ajax.onError = function() { alert('Ajax error on saving dataset order'); };
		ajax.onCompletion = function() { return true; };
		ajax.runAJAX();
		ProjectManager.dataFieldSpanFadeOut(dataset_id, formfield_id, '', formfield_type);
	}
}

ProjectManager.saveOrder = function(order) {
	var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
	ajax.execute = 1;
	ajax.method = 'POST';
	ajax.setVar( "action", "projectmanager_save_dataset_order" );
	ajax.setVar( "order", order );
	ajax.onError = function() { alert('Ajax error on saving dataset order'); };
	ajax.onCompletion = function() { return true; };
	ajax.runAJAX();
}

ProjectManager.ajaxSaveDatasetName = function( dataset_id ) {
	tb_remove();
	ProjectManager.isLoading('loading_name_' + dataset_id);
	var dataset_name = document.getElementById('dataset_name' + dataset_id).value;
	dataset_name = ProjectManager.addslashes(dataset_name);
	window.setTimeout("ProjectManager.datasetnameSpanFadeOut(" + dataset_id + ",'" + dataset_name + "')", 50);
}

ProjectManager.datasetnameSpanFadeOut = function( dataset_id, dataset_name ) {
	//dataset_name = ProjectManager.addslashes(dataset_name);
	jQuery("span#dataset_name_text" + dataset_id).fadeIn('fast', function() {
		var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
		ajax.execute = 1;
		ajax.method = 'POST';
		ajax.setVar( "action", "projectmanager_save_name" );
		ajax.setVar( "dataset_id", dataset_id );
		ajax.setVar( "new_name", dataset_name );
		ajax.onError = function() { alert('Ajax error on saving group'); };
		ajax.onCompletion = function() { ProjectManager.reInit(); };
		ajax.runAJAX();
	});
	//jQuery("span#dataset_name" + dataset_id).html( loading );
	return true;
}

ProjectManager.ajaxSaveCategories = function( dataset_id ) {
	tb_remove();
	ProjectManager.isLoading('loading_category_' + dataset_id);
	var n = jQuery("#categorychecklist" + dataset_id + " input:checked").length;
	//var cats = '';
	var cats = new Array();
	for(var a=0;a<n;a++){
		cats += jQuery("#categorychecklist" + dataset_id + " input:checked")[a].value + ",";
	}
	window.setTimeout("ProjectManager.categorySpanFadeOut(" + dataset_id + ",'" + cats + "')", 50);
}
ProjectManager.categorySpanFadeOut = function( dataset_id, cats ) {
	jQuery("span#dataset_category_text" + dataset_id).fadeIn('fast', function() {
		var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
		ajax.execute = 1;
		ajax.method = 'POST';
		ajax.setVar( "action", "projectmanager_save_categories" );
		ajax.setVar( "dataset_id", dataset_id );
		ajax.setVar( "cat_ids", cats );
		ajax.onError = function() { alert('Ajax error on saving group'); };
		ajax.onCompletion = function() { ProjectManager.reInit(); };
		ajax.runAJAX();
	});
	//jQuery("span#dataset_group" + dataset_id).html( loading );
	return true;
}

ProjectManager.ajaxSaveDataField = function( dataset_id, formfield_id, formfield_type ) {
	tb_remove();
	ProjectManager.isLoading('loading_' + formfield_id + '_' + dataset_id);

	if ( formfield_type == 'date' ) {
		var day = document.getElementById('form_field_' + formfield_id + '_' + dataset_id + '_day').value;
		var month = document.getElementById('form_field_' + formfield_id + '_' + dataset_id + '_month').value;
		var year = document.getElementById('form_field_' + formfield_id + '_' + dataset_id + '_year').value;
		var newvalue = year+"-"+month+"-"+day;
	} else if ( formfield_type == 'time' ) {
		var hour = document.getElementById('form_field_' + formfield_id + '_' + dataset_id + '_hour').value;
		var minute = document.getElementById('form_field_' + formfield_id + '_' + dataset_id + '_minute').value;
		var newvalue = hour+":"+minute;
	} else if ( formfield_type == 'checkbox' || 'project' == formfield_type ) {
		var values = ProjectManager.getSelectedCheckboxValue(document.getElementsByName("form_field_"+formfield_id+"_"+dataset_id));
		var newvalue = '';
		for(var a=0;a<values.length;a++){
			newvalue += values[a] + ",";
		}
	} else if ( formfield_type == 'radio' ) {
		var newvalue = ProjectManager.getSelectedRadioValue(document.getElementsByName("form_field_"+formfield_id+"_"+dataset_id));
	} else {
		var newvalue = document.getElementById('form_field_' + formfield_id + '_' + dataset_id).value.split('\n').join('\\n');
	}
	newvalue = ProjectManager.addslashes(newvalue);
	window.setTimeout("ProjectManager.dataFieldSpanFadeOut(" +  dataset_id  +  ","  + formfield_id + ",'" + newvalue + "','" + formfield_type + "')", 50);
}
ProjectManager.dataFieldSpanFadeOut = function( dataset_id, formfield_id, newvalue, formfield_type ) {
	jQuery("span#datafield" + formfield_id + "_" + dataset_id).fadeIn('fast', function() {
		var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
		ajax.execute = 1;
		ajax.method = 'POST';
		ajax.setVar( "action", "projectmanager_save_form_field_data" );
		ajax.setVar( "dataset_id", dataset_id );
		ajax.setVar( "formfield_id", formfield_id );
		ajax.setVar( "formfield_type", formfield_type );
		ajax.setVar( "new_value", newvalue );
		ajax.onError = function() { alert('Ajax error on saving group'); };
		ajax.onCompletion = function() { ProjectManager.reInit(); };
		ajax.runAJAX();
});
	//jQuery("span#datafield" + formfield_id + "_" + dataset_id).html( loading );
	return true;
}

ProjectManager.ajaxSaveFormFieldOptions = function ( form_id ) {
	tb_remove();
	jQuery("a#options_link" + form_id).fadeIn('fast', function() {
		form_field_options = document.getElementsByName('form_field_option_' + form_id);
		var form_field_options_values = '';
		for ( var i = 0; i < form_field_options.length; i++ ) {
			form_field_options_values += form_field_options[i].value + "|";
		}

		var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
		ajax.execute = 1;
		ajax.method = 'POST';
		ajax.setVar( "action", "projectmanager_save_form_field_options" );
		ajax.setVar( "form_id", form_id );
		ajax.setVar( "options", form_field_options_values );
		ajax.onError = function() { alert('Ajax error on saving group'); };
		ajax.onCompletion = function() { ProjectManager.reInit(); };
		ajax.runAJAX();
	});
	return true;
}


ProjectManager.reInit = function () {
	tb_init('a.thickbox, area.thickbox, input.thickbox');
}
