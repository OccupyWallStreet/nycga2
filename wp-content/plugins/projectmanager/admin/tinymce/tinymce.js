function init() {
	tinyMCEPopup.resizeToInnerSize();
}


function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function ProjectManagerInsertLink() {
	
	var tagtext;
	
	var project = document.getElementById('project_panel');
	var dataset = document.getElementById('dataset_panel');
	var search = document.getElementById('search_panel');
	var datasetform = document.getElementById('datasetform_panel');
	
	// who is active ?
	if (project.className.indexOf('current') != -1) {
		var projectId = document.getElementById('projects').value;
		var template = getCheckedValue(document.getElementsByName('project_template'));
		var cat = document.getElementById('cat_id').value;
		var orderby = document.getElementById('orderby').value;
		var formfield_id = document.getElementById('formfield_id').value;
		var order = document.getElementById('order').value;
		
		if ( orderby != '' ) {
			if ( orderby == 'formfields' && formfield_id != '' )
				orderby = " orderby=" + orderby + "-" + formfield_id;
			else
				orderby = " orderby=" + orderby;
		}
		if ( order != '' ) {	
			order = " order=" + order;
		}
		
		if ( cat <= 0 )
			cat = '';
		else
			cat = " cat_id=" + cat;
	
		if (projectId != 0)
			tagtext = "[project id=" + projectId + " template=" + template + cat + orderby + order +"]";
		else
			tinyMCEPopup.close();
	}
	
	if (dataset.className.indexOf('current') != -1) {
		var datasetId = document.getElementById('datasets').value;
		if (datasetId != 0)
			tagtext = "[dataset id=" + datasetId + "]";
		else
			tinyMCEPopup.close();
	}
	
	if (search.className.indexOf('current') != -1) {
		var projectId = document.getElementById('search_projects').value;
		var template = getCheckedValue(document.getElementsByName('search_display'));
		
		if (projectId != 0)
			tagtext = "[project_search project_id=" + projectId + " template=" + template + "]";
		else
			tinyMCEPopup.close();
	}

	if (datasetform.className.indexOf('current') != -1) {
		var projectId = document.getElementById('datasetform_projects').value;
		
		if (projectId != 0)
			tagtext = "[dataset_form project_id=" + projectId + "]";
		else
			tinyMCEPopup.close();
	}

	if(window.tinyMCE) {
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		//Peforms a clean up of the current editor HTML. 
		//tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches. 
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}
