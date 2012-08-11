#!/bin/bash

LESSC="lessc --yui-compress --include-path=."

if which -s lessc; then
	$LESSC bootstrap.less > ../css/bootstrap.min.css
else
  echo 'Error: lessc not found. Install Node.js then: npm install -g less';
	exit 1;
fi
