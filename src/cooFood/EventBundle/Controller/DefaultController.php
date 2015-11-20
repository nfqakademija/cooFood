<?php

namespace cooFood\EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $eventRepository = $em->getRepository('cooFoodEventBundle:Event');
        $userEventRepository = $em->getRepository('cooFoodEventBundle:UserEvent');

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();

        $myEvents = array();
        $events = array();
        $userEventId = array();

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            ;
            $userEvents = $user->getUserEvents();
            $allEvents = $eventRepository->findAll();

            foreach ($userEvents as $event)//my events
            {
                $eventId = $event->getIdEvent()->getId();
                array_push($userEventId, $eventId);
                $event = $eventRepository->findOneByid($eventId);
                array_push($myEvents, $event);
            }

            foreach ($allEvents as $event) //all events
            {
                $eventId = $event->getId();
                if (!in_array($eventId, $userEventId)) {
                    $event = $eventRepository->findOneByid($eventId);
                    array_push($events, $event);
                }
            }
        } else {
            $myEvents = null;
        }

        if ($myEvents == null) {
            $events = $eventRepository->findAll();
        }

        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..'),
            'events' => $events,
            'my_events' => $myEvents
        ));
    }
}