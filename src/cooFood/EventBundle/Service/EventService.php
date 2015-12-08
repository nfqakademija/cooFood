<?php

namespace cooFood\EventBundle\Service;

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

        $token = $tokenStorage->getToken(0);
        $this->user = $token->getUser();

        $this->userEventsRepository = $em->getRepository('cooFoodEventBundle:UserEvent');
        $this->orderItemsRepository = $em->getRepository('cooFoodEventBundle:OrderItem');
        $this->sharedOrdersRepository = $em->getRepository('cooFoodEventBundle:SharedOrder');
        $this->eventsRepository = $em->getRepository('cooFoodEventBundle:Event');
        $this->usersRepository = $em->getRepository('cooFoodUserBundle:User');
    }

    /**
     * Get event by specified id
     *
     * @param $id
     * @return cooFood\EventBundle\Entity\Event;
     */
    public function getEvent($id)
    {
        return $event = $this->eventsRepository->find($id);
    }

    /**
     * Delete event and all user events related to it, by specified id
     *
     * @param $id
     */
    public function deleteEvent($id)
    {
        $event = $this->getEvent($id);
        $this->em->remove($event);

        $userEvent = $this->userEventsRepository->findByidEvent($id);

        foreach ($userEvent as $event) {
            $this->em->remove($event);
        }
        $this->em->flush();
    }

    /**
     * Check if current user is event organizer
     *
     * @param $id
     * @return bool
     */
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

    /**
     * Check if current user is joined event
     *
     * @param $idEvent
     * @return bool
     */
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

    /**
     * Check if current user is approved to join event
     *
     * @param $idEvent
     * @return bool
     */
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

    /**
     * Check if there already is event with given name
     *
     * @param $name
     * @return bool
     */
    public function checkIfEventExist($name)
    {
        $event = $this->eventsRepository->findOneBy(array('name' => $name));
        if ($event == null) {
            $exist = false;
        } else {
            $exist = true;
        }
        return $exist;
    }

    /**
     * Get all users that have joined specified event
     *
     * @param $idEvent
     * @return array
     */
    public function getEventParticipants($idEvent)
    {
        $participants = array();
        $users = $this->usersRepository->findEventUsers($idEvent);

        foreach ($users as $user) {
            $participants[] = $user->getName() . " " . $user->getSurname();
        }
        return $participants;
    }
}