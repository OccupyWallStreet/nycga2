#!/bin/bash

if which -s uglifyjs; then
	cat bootstrap-*.js general.js | uglifyjs -nc > general.min.js
	uglifyjs -nc calendar.js > calendar.min.js
	uglifyjs -nc event.js > event.min.js
else
	echo 'Error: uglifyjs not found. Install Node.js then: npm install -g uglify-js';
	exit 1;
fi
