<?php
include("../bootstrap.php");

$return = array();

$orgs = $api->org->getOrgs();
if (is_array($orgs)) {
  foreach ($orgs as $orgIndex => $org) {
    $orgSpaces = array();
    if (is_array($org['spaces'])) {
      foreach ($org['spaces'] as $spaceIndex => $space) {
        // get apps in space
        $orgs[$orgIndex]['spaces'][$spaceIndex]['apps'] = $api->app->getSpaceApps($space['space_id']);
      }
    }
  }
}
echo json_encode($orgs);
?>