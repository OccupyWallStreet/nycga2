//	This file is part of the "jQuery.Syntax" project, and is licensed under the GNU AGPLv3.
//	Copyright 2010 Samuel Williams. All rights reserved.
//	See <jquery.syntax.js> for licensing details.

if (!RegExp.prototype.indexOf) {
	RegExp.indexOf = function (match, index) {
		return match[0].indexOf(match[index]) + match.index;
	};
}

if (!RegExp.prototype.escape) {
	RegExp.escape = function (pattern) {
		return pattern.replace(/[\-\[\]{}()*+?.\\\^$|,#\s]/g, "\\$&");
	};
}

// The jQuery version of container.text() is broken on IE6.
// This version fixes it... for pre elements only. Other elements
// in IE will have the whitespace manipulated.
Syntax.getCDATA = function ( elems ) {
	var ret = "", elem;

	for ( var i = 0; elems[i]; i++ ) {
		elem = elems[i];

		// Get the text from text nodes and CDATA nodes
		if ( elem.nodeType === 3 || elem.nodeType === 4 ) {
			ret += elem.nodeValue;

		// Use textContent || innerText for elements
		} else if ( elem.nodeType === 1 ) {
			if ( typeof(elem.textContent) === 'string' )
				ret += elem.textContent;
			else if ( typeof(elem.innerText) === 'string' )
				ret += elem.innerText;
			else
				ret += getText( elem.childNodes );

		// Traverse everything else, except comment nodes
		} else if ( elem.nodeType !== 8 ) {
			ret += getText( elem.childNodes );
		}
	}

	return ret;
}

Syntax.layouts.plain = function (options, html, container) {
	return html;
};

Syntax.modeLineOptions = {
	'tab-width': function(name, value, options) { options.tabWidth = parseInt(value, 10); }
};

Syntax.convertTabsToSpaces = function (text, tabSize) {
	var space = [], pattern = /\r|\n|\t/g, tabOffset = 0;

	for (var i = ""; i.length <= tabSize; i = i + " ") {
		space.push(i);
	}

	text = text.replace(pattern, function(match) {
		var offset = arguments[arguments.length - 2];
		if (match === "\r" || match === "\n") {
			tabOffset = -(offset + 1);
			return match;
		} else {
			var width = tabSize - ((tabOffset + offset) % tabSize);
			tabOffset += width - 1;
			return space[width];
		}
	});

	return text;
};

Syntax.extractMatches = function() {
	var rules = arguments;

	return function(match) {
		var matches = [];

		for (var i = 0; i < rules.length; i += 1) {
			var rule = rules[i];
			var index = rule.index || (i+1);

			if (match[index].length > 0) {
				if (rule.brush)
					matches.push(Syntax.brushes[rule.brush].buildTree(match[index], RegExp.indexOf(match, index)));
				else
					matches.push(new Syntax.Match(RegExp.indexOf(match, index), match[index].length, rule, match[index]));
			}
		}

		return matches;
	};
};

Syntax.register = function (name, callback) {
	var brush = Syntax.brushes[name] = new Syntax.Brush();
	brush.klass = name;

	callback(brush);
};

Syntax.lib.cStyleComment = {pattern: /\/\*[\s\S]*?\*\//gm, klass: 'comment', allow: ['href']};
Syntax.lib.cppStyleComment = {pattern: /\/\/.*$/gm, klass: 'comment', allow: ['href']};
Syntax.lib.perlStyleComment = {pattern: /#.*$/gm, klass: 'comment', allow: ['href']};

Syntax.lib.cStyleFunction = {pattern: /([a-z_][a-z0-9_]+)\s*\(/gi, matches: Syntax.extractMatches({klass: 'function'})};
Syntax.lib.camelCaseType = {pattern: /\b_*[A-Z_][\w]*\b/g, klass: 'type'};

Syntax.lib.xmlComment = {pattern: /(&lt;|<)!--[\s\S]*?--(&gt;|>)/gm, klass: 'comment'};
Syntax.lib.webLink = {pattern: /\w+:\/\/[\w\-.\/?%&=@:;#]*/g, klass: 'href'};

Syntax.lib.hexNumber = {pattern: /0x[0-9a-fA-F]+/g, klass: 'constant'};
Syntax.lib.decimalNumber = {pattern: /[0-9]+(\.[0-9]+)?/g, klass: 'constant'};

Syntax.lib.doubleQuotedString = {pattern: /"([^\\"\n]|\\.)*"/g, klass: 'string'};
Syntax.lib.singleQuotedString = {pattern: /'([^\\'\n]|\\.)*'/g, klass: 'string'};
Syntax.lib.multiLineDoubleQuotedString = {pattern: /"([^\\"]|\\.)*"/g, klass: 'string'};
Syntax.lib.multiLineSingleQuotedString = {pattern: /'([^\\']|\\.)*'/g, klass: 'string'};
Syntax.lib.stringEscape = {pattern: /\\./g, klass: 'escape', only: ['string']};

Syntax.Match = function (offset, length, expr, value) {
	this.offset = offset;
	this.endOffset = offset + length;
	this.length = length;
	this.expression = expr;
	this.value = value;
	this.children = [];
	this.parent = null;

	// When a node is bisected, this points to the next part.
	this.next = null;
};

Syntax.Match.prototype.shift = function (x) {
	this.offset += x;
	this.endOffset += x;
};

Syntax.Match.sort = function (a,b) {
	return (a.offset - b.offset) || (b.length - a.length);
};

Syntax.Match.prototype.contains = function (match) {
	return (match.offset >= this.offset) && (match.endOffset <= this.endOffset);
};

Syntax.Match.defaultReduceCallback = function (node, container) {
	// We avoid using jQuery in this function since it is incredibly performance sensitive.
	// Using jQuery jQuery.fn.append() can reduce performance by as much as 1/3rd.
	if (typeof(node) === 'string') {
		// Add &nbsp; characters
		node = node.replace(/[ ]{2}/g, "\u00a0");

		// Fixes a bug where a single space might be coalesced if it is at the start or end of an element.
		node = node.replace(/(^[ ])/g, "\u00a0");

		node = document.createTextNode(node);
	} else {
		node = node[0];
	}

	container[0].appendChild(node);
};

Syntax.Match.prototype.reduce = function (append) {
	var start = this.offset;
	var container = jQuery('<span></span>');

	append = append || Syntax.Match.defaultReduceCallback;

	if (this.expression && this.expression.klass) {
		container.addClass(this.expression.klass);
	}

	for (var i = 0; i < this.children.length; i += 1) {
		var child = this.children[i], end = child.offset;
		var text = this.value.substr(start - this.offset, end - start);

		append(text, container);
		append(child.reduce(append), container);

		start = child.endOffset;
	}

	if (start === this.offset) {
		append(this.value, container);
	} else if (start < this.endOffset) {
		append(this.value.substr(start - this.offset, this.endOffset - start), container);
	} else if (start > this.endOffset) {
		alert("Syntax Warning: Start position " + start + " exceeds end of value " + this.endOffset);
	}

	return container;
};

Syntax.Match.prototype.canContain = function (match) {
	// Can't add anything into complete trees.
	if (this.complete) {
		return false;
	}

	// match.expression.only will be checked on insertion using this.canHaveChild(match)
	if (match.expression.only) {
		return true;
	}

	// If allow is undefined, default behaviour is no children.
	if (typeof(this.expression.allow) === 'undefined') {
		return false;
	}

	// false if {disallow: [..., klass, ...]}
	if (jQuery.isArray(this.expression.disallow) && jQuery.inArray(match.expression.klass, this.expression.disallow) !== -1) {
		return false;
	}

	// true if {allow: '*'}
	if (this.expression.allow === '*') {
		return true;
	}

	// true if {allow: [..., klass, ...]}
	if (jQuery.isArray(this.expression.allow) && jQuery.inArray(match.expression.klass, this.expression.allow) !== -1) {
		return true;
	}

	// else, false.
	return false;
};

Syntax.Match.prototype.canHaveChild = function (match) {
	var only = match.expression.only;

	// This condition is fairly slow
	if (match.expression.only) {
		var cur = this;

		while (cur !== null) {
			if (jQuery.inArray(cur.expression.klass, match.expression.only) !== -1) {
				return true;
			}

			cur = cur.parent;

			// We don't traverse into other trees.
			if (cur && cur.complete) {
				break;
			}
		}

		return false;
	}

	return true;
};

Syntax.Match.prototype._splice = function(i, match) {
	if (this.canHaveChild(match)) {
		this.children.splice(i, 0, match);
		match.parent = this;
		return this;
	} else {
		return null;
	}
};

// This is not a general tree insertion function. It is optimised to run in almost constant
// time, but data must be inserted in sorted order, otherwise you will have problems.
Syntax.Match.prototype.insertAtEnd = function (match) {
	if (!this.contains(match)) {
		alert("Syntax Error: Child is not contained in parent node!");
		return null;
	}

	if (!this.canContain(match)) {
		return null;
	}

	if (this.children.length > 0) {
		var i = this.children.length-1;
		var child = this.children[i];

		if (match.offset < child.offset) {
			if (match.endOffset <= child.offset) {
				// displacement = 'before'
				return this._splice(i, match);
			} else {
				// displacement = 'left-overlap'
				return null;
			}
		} else if (match.offset < child.endOffset) {
			if (match.endOffset <= child.endOffset) {
				// displacement = 'contains'
				var result = child.insertAtEnd(match);
				return result;
			} else {
				// displacement = 'right-overlap'
				// If a match overlaps a previous one, we ignore it.
				return null;
			}
		} else {
			// displacement = 'after'
			return this._splice(i+1, match);
		}

		// Could not find a suitable placement
		return null;
	} else {
		return this._splice(0, match);
	}
};

Syntax.Match.prototype.halfBisect = function(offset) {
	if (offset > this.offset && offset < this.endOffset) {
		return this.bisectAtOffsets([offset, this.endOffset]);
	} else {
		return null;
	}
};

Syntax.Match.prototype.bisectAtOffsets = function(splits) {
	var parts = [], start = this.offset, prev = null, children = jQuery.merge([], this.children);

	// Copy the array so we can modify it.
	splits = splits.slice(0);

	// We need to split including the last part.
	splits.push(this.endOffset);

	splits.sort(function (a,b) {
		return a-b;
	});

	for (var i = 0; i < splits.length; i += 1) {
		var offset = splits[i];

		if (offset < this.offset || offset > this.endOffset || (offset - start) == 0) {
			break;
		}

		var match = new Syntax.Match(start, offset - start, this.expression);
		match.value = this.value.substr(start - this.offset, match.length);

		if (prev) {
			prev.next = match;
		}

		prev = match;

		start = match.endOffset;
		parts.push(match);
	}

	// We only need to split to produce the number of parts we have.
	splits.length = parts.length;

	for (var i = 0; i < parts.length; i += 1) {
		var offset = splits[0];

		while (children.length > 0) {
			if (children[0].endOffset <= parts[i].endOffset) {
				parts[i].children.push(children.shift());
			} else {
				break;
			}
		}

		if (children.length) {
			// We may have an intersection
			if (children[0].offset < parts[i].endOffset) {
				var children_parts = children.shift().bisectAtOffsets(splits), j = 0;

				// children_parts are the bisected children which need to be merged with parts
				// in a linear fashion
				for (; j < children_parts.length; j += 1) {
					parts[i+j].children.push(children_parts[j]);
				}

				// Skip any parts which have been populated already
				// (i is incremented at the start of the loop, splits shifted at the end)
				i += (children_parts.length-2);
				splits.splice(0, children_parts.length-2);
			}
		}

		splits.shift();
	}

	if (children.length) {
		alert("Syntax Error: Children nodes not consumed, " + children.length + " remaining!");
	}

	return parts;
};

Syntax.Match.prototype.split = function(pattern) {
	var splits = [], match;

	while ((match = pattern.exec(this.value)) !== null) {
		splits.push(pattern.lastIndex);
	}

	return this.bisectAtOffsets(splits);
};

Syntax.Brush = function () {
	this.klass = null;
	this.rules = [];
};

Syntax.Brush.prototype.push = function () {
	if (jQuery.isArray(arguments[0])) {
		var patterns = arguments[0], rule = arguments[1];

		for (var i = 0; i < patterns.length; i += 1) {
			this.push(jQuery.extend({pattern: patterns[i]}, rule));
		}
	} else {
		var rule = arguments[0];

		if (typeof(rule.pattern) === 'string') {
			rule.string = rule.pattern;
			var prefix = "\\b", postfix = "\\b";

			if (!rule.pattern.match(/^\w/)) {
				if (!rule.pattern.match(/\w$/)) {
					prefix = postfix = "";
				} else {
					prefix = "\\B";
				}
			} else {
				if (!rule.pattern.match(/\w$/)) {
					postfix = "\\B";
				}
			}


			rule.pattern = new RegExp(prefix + RegExp.escape(rule.pattern) + postfix, rule.options || 'g');
		}

		if (typeof(XRegExp) !== 'undefined') {
			rule.pattern = new XRegExp(rule.pattern);
		}

		if (rule.pattern.global) {
			this.rules.push(rule);
		} else {
			alert("Syntax Error: Malformed rule! All rules need to be global! " + rule);
		}
	}
};

Syntax.Brush.prototype.getMatchesForRule = function (text, expr, offset) {
	var matches = [], match = null;

	while((match = expr.pattern.exec(text)) !== null) {
		if (expr.matches) {
			matches = matches.concat(expr.matches(match, expr));
		} else {
			matches.push(new Syntax.Match(match.index, match[0].length, expr, match[0]));
		}
	}

	if (offset && offset > 0) {
		for (var i = 0; i < matches.length; i += 1) {
			matches[i].shift(offset);
		}
	}

	return matches;
};

Syntax.Brush.prototype.getMatches = function(text, offset) {
	var matches = [];

	for (var i = 0; i < this.rules.length; i += 1) {
		matches = matches.concat(this.getMatchesForRule(text, this.rules[i], offset));
	}

	return matches;
};

Syntax.Brush.prototype.buildTree = function(text, offset) {
	offset = offset || 0;

	// Fixes code that uses \r\n for line endings. /$/ matches both \r\n, which is a problem..
	text = text.replace(/\r/g, "");

	var matches = this.getMatches(text, offset);
	var top = new Syntax.Match(offset, text.length, {klass: this.klass, allow: '*'}, text);

	// This sort is absolutely key to the functioning of the tree insertion algorithm.
	matches.sort(Syntax.Match.sort);

	for (var i = 0; i < matches.length; i += 1) {
		top.insertAtEnd(matches[i]);
	}

	top.complete = true;

	return top;
};

Syntax.Brush.prototype.process = function(text) {
	var top = this.buildTree(text);

	var lines = top.split(/\n/g);

	var html = jQuery('<pre class="syntax"></pre>');

	for (var i = 0; i < lines.length; i += 1) {
		var line = lines[i].reduce();
		html.append(line);
	}

	return html;
};

Syntax.highlight = function (elements, options, callback) {
	if (typeof(options) === 'function') {
		callback = options;
		options = {};
	}

	options.layout = options.layout || 'plain';

	if (typeof(options.tabWidth) === 'undefined') {
		options.tabWidth = 4;
	}

	elements.each(function () {
		var container = jQuery(this);

		var text = Syntax.getCDATA(container);

		var match = text.match(/-\*- mode: (.+?);(.*?)-\*-/i);
		var endOfSecondLine = text.indexOf("\n", text.indexOf("\n") + 1);

		if (match && match.index < endOfSecondLine) {
			options.brush = match[1].toLowerCase();
			var modeline = match[2];

			var mode = /([a-z\-]+)\:(.*?)\;/gi;

			while((match = mode.exec(modeline)) !== null) {
				var setter = Syntax.modeLineOptions[match[1]];

				if (setter) {
					setter(match[1], match[2], options);
				}
			}
		}

		var brushName = options.brush || 'plain';
		brushName = Syntax.aliases[brushName] || brushName;

		Syntax.brushes.get(brushName, function(brush) {
			container.addClass('syntax');

			if (options.tabWidth) {
				text = Syntax.convertTabsToSpaces(text, options.tabWidth);
			}

			var html = brush.process(text);

			if (options.linkify !== false) {
				jQuery('span.href', html).each(function(){
					jQuery(this).replaceWith(jQuery('<a>').attr('href', this.innerHTML).text(this.innerHTML));
				});
			}

			Syntax.layouts.get(options.layout, function(layout) {
				html = layout(options, html, container);

				if (brush.postprocess) {
					html = brush.postprocess(options, html, container);
				}

				if (callback) {
					html = callback(options, html, container);
				}

				if (html && options.replace === true) {
					// IE6 requires html.clone(true). No other browser requires this line AFAIK.
					// This could be removed once browser compatibility for IE6 is dropped.
					container.replaceWith(html.clone(true));
				}
			});
		});
	});
};

// Register the file as being loaded
Syntax.loader.core = true;
