<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"ratesData.csv\"");
readfile('data.txt');
?> 