<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

/**
 * Zpracovává odepření přístupu uživateli.
 * @package App\Security
 */
class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    /** @var Router Router aplikace. */
    private $router;

    /** @var Session Session aplikace. */
    private $session;

    /**
     * Konstruktor třídy s předáním závislostí.
     * @param RouterInterface  $router  router aplikace
     * @param SessionInterface $session session aplikace
     */
    public function __construct(RouterInterface $router, SessionInterface $session)
    {
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * Zpracovává akci odepření přístupu.
     * @param Request               $request               HTTP požadavek
     * @param AccessDeniedException $accessDeniedException výjimka vyhozená při odepření přístupu
     * @return Response HTTP odpověď
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $this->session->getFlashBag()->add('warning', 'Nejsi přihlášený nebo nemáš dostatečná oprávnění!');
        return new RedirectResponse($this->router->generate('login'));
    }
}