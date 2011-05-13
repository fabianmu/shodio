<?php

class login extends Mustache {
  public $APP_NAME = APP_NAME;
  public $APP_ROOT_URL = APP_ROOT_URL;
  public $GOOGLE_ANALYTICS_ID = false;
  
  public $message = null;
  
  public function __construct ($aApp, $message = null) {
    if (isset($aApp['google_analytics_id'])) {
      $this->GOOGLE_ANALYTICS_ID = $aApp['google_analytics_id'];
    }
    if (trim($message) != '') {
      $this->message = trim($message);
    }
  }
}
?>