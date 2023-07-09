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

// 
$routes->add('user-index', new Route(constant('PATH_SUBFOLDER') .'/user-index',
  array(
    'controller' => 'UserController',
    'method'=>'index'
  )
));

$routes->add('user-create', new Route(constant('PATH_SUBFOLDER') .'/user-create',
  array(
    'controller' => 'UserController',
    'method'=>'create'
  )
));

$routes->add('user-edit', new Route(constant('PATH_SUBFOLDER') .'/user-edit',
  array(
    'controller' => 'UserController',
    'method'=>'edit'
  )
));

$routes->add('user-destroy', new Route(constant('PATH_SUBFOLDER') .'/user-delete',
  array(
    'controller' => 'UserController',
    'method'=>'destroy'
  )
));