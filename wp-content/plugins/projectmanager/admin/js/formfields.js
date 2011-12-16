ProjectManager.addFormField = function( projectId ) {
  time = new Date();
  new_element_number = time.getTime();
  new_element_id = "form_id_"+new_element_number;
  
  new_element_contents = "";
  new_element_contents = "<td>&#160;</td>";
  new_element_contents += "<td><input type='text' name='new_formfields["+new_element_number+"][name]' value='' /></td>\n\r";
  new_element_contents += "<td id='form_field_options_box"+new_element_number+"'><select onChange='ProjectManager.toggleOptions(" + projectId + "," + new_element_number + ", this.value, \"\");' name='new_formfields["+new_element_number+"][type]' size='1'>"+PRJCTMNGR_HTML_FORM_FIELD_TYPES+"</select><span id='loading_formfield_options_"+new_element_number+"'></span></td>\n\r";
  new_element_contents += "<td><input type='checkbox' name='new_formfields["+new_element_number+"][show_on_startpage]' value='1' /></td>\n\r";
  new_element_contents += "<td><input type='checkbox' name='new_formfields["+new_element_number+"][show_in_profile]' value='1' checked='checked' /></td>\n\r";
  new_element_contents += "<td><input type='text' size='2' name='new_formfields["+new_element_number+"][order]' value='' /></td>\n\r";
  new_element_contents += "<td><input type='checkbox' name='new_formfields["+new_element_number+"][orderby]' value='1' /></td>\n\r";
  new_element_contents += "<td  style='text-align: center; width: 12px; vertical-align: middle;'><a class='image_link' href='#' onclick='return ProjectManager.removeFormField(\""+new_element_id+"\");'><img src='../wp-content/plugins/projectmanager/admin/icons/trash.gif' alt='" + ProjectManagerAjaxL10n.Delete + "' title='" + ProjectManagerAjaxL10n.Delete + "' /></a></td>\n\r";
  
  new_element = document.createElement('tr');
  new_element.id = new_element_id;
   
  document.getElementById("projectmanager_form_fields").appendChild(new_element);
  document.getElementById(new_element_id).innerHTML = new_element_contents;
  return false;
}

ProjectManager.removeFormField = function(id) {
  element_count = document.getElementById("projectmanager_form_fields").childNodes.length;
  if(element_count > 1) {
    target_element = document.getElementById(id);
    document.getElementById("projectmanager_form_fields").removeChild(target_element);
  }
  return false;
}

ProjectManager.addFormFieldOption = function(form_field_id) {
  time = new Date();
  new_element_number = time.getTime();
  new_element_id = "form_field_option_"+new_element_number;
  
  new_element_contents = "<input type='text' name='form_field_option_" + form_field_id + "' size='30' />";
  new_element_contents += "<a class='image_link' href='#' onClick='return ProjectManager.removeFormFieldOption(\"" + new_element_id + "\", " + form_field_id + ");'><img src='../wp-content/plugins/projectmanager/admin/icons/trash.gif' alt='Delete' title='Delete Option' /></a>";

  new_element = document.createElement('li');
  new_element.id = new_element_id;
   
  document.getElementById("form_field_options_" + form_field_id).appendChild(new_element);
  document.getElementById(new_element_id).innerHTML = new_element_contents;
  return true;
}

ProjectManager.removeFormFieldOption = function(id, form_field_id) {
  element_count = document.getElementById("form_field_options_" + form_field_id).childNodes.length;
  if(element_count > 1) {
    target_element = document.getElementById(id);
    document.getElementById("form_field_options_" + form_field_id).removeChild(target_element);
  }
  return false;
}

ProjectManager.toggleOptions = function(projectId, form_id, type, options) {
	ProjectManager.isLoading('loading_formfield_options_' + form_id);

	var ajax = new sack(ProjectManagerAjaxL10n.requestUrl);
	ajax.execute = 1;
	ajax.method = 'POST';
	ajax.setVar( "action", "projectmanager_toggle_formfield_options" );
	ajax.setVar( "project_id", projectId );
	ajax.setVar( "formfield_id", form_id );
	ajax.setVar( "formfield_type", type );
	ajax.setVar( "options", options );
	ajax.onError = function() { alert('Ajax error on saving dataset order'); };
	ajax.onCompletion = function() { ProjectManager.doneLoading('loading_formfield_options_'+ form_id); return true; };
	ajax.runAJAX();
}

