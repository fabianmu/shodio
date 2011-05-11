<?php
// define some basic runtime things
define('APP_ROOT_PATH', dirname(realpath(__FILE__)).'/');
define('APP_ROOT_URL', "http://" . $_SERVER['HTTP_HOST'] . "/");
define('APP_NAME', "shodio");
define('PODIO_ERROR_LOG', APP_ROOT_PATH . "log/podio_error.log");

if ($_SERVER['HTTP_HOST'] == 'shodio.fabian.mu') {
  $path = '/is/htdocs/wp10596280_Y0IYCNO7PM/pear/PEAR';
  set_include_path(get_include_path() . PATH_SEPARATOR . $path);
}

include APP_ROOT_PATH . 'config/config.php';

define('PODIO_ICON_PATH', $aApp['podio']['icon_path']);

mb_language('uni');
mb_internal_encoding('UTF-8');

// XSS protection
foreach (get_defined_vars() as $varname) {
  unset($varname);
}

// disable magic quotes
if(function_exists('set_magic_quotes_runtime')) {
  @set_magic_quotes_runtime(FALSE);
}

if (get_magic_quotes_gpc()) {
  function stripslashes_deep($value) {
    $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
      return $value;
    }
    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

/*$expireTime = 60 * 120; // 2 hours
session_set_cookie_params($expireTime);
*/
session_start();

include APP_ROOT_PATH . 'external/mustache.php';
include APP_ROOT_PATH . 'external/helper.php';

include  $aApp['podio']['api'];

include APP_ROOT_PATH . 'controllers/index.php';
include APP_ROOT_PATH . 'controllers/item.php';

$oauth = PodioOAuth::instance();
$baseAPI = PodioBaseAPI::instance($aApp['podio']['server'], $aApp['podio']['client_id'], $aApp['podio']['client_secret'], $aApp['podio']['upload_end_point']);
$baseAPI->setLogHandler('file', PODIO_ERROR_LOG, '');

if ($_SESSION['oauth']['refresh_token']) {
  // Obtain access token
  $oauth->getAccessToken('refresh_token', array('refresh_token' => $_SESSION['oauth']['refresh_token']));
  $_SESSION['oauth']['refresh_token'] = $oauth->refresh_token;
}

$api = new PodioAPI();

?>