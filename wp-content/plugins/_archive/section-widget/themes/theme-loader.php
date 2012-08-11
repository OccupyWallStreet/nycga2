<?php

$theme = isset($_GET['theme'])? strtolower(trim($_GET['theme'])) : 'base';
$scope = isset($_GET['scope'])? trim($_GET['scope']) : '';

$content = @file_get_contents("./{$theme}/sw-theme.css");

if(!$content) {
    // Try again with the default theme
    $content = @file_get_contents("./base/sw-theme.css");
}

if($content) {
    $content = str_replace('%scope%', $scope, $content);
    $content = str_replace('url(images', "url({$theme}/images", $content);
    header('Content-Type: text/css');
    echo $content;
}

?>