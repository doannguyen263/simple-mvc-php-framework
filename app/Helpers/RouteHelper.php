<?php 
namespace App\Helpers;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class RouteHelper
{
    private $requestStack;
    private $urlMatcher;

    public function __construct(RequestStack $requestStack, UrlMatcherInterface $urlMatcher)
    {
        $this->requestStack = $requestStack;
        $this->urlMatcher = $urlMatcher;
    }

    public function getCurrentRoute()
    {
        $request = $this->requestStack->getCurrentRequest();
        $parameters = $this->urlMatcher->match($request->getPathInfo());

        return $parameters['_route'] ?? null;
    }
}