use ExtUtils::Installed;
my ($inst) = ExtUtils::Installed->new();
my (@modules) = $inst->modules();

print <<HTML;
Content-type: text/html

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
HTML

for my $i ($[ .. $#modules) {
   my $version = $inst->version($modules[$i]) || "???";
   my $class = ($i % 2) ? "alt" : "normal";
   print <<HTML;
<tr class="$class"><td valign="top">$modules[$i]</td><td>$version</td></tr>
HTML
}

print <<HTML;
</table>
</body>
</html>
HTML
