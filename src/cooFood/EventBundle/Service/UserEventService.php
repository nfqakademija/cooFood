<?php

namespace cooFood\EventBundle\Service;

use cooFood\EventBundle\Entity\OrderItem;
use cooFood\EventBundle\Entity\SharedOrder;
use cooFood\EventBundle\Entity\UserEvent;
use Doctrine\ORM\EntityManager;
use Proxies\__CG__\cooFood\EventBundle\Entity\Event;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserEventService
{
    private $em;
    private $formFactory;
    private $user;

    private $orderItemsRepository;
    private $userEventsRepository;
    private $sharedOrdersRepository;

    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->user = $tokenStorage->getToken()->getUser();

        $this->userEventsRepository = $this->em->getRepository('cooFoodEventBundle:UserEvent');
        $this->orderItemsRepository = $this->em->getRepository('cooFoodEventBundle:OrderItem');
        $this->sharedOrdersRepository = $this->em->getRepository('cooFoodEventBundle:SharedOrder');
    }

    /**
     * Check if user is joined to event
     *
     * @param $idEvent
     * @return bool
     */
    public function checkIfUserEventExist($idEvent)
    {
        $userEvent = $this->userEventsRepository->findOneBy(array('idEvent' => $idEvent, 'idUser' => $this->user));
        if ($userEvent == null) {
            $exist = false;
        } else {
            $exist = true;
        }
        return $exist;
    }

    /**
     * Create user event record in database
     *
     * @param $event
     */
    public function createUserEvent($event)
    {
        $userEvent = new UserEvent();
        $userEvent->setIdUser($this->user);
        $userEvent->setIdEvent($event);
        $userEvent->setPaid(0);
        $userEvent->setAcceptedUser(0);
        $userEvent->setAcceptedHost(0);

        $this->em->persist($userEvent);
        $this->em->flush();
    }

    /**
     * Remove user from event also deleting all his orders,
     * and pass shared order ownership to someone else
     * if leaving user created one
     *
     * @param $idEvent
     */
    public function deleteUserEvent($idEvent)
    {
        $sharedOrders = $this->sharedOrdersRepository->findUserSharedOrders($this->user, $idEvent);
        $orderItems = $this->orderItemsRepository->findSimpleUserOrders($this->user, $idEvent);
        $sharableOrderItems = $this->orderItemsRepository->findSharableUserOrders($this->user, $idEvent);
        $userEvent = $this->userEventsRepository->findOneBy(array('idUser' => $this->user, 'idEvent' => $idEvent));

        foreach ($sharedOrders as $sharedOrder) {
            $this->em->remove($sharedOrder);
        }
        $this->em->flush();

        foreach ($sharableOrderItems as $orderItem) {
            $sharedItem = $this->sharedOrdersRepository->findOneByIdOrderItem($orderItem);

            //if someone joined order created by this user , give all 'rights' of it to someone
            //otherwise delete that also
            if ($sharedItem != null) {
                $allUserEvents = $sharedItem->getIdUser()->getUserEvents();
                foreach ($allUserEvents as $usrEvent) {
                    if ($usrEvent->getIdEvent()->getId() == $idEvent) {
                        $orderItem->setIdUserEvent($usrEvent);
                        $this->em->persist($orderItem);
                        break;
                    }
                }
            } else {
                $this->em->remove($orderItem);
            }
        }

        foreach ($orderItems as $orderItem) {
            $this->em->remove($orderItem);
        }

        $this->em->remove($userEvent);
        $this->em->flush();
    }



}