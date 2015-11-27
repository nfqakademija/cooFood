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
        $sharedOrdersRepository = $em->getRepository('cooFoodEventBundle:SharedOrder');

        $query = $sharedOrdersRepository->createQueryBuilder('so')// visi userio shared orderiai sitam evente
        ->select('so')
            ->leftJoin('so.idUser', 'u', 'WITH', 'u = :usr')
            ->leftJoin('so.idOrderItem', 'oi', 'WITH', 'oi = so.idOrderItem')
            ->leftJoin('oi.idUserEvent', 'ue', 'WITH', 'ue = oi.idUserEvent')
            ->leftJoin('ue.idEvent', 'e', 'WITH', 'e.id = :eventId')
            ->where('so.idUser = :usr', 'e.id = :eventId')
            ->setParameter('usr', $user)
            ->setParameter('eventId', $event)
            ->getQuery();
        $sharedOrders = $query->getResult();

        //    die(var_dump(serialize($sharedOrders)));
        //  die(var_dump(serialize('whut')));
        foreach ($userEvents as $userEvent) {
            if ($userEvent->getIdEvent()->getId() == $event) {
                $orderItems = $userEvent->getOrderItems();
                foreach ($sharedOrders as $sharedOrder) {
                    $em->remove($sharedOrder);
                }
                $em->flush(); //rekia sito del apacios ciklo

                foreach ($orderItems as $orderItem) {
                    //jei orderi palieka jo kurejas, perduodam teises kitam :service1

                    if ($orderItem->getidUserEvent()->getIdUser() == $user) {
                        $shared = $sharedOrdersRepository->findOneByIdOrderItem($orderItem);
                        if ($shared != null) {
                            $allUserEvents = $shared->getIdUser()->getUserEvents();

                            foreach ($allUserEvents as $usrEvent) {
                                if ($usrEvent->getIdEvent()->getId() == $event) {
                                    $orderItem->setIdUserEvent($usrEvent);
                                    $em->persist($orderItem);
                                    $em->flush();
                                    break;
                                }
                            }
                        }
                    } else {
                        $em->remove($orderItem);
                    }
                }
                $em->remove($userEvent);
                $em->flush();
                break;
            }
        }

        return $this->redirectToRoute('homepage');
    }
}
