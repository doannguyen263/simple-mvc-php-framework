<?php 

namespace App\Controllers;

use App\Models\User;

use Symfony\Component\Routing\RouteCollection;

class PageController
{
	protected $user;

	public function __construct()
    {
    	$user = new User();
        $this->user = $user;
    }
    // Homepage action
	public function indexAction(RouteCollection $routes)
	{
		session_start();
		$this->user->checkAuthentication();

		$this->user->checkRole(['admin']);

		$routeToProduct = str_replace('{id}', 1, $routes->get('product')->getPath());

    require_once URL_VIEWS_ADMIN . '/pages/index.php';
	}
}