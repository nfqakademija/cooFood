<?php

namespace cooFood\eventBundle\Controller;

use cooFood\eventBundle\Entity\Event;
use cooFood\eventBundle\Form\Type\EventType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/event/new")
     * @Template("cooFoodeventBundle:NewEvent:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $event = new Event();
        $form = $this->createForm('event', $event);


        $form->handleRequest($request);

        if ($form->isValid()) {
            // ... perform some action, such as saving the task to the database

           // return $this->redirectToRoute('');
        }

        return  array(
            'form' => $form->createView(),
        );
    }
}
