
tinyMCEPopup.requireLangPack();
var wptabsDialog = {
	init : function() {
		var f = document.forms[0];
		
		f.wpdraggable.value = tinyMCEPopup.editor.selection.getContent({format: checkbox});
		
	},
	
	insert : function() {
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, document.forms[0].wpdraggable.value);
		tinyMCEPopup.close();
		
	}
	
	
}

tinyMCEPopup.onInit.add(wptabsDialog.init, wptabsDialog);
