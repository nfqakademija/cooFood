<?php

namespace cooFood\EventBundle\Entity;

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
     * @ORM\ManyToOne(targetEntity="cooFood\SupplierBundle\Entity\Product", inversedBy="id")
     * @ORM\JoinColumn(name="id_product_id", referencedColumnName="id")
     */
    private $idProduct;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="cooFood\EventBundle\Entity\EventOrder", inversedBy="items")
     * @ORM\JoinColumn(name="id_event_order", referencedColumnName="id")
     */
    private $idEventOrder;

    /**
     * @var integer
     *
     * @ORM\Column(name="share_limit", type="integer")
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
     * @param integer $idProduct
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
     * @return integer
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
     * Set idEventOrder
     *
     * @param integer $idEventOrder
     *
     * @return OrderItem
     */
    public function setIdEventOrder($idEventOrder)
    {
        $this->idEventOrder = $idEventOrder;

        return $this;
    }

    /**
     * Get idEventOrder
     *
     * @return integer
     */
    public function getIdEventOrder()
    {
        return $this->idEventOrder;
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
