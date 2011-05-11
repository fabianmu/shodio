<?php
$aApp['podio']['server']           = "https://api.podio.com:443";
$aApp['podio']['client_id']        = "Your Client ID";
$aApp['podio']['client_secret']    = "Your Client Secret";
$aApp['podio']['upload_end_point'] = "https://upload.podio.com/upload.php";
$aApp['podio']['icon_path']        = "https://d2cmuesa4snpwn.cloudfront.net/static/icons/";

// those two options are only needed if you need to pull the status page
$aApp['podio']['status']['username'] = "";
$aApp['podio']['status']['password'] = "";

// change this to whereever your PodioAPI.php is
$aApp['podio']['api']                = APP_ROOT_PATH . 'external/podio-php/PodioAPI.php';

// you might need to adjust this to asure that PEAR is includable
//set_include_path(get_include_path() . ":" . "/usr/lib/php");
?>