function ppn_ajax_edit_note( note_id, note_text, note_type ) {
   var petersack = new sack( ajaxurl );    

    petersack.execute = 1;
    petersack.method = 'POST';
    petersack.setVar( "action", "ppn_edit_note" );
    petersack.setVar( "note_id", note_id );
    petersack.setVar( "note_type", note_type );
    petersack.setVar( "note_text", note_text.value );
    petersack.encVar( "cookie", document.cookie, false );
    petersack.onError = function() { alert('Ajax error in editing note' )};
    petersack.runAJAX();

  return true;
} // end of JavaScript function ppn_ajax_edit_note

function ppn_ajax_delete_note( note_id, note_type ) {
   var petersack = new sack( ajaxurl );    

    petersack.execute = 1;
    petersack.method = 'POST';
    petersack.setVar( "action", "ppn_delete_note" );
    petersack.setVar( "note_id", note_id );
    petersack.setVar( "note_type", note_type );
    petersack.encVar( "cookie", document.cookie, false );
    petersack.onError = function() { alert('Ajax error in deleting note' )};
    petersack.runAJAX();

  return true;
} // end of JavaScript function ppn_ajax_delete_note

function ppn_ajax_edit_form( note_id ) {
    document.getElementById("ppn_notecontent_" + note_id).style.display = 'none';
    document.getElementById("ppn_noteform_" + note_id).style.display = '';
} // end of JavaScript function ppn_ajax_edit_form

function ppn_ajax_edit_form_cancel( note_id ) {
    document.getElementById("ppn_notecontent_" + note_id).style.display = '';
    document.getElementById("ppn_noteform_" + note_id).style.display = 'none';
} // end of JavaScript function ppn_ajax_edit_form_cancel

function ppn_ajax_load_page( ppn_page, ppn_personal ) {
   var petersack = new sack( ajaxurl );    

    petersack.execute = 1;
    petersack.method = 'POST';
    petersack.setVar( "action", "ppn_load_page" );
    petersack.setVar( "ppn_page", ppn_page );
    petersack.setVar( "ppn_personal", ppn_personal );
    petersack.encVar( "cookie", document.cookie, false );
    petersack.onError = function() { alert('Ajax error in loading page' )};
    petersack.runAJAX();

  return true;
} // end of JavaScript function ppn_ajax_load_page

var ppn_colour_value = 0;
 
function ppn_fadeout( div_to_fade ){
// Modified from http://www.albeesonline.com/blog/2008/09/25/javascript-fading-effect/
    if(ppn_colour_value < 255) { 
        ppn_colour_value += 10; 
        document.getElementById(div_to_fade).style.backgroundColor="rgb(255,"+ppn_colour_value+","+ppn_colour_value+")";
        ppn_timeout = setTimeout("ppn_fadeout('" + div_to_fade + "')",20); 
    }
    else {
        clearTimeout(ppn_timeout);
        document.getElementById(div_to_fade).style.display="none";
        ppn_colour_value = 0;        
    }
} // end of JavaScript function ppn_fadeout

var ppn_colouredit_value = 0;
function ppn_fadeedit( div_to_fade ){
    if(ppn_colouredit_value < 255) { 
        ppn_colouredit_value += 2; 
        document.getElementById(div_to_fade).style.backgroundColor="rgb(255,255,"+ppn_colouredit_value+")";
        ppn_timeout = setTimeout("ppn_fadeedit('" + div_to_fade + "')",20); 
    }
    else {
        clearTimeout(ppn_timeout);
        ppn_colouredit_value = 0;        
    }
} // end of JavaScript function ppn_fadeedit