<?php 

namespace App\Controllers;

use Symfony\Component\Routing\RouteCollection;

use App\Helpers\View;

use App\Controllers\AuthController;
use App\Models\User;

class PageController
{
	protected $authModel;
	protected $userModel;

	public function __construct()
    {
    	$userModel = new User();
      $this->userModel = $userModel;

			$authModel = new AuthController;
			$this->authModel = $authModel;
			$authModel->checkUserLogin();
    }
    // Homepage action
	public function indexAction(RouteCollection $routes)
	{
		// $routeToProduct = str_replace('{id}', 1, $routes->get('product')->getPath());
		View::render('admin/pages/index');
	}
	function layoutExample(){
		View::render('layout-example');
	}
}