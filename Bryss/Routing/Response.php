<?php

namespace Bryss\Routing;

use Bryss\Interfaces\IResponse;

// include_once 'IResponse.php';

class Response implements IResponse
{


  public function json($data, $status=200)
  {
    header('HTTP/1.1 '.$status.'');
    header('Content-type: application/json');

      return json_encode($data);

  }

  public function send($body, $status=200)
  {
    header('HTTP/1.1 '.$status.'');
    header("Content-type: text/html; charset=UTF-8");
    return $body;
  }

  public function html($body, $status=200)
  {
    header('HTTP/1.1 '.$status.'');
    return $body;
  }

  public function xml($body, $status = 200)
  {
    header('HTTP/1.1' . $status . '');
    header('Content-type: application/xhtml+xml; charset=UTF-8');

    $body = array_flip($body);
    $Xml = new \SimpleXMLElement('<root/>');
    array_walk_recursive($body, array($Xml, 'addChild'));
    return $Xml->asXml();
  }
}
