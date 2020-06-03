<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->addGroup('/shop',function (RouteCollector $collector){
            $collector->post('/login', '/Api/Shop/Auth/login');
            $collector->get('/user', '/Api/Shop/Auth/findAccountInfo');
            $collector->put('/user', '/Api/Shop/Auth/updateAccountInfo');
        });
    }
}