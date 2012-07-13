//	This file is part of the "jQuery.Syntax" project, and is licensed under the GNU AGPLv3.
//	Copyright 2010 Samuel Williams. All rights reserved.
//	See <jquery.syntax.js> for licensing details.

function createWindow (url, name, width, height, options) {
	var x = (screen.width - width) / 2, y = (screen.height - height) / 2;

	options +=	',left=' + x +
					',top=' + y +
					',width=' + width +
					',height=' + height;

	options = options.replace(/^,/, '');

	var win = window.open(url, name, options);

	win.focus();

	return win;
}

function dirname(path) {
	return path.replace(/\\/g,'/').replace(/\/[^\/]*$/, '');
}

Syntax.layouts.table = function(options, code, container) {
	var table = jQuery('<table class="syntax"></table>'), tr = null, td = null, a = null
	var line = 1;

// create toolbar row
tr = document.createElement('tr');
tr.className = "toolbar_row";
td = document.createElement('td');
td.className = "toolbar_row";
tr.appendChild(td);
td = document.createElement('td');
td.className = "toolbar_row";
tr.appendChild(td);
table[0].appendChild(tr);

	// Source code
	code.children().each(function() {
		tr = document.createElement('tr');
		tr.className = "line ln" + line;

		if (line % 2) {
			tr.className += " alt";
		}

		td = document.createElement('td');
		td.className = "number";
		number = document.createElement('span');
		number.innerHTML = line;
		td.appendChild(number);
		tr.appendChild(td);

		td = document.createElement('td');
		td.className = "source " + this.className;

		td.innerHTML += this.innerHTML;
		tr.appendChild(td);

		table[0].appendChild(tr);
		line = line + 1;
	});

	// Toolbar
	var toolbar = jQuery('<div class="toolbar"></div>');
	a = jQuery('<input type="button" class="sfcodeselect" value="View Raw Code" />');
	a.click(function() {
		var win = createWindow('#', '_blank', 700, 500, 'location=0, resizable=1, menubar=0, scrollbars=1');
		win.document.write('<html><head><base href="' + dirname(window.location.href) + '/" /></head><body id="syntax-raw"><pre class="syntax">' + code.html() + '</pre></body></html>');
		win.document.close();
		jQuery('link').each(function(){
			if (this.rel != 'stylesheet') {
				return;
			}

			var link = jQuery('<link rel="stylesheet">', win.document);

			link.attr('type', this.type);
			link.attr('href', this.href);
			link.attr('media', this.media);

			jQuery("head", win.document).append(link);
		});

		return false;
	});

	toolbar.append(a);
	jQuery('td:eq(1)', table).prepend(toolbar);

	return table;
};
