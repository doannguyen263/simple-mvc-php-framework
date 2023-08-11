<?php 

namespace App\Controllers;

use Symfony\Component\Routing\RouteCollection;

class LoginController
{
    // Homepage action
	public function indexAction(RouteCollection $routes)
	{
		$routeToProduct = str_replace('{id}', 1, $routes->get('product')->getPath());

        require_once APP_ROOT . '/resources/views/home.php';
	}
}