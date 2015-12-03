<?php

namespace cooFood\UserBundle\Controller;

use cooFood\EventBundle\Entity\Event;
use cooFood\EventBundle\Entity\OrderItem;
use cooFood\EventBundle\Entity\SharedOrder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use cooFood\EventBundle\Entity\UserEvent;

/**
 * UserEvent controller.
 *
 * @Route("/userevent")
 */
class UserEventController extends Controller
{
    /**
     * Creates a new UserEvent entity.
     *
     * @Route("/{event}", name="userevent_create")
     * @Method("POST")
     * @Template("cooFoodUserBundle:UserEvent:new.html.twig")
     */
    public function createAction(Event $event)
    {
        $userEventService = $this->get("user_event_manager");
        $userEventService->createUserEvent($event);

        return $this->redirectToRoute('homepage');
    }

    /**
     * Deletes a UserEvent entity.
     *
     * @Route("/{idEvent}", name="userevent_delete")
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction($idEvent)
    {
        $userEventService = $this->get("user_event_manager");
        $userEventService->deleteUserEvent($idEvent);

        return $this->redirectToRoute('homepage');
    }
}
