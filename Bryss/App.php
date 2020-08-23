<?php

namespace Bryss;

use Bryss\Routing\Request;
use Bryss\Routing\Response;
use Bryss\Routing\Router;

use Bryss\Utils;


// include_once 'Routing/Request.php';
// include_once 'Routing/Response.php';
// include_once 'Routing/Router.php';

// include_once 'Utils.php';


class App {
    public $router;
    public $store;
    function __construct()
    {
        $router = new Router(new Request, new Response);
        $this->router = $router;
    }

    public function set($name, $value){
        $this->store{$name} = $value;
    }

    public function  get($name, $default){
        $value = $this->store{$name};
        if($value){
            return $value;
        }
        return $default;
    }

    static function create(){
        $router = new Router(new Request, new Response);
        return $router;
    }
}
