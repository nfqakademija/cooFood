<?php
/**
 * Created by PhpStorm.
 * User: klaudijus
 * Date: 15.11.12
 * Time: 01.22
 */

namespace cooFood\UserBundle\Entity;


class userEventRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByNot($field, $value)
{
    $qb = $this->createQueryBuilder('builder');
    $qb->where($qb->expr()->not($qb->expr()->eq('builder.'.$field, '?1')));
    $qb->setParameter(1, $value);

    return $qb->getQuery()
        ->getResult();
}
}