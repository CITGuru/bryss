<?php

namespace Bryss\Routing;

use Bryss\Interfaces\IRequest;

// include_once 'IRequest.php';

class Request implements IRequest
{
  function __construct()
  {
    $this->bootstrapSelf();
  }

  private function bootstrapSelf()
  {
    foreach($_SERVER as $key => $value)
    {
      $this->{$this->toCamelCase($key)} = $value;
    }

  }

  private function toCamelCase($string)
  {
    $result = strtolower($string);
        
    preg_match_all('/_[a-z]/', $result, $matches);

    foreach($matches[0] as $match)
    {
        $c = str_replace('_', '', strtoupper($match));
        $result = str_replace($match, $c, $result);
    }

    return $result;
  }

  public function getBody()
  {
    if($this->requestMethod === "GET")
    {
      return;
    }

    $postMethods = ["POST", "PUT", "DELETE"];

    if (in_array($this->requestMethod, $postMethods))
    {
    $_POST = json_decode(file_get_contents('php://input'), true);

    
    //   $body = array();
    //   foreach($_POST as $key => $value)
    //   {
    //     $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
    //   }

      return $_POST;
    }
  }

  public function getHeader(){
    $header = getallheaders();
    return $header;
  }

  public function input($key, $default=NULL)
  {
    $body = $this->getBody();
    $data = $body[$key];
    if(!$data){
        return $default;
    }
    return $data;
  }


  public function header($key, $default=NULL)
  {
    $header = $this->getHeader();
    $data = $header[$key];
    if(!$data){
        return $default;
    }
    return $data;
  }

  public function json($body, $status=200)
  {
    header('HTTP/1.1 '.$status.'');
    header('Content-type: application/json');

      return json_encode($body);
    
  }

  public function html($body, $data)
  {



      return json_encode($body);
    
  }
}