<?php

class index extends Mustache {
  public $APP_NAME = APP_NAME;
  public $APP_ROOT_URL = APP_ROOT_URL;
  
  public $title = null;
  public $og_slug = null;
  
  public function __construct ($title = null, $slug = null) {
    if (trim($title) != '') {
      $this->title = trim($title);
    }
    if (trim($slug) != '') {
      $this->og_slug = trim($slug);
    }
  }
}
?>