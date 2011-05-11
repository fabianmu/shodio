<?php
include("../bootstrap.php");
//print_r($_POST);
/*
Array
(
    [appId] => 35997
    [types] => Array
        (
            [219909] => title
            [219910] => progress
            [219912] => date
            [219913] => text
            [219911] => member
            [225255] => text
            [228199] => state
            [228560] => state
        )

    [fields] => Array
        (
            [219909] => title
            [219910] => 10
            [219912] => Array
                (
                    [start] => 01/07/2011 20:00
                    [end] => 
                )

            [219913] => desc
            [219911] => 13026
            [225255] => bla
            [228199] => state 1
            [228560] => test 1
        )

    [219912_start_date] => 01/07/2011
    [219912_start_time] => 20:00
    [219912_end_date] => 
    [219912_end_time] => 
)
*/
// create that item
$newItem = array();
$newItem['tags'] = array();
$newItem['appId'] = $_POST['appId'];
$newItem['external_id'] = null;
foreach ($_POST['fields'] as $field_id => $field) {
  $newField = array();
  switch ($_POST['types'][$field_id]) {
    /*case 'image']:
    case 'media']:
    case 'money']:
    case 'video']:*/
    
    case 'title':
    case 'text':
    case 'state':
    case 'location':
      if ($field != '') { // do not send empty fields
        $newField["field_id"] = $field_id;
        $newField['values'][]['value'] = $field;
      }
      break;
    case 'progress':
    case 'number':
    case 'duration':
      if ($field != '') { // do not send empty fields
        $newField["field_id"] = $field_id;
        $newField['values'][]['value'] = (int)$field;
      }
      break;
    case 'app':
      if ($field != '') { // do not send empty fields
        $items = explode(",", $field);
        $newField["field_id"] = $field_id;
        foreach ($items as $index => $item_id) {
          if (is_numeric($item_id)) {
            $newField['values'][$index]['value'] = (int)$item_id;
          }
        }
        $newField["field_id"] = $field_id;
      }
      break;
    case 'member':
      if ($field != '') { // do not send empty fields
        $newField["field_id"] = $field_id;
        $newField['values'][]['value'] = $field;
      }
      break;
    case 'date':
      // change date to match podio format
      if ($field['start'] != '') {
        $newField["field_id"] = $field_id;
        $dateTime = explode(" ", $field['start']);
        $date = explode("/", $dateTime[0]);
        $newField['values'][0]["start"] = $date[2] . "-" . $date[0] . "-" . $date[1] . " " . $dateTime[1].":00";
      }
      if ($field['end'] != '') {
        $dateTime = explode(" ", $field['end']);
        $date = explode("/", $dateTime[0]);
        $newField['values'][0]["end"] = $date[2] . "-" . $date[0] . "-" . $date[1] . " " . $dateTime[1].":00";
      }
      break;
  }
  
  if (isset($newField["field_id"])) {
    $newItem['fields'][] = $newField;
  }
}
//print_r($newItem);
//exit();


$item = $api->item->create($newItem['appId'], $newItem['fields'], array(), $newItem['tags'], $newItem['external_id']);
if ($item['item_id']) {
  echo $item['item_id'];
} else {
  echo "ERROR";
}
?>