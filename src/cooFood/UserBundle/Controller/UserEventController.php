<?php

namespace cooFood\UserBundle\Controller;

use cooFood\EventBundle\Entity\Event;
use cooFood\EventBundle\Entity\OrderItem;
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


    //Komentuotu daliu dar gali prireikti !!! <-----------------------------------------------


//    /**
//     * Lists all UserEvent entities.
//     *
//     * @Route("/", name="userevent")
//     * @Method("GET")
//     * @Template()
//     */
//    public function indexAction()
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entities = $em->getRepository('cooFoodUserBundle:UserEvent')->findAll();
//
//        return array(
//            'entities' => $entities,
//        );
//    }
    /**
     * Creates a new UserEvent entity.
     *
     * @Route("/{event}", name="userevent_create")
     * @Method("POST")
     * @Template("cooFoodUserBundle:UserEvent:new.html.twig")
     */
    public function createAction(Event $event)
    {
        $securityContext = $this->container->get('security.token_storage');
        $user = $securityContext->getToken()->getUser();

        $entity = new UserEvent();
        $entity->setIdUser($user);
        $entity->setIdEvent($event);
        $entity->setPaid(1);
        $entity->setAcceptedUser(0);
        $entity->setAcceptedHost(0);

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return $this->redirectToRoute('homepage');
    }

    /**
     * Deletes a UserEvent entity.
     *
     * @Route("/{event}", name="userevent_delete")
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction($event)
    {
        $securityContext = $this->container->get('security.token_storage');
        $user = $securityContext->getToken()->getUser();
        $userEvents = $user->getUserEvents();

        $em = $this->getDoctrine()->getManager();

        foreach ($userEvents as $userEvent) {
            if ($userEvent->getIdEvent()->getId() == $event) {
                $orderItems = $userEvent->getOrderItems();
                foreach ($orderItems as $orderItem) {               //iseinami is evento naikiname visus orderius
                    $sharedOrders = $orderItem->getSharedOrders();
                    foreach ($sharedOrders as $sharedOrder) {       //bei sharinamus
                        $em->remove($sharedOrder);
                    }
                    $em->remove($orderItem);
                }
                $em->remove($userEvent);
                $em->flush();
                break;
            }
        }

        return $this->redirectToRoute('homepage');
    }
}
