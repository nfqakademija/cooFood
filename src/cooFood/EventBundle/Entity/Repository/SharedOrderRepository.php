<?php

namespace cooFood\EventBundle\Entity\Repository;


class SharedOrderRepository extends \Doctrine\ORM\EntityRepository
{
    public function findUserSharedOrders($user, $idEvent)
    {
        //all event shared orders where user participates
        $query = $this->createQueryBuilder('so')
        ->select('so')
            ->leftJoin('so.idUser', 'u', 'WITH', 'u = :usr')
            ->leftJoin('so.idOrderItem', 'oi', 'WITH', 'oi = so.idOrderItem')
            ->leftJoin('oi.idUserEvent', 'ue', 'WITH', 'ue = oi.idUserEvent')
            ->leftJoin('ue.idEvent', 'e', 'WITH', 'e.id = :eventId')
            ->where('so.idUser = :usr', 'e.id = :eventId')
            ->setParameter('usr', $user)
            ->setParameter('eventId', $idEvent)
            ->getQuery();

        return $query->getResult();
    }

    public function findNotUserSharedOrders($user, $idEvent, $activeOrders)
    {
        //all event shared orders where user don't take part
        $query = $this->createQueryBuilder('so')
        ->select('so')
            ->leftJoin('so.idUser', 'u', 'WITH', 'u = :usr')
            ->leftJoin('so.idOrderItem', 'oi', 'WITH', 'oi = so.idOrderItem')
            ->leftJoin('oi.idUserEvent', 'ue', 'WITH', 'ue = oi.idUserEvent')
            ->leftJoin('ue.idEvent', 'e', 'WITH', 'e.id = :eventId')
            ->where('so.idUser != :usr', 'e.id = :eventId', 'so.idOrderItem NOT IN (:myShared)')
            ->setParameter('usr', $user)
            ->setParameter('eventId', $idEvent)
            ->setParameter('myShared', $activeOrders)
            ->getQuery();

        return $query->getResult();
    }

    public function findAllUserSharedOrders($user, $idEvent)
    {
        //all shared orders in event if user haven't joined any yet
        $query = $this->createQueryBuilder('so')
        ->select('so')
            ->leftJoin('so.idUser', 'u', 'WITH', 'u = :usr')
            ->leftJoin('so.idOrderItem', 'oi', 'WITH', 'oi = so.idOrderItem')
            ->leftJoin('oi.idUserEvent', 'ue', 'WITH', 'ue = oi.idUserEvent')
            ->leftJoin('ue.idEvent', 'e', 'WITH', 'e.id = :eventId')
            ->where('so.idUser != :usr', 'e.id = :eventId')
            ->setParameter('usr', $user)
            ->setParameter('eventId', $idEvent)
            ->getQuery();

        return $query->getResult();
    }

    public function getOrderUserCount($order)
    {
        $query = $this->createQueryBuilder('so')
            ->select('COUNT(so)')
            ->where('so.idOrderItem = :orderItem')
            ->setParameter('orderItem', $order->getIdOrderItem())
            ->getQuery();

        return $query->getSingleScalarResult();
    }
}