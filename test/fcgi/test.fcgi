#!/usr/bin/python

import fcgi, os, sys, cgi

count=0

while fcgi.isFCGI():
	req = fcgi.Accept()
	count = count+1
				
	req.out.write("Content-Type: text/html\n\n")
	req.out.write("""<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html>
	<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="../../css/style.css">
	</head>
	<body>
	<table cellspacing="0" cellpadding="0" border="0">
	<tr class="subhead" align="Left"><th>Name</th><th>Value</th></tr>""")
	req.out.write('<tr class="normal"><td>%s</td><td>%s</td></tr>\n' % ("Request counter", count))
	names = req.env.keys()
	names.sort()
	cl = ('alt','normal')
	i= 0
	for name in names:
		if not name.find("HTTP") or not name.find("REQUEST"):
			req.out.write('<tr class="%s"><td>%s</td><td>%s</td></tr>\n' % (cl[i%2],
				name, cgi.escape(`req.env[name]`)))
			i = i+1

	req.out.write('</table>\n</body></html>\n')

	req.Finish()
