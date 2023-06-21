<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends FrontendController
{
    /**
     * @param Request $request
     *
     */
    public function examplesAction(Request $request): Response
    {
        return $this->render('default/default.html.twig', []);
    }
    
    /**
     *
     * @return array
     */
    #[Template('default/default.html.twig')]
    public function defaultAction(): array
    {
        return [];
    }

}
