/**
* @version $Id; contenthash.js 1.0 10:35:23 PM 15/01/2012 WipeOutMedia$
* Manage blocks content hashing.
* Content hashing is used to detect blocks data changing.
*/

/**
* Content hashing class.
*/
function ContentHash(options) {

	/**
	* jQuery object reference.
	* 
	* @var jQuery.
	*/
	var $ = jQuery;
	
	/**
	* Pointer to this object.
	*
	* @var [object ContentHash]
	*/
	var $this = this;
	
	/**
	* List of fields that should affect the content hash.
	*
	*	@var array
	*/
	this.fields = ['code', 'block_name', 'location', 'links', 'scripts', 'page][', 'category]['];
	
	/**
	*	Blocks manage form reference.
	*
	* @var HTMLForm
	*/
	this.form = null;
		
	/**
	* Blocks hash list array.
	*
	* @var Array
	*/
	this.hashList = [];
	
	/**
	* Options object.
	*
	* @var object
	*/
	this.options = {};

	/**
	* Add new block to the hash list.
	*
	* This method should be called after a nw block is added.
	*
	* @param integer Block id.
	* @return void
	*/
	this.add = function(id) {
		var blockHash = '';
		if ($this.hashList[id] != undefined) {
			$.error('block id is in use!');
		}
		else{
			if ($this.options.UnsavedEmptyBlocks == 'createHash') {
				blockHash = $this.getSingleBlockHash(id);
			}
		}
		$this.hashList[id] = {
			sync : false,
			hash : blockHash
		};
	}
	
	/**
	* Clear hash list.
	*                         
	* @return void
	*/
	this.clear = function() {
		$this.hashList = [];
	}
	
	/**
	* Delete block hash from the block list.
	*
	* This method should be called after deleting tthe block.
	*
	* @param integer Block id.
	* @return void
	*/
	this.deleteBlock = function(id) {
		var block = {};
		if ($this.hashList[id] == undefined) {
			$.error('Block id not exists');
		}
		else {
			block = $this.hashList[id];
			// Delete only unsync (not saved on server) blocks.
			// Deleting sync (saved on server) blocks broke detecing changes.
			if (!block.sync) {
				delete $this.hashList[id];
			}
		}
	}
	
	/**
	* Generate block hash list.
	*
	* The method find all the availbale block and create
	* hash for each block.
	*
	* @return boolean
	*/
	this.generate = function(reset) {
		var blocks = $this.form.elements['blocks[]'];
		// Make sure to delete all hashes first.
		$this.clear();
		// For $.each to behave correctly we need to wrap 
		// blocks in array if there is only one block available.
		if (blocks.length == undefined) {
			blocks = [blocks];
		}
		// Create new hashes.
		$.each(blocks, function(){
			var id = parseInt(this.value);
			var blockNodeId = id + 1;
			var blockHash = (reset == true) ? '' : $this.getSingleBlockHash(id);
			var isSync = $('#cjtoolbox-' + blockNodeId + ' input[name="sync"]').val();
			isSync = parseInt(isSync);
			$this.hashList[id] = {
				sync : isSync,
				hash : blockHash
			};
		});
		return true;
	}
	
	/**
	* @internal
	*
	* Create hash for a single block.
	*
	* Create Hash from cjtoolbox array contents.
	*
	* @param integer Block id.
	* @return string Block content MD5 hash.
	*/
	this.getSingleBlockHash = function(id) {
		// Block fields to detect.
		var fieldVariableName = '';
		var fieldElement = null;
		var content = '';
		var hash = '';
		var blockNodeId = parseInt(id) + 1;
		var isBlockExists = $('#cjtoolbox-' + blockNodeId).get(0);
		// Check block existance, if deleted we should return hash('').
		if (isBlockExists != undefined) {
			// Get fields values from block.
			$.each($this.fields, function(index, fieldName){
				// build variable name.
				fieldVariableName = 'cjtoolbox[' + id + '][' + fieldName + ']';			
				fieldElement = $this.form.elements[fieldVariableName];
				// If the element is nodeList get all checkboxes element checked values.
				if(fieldElement == '[object NodeList]') {
					$.each(fieldElement, function() {
						content += $(this).prop('checked');						
				});
				}
				else {
					content += fieldElement.value;
				}
			});		
		}
		hash = hex_md5(content);
		return hash;
	}
	/**
	* Initialize contentHash object.
	*
	* @return void
	*/
	this.init = function() {
		// Prepare options.
		var defaultOptions = {
			UnsavedEmptyBlocks : 'createHash' // Should unsaved/new blocks with no data changed treated as changes?
		};
		$this.options = $.extend(defaultOptions, options);
		// Initialize variables for feature uses.
		$this.form = document.getElementById('cjtoolbox_form');
	}
	
	/**
	* Check if blocks data changed.
	* 
	* @return boolean True if there is a change or false otherwise.
	*/
	this.isChanged = function() {
		var changed = false;
		var targetHash = '';
		$.each($this.hashList, function(id, block){
			// Avoid array gaps.
			if (block != undefined) {
				targetHash = $this.getSingleBlockHash(id);
				if (targetHash != block.hash) {
					changed = true;
					return;
				}			
			}
		});
		return changed;
	}
	// Initialize object.
	$this.init();
	
}