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

function tjshortcodesubmit() {
	
	var tagtext;
	
	var tj_shortcode = document.getElementById('tjshortcode_panel');
	
	// who is active ?
	if (tj_shortcode.className.indexOf('current') != -1) {
		var tj_shortcodeid = document.getElementById('tjshortcode_tag').value;
		switch(tj_shortcodeid)
{
case 0:
 	tinyMCEPopup.close();
  break;

case "button":
	tagtext = "["+ tj_shortcodeid + "  url=\"#\" style=\"white\" size=\"small\"] Button text [/" + tj_shortcodeid + "]";
break;

case "alert":
	tagtext = "["+ tj_shortcodeid + " style=\"white\"] Alert text [/" + tj_shortcodeid + "]";
break;

case "toggle":
	tagtext = "["+ tj_shortcodeid + " title=\"Title goes here\"] Content here [/" + tj_shortcodeid + "]";
break;

case "tabs":
	tagtext="["+tj_shortcodeid + " tab1=\"Tab 1 Title\" tab2=\"Tab 2 Title\" tab3=\"Tab 3 Title\"] [tab]Insert tab 1 content here[/tab] [tab]Insert tab 2 content here[/tab] [tab]Insert tab 3 content here[/tab] [/" + tj_shortcodeid + "]";
break;

default:
tagtext="["+tj_shortcodeid + "] Insert you content here [/" + tj_shortcodeid + "]";
}
}

if(window.tinyMCE) {
		//TODO: For QTranslate we should use here 'qtrans_textarea_content' instead 'content'
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		//Peforms a clean up of the current editor HTML. 
		//tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches. 
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}