<?php

class item extends Mustache {
  public $APP_NAME = APP_NAME;
  public $APP_ROOT_URL = APP_ROOT_URL;
  public $PODIO_ICON_PATH = PODIO_ICON_PATH;
  
  private $api = null;
  
  public $appId = -1;
  public $spaceId = -1;
  public $spaceName = '';
  public $spaceUrl = '';
  
  private $app;
  
  private $fields = array();
  
  private $fieldStateFunctions = array();
  
  public function __construct($api, $appId = -1) {
    $this->api = $api;
    $this->appId = $appId;
    
    $oauth = PodioOAuth::instance();
    // render fields
    $app = $api->app->get($this->appId);
    $this->app = $app['config'];
    $this->spaceId = $app['space_id'];
    
    // create temporary array with given values
    $space = $api->space->get($app['space_id']);
    if ($space['name'] != '') {
      $this->spaceName = $space['name'];
      $this->spaceUrl = $space['url'];
    }
    if (is_array($app['fields'])) {
      foreach ($app['fields'] as $index => $field) {
        $newField = array();
        $newField['tabindex'] = $index;
        $newField['title'] = false;
        $newField['smallText'] = false;
        $newField['largeText'] = false;
        $newField['number'] = false;
        $newField['state'] = false;
        $newField['image'] = false;
        $newField['media'] = false;
        $newField['date'] = false;
        $newField['app'] = false;
        $newField['member'] = false;
        $newField['money'] = false;
        $newField['progress'] = false;
        $newField['location'] = false;
        $newField['video'] = false;
        
        $newField['value'] = "";
        $newField["field_id"] = $field['field_id'];
        $newField['name'] = $field['config']['name'];
        $newField['required'] = $field['config']['required'];
        $newField['label'] = $field['config']['label'];
        $newField['type'] = $field['type'];
        
        switch ($field['type']) {
          case 'title':
            $newField['title'] = true;
            break;
          case 'text':
            if ($field['config']['settings']['size'] == "large") {
              $newField['largeText'] = true;
            } else {
              $newField['smallText'] = true;
            }
            break;
          case 'number':
            $newField['number'] = true;
            break;
          case 'duration':
            $newField['duration'] = true;
            break;
          case 'state':
            $newField['state'] = true;
            $stateValues = array();
            // we'll create an anonymous function there to be able to show the state-values since we need to get the options into the template somehow and afaik mustache does not support multiple iterations within each other
            
            // store new function to return array of values
            $this->fieldStateFunctions[$field['field_id']] = create_function('$arg', 'return new ArrayIterator($arg);');
            
            // create temporary array with given values
            foreach ($field['config']['settings']['allowed_values'] as $key => $value) {
              $stateValues[] = array("item" => $value);
            }
            
            // create variable (usable via {{#state_values_field_id}} {{item}} {{/state_values_field_id}}) in mustache
            $newField['state_values_field_id'] = $this->fieldStateFunctions[$field['field_id']]($stateValues);
            unset($stateValues);
            break;
          case 'image':
            $newField['image'] = true;
            break;
          case 'media':
            $newField['media'] = true;
            break;
          case 'date':
            $newField['date'] = true;
            break;
          case 'app':
             $newField['app'] = true;
            break;
          case 'member':
            $newField['member'] = true;
            // get space members
            $memberValues = array();
            // we'll create an anonymous function there to be able to show the state-values since we need to get the options into the template somehow and afaik mustache does not support multiple iterations within each other
            
            // store new function to return array of values
            $this->fieldStateFunctions[$field['field_id']] = create_function('$arg', 'return new ArrayIterator($arg);');
            
            // create temporary array with given values
            $members = $api->space->getMembers($app['space_id']);
            if (count($members) > 0) {
              foreach ($members as $member) {
                $memberValues[] = array(
                  "member_id"      => $member['user']['user_id'],
                  "member_name"    => $member['user']['name'],
                  "member_avatar"  => $member['user']['avatar']
                );
                /*$newField['values'][$member['user']['user_id']]['user_id'] = $member['user']['user_id'];
                $newField['values'][$member['user']['user_id']]['name'] = $member['user']['name'];
                $newField['values'][$member['user']['user_id']]['avatar'] = $member['user']['avatar'];*/
              }
            }
            
            // create variable (usable via {{#state_values_field_id}} {{item}} {{/state_values_field_id}}) in mustache
            $newField['member_values_field_id'] = $this->fieldStateFunctions[$field['field_id']]($memberValues);
            unset($memberValues);
            
            break;
          case 'money':
            $newField['money'] = true;
            break;
          case 'progress':
            $newField['progress'] = true;
            $newField['value'] = 0;
            break;
          case 'location':
            $newField['location'] = true;
            break;
          case 'video':
            $newField['video'] = true;
            break;
        }
        //print_r($newField);
        
        $this->fields[] = $newField;
      }
    }
  }
  public function field () {
    $f = new ArrayIterator($this->fields);
    return $f;
  }
  
  public function notEmpty() {
    return !($this->isEmpty());
  }
  
  public function isEmpty() {
    return count($this->fields) === 0;
  }
  
  public function appName() {
    return $this->app['item_name'];
  }
  
  public function appIcon() {
    return $this->app['icon'];
  }
  
}