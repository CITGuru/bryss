<?php

namespace Bryss\Routing;

use Bryss\Interfaces\IRequest;
use Bryss\Interfaces\IResponse;


error_reporting(E_ALL & ~E_NOTICE);

class Router
{
  private $request;
  private $response;
  private $supportedHttpMethods = array(
    "GET",
    "POST",
    "PUT",
    "DELETE"
  );

  function __construct(IRequest $request, IResponse $response)
  {
   $this->request = $request;
   $this->response = $response;

  }

  function __call($name, $args)
  {
    list($route, $method) = $args;

    if(!in_array(strtoupper($name), $this->supportedHttpMethods))
    {
      $this->invalidMethodHandler();
    }
    $this->{strtolower($name)}[$this->formatRoute($route)] = $method;
  }

  /**
   * Removes trailing forward slashes from the right of the route.
   * @param route (string)
   */
  private function formatRoute($route)
  {
    $result = rtrim($route, '/');
    if ($result === '')
    {
      return '/';
    }
    return $result;
  }

  private function startsWith ($string, $startString) 
{ 
    $len = strlen($startString); 
    return (substr($string, 0, $len) === $startString); 
} 

// 405 method not allowed handler

  private function invalidMethodHandler()
  {
    header("{$this->request->serverProtocol} 405 Method Not Allowed");
    header('Content-type: application/json');
    echo json_encode(array("status"=>"405", "message"=>"Method Not Allowed"));
    return;

  }

  // 500 server error handler

  private function serverErrorHandler($e)
  {
    header("{$this->request->serverProtocol} 500 Server Error");
    header('Content-type: application/json');
    echo json_encode(array("status"=>"500", "message"=>"An unknown error occured while trying to perform an operation", "error"=>$e));
    return;

  }

  // Default 404 request handler for unknown routess

  private function defaultRequestHandler()
  {
    header("{$this->request->serverProtocol} 404 Not Found");
    header('Content-type: application/json');

    echo json_encode(array("status"=>"404", "message"=>"Not Found"));
    return;
  }

  /**
   * Resolves a route
   */
  function resolve()
  {

    $queries = array();
    parse_str($this->request->queryString, $queries);
    $args = array(
      "params"=> array(),
      "queries"=>$queries
    );

    $methodDictionary = $this->{strtolower($this->request->requestMethod)};

    // Double check resource path uri

    $path = $this->request->pathInfo;
    if(!$path){
      $path = $this->request->requestUri;
    }
    $formatedRoute = $this->formatRoute($path);


    if($methodDictionary && array_key_exists($formatedRoute, $methodDictionary)){
      $method = $methodDictionary[$formatedRoute];

    }else{
      // Match path with params - :paramName
      // Finding a more smarter way
      $path = $this->formatRoute($path);
      if($methodDictionary){
        foreach($methodDictionary as $k => $v){
          if(strpos($k,":") !==false){
            $pathSplit = explode("/", $path);
          $currentPathSplit = explode("/", $k);
          $pathCount = count($pathSplit);
          if($pathCount == count($currentPathSplit)){
            $equalCount = 0;
            $pathParams = array();
            for ($x = 0; $x < $pathCount; $x++) {
              if($pathSplit[$x] == $currentPathSplit[$x]){
                $equalCount++;
              }elseif($this->startsWith($currentPathSplit[$x], ":")){
                $equalCount++;
                $paramKey = substr($currentPathSplit[$x], 1);
                $pathParams[$paramKey] = $pathSplit[$x];
              }
            }
            if($equalCount==$pathCount){
              $method = $v;
              $args["params"] = $pathParams;
              break;
            }
          }
          }
        }
      }
      
    
    }
    
    if(is_null($method))
    {

      $this->defaultRequestHandler();
      return;
    }

    $this->request->params = $args["params"];
    $this->request->queries = $args["queries"];
    $this->request->body = $this->request->getBody();
    $this->request->headers = $this->request->getHeader();
    $this->request->path = $path;
    
    echo call_user_func_array($method, array($this->request, $this->response,  $args));
 
  }

  function __destruct()
  {
    $this->resolve();
  }
}