<?php

namespace cooFood\EventBundle\Entity\Repository;


class OrderItemRepository extends \Doctrine\ORM\EntityRepository
{
    public function findBaseOrders($idEvent)
    {
        //get one instance of each order from all user orders in event
        $query = $this->createQueryBuilder('oi')
            ->select('oi')
            ->leftJoin('oi.idUserEvent', 'ue', 'WITH', 'ue = oi.idUserEvent')
            ->leftJoin('ue.idEvent', 'e', 'WITH', 'e.id = :eventId')
            ->where('e.id = :eventId')
            ->groupBy('oi.idProduct')
            ->setParameter('eventId', $idEvent)
            ->getQuery();

        return $query->getResult();
    }

    public function findBaseOrdersDuplicates($idEvent, $baseOrders)
    {
        //get 'duplicates' of base orders by product
        $query = $this->createQueryBuilder('oi')
            ->select('oi')
            ->leftJoin('oi.idUserEvent', 'ue', 'WITH', 'ue = oi.idUserEvent')
            ->leftJoin('ue.idEvent', 'e', 'WITH', 'e.id = :eventId')
            ->where('e.id = :eventId', 'oi NOT IN (:base)')
            ->setParameter('eventId', $idEvent)
            ->setParameter('base', $baseOrders)
            ->getQuery();

        return $query->getResult();
    }

    public function findSimpleUserOrders($user, $idEvent)
    {
        //get all simple user orders in event
        $query = $this->createQueryBuilder('oi')
            ->select('oi')
            ->leftJoin('oi.idUserEvent', 'ue', 'WITH', 'ue = oi.idUserEvent')
            ->leftJoin('ue.idEvent', 'e', 'WITH', 'e.id = :eventId')
            ->where('e.id = :eventId', 'ue.idUser = :user', 'oi.shareLimit = 1')
            ->setParameter('eventId', $idEvent)
            ->setParameter(':user', $user)
            ->getQuery();

        return $query->getResult();
    }

    public function findSharableUserOrders($user, $idEvent)
    {
        //get all order items which user wants to share
        $query = $this->createQueryBuilder('oi')
            ->select('oi')
            ->leftJoin('oi.idUserEvent', 'ue', 'WITH', 'ue = oi.idUserEvent')
            ->leftJoin('ue.idEvent', 'e', 'WITH', 'e.id = :eventId')
            ->where('e.id = :eventId', 'ue.idUser = :user', 'oi.shareLimit > 1')
            ->setParameter('eventId', $idEvent)
            ->setParameter(':user', $user)
            ->getQuery();

        return $query->getResult();
    }
}