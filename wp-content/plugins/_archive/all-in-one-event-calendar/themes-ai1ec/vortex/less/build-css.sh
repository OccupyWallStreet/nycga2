#!/bin/bash

LESSC="lessc --include-path=."

if which -s lessc; then
	$LESSC general.less > ../css/general.css
	$LESSC calendar.less > ../css/calendar.css
	$LESSC event.less > ../css/event.css
	$LESSC print.less > ../css/print.css
else
  echo 'Error: lessc not found. Install Node.js then: npm install -g less';
	exit 1;
fi
