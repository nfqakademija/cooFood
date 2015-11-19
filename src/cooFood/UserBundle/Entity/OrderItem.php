<?php

namespace cooFood\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderItem
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class OrderItem
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
     * @ORM\ManyToOne(targetEntity="cooFood\SupplierBundle\Entity\Product")
     * @ORM\JoinColumn(name="id_product", referencedColumnName="id")
     */
    private $idProduct;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="cooFood\UserBundle\Entity\UserEvent")
     * @ORM\JoinColumn(name="id_user_event", referencedColumnName="id")
     */
    private $idUserEvent;

    /**
     * @var integer
     *
     * @ORM\Column(name="shareLimit", type="integer")
     */
    private $shareLimit;


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
     * Set idProduct
     *
     * @param \stdClass $idProduct
     *
     * @return OrderItem
     */
    public function setIdProduct($idProduct)
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    /**
     * Get idProduct
     *
     * @return \stdClass
     */
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return OrderItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set idUserEvent
     *
     * @param \stdClass $idUserEvent
     *
     * @return OrderItem
     */
    public function setIdUserEvent($idUserEvent)
    {
        $this->idUserEvent = $idUserEvent;

        return $this;
    }

    /**
     * Get idUserEvent
     *
     * @return \stdClass
     */
    public function getIdUserEvent()
    {
        return $this->idUserEvent;
    }

    /**
     * Set shareLimit
     *
     * @param integer $shareLimit
     *
     * @return OrderItem
     */
    public function setShareLimit($shareLimit)
    {
        $this->shareLimit = $shareLimit;

        return $this;
    }

    /**
     * Get shareLimit
     *
     * @return integer
     */
    public function getShareLimit()
    {
        return $this->shareLimit;
    }
}

