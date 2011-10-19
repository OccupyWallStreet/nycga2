<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html">
<link rel="stylesheet" type="text/css" href="../../css/style.css">
</head>
<body>
<table cellspacing="0" cellpadding="0" border="0">
<tr class="subhead" align="Left"><th>Name</th><th>Value</th></tr>
<% my $class %>
<% my $vars = $Request->ServerVariables() %>
<% for (sort keys %{$vars}) { %>
	<% next unless /^HTTP_|^REQUEST_/ %>
	<% $class = ($class ne 'normal')? 'normal': 'alt' %>
	<tr class="<%=$class%>">
		<td><%=$_%></td>
		<td><%=$vars->{$_}%></td>
	</tr>
<% } %>
</table>
</body>
</html>
