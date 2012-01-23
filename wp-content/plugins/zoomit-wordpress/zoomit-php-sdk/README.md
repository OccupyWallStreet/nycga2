Zoom.it PHP SDK
===============

This client library is designed to support the 
[Zoom.it API](http://zoom.it/pages/api).

Basic usage:

    require('Zoomit.class.php');

    $zoomit = new Zoomit();
    $result = $zoomit->getContentInfoByURL('http://www.zoom.it/');
    var_dump($result);

Reporting Issues
----------------

Please [file bugs or issues][issues] you encounter.

[issues]: https://github.com/openzoom/zoomit-php-sdk/issues
