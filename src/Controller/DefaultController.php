<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


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
    * @Template
    * @param Request $request
    * @return array
    */
    public function defaultAction(Request $request)
    {
       return [];
    }

}
