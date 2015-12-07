<?php
/**
 * Created by PhpStorm.
 * User: klaudijus
 * Date: 15.12.4
 * Time: 13.34
 */

namespace cooFood\EventBundle\Service;

use cooFood\EventBundle\Entity\OrderItem;
use cooFood\EventBundle\Entity\SharedOrder;
use cooFood\EventBundle\Entity\UserEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EventService
{
    private $em;
    private $formFactory;
    private $user;

    private $orderItemsRepository;
    private $userEventsRepository;
    private $sharedOrdersRepository;
    private $eventsRepository;
    private $usersRepository;

    public function __construct(
         $em,
         $tokenStorage,
         $formFactory
    ) {
        $this->em = $em;
        $this->formFactory = $formFactory;
       // $this->user = $tokenStorage->getToken(0)->getUser();
        $token = $tokenStorage->getToken(0);
        $this->user = $token->getUser();

        $this->userEventsRepository = $em->getRepository('cooFoodEventBundle:UserEvent');
        $this->orderItemsRepository = $em->getRepository('cooFoodEventBundle:OrderItem');
        $this->sharedOrdersRepository = $em->getRepository('cooFoodEventBundle:SharedOrder');
        $this->eventsRepository = $em->getRepository('cooFoodEventBundle:Event');
        $this->usersRepository = $em->getRepository('cooFoodUserBundle:User');
    }

    public function getEvent($id)
    {
        return $event = $this->eventsRepository->find($id);
    }

    public function deleteEvent($id)
    {
        $event = $this->getEvent($id);
        $this->em->remove($event);;

        $userEvent = $this->userEventsRepository->findByidEvent($id);

        foreach ($userEvent as $event) {
            $this->em->remove($event);
        }
        $this->em->flush();
    }

    public function checkIfOrganizer($id)
    {
        $event = $this->getEvent($id);
        if ($event == null) {
            return false;
        }

        if ($event->getIdUser() == $this->user) {
            $organizer = true;
        } else {
            $organizer = false;
        }
        return $organizer;
    }

    public function checkIfJoined($idEvent)
    {
        $userEvent = $this->userEventsRepository->findOneBy(array('idUser' => $this->user, 'idEvent' => $idEvent));
        if ($userEvent == null) {
            $joined = false;
        } else {
            $joined = true;
        }

        return $joined;
    }

    public function checkIfUserApprove($idEvent)
    {
        $userEvent = $this->userEventsRepository->findOneBy(array('idUser' => $this->user, 'idEvent' => $idEvent));
        if ($userEvent == null) {
            $userApprove = false;
        } else {
            $userApprove = $userEvent->getAcceptedUser();
        }

        return $userApprove;
    }

    public function getEventParticipants($idEvent)
    {
        $participants = array();
        $users = $this->usersRepository->findEventUsers($idEvent);

        foreach ($users as $user) {
            $participants[] = $user->getName() . " " . $user->getSurname() . " (" . $user->getEmail() . ")";
        }
        return $participants;
    }
}