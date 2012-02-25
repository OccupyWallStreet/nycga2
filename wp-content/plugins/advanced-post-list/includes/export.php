<?php

require_once '../../../../wp-load.php';
require_once '/Class/APLCore.php';
require_once 'Class/APLPresetDbObj.php';
require_once 'Class/APLPresetObj.php';

if (isset($_GET['filename']))
{
  if (check_ajax_referer("APL_handler_export"))
  {
    $filename = $_GET["filename"];
    $presetDbObj = new APLPresetDbObj('default');

    //Output file
    header('Content-type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '.json"');
    echo trim(json_encode($presetDbObj));
  }
}
if (isset($_GET['presetname']))
{
  $filename = '';
  $presetName = $_GET['presetname'];
  $presetDbObj = new APLPresetDbObj('default');
//  $tmp_preset_db = new stdClass();
//  $tmp_preset_db->$presetName = $presetDbObj->_preset_db->$presetName;
  $tmp_presetDbObj = new stdClass();
  $tmp_presetDbObj->_preset_db->$presetName = $presetDbObj->_preset_db->$presetName;
  $filename = 'APL.' . $presetName . '.' . date('Y-m-d');

  header('Content-type: application/json');
  header('Content-Disposition: attachment; filename="' . $filename . '.json"');
  $a1 = trim(json_encode($tmp_presetDbObj));
  echo trim(json_encode($tmp_presetDbObj));
}

//echo '<script language="javascript">confirm("Do you want this?")</script>;';
?>
