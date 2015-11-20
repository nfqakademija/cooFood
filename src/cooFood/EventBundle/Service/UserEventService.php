<?php

namespace cooFood\EventBundle\Service;

use cooFood\EventBundle\Entity\UserEvent;

class UserEventService
{
//    private $eventId;

//    function __construct($eventId) {
//        $this->eventId = $eventId;
//    }

    function createUserEvent($id, $eventId)
    {
        $entity = new UserEvent();
        $entity->setIdUser($id);
        $entity->setIdEvent($eventId);
        $entity->setPaid(1);
        $entity->setAcceptedUser(0);
        $entity->setAcceptedHost(0);

        return($entity);
    }

}