<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->addGroup('/api',function (RouteCollector $collector){
            $collector->post('/login', '/Api/Shop/Auth/login');
            $collector->put('/user', '/Api/Shop/Auth/updateAccountInfo');

            $collector->get('/shops', '/Api/Shop/Shop/shopList');
            $collector->post('/shop', '/Api/Shop/Shop/createShop');
            $collector->get('/shop', '/Api/Shop/Shop/shopInfo');
            $collector->put('/shop', '/Api/Shop/Shop/updateShop');
            $collector->delete('/shop', '/Api/Shop/Shop/deleteShop');

            $collector->get('/products', '/Api/Shop/Product/productList');
            $collector->get('/product', '/Api/Shop/Product/productInfo');
            $collector->post('/product', '/Api/Shop/Product/createdProduct');
            $collector->put('/product', '/Api/Shop/Product/updateProduct');
            $collector->delete('/product', '/Api/Shop/Product/deleteProduct');

            $collector->get('/styles', '/Api/Shop/Style/styleList');
            $collector->get('/style', '/Api/Shop/Style/styleInfo');
            $collector->post('/style', '/Api/Shop/Style/createdStyle');
            $collector->put('/style', '/Api/Shop/Style/updateStyle');
            $collector->delete('/style', '/Api/Shop/Style/deleteStyle');

            $collector->get('/supplies', '/Api/Shop/Supply/supplyList');
            $collector->get('/supply', '/Api/Shop/Supply/supplyInfo');
            $collector->post('/supply', '/Api/Shop/Supply/createSupply');
            $collector->put('/supply', '/Api/Shop/Supply/updateSupply');
            $collector->delete('/supply', '/Api/Shop/Supply/deleteSupply');

            $collector->get('/carts', '/Api/Shop/Cart/cartList');
            $collector->post('/cart', '/Api/Shop/Cart/createCart');
            $collector->put('/cart', '/Api/Shop/Cart/updateCart');
            $collector->delete('/cart', '/Api/Shop/Cart/deleteCart');

            $collector->get('/orders', '/Api/Shop/Order/orderList');
            $collector->post('/order', '/Api/Shop/Order/createOrder');
            $collector->get('/order', '/Api/Shop/Order/orderInfo');

            $collector->get('/collects', '/Api/Shop/User/collectList');
            $collector->post('/collect', '/Api/Shop/User/createCollect');
            $collector->delete('/collect', '/Api/Shop/User/deleteCollect');

            $collector->post('/file', '/Api/Common/CommonBase/upload');
        });
    }
}