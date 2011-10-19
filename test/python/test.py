import sys
import os
import re

def print_environ(environ=os.environ):
    """Dump the shell environment as HTML."""
    keys = environ.keys()
    keys.sort()
    i = 0
    for key in keys:
        if not re.search("^HTTP_|^REQUEST_", key):
			continue
        if i == 0:
            print """<tr class="normal"><td>""", escape(key), "</td><td>", escape(environ[key]), "</td></tr>"
            i = 1
        else:
            print """<tr class="alt"><td>""", escape(key), "</td><td>", escape(environ[key]), "</td></tr>"
            i = 0

def escape(s, quote=None):
    """Replace special characters '&', '<' and '>' by SGML entities."""
    s = s.replace("&", "&amp;") # Must be done first!
    s = s.replace("<", "&lt;")
    s = s.replace(">", "&gt;")
    if quote:
        s = s.replace('"', "&quot;")
    return s


print """Content-type: text/html

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>
<table cellspacing="0" cellpadding="0" border="0">
<tr class="subhead" align="Left"><th>Name</th><th>Value</th></tr>"""
print_environ()
print """</table>
</body>
</html>"""
