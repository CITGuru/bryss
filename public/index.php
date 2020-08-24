<?php

use Bryss\App;
use Bryss\Validator as InputValidator;


require __DIR__ . '/../vendor/autoload.php';

$app = App::create();

// Currently supported methods: GET, POST, PUT, DELETE


$app->get("/hello", function($req, $res, $args){
    return $res->json(array(
        "message"=>"Hello World",
        "author" => "Oyetoke Tobi"
    ));
});

$app->get('/hello-xml', function($req, $res, $args) {
  return $res->xml(array(
    "message" => "Hello World",
    "author" => "Ilori Stephen A <stephenilori458@gmail.com>"
  ));
});

$app->get("/hello/:name", function($req, $res, $args){
    $name = $req->params["name"];
    return $res->json(array(
        "message"=>"Hello, ".$name
    ));
});

// Post endpoint with validation

$app->post('/api/v1/register', function($req, $res){
    $body = $req->getBody();
    $email = $req->input("email");
    $password = $req->input("password");
    $name = $req->input("name");
    $role = $req->input("role", "user");

    $validator = InputValidator::schema($body, array(
        "email"=>"required|email",
        "password"=>"required|min:8",
        "name"=>"required|min:2",
    ));

    if(count($validator)!=0){
        return $req->json(array(
            "status"=>"error",
            "message"=>"Validation error",
            "errors"=>$validator
        ), 400);
    }

    return $res->json(array(
        "status"=>"success",
        "message"=>"Registration successfull",
        "data"=>$body
    ), 200);

});

$app->put("/api/v1/user", function($req, $res){
    return $res->json(array(
        "message"=>"PUT method"
    ));
});

$app->delete("/api/v1/user", function($req, $res){
    return $res->json(array(
        "message"=>"DELETE method"
    ));
});
