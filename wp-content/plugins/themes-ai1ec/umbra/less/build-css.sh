#!/bin/bash

PARENT="../../vortex/less"
LESSC="lessc --include-path=.:$PARENT"

if which -s lessc; then
	$LESSC general.less > ../css/general.css
	$LESSC $PARENT/calendar.less > ../css/calendar.css
	$LESSC $PARENT/event.less > ../css/event.css
else
	echo 'Error: lessc not found. Install Node.js then: npm install -g less';
	exit 1;
fi
