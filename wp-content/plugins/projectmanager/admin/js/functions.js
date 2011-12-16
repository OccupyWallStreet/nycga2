var ProjectManager = new Object();

ProjectManager.checkAll = function(form) {
   for (i = 0, n = form.elements.length; i < n; i++) {
      if(form.elements[i].type == "checkbox" && !(form.elements[i].getAttribute('onclick',2))) {
         if(form.elements[i].checked == true)
            form.elements[i].checked = false;
         else
            form.elements[i].checked = true;
      }
   }
} 

ProjectManager.getSelectedRadio = function(buttonGroup) {
   // returns the array number of the selected radio button or -1 if no button is selected
   if (buttonGroup[0]) { // if the button group is an array (one button is not an array)
      for (var i=0; i<buttonGroup.length; i++) {
         if (buttonGroup[i].checked) {
            return i
         }
      }
   } else {
      if (buttonGroup.checked) { return 0; } // if the one button is checked, return zero
   }
   // if we get to this point, no radio button is selected
   return -1;
} // Ends the "getSelectedRadio" function

ProjectManager.getSelectedRadioValue = function(buttonGroup) {
   // returns the value of the selected radio button or "" if no button is selected
   var i = ProjectManager.getSelectedRadio(buttonGroup);
   if (i == -1) {
      return "";
   } else {
      if (buttonGroup[i]) { // Make sure the button group is an array (not just one button)
         return buttonGroup[i].value;
      } else { // The button group is just the one button, and it is checked
         return buttonGroup.value;
      }
   }
} // Ends the "getSelectedRadioValue" function

ProjectManager.getSelectedCheckbox = function(buttonGroup) {
   // Go through all the check boxes. return an array of all the ones
   // that are selected (their position numbers). if no boxes were checked,
   // returned array will be empty (length will be zero)
   var retArr = new Array();
   var lastElement = 0;
   if (buttonGroup[0]) { // if the button group is an array (one check box is not an array)
      for (var i=0; i<buttonGroup.length; i++) {
         if (buttonGroup[i].checked) {
            retArr.length = lastElement;
            retArr[lastElement] = i;
            lastElement++;
         }
      }
   } else { // There is only one check box (it's not an array)
      if (buttonGroup.checked) { // if the one check box is checked
         retArr.length = lastElement;
         retArr[lastElement] = 0; // return zero as the only array value
      }
   }
   return retArr;
} // Ends the "getSelectedCheckbox" function

ProjectManager.getSelectedCheckboxValue = function(buttonGroup) {
   // return an array of values selected in the check box group. if no boxes
   // were checked, returned array will be empty (length will be zero)
   var retArr = new Array(); // set up empty array for the return values
   var selectedItems = ProjectManager.getSelectedCheckbox(buttonGroup);
   if (selectedItems.length != 0) { // if there was something selected
      retArr.length = selectedItems.length;
      for (var i=0; i<selectedItems.length; i++) {
         if (buttonGroup[selectedItems[i]]) { // Make sure it's an array
            retArr[i] = buttonGroup[selectedItems[i]].value;
         } else { // It's not an array (there's just one check box and it's selected)
            retArr[i] = buttonGroup.value;// return that value
         }
      }
   }
   return retArr;
}


ProjectManager.addslashes = function( str ) {
    // Quote string with slashes
    // 
    // +    discuss at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_addslashes/
    // +       version: 809.2122
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Ates Goral (http://magnetiq.com)
    // +   improved by: marrtins
    // +   improved by: Nate
    // +   improved by: Onno Marsman
    // *     example 1: addslashes("kevin's birthday");
    // *     returns 1: 'kevin\'s birthday'
 
    return (str+'').replace(/([\\"'])/g, "\\$1").replace(/\0/g, "\\0");
}


ProjectManager.basename = function(path, suffix) {
	// Returns the filename component of the path 
	//
	// version: 812.316
	// discuss at: http://phpjs.org/functions/basename
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Ash Searle (http://hexmen.com/blog/)
	// +   improved by: Lincoln Ramsay
	// +   improved by: djmix
	// *     example 1: basename('/www/site/home.htm', '.htm');
	// *     returns 1: 'home'
	var b = path.replace(/^.*[\/\\]/g, '');
	    
	if (typeof(suffix) == 'string' && b.substr(b.length-suffix.length) == suffix) {
	   b = b.substr(0, b.length-suffix.length);
	}
	   
	return b;
}


/*
function is_array(variable) {
   return typeof(variable) == "object" && (variable instanceof Array);
}
*/