<?php
include("bootstrap.php");

//print_r($_POST);
//print_r($user);
//print_r($_GET);
//print_r($_SERVER);

if (isset($_GET['logout'])) { // logout
  $_SESSION['oauth'] = null;
  session_destroy();
}

if ($_SERVER['REQUEST_URI'] == '/about') {
  $viewToRender = "about";
  $template = new index;
  echo $template->render(getView($viewToRender));
  exit();
}
if ($_SERVER['REQUEST_URI'] == '/help') {
  $viewToRender = "help";
  $template = new index;
  echo $template->render(getView($viewToRender));
  exit();
}
if ($_SERVER['REQUEST_URI'] == '/install') {
  $viewToRender = "install";
  $template = new index;
  echo $template->render(getView($viewToRender));
  exit();
}
if ($_SERVER['REQUEST_URI'] == '/feedback') {
  $viewToRender = "feedback";
  $template = new index;
  echo $template->render(getView($viewToRender));
  exit();
}
if ($_SERVER['REQUEST_URI'] == '/status') {
  $viewToRender = "status";
  include APP_ROOT_PATH . 'controllers/status.php';
  $template = new status($aApp);
  echo $template->render(getView($viewToRender));
  exit();
}

if (!$_SESSION['oauth'] && $_POST['action'] == "login") { // loggin approach
  $oauth->getAccessToken('password', array('username' => $_POST['username'], 'password' => $_POST['password']));
  if ($oauth->access_token == '') { // failed
    include APP_ROOT_PATH . 'controllers/login.php';
    $viewToRender = "login";
    $template = new login("Login failed. Please check try again.");
  } else { // login ok
    $_SESSION['oauth']['refresh_token'] = $oauth->refresh_token;
    $viewToRender = "index";
    $template = new index;
  }
} else if (!$_SESSION['oauth'] && isset($_POST['action']) == false) { // not loggedin, no loggin approach
  include APP_ROOT_PATH . 'controllers/login.php';
  $viewToRender = "login";
  $template = new login;
} else { // loggedin
  $viewToRender = "index";
  $template = new index;
}

echo $template->render(getView($viewToRender));

?>
