<?php
include("../bootstrap.php");
header('content-type: application/json; charset=utf-8');

$fieldId = $_GET['fieldId'];
$text = $_GET['text'];

$return = array();

if (is_numeric($fieldId)) {
  $result = $api->item->searchField($fieldId, $text);
  foreach ($result as $reference) {
    $return[] = array("id" => $reference["item_id"], "name" => $reference["title"]);
  }
  $return = json_encode($return);
  //$return = json_encode($api->item->searchField($fieldId, $text));
} else {
  $return['error'] = true;
}

echo isset($_GET['callback'])
    ? "{$_GET['callback']}($result)"
    : $return;
/*$return = array();
$return['ajax'] = true;
$return['success'] = false;

$c = new content();
$c->getById($_GET['id'], $return);

echo json_encode($return);
*/

?>


