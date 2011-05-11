<?php

function esc($string) {
  return mysql_escape_string($string);
}#

/**
 * getView returns mustache template as string
 * 
 * @param string $classname which template to load
 * @return template as string
 * @author Fabian
 **/
function getView($classname) {
  if (file_exists(APP_ROOT_PATH . "views/" . strtolower($classname) . ".html")) {
    return file_get_contents(APP_ROOT_PATH . "views/" . strtolower($classname) . ".html");
  }
}#

?>