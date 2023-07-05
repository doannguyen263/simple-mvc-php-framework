<?php 

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

// Routes system
$routes = new RouteCollection();
$routes->add('homepage', new Route(constant('URL_SUBFOLDER') . '/', 
  array(
    'controller' => 'PageController',
    'method'=>'indexAction'
  ), 
  array('POST')
));

// Auth
$routes->add('login', new Route(constant('URL_SUBFOLDER') .'/login',
  array(
    'controller' => 'AuthController',
    'method'=>'login'
  ),
));

// 
$routes->add('user.index', new Route(constant('URL_SUBFOLDER') .'/user-index',
  array(
    'controller' => 'UserController',
    'method'=>'index'
  )
));

$routes->add('user.create', new Route(constant('URL_SUBFOLDER') .'/user-create',
  array(
    'controller' => 'UserController',
    'method'=>'create'
  )
));
$routes->add('user.create', new Route(constant('URL_SUBFOLDER') .'/user-create',
  array(
    'controller' => 'UserController',
    'method'=>'store'
  )
));

$routes->add('user.edit', new Route(constant('URL_SUBFOLDER') .'/user-edit',
  array(
    'controller' => 'UserController',
    'method'=>'edit'
  )
));