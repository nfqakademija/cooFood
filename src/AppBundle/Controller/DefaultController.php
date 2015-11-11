<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use cooFood\eventBundle\Controller\eventController;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $eventRepository = $em->getRepository('cooFoodeventBundle:event');
        $securityContext = $this->container->get('security.context');

        $user = $securityContext->getToken()->getUser();

        if($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $id = $user->getId();
            $entities = $eventRepository->findByNot('idUser', $id);
            $myEvents = $eventRepository->findByidUser($id);
        }
        else
        {
            $entities = $eventRepository->findAll();
            $myEvents = null;
        }

        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            'events' => $entities,
            'my_events' => $myEvents
        ));
    }
}
