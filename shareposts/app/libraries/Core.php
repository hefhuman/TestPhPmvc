<?php
/**
App core class
Creates URL & loads core controller
URL FORMAT - /controller/method/params
**/
class Core {
protected $currentController = "Pages";
protected $currentMethod = "index";
protected $params = []  ;

public function __construct(){
  //print_r($this->getUrl());

  $url = $this->getUrl();

  //Looking controller for the first value
  if(file_exists('../app/controllers/' . ucwords($url[0]) . '.php')){
    //If exists, set as controllers
    $this->currentController = ucwords($url[0]);
    //Unset the 0 Index
    unset($url[0]);
  }

//Require the Controller
require_once '../app/controllers/'. $this->currentController . '.php';

//Instantiate controller class
$this->currentController = new $this->currentController;

//Check for second part the url
if(isset($url[1])){
  //Check to see if method exists in controllers
  if(method_exists($this->currentController, $url[1])){
    $this->currentMethod = $url[1];
    //Unset 1 index
    unset($url[1]);
  }
}
// Get params
$this->params = $url ? array_values($url) : [];

// Call a callback with array of params
call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
}

public function getUrl(){
  if(isset($_GET['url'])){
    $url = rtrim($_GET['url'], '/');
    $url = filter_var($url, FILTER_SANITIZE_URL);
    $url = explode('/',$url);
    return $url;
  }
}
}

 ?>
