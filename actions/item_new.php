<?php
include("../bootstrap.php");

/*$return = array();
$return['ajax'] = true;
$return['success'] = false;

$c = new content();
$c->getById($_GET['id'], $return);

echo json_encode($return);
*/
$appId = $_GET['appId'];
if (is_numeric($appId)) {
  $c = new item($api, $appId);
  echo $c->render(getView('item_new'));
} else {
  return "error";
}

?>