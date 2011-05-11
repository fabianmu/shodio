<?php

class login extends Mustache {
  public $APP_NAME = APP_NAME;
  public $APP_ROOT_URL = APP_ROOT_URL;
  
  public $message = null;
  
  public function __construct ($message = null) {
    if (trim($message) != '') {
      $this->message = trim($message);
    }
  }
}
?>