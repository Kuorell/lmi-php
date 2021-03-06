<?php

namespace LmiSchool\Controller;

use Aura\Router\Exception\RouteNotFound;
use Aura\Router\Router;
use LmiSchool\Core\Request;
use Twig_Environment;

/**
 * @author Dmitry Landa <dmitry.landa@opensoftdev.ru>
 */
abstract class AbstractController
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @param Request $request
     * @param Router $router
     * @param Twig_Environment $twig
     */
    public function __construct(Request $request, Router $router, Twig_Environment $twig)
    {
        $this->request = $request;
        $this->router = $router;
        $this->twig = $twig;
    }

    /**
     * @param string $routeName
     * @param array $options
     * @return string
     */
    protected function generateUrl($routeName, array $options = [])
    {
        try {
            return $this->router->generate($routeName, $options);
        } catch (RouteNotFound $e) {
            return $this->router->generate('error.not_found');
        }
    }

    /**
     * @param string $routeName
     * @param array $options
     */
    protected function redirectTo($routeName, array $options = [])
    {
        $url = $this->generateUrl($routeName, $options);

        header(sprintf('Location: %s', $url));
        exit;
    }

    /**
     * @param string $viewName
     * @param array $options
     */
    protected function render($viewName, array $options = [])
    {
        $options = array_merge(array(
            'router' => $this->router
        ), $options);
        $template = $this->twig->loadTemplate($viewName);
        echo $template->render($options);
        exit;
    }

    /**
     * @param integer $maximumAllowedPage
     * @return integer
     */
    protected function getPage($maximumAllowedPage)
    {
        $page = $this->request->get('page', 1);
        $page = $page ?: 1;

        return min($maximumAllowedPage, $page);
    }
}
