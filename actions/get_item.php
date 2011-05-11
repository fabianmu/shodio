<?php
include("../bootstrap.php");

$return = array();

$orgs = $api->item->get(71562);
echo json_encode($orgs);
?>