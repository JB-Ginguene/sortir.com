<?php


namespace App\ManageRoutes;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CustomRedirections
{
    private $router;
    private $security;

    public function __construct(UrlGeneratorInterface $router, Security $security)
    {
        $this->router=$router;
        $this->security=$security;
    }

    public function notLoggedRedirect(){
        if (null == $this->security->getUser()){
            return new RedirectResponse($this->router->generate('app_login'));
        }
    }
}