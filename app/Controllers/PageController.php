<?php 

namespace App\Controllers;

use App\Models\User;

use Symfony\Component\Routing\RouteCollection;

class PageController
{
	protected $userModel;

	public function __construct()
    {
    	$userModel = new User();
      $this->userModel = $userModel;
    }
    // Homepage action
	public function indexAction(RouteCollection $routes)
	{
		session_start();
		$checkAuthentication = $this->userModel->checkAuthentication();
		$this->userModel->checkRole(['admin']);

		// $routeToProduct = str_replace('{id}', 1, $routes->get('product')->getPath());

    require_once URL_VIEWS_ADMIN . '/pages/index.php';
	}
}