<?php
include("../bootstrap.php");
$notifications = $api->notification->getNewCount();
echo $notifications['new'];