<?php

namespace cooFood\eventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * order_item
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class order_item
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
     * @ORM\Column(name="id_product", type="integer")
     */
    private $idProduct;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_event_order", type="integer")
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
     * @return order_item
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
     * @return order_item
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
     * @return order_item
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
     * @return order_item
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

