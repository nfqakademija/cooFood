<?php

namespace cooFood\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="cooFood\EventBundle\Entity\Repository\OrderItemRepository")
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
     * @ORM\OneToMany(targetEntity="cooFood\EventBundle\Entity\SharedOrder", mappedBy="idOrderItem")
     */
    private $sharedOrders;

    /**
     * @ORM\ManyToOne(targetEntity="cooFood\SupplierBundle\Entity\Product", inversedBy="orderItems")
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
     * @ORM\ManyToOne(targetEntity="cooFood\EventBundle\Entity\UserEvent", inversedBy="items")
     * @ORM\JoinColumn(name="id_user_event", referencedColumnName="id")
     */
    private $idUserEvent;

    /**
     * @var integer
     *
     * @ORM\Column(name="share_limit", type="integer")
     */
    private $shareLimit;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sharedOrders = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    /**
     * Add sharedOrder
     *
     * @param \cooFood\EventBundle\Entity\SharedOrder $sharedOrder
     *
     * @return OrderItem
     */
    public function addSharedOrder(\cooFood\EventBundle\Entity\SharedOrder $sharedOrder)
    {
        $this->sharedOrders[] = $sharedOrder;

        return $this;
    }

    /**
     * Remove sharedOrder
     *
     * @param \cooFood\EventBundle\Entity\SharedOrder $sharedOrder
     */
    public function removeSharedOrder(\cooFood\EventBundle\Entity\SharedOrder $sharedOrder)
    {
        $this->sharedOrders->removeElement($sharedOrder);
    }

    /**
     * Get sharedOrders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSharedOrders()
    {
        return $this->sharedOrders;
    }

    /**
     * Set idProduct
     *
     * @param \cooFood\SupplierBundle\Entity\Product $idProduct
     *
     * @return OrderItem
     */
    public function setIdProduct(\cooFood\SupplierBundle\Entity\Product $idProduct = null)
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    /**
     * Get idProduct
     *
     * @return \cooFood\SupplierBundle\Entity\Product
     */
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * Set idUserEvent
     *
     * @param \cooFood\EventBundle\Entity\UserEvent $idUserEvent
     *
     * @return OrderItem
     */
    public function setIdUserEvent(\cooFood\EventBundle\Entity\UserEvent $idUserEvent = null)
    {
        $this->idUserEvent = $idUserEvent;

        return $this;
    }

    /**
     * Get idUserEvent
     *
     * @return \cooFood\EventBundle\Entity\UserEvent
     */
    public function getIdUserEvent()
    {
        return $this->idUserEvent;
    }
}
