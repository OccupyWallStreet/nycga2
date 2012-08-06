
var cjt_data_saved_callbacks = [];

jQuery(document).ready(function($) {
  $('form#cjtoolbox_form').submit(function() {
    var data = $(this).serialize();
    var manage_form = this;
    $('#cj-ajax-load').fadeIn();
    $.post(ajaxurl, data, function(response) {
      var success = $('#cj-popup-save');
      var loading = $('#cj-ajax-load');
      $.each(cjt_data_saved_callbacks, function(index, callback) {
      	var callbackExp = callback[0] + '.' + callback[1] + ';';
      	var callbackMethod = eval(callbackExp);
      	callbackMethod();
      });
      loading.fadeOut();
      success.fadeIn();
      window.setTimeout(function(){
         success.fadeOut(); 
      }, 3000);
      var blocks = manage_form.elements['blocks[]'];
      if (blocks.length == undefined) {
      	blocks = [blocks];
      }
      // If the blocks count > 1, make sure that all the blocks delete buttons are visible.
      if (response.availableCount > 1) {
        $.each(blocks, function(index, block){
          var blockId = parseInt(block.value) + 1;
          var selector = '#cjtoolbox-' + blockId;
          var deleteButton = $(selector + ' a.delete_block_button');
          deleteButton.show();
        });
      }
      // Mark which blocks is synchronized(saved on server).
      $.each(blocks, function(index, block) {
        var blockNodeId = parseInt(block.value) + 1;
        var selector = '#cjtoolbox-' + blockNodeId + ' input[name="sync"]';
        var syncField = $(selector);
        // If the block is saved set sync = true, else set sync = false.        
        if (response.savedIds.indexOf(parseInt(block.value)) == -1) {
        	syncField.val(0);
        }
        else {
        	syncField.val(1);
        }
      });      
      // Refresh content hash list.
      contentHash.generate();
    }, 'json'); 
    return false;
  });
	
  //Update Message popup
  jQuery.fn.center = function () {
    this.animate({"top":( jQuery(window).height() - this.height() - 200 ) / 2 + jQuery(window).scrollTop() + "px"}, 50);
    this.animate({"left":( jQuery(window).width() - this.width() - 200 ) / 2 + "px"}, 50);
    return this;
  }

  // Update loading
  jQuery.fn.loadingcenter = function () {
    this.animate({"top":( jQuery(window).height() - this.height() - 60 ) / 2 + jQuery(window).scrollTop() + "px"}, 50);
    this.animate({"left":( jQuery(window).width() - this.width() - 60 ) / 2 + "px"}, 50);
    return this;
  }


  jQuery('#cj-popup-save').center();
  jQuery('#cj-popup-reset').center();
  jQuery('#cj-ajax-load img').loadingcenter();
  jQuery(window).scroll(function() { 
    jQuery('#cj-popup-save').center();
    jQuery('#cj-popup-reset').center();
    jQuery('#cj-ajax-load img').loadingcenter();
  });

  jQuery('#cjtoolbox-addblock').click(function(e) {
    var count = jQuery('#cjblock-count').val();
    var id = parseInt(count);
    var security = jQuery('#cjsecurity').val();
    var data = {
      action : 'cjtoolbox_add_block',
      count : count++,
      security : security
    };
    jQuery('#cj-ajax-load').fadeIn();
    jQuery.post(ajaxurl, data, function(response) {
      var loading = jQuery('#cj-ajax-load');
      loading.fadeOut();
      if(response == '' || response == 0) {
        alert(localization.addBlockFailed);
      } else {
      	var newId = 'cjtoolbox-' + count;
      	jQuery('#normal-sortables > div').removeClass('postbox'); // For postboxes.add_postbox_toggles to work.
        jQuery('#normal-sortables').append(response);
        jQuery(".meta-box-sortables").sortable('refresh');
        jQuery('#cjblock-count').val(count);
        jQuery('#' + newId + ' a.edit_block_button').click();
        postboxes.add_postbox_toggles('settings_page_cjtoolbox'); // Add to toggling objects.
        jQuery('#normal-sortables > div').addClass('postbox'); // Reset blocks.
      }
    });
    return false; // For link to behave inactive
  });

});

// Save all changes.
function block_saveAllChanges() {
	jQuery('form#cjtoolbox_form').submit();
}
	
function edit_code(securityToken, blockId, dialogTitle, type, width, height) {
	// Get template id based on the type.
	var listId = '#cjtoolbox-' + type + '-' + blockId;
	var list = jQuery(listId).get(0);
	var id = list.options[list.selectedIndex].value;
	// Query string.
	var data = {
		action : 'cjtoolbox_form',
		type : type,
		id : id,
		width : width,
		height : height,
		security: securityToken 
	};
	var url = ajaxurl + '?' + jQuery.param(data);
	tb_show(dialogTitle, url);
}

function insert_code(type, id) {
  var cid = jQuery('#cjtoolbox-'+type+'-'+id+ ' option:selected').val();
  var security = jQuery('#cjsecurity').val();
  var data = {
    action : 'cjtoolbox_get_code',
    type : type,
    id : cid,
    security : security
  };
  jQuery('#cj-ajax-load').fadeIn();
  jQuery.post(ajaxurl, data, function(response) {
    var loading = jQuery('#cj-ajax-load');
    loading.fadeOut();
    if(response == '' || response == 0) {
      alert(localization.unableToReadCode.replace('{type}', type));
    } else {
      jQuery('#cjcode-'+id).insertAtCaret(response);
    }
  });
  return false; // For link to behave inactive
}

function delete_code(type, id) {
  var sure = confirm(localization.confirmDeleteTemplate);
  if(!sure) return false;

  var cid = jQuery('#cjtoolbox-'+type+'-'+id+ ' option:selected').val();
  var security = jQuery('#cjsecurity').val();
  var data = {
    action : 'cjtoolbox_delete_code',
    type : type,
    id : cid,
    security : security
  };
  jQuery('#cj-ajax-load').fadeIn();
  jQuery.post(ajaxurl, data, function(response) {
    var loading = jQuery('#cj-ajax-load');
    loading.fadeOut();
    if(response == '' || response == 0) {
      alert(localization.cantDeleteTemplate.replace('{type}', type));
    } else {
      alert(localization.templateDeleted.replace('{type}', type));
      jQuery('.cjtoolbox-'+type).each(function() {
        jQuery(this).find('option[value='+cid+ ']').remove();
      });
    }
  });
  return false; // For link to behave inactive
}

function delete_block(id, isInternal) {
  var manage_form = jQuery('#cjtoolbox_form').get(0);
  var block = jQuery('#cjtoolbox-' + id);
  var block_name = manage_form.elements['cjtoolbox[' + (id - 1) +  '][block_name]'].value;
  var confirmMessage = localization.confirmDeleteBlock.replace('{block_name}', block_name);
  if ((isInternal != undefined) || confirm(confirmMessage)) {
    // Remove block box.
    block.remove();
    // Disallow deleting the last block.
    var blocks = manage_form.elements['blocks[]'];
    // If the length is undefined then the returned object is HTMLInput not a Nodelist.
    if (blocks.length == undefined) { 
      var blockBoxId = (parseInt(blocks.value) + 1);
      var deleteButton = jQuery('#cjtoolbox-' + blockBoxId + ' a.delete_block_button');
      deleteButton.hide();
    }
    // Delete only when the block is saved in the server
    // Isinternal = true mean the block is added and deleted
    // without server knowledge. So ignore it.
    if (!isInternal) {
	  	// Delete block from content hashing object.
	  	contentHash.deleteBlock(id - 1);    
    }
  }
  return false; // For link to behave inactive
}

jQuery(document).ready(function($) {
  $('#cjtoolbox_newcode').live('submit',
    function (event) {
      event.preventDefault();
      var title = $('#cjtoolbox_newcode #new_title').val();
      var code = $('#cjtoolbox_newcode #new_code').val();
      var type = $('#cjtoolbox_newcode #new_type').val();
      var id = $('#cjtoolbox_newcode #new_id').val();
      var content_hash = $('#cjtoolbox_newcode #content_hash').val();
      var security = $('#cjtoolbox_newcode #new_security').val();
      if(!title) { 
      	alert(localization.titleFieldMissing);
      }
      else if(!code) { 
      	alert(localization.codeFieldMissing); 
      }
      else {
      	// If there is no change made with the template data get out. 
        var new_content_hash = getCodeTemplateContentHash();
      	if (new_content_hash == content_hash) {
      		var exit = confirm(localization.noChangeMadeCouldNotSaveTemplate);
      		if (exit) {
      			tb_remove();
      		}
      		return;
				}
				var data = {
					action : 'cjtoolbox_save_newcode',
					type : type,
					title : title,
					code : code,
					id : id,
					security : security
				};
				jQuery('#cjtoolbox_popup .ajax-loading-img').fadeIn();
				jQuery.post(ajaxurl, data, function(result) {
					var loading = jQuery('#cjtoolbox_popup .ajax-loading-img');
					loading.fadeOut();
					if (!result.responseCode) {
						alert(localization.couldNotSaveTemplate);
						return;
					}
					if (result.operation == 'update') {
						jQuery('select.cjtoolbox-' + type + ' > option[value=' + id + ']').each(function() {
							jQuery(this).text(title);
						});
					}
					else if (result.operation == 'insert') {
						jQuery('.cjtoolbox-' + type).each(function() {
							jQuery(this).append('<option value="' + result.id + '">' + title + '</option>');
						});
					}
					alert(localization.templateSavedSuccessful.replace('{title}', title).replace('{type}', type.toUpperCase()));
					tb_remove();
				}, 'json');
				return false;
			}
  });
});

jQuery(document).ready(function($){
  $('#cjtoolbox_block_name').live('submit', function(event){
    event.preventDefault();
    // Get forms.
    var popup_form = this;
    var manage_form = $('#cjtoolbox_form').get(0);
    var block_id = parseInt(popup_form.block_id.value); // Index based-zero id.
    var selectBlockId = block_id + 1; // Index based-one id.
    var newBlockMarker = jQuery('#cjtoolbox-' + selectBlockId + ' input[name="is_new"]');
    // Block name cannot be null
    if (!popup_form.block_name.value) {
      alert(localization.blockNameMissing);
      return;
    }
    else {
    	// Check if the name used by another block.
    	var blocks = manage_form.elements['blocks[]'];
    	var blkName = '';
    	var isUnique = true;
    	if (blocks.length == undefined) {
    		blocks = [blocks];
    	}
    	$.each(blocks, function(index, id) {
    		blkName = manage_form.elements['cjtoolbox[' + id.value + '][block_name]'].value;
    		if ((blkName == popup_form.block_name.value) && (id.value != block_id)) {
    			isUnique = false;
    			return;
    		}
    	});
    	if (!isUnique) {
    		alert(localization.blockNameIsInUse);
    		return;
    	}
    }
    // Update block name field.
    manage_form.elements['cjtoolbox[' + block_id + '][block_name]'].value = popup_form.block_name.value;
    // Also update block display name.
    var block_display_selector = '#cjtoolbox-' + selectBlockId + ' h3.hndle > span'
    var newTitle = 'CSS & JavaScript Block: ' + popup_form.block_name.value;
    $(block_display_selector).text(newTitle);
    // If adding new block add to content hash object.
		if (parseInt(newBlockMarker.val())) {
			contentHash.add(block_id);
		}
    // Mark the block as "not new".
    newBlockMarker.val(0);
    tb_remove();
  });
});

function editBlockNameForm(dialogTitle, securityNonce, blockId, width, height) {
	var blockSerialId = blockId + 1;
	var manage_form = jQuery('#cjtoolbox_form').get(0);
	var isNewBlock = jQuery('#cjtoolbox-' + blockSerialId + ' input[name="is_new"]').val();
	var data = {
		security : securityNonce,
		action : 'cjtoolbox_request_template',
		name : 'blockname',
		block_name : manage_form.elements['cjtoolbox[' + blockId + '][block_name]'].value,
		block_id : blockId,
		width : width,
		height : height,
		isNew : isNewBlock
	};
	var url = ajaxurl + '?' + jQuery.param(data);
	tb_show(dialogTitle, url);
}

function closeBlockNameForm(blockId, isNew){
	if (isNew) {
		delete_block(blockId + 1, true);
	}
	tb_remove();
}

function scriptsForm(dialogTitle, securityNonce, blockId, width, height) {
	var blockSerialId = blockId + 1;
	var manage_form = jQuery('#cjtoolbox_form').get(0);
	var data = {
		security : securityNonce,
		action : 'cjtoolbox_request_template',
		name : 'scripts',
		selections : manage_form.elements['cjtoolbox[' + blockId + '][scripts]'].value,
		block_id : blockId,
		width : width,
		height : height
	};
	var url = ajaxurl + '?' + jQuery.param(data);
	tb_show(dialogTitle, url);
}

// Save embedded scripts list.
(function($) {
	$('form#cjtoolbox_embedded_scripts').live('submit', function(event) {
		event.preventDefault();
		var manage_form = jQuery('#cjtoolbox_form').get(0);
		var blockId = this.block_id.value;
		var scripts = [];
		$.each(this.elements['cjt-scripts[]'], function(index, scriptNode) {
			if ($(scriptNode).prop('checked')) {
				scripts.push(scriptNode.value);
			}
		});
		scripts = scripts.join(',');
		manage_form.elements['cjtoolbox[' + blockId + '][scripts]'].value = scripts;
		tb_remove();
	});
})(jQuery);

function getCodeTemplateContentHash(){
	var title = jQuery('#cjtoolbox_newcode #new_title').val();
	var code = jQuery('#cjtoolbox_newcode #new_code').val();
	return hex_md5(title + code);
}

jQuery.fn.extend({
  insertAtCaret: function(myValue){
    return this.each(function(i) {
      if (document.selection) {
        this.focus();
        sel = document.selection.createRange();
        sel.text = myValue;
        this.focus();
      }
      else if (this.selectionStart || this.selectionStart == '0') {
        var startPos = this.selectionStart;
        var endPos = this.selectionEnd;
        var scrollTop = this.scrollTop;
        this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
        this.focus();
        this.selectionStart = startPos + myValue.length;
        this.selectionEnd = startPos + myValue.length;
        this.scrollTop = scrollTop;
      } else {
        this.value += myValue;
        this.focus();
      }
    })
  }
});

// 
function embedded_block_scripts(blockId) {
	
}

/**
* Notify saving changes.
*/
(function($){
	$(document).ready(function() {
		$(window).bind('beforeunload', function() {
			if (contentHash.isChanged()) {
				return "The changes you made will be lost if you navigate away from this page.";
			}
		});
	});
})(jQuery);