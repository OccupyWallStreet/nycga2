// brush: "yaml" aliases: []

//	This file is part of the "jQuery.Syntax" project, and is licensed under the GNU AGPLv3.
//	Copyright 2010 Samuel Williams. All rights reserved.
//	See <jquery.syntax.js> for licensing details.

Syntax.register('yaml', function(brush) {
	brush.push({
		pattern: /^\s*#.*$/gm,
		klass: 'comment',
		allow: ['href']
	});
	
	brush.push(Syntax.lib.singleQuotedString);
	brush.push(Syntax.lib.doubleQuotedString);
	
	brush.push({
		pattern: /(&|\*)[a-z0-9]+/gi,
		klass: 'constant'
	});
	
	brush.push({
		pattern: /(.*?):/gi,
		matches: Syntax.extractMatches({klass: 'keyword'})
	});
	
	brush.push(Syntax.lib.webLink);
});

