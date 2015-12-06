<?php

namespace cooFood\UserBundle\Entity\Repository;

class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function findEventUsers($idEvent)
    {
        //return all users of specified event
        $query = $this->createQueryBuilder('us')
            ->select('us')
            ->leftJoin('us.userEvents', 'ue', 'WITH', 'ue.idUser = us')
            ->leftJoin('ue.idEvent', 'e', 'WITH', 'e.id = :eventId')
            ->where('e.id = :eventId')
            ->setParameter('eventId', $idEvent)
            ->getQuery();

        return $query->getResult();
    }

}