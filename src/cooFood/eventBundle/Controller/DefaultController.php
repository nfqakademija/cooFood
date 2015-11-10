<?php

namespace cooFood\eventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/event/new")
     * @Template("cooFoodeventBundle:NewEvent:index.html.twig")
     */
    public function indexAction()
    {
        return array();
    }
}
