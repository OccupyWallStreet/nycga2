
var bp_forum_form_validate = {
	form: {},
	error_div: {},
	error_msg: {},
	
	init: function(args){
		this.form = jQuery('#forum-topic-form');
		this.error_div = jQuery('<div id="ubpfeditor-error"/>').hide();
		this.error_msg = ubpfeditor_form_validate_vars;
		
		if( ! this.form.length ) return;
		
		if( jQuery('p.submit', this.form).length ){
			jQuery('p.submit', this.form).before(this.error_div);
			
		}else if( jQuery('div.submit', this.form).length ){
			jQuery('div.submit', this.form).before(this.error_div);
			
		}else if( jQuery('input#submit', this.form).length ){
			jQuery('input#submit', this.form).before(this.error_div);
			
		}else{
			this.form.append(this.error_div);
		}
		
		this.form.bind('submit', this, this.submit);
	},
	
	show_error: function(key){
		var str = this.error_msg[key];
		if( str ){
			if( this.error_div.text()!='' ) str = '<br>'+str;
			this.error_div.append(str).show();
		}
	},
	
	submit: function(e){
		var t = e.data;
		
		t.error_div.empty().hide();
		
		// subject
		if( jQuery('input#topic_title', t.form).length && jQuery.trim(jQuery('input#topic_title', t.form).val())=='' )
			t.show_error('title_error');
		
		// content
		jQuery('textarea.theEditor', t.form).filter(function(){
			var id = this.id ? this.id : this.name;
			var content = (typeof tinyMCE=='object' && typeof tinyMCE.get(id)=='object' && this.style.display=='none') ? tinyMCE.get(id).getContent() : this.value;
			if( jQuery.trim(content)=='' )
				t.show_error('content_error');
		});
		
		// group id
		if( jQuery('select#topic_group_id', t.form).length && jQuery('select#topic_group_id', t.form).val()=='' )
			t.show_error('group_id_error');
		
		if( t.error_div.text()!='' )
			return false;
	}
}