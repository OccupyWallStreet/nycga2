/**
*
*
*/
var v03e = {};

/**
*
*
*
*/
jQuery(function($){
	/**
	*
	*
	*
	*/
	v03e = {
	  
	  errorTypesParameters : {
	  	'error' : {css : {className : 'notify-error'}},
	  	'warning' : {css : {className : 'notify-warning'}}
	  },
		
		/**
		*
		*
		*
		*/
		cleanUp : function(blockId) {
			var blockData = v03e.getBlockMetaData(blockId);
			var meta = blockData.meta;
			var matchLength = 0;
			var matchOffset = 0;
			var matchEndOffset = 0;
			var uncleanedCode = blockData.code.val();
			var originalCodeLength = uncleanedCode.length;
			var uncleanedMatch = '';
			var cleanedMatch = '';
			var cleanedCode = '';
			var correctionRoutine = 'correctMatch_' + meta.errorType;
			
			$.each(meta.matchesList, function() {
				offsetDifference = originalCodeLength - uncleanedCode.length;
				matchOffset = this[0] - offsetDifference;
				uncleanedMatch = this[1];
				if (this[2] == true) { // If user tick the checkbox.
					cleanedMatch = v03e[correctionRoutine](uncleanedMatch);
					//cleanedMatch = uncleanedMatch.replace(errorExpression, '$1$2$1');
					uncleanedCode = uncleanedCode.replace(uncleanedMatch, cleanedMatch);
					matchLength = cleanedMatch.length;
				}
				else{
					matchLength = uncleanedMatch.length;
				}
				// Put the cleaned match to the cleaned code.
				matchEndOffset = matchOffset + matchLength;
				cleanedCode += uncleanedCode.substr(0, matchEndOffset);
				// Remove last match from unlcleaned code.
				uncleanedCode = uncleanedCode.substr(matchEndOffset);
			});
			// The remaining code is considered as clean.
			cleanedCode += uncleanedCode;
			// Apply the cleaned code.
			blockData.code.val(cleanedCode);
			return blockData;
		},
		

		/**
		*
		*
		*
		*/		
		cleanUpBlock : function(blockId) {
			var doIt = confirm(CJTv03eLocalization.confirmCleanup);
			if (doIt) {
				var blockData = v03e.getBlockMetaData(blockId);
				// Check if the scan is outdated.
				var codeHash = hex_md5(blockData.code.val());
				if (codeHash != blockData.meta._code_hash) {
					alert(CJTv03eLocalization.CleanupScanIsOutdated);
				}
				else{
					v03e.cleanUp(blockId);
					v03e.nextError(blockId, false);
				}
			}
		},
		
		/**
		*
		*
		*
		*/
		correctMatch_error : function(match) {
			var expression = /\\(\"|\')((?:\\\\\1|.)*)?\\\1/;
			var cleanedMatch = match.replace(expression, '$1$2$1');
			return cleanedMatch;
		},
		
		/**
		*
		*
		*
		*/
		correctMatch_warning : function(match) {
			var slashCount = match.length;
			var originalSlashCount = Math.floor(slashCount / 2);
			var cleanedMatch = match.substr(0, originalSlashCount);
			return cleanedMatch;
		},
		
		/**
		*
		*
		*
		*/		
		createBlockHash : function(blockData) {
			blockData = v03e.getBlockMetaData(blockData.blockId);
			blockData.meta._code_hash_object.val(hex_md5(blockData.code.val()));
		},
		
		/**
		*
		*
		*
		*/
		dismiss : function(blockId, showMessage) {
			var blockData = v03e.getBlockMetaData(blockId);
			var dimissedErrorType = blockData.meta.errorType;
			var dismissed = false;
			if (!showMessage || confirm(CJTv03eLocalization.confirmDismiss)) {
				dismissed = true;
				// Mark error type as dimissed.
				var dismissed = '<input type="hidden" name="cjtoolbox[' + blockId + '][meta][v03e][dismissed-' + dimissedErrorType + ']" value="true" />';
				blockData.manageForm.append(dismissed);
			}
			return dismissed;
		},
		
		/**
		*
		*
		*
		*/
		getBlockBoxData : function(blockId) {
			var boxId = blockId + 1;
			var blockSelector = '#cjtoolbox-' + boxId;
			var manageForm = $('#cjtoolbox_form');
			var blockBox = $(blockSelector);
			var data = {
				security: manageForm.find('input:hidden[name="security"]').val(),
				blockId: blockId,
				boxId: boxId,
				selector: blockSelector,
				blockBox: blockBox,
				code: blockBox.find('.datablock textarea'),
				manageForm: manageForm
			}
			return data;
		},
		
		/**
		*
		*
		*
		*/
		getBlockMetaData : function(blockId) {
			var blockData = this.getBlockBoxData(blockId);
			var metaFieldSelector = 'cjtoolbox[' + blockData.blockId + '][meta][v03e]';
			var matchesListSelector = 'input:hidden[id="' + metaFieldSelector + '[_list_json]' + '"]';
			var matchesListJSON = blockData.blockBox.find(matchesListSelector).val();
			var matchesList = $.parseJSON(matchesListJSON);
			var matchSelected = null;
			var blockMeta = {};
			var otherMetaFields = ['name:allErrorTypes', 'name:errorType', 'id:_code_hash'];
			// Matches list is a json list but we need to determing 
			// user selection too.
			$.each(matchesList, function(index, match) {
				currentMetaSelector = 'input:checkbox[name="' + metaFieldSelector + '[_list][' + index + '][1]' + '"]';
				matchSelected = blockData.blockBox.find(currentMetaSelector).prop('checked');
				// i:0 = Offset, i:1 = string match. Add user selection at index 2.
				matchesList[index].push(matchSelected);
			});
			// Get simple types meta fields.
			$.each(otherMetaFields, function() {
				var metaFieldComponents = this.split(':');
				currentMetaSelector = 'input:hidden[' + metaFieldComponents[0] + '="' + metaFieldSelector + '[' + metaFieldComponents[1] + ']' + '"]'
				blockMeta[metaFieldComponents[1] + '_object'] = blockData.blockBox.find(currentMetaSelector);
				blockMeta[metaFieldComponents[1]] = blockData.blockBox.find(currentMetaSelector).val();
			});
			blockMeta.allErrorTypes = $.parseJSON(blockMeta.allErrorTypes); // Serialized array.
			blockMeta.matchesList = matchesList;
			blockData.meta = blockMeta;
			return blockData;
		},
		
		/**
		*
		*
		*
		*
		*/
		getMessageArea : function(blockId) {
		  var blockData = v03e.getBlockBoxData(blockId);
		  var messageArea = blockData.blockBox.find('.v03e-block-message .error-message');
		  return messageArea;
		},
		
		/**
		*
		*
		*
		*/
		nextError : function(blockId, confirm) {
			var blockData = v03e.getBlockMetaData(blockId);
			var meta = blockData.meta;
			var currentError = meta.errorType;
			var errorIndex = meta.allErrorTypes.indexOf(currentError);
			var nextErrorIndex = ++errorIndex;
			var nextError = '';
			if (v03e.dismiss(blockData.blockId, confirm)) {
				if (nextErrorIndex == meta.allErrorTypes.length) { // No more error types.
				 	// Remove error meta fields.
				 	var errorMeta = ['errorType', 'allErrorTypes'];
				 	$.each(errorMeta, function() {
				 		var metaFieldSelector = 'input:hidden[name="cjtoolbox[' + blockId + '][meta][v03e][' + this + ']"]';
				 		blockData.blockBox.find(metaFieldSelector).remove();
				 	});
					// Remove error message element.
					var message = $('#v03e-error-note-block-' + blockId);
					message.remove();
					alert(CJTv03eLocalization.codeCleaned);
				}
				else{
					nextError = meta.allErrorTypes[nextErrorIndex];
					blockData.meta.errorType_object.val(nextError);
					v03e.rescan(blockId);
				}			
			}
		},
		
		/**
		*
		*
		*
		*/
		rescan : function(blockId) {
			var url = ajaxurl;
			var action = 'cjtoolbox_v03e_checkCode';
			var blockData = v03e.getBlockMetaData(blockId);
			var messageArea = v03e.getMessageArea(blockId);
			var loader = $('#v03e-block-loader-' + blockId);
			var data = {
				security: blockData.security, 
				action: action,
				blockId: blockId,
				errorType: blockData.meta.errorType,
				code: blockData.code.val()
			};
			messageArea.hide();
			loader.show();
			if (data.code) {
				$.post(url, $.param(data), function(response) {
					loader.hide();
					if (response.has) {
						messageArea.replaceWith(response.message);
						v03e.createBlockHash(blockData);
						v03e.setNotificationBarStyle(blockId, blockData.meta.errorType);
					}
					else {
						v03e.nextError(blockId, false);
					}
				}, 'json');
			}
      else {
      	alert('Block has no code!!!');
      }
		},
		
		/**
		*
		*
		*
		*/
		setNotificationBarStyle : function(blockId, errorType) {
			var notificationBarSelector = '#v03e-error-note-block-' + blockId + ' .notification-bar';
			var notificationBar = $(notificationBarSelector);
			var errorTypeCSS = v03e.errorTypesParameters[errorType].css;
			notificationBar.removeClass();
			notificationBar.addClass('notification-bar notify-' + errorType);
		}
				
	} // End v03E class.
	
	/**
	* MISC
	*/
	
	// Allow toggling the notification bar
	$('.v03e-block-message .notification-bar p').click(function() {
		var bar = $(this);
		var messageArea = $('.message-area', bar.parent().parent());
		messageArea.toggle('slow', function(){
			if (this.style.display == 'block') {
				bar.text(CJTv03eLocalization.errorBoxTitle);
			}
			else{
				bar.text(CJTv03eLocalization.errorBoxTitle);
			}		
		});
	});
	
	// Select matches string in the block code editor with
	// user selection.
	$('ul.error-list li').live('click', function(event) {
		var li = $(this);
		var ul = li.parent();
		var blockId = parseInt(ul.get(0).className.match(/blockID\-(\d+)/)[1]);
		var blockData = v03e.getBlockMetaData(blockId);
		var codeEditor = blockData.code.get(0);
		var matchOffset = parseInt(li.find('input:hidden').val());
		var matchLength = li.find('input:checkbox').val().length;
		// Check if the scan is outdated.
		var codeHash = hex_md5(blockData.code.val());
		if (codeHash != blockData.meta._code_hash) {
			var rescan = confirm(CJTv03eLocalization.rescanOutdated);
			if (rescan) {
				v03e.rescan(blockId);
			}
			return;
		}
		ul.children().removeClass('selected');
		li.toggleClass('selected');
		if (codeEditor.createTextRange) { // IE
			var selection = codeEditor.createTextRange();
			selection.collapse(true);
			selection.moveStart('character', matchOffset);
			selection.moveEnd('character', matchLength);
			selection.select();
		}
		else if (codeEditor.setSelectionRange){
			// Opera exludes new lines from the selection.
			// Selection is active on only on the printable characters.
			if (window.navigator.userAgent.toLowerCase().indexOf('opera') != -1) {
				var precedingText = codeEditor.value.substr(0, matchOffset);
				// The lines count is the returned lines count - the first line (1).
				var linesCount = precedingText.split("\n").length - 1;
				// Remove \n characters from the selection.
				matchOffset += linesCount;
			}
			codeEditor.setSelectionRange(matchOffset, matchOffset + matchLength);
		}
		codeEditor.focus();
	});
	
	// Create hash for all error blocks.
	$('input:hidden[name="blocks[]"]').each(function() {
		var blockId = parseInt(this.value);
		var blockData = v03e.getBlockBoxData(blockId);
		var errorTypeSelector = 'input:hidden[name="cjtoolbox[' + blockId + '][meta][v03e][errorType]' + '"]';
		var hasError = (blockData.blockBox.find(errorTypeSelector).val() != undefined);
		if (hasError) {
			v03e.createBlockHash(blockData);
		}
	});
});