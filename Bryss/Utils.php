<?php

namespace Bryss;

function generateToken( int $length = 64){
    
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    if ($length < 1) {
        throw new \RangeException("Length must be a positive integer");
    }

    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }

    return implode('', $pieces);

}


function httpGet($url, $headers=array(), $param=array()){
    $requestHeaders = array();
    $requestHeaders[] = 'Content-type: application/json';
    
    foreach($headers as $key => $value){
        $requestHeaders[] =  $key.": ".$value;
    }

    $ch = curl_init();

    curl_setopt_array($ch, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => $requestHeaders,
    ));
    
    $response = curl_exec($ch);
    
    // curl_close($ch);
   
   $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

   $err = curl_error($ch);

   curl_close($ch);
   $res = json_decode($response);
   if ($err){
       ### failed
       return array("status"=>false, "httpcode"=> 500, "message"=>json_encode(array("error"=>$err)));
   }else{
       switch ($httpcode){
           case 201:
           case 200:  # OK
               // echo $res;
               return array("status"=>true,"message"=>$res,"httpcode"=>$httpcode);
               break;
           default:
               return array("status"=>false,"message"=>$res,"httpcode"=>$httpcode);
       }
   }
}
function httpPost($url, $body=array(), $headers=array()){
    
    $requestHeaders = array();
    $requestHeaders[] = 'Content-type: application/json';
    
    foreach($headers as $key => $value){
        $requestHeaders[] =  $key.": ".$value;
    }


    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    $response = curl_exec($ch);
    $res = json_decode($response);

    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $err = curl_error($ch);

    curl_close($ch);

    if ($err){
        ### failed
        return array("status"=>false,"message"=>json_encode(array("error"=>$err)));
    }else{
        switch ($httpcode){
            case 200:
            case 201:  # OK
               
                return array("status"=>true,"message"=>$res,"httpcode"=>$httpcode);
                break;
            default:
                return array("status"=>false,"message"=>$res,"httpcode"=>$httpcode);
        }
    }
    
}

