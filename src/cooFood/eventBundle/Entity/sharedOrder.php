<?php

namespace cooFood\eventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * sharedOrder
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class sharedOrder
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
     * @var integer
     *
     * @ORM\Column(name="id_user", type="integer")
     */
    private $idUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_item", type="integer")
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
     * @param integer $idUser
     *
     * @return sharedOrder
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set idOrderItem
     *
     * @param integer $idOrderItem
     *
     * @return sharedOrder
     */
    public function setIdOrderItem($idOrderItem)
    {
        $this->idOrderItem = $idOrderItem;

        return $this;
    }

    /**
     * Get idOrderItem
     *
     * @return integer
     */
    public function getIdOrderItem()
    {
        return $this->idOrderItem;
    }
}

