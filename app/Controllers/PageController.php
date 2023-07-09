<?php 

namespace App\Controllers;

use Symfony\Component\Routing\RouteCollection;

use App\Helpers\View;

use App\Models\Auth;
use App\Models\User;

class PageController
{
	protected $userAuth;
	protected $userModel;

	public function __construct()
    {
    	$userModel = new User();
      $this->userModel = $userModel;

			$userAuth = new Auth();
      $this->userAuth = $userAuth;
    }
    // Homepage action
	public function indexAction(RouteCollection $routes)
	{
		session_start();
		$this->userAuth->checkRole(['admin']);

		// $routeToProduct = str_replace('{id}', 1, $routes->get('product')->getPath());
		View::render('admin/pages/index');
	}
	function layoutExample(){
		View::render('layout-example');
	}
}