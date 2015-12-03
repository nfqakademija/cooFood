<?php

namespace cooFood\EventBundle\Service;

use cooFood\EventBundle\Entity\UserEvent;

class UserEventService
{

    function createUserEvent($id, $eventId)
    {
        $entity = new UserEvent();
        $entity->setIdUser($id);
        $entity->setIdEvent($eventId);
        $entity->setPaid(0);
        $entity->setAcceptedUser(0);
        $entity->setAcceptedHost(0);

        return($entity);
    }

}