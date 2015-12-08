<?php

namespace cooFood\EventBundle\Entity\Repository;


class EventRepository extends \Doctrine\ORM\EntityRepository
{
    public function findValidEvents()
    {
        //get all order items which user wants to share
        $query = $this->createQueryBuilder('e')
            ->select('e')
            ->where('e.orderDeadlineDate > :date')
            ->setParameter('date', date("Y-m-d H:i:s"))
            ->getQuery();
        return $query->getResult();
    }
}