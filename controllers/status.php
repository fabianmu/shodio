<?php

class status extends Mustache {
  public $APP_NAME = APP_NAME;
  public $APP_ROOT_URL = APP_ROOT_URL;
  public $PODIO_ICON_PATH = PODIO_ICON_PATH;
  
  private $feedback = array();
  
  public function __construct($aApp) {
    $oauthForStatus = PodioOAuth::instance();
    $baseStatusAPI = PodioBaseAPI::instance($aApp['podio']['server'], $aApp['podio']['client_id'], $aApp['podio']['client_secret'], $aApp['podio']['upload_end_point']);
    $baseStatusAPI->setLogHandler('file', PODIO_ERROR_LOG, '');
    $oauthForStatus->getAccessToken('password', array('username' => $aApp['podio']['status']['username'], 'password' => $aApp['podio']['status']['password']));
    $statusApi = new PodioAPI();
    $feedbackAppId = 38098;
    $feedbackAppSortFieldId = 959376;
    
    $items = $statusApi->item->getItems($feedbackAppId, 10000, 0, $feedbackAppSortFieldId, 1, array());
    
    foreach ($items['items'] as $index => $item) {
      $status = null;
      $description = null;
      
      foreach ($item['fields'] as $field) {
        if ($field['external_id'] == "status") {
          $status = $field['values'][0]['value'];
        }
        if ($field['external_id'] == "description") {
          $description = $field['values'][0]['value'];
        }
      }
      $this->feedback[$index]['status'] = $status;
      $this->feedback[$index]['title'] = $item['title'];
      $this->feedback[$index]['description'] = $description;
    }
    
  }
  
  public function feedback () {
    $f = new ArrayIterator($this->feedback);
    return $f;
  }
  
}