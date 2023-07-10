<?php 
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
// Routes system
$routes = new RouteCollection();

$routes->add('layout-example', new Route(constant('PATH_SUBFOLDER') . '/layout-example', 
  array(
    'controller' => 'PageController',
    'method'=>'layoutExample'
  )
));



$routes->add('homepage', new Route(constant('PATH_SUBFOLDER') . '/', 
  array(
    'controller' => 'PageController',
    'method'=>'indexAction'
  ), 
  array('POST')
));

// Auth
$routes->add('login', new Route(constant('PATH_SUBFOLDER') .'/login',
  array(
    'controller' => 'AuthController',
    'method'=>'login'
  ),
));

function addRoutes($routes, $routesData, $controller)
{
    foreach ($routesData as $routeData) {
        $routePath = constant('PATH_SUBFOLDER') . $routeData['path'];
        $routes->add($routeData['name'], new Route($routePath, array(
            'controller' => $controller,
            'method' => $routeData['method']
        )));
    }
}

$routesDataUser = array(
    array('name' => 'user-index', 'path' => '/user-index', 'method' => 'index'),
    array('name' => 'user-create', 'path' => '/user-create', 'method' => 'create'),
    array('name' => 'user-edit', 'path' => '/user-edit', 'method' => 'edit'),
    array('name' => 'user-destroy', 'path' => '/user-delete', 'method' => 'destroy')
);

addRoutes($routes, $routesDataUser, 'UserController');