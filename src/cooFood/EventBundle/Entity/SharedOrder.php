<?php

namespace cooFood\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SharedOrder
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SharedOrder
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="cooFood\UserBundle\Entity\User", inversedBy="sharedOrders")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     */
    private $idUser;

    /**
     * @ORM\ManyToOne(targetEntity="cooFood\EventBundle\Entity\OrderItem", inversedBy="sharedOrders")
     * @ORM\JoinColumn(name="id_order_item", referencedColumnName="id")
     */
    private $idOrderItem;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idUser
     *
     * @param \cooFood\UserBundle\Entity\User $idUser
     *
     * @return SharedOrder
     */
    public function setIdUser(\cooFood\UserBundle\Entity\User $idUser = null)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return \cooFood\UserBundle\Entity\User
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set idOrderItem
     *
     * @param \cooFood\EventBundle\Entity\OrderItem $idOrderItem
     *
     * @return SharedOrder
     */
    public function setIdOrderItem(\cooFood\EventBundle\Entity\OrderItem $idOrderItem = null)
    {
        $this->idOrderItem = $idOrderItem;

        return $this;
    }

    /**
     * Get idOrderItem
     *
     * @return \cooFood\EventBundle\Entity\OrderItem
     */
    public function getIdOrderItem()
    {
        return $this->idOrderItem;
    }
}
