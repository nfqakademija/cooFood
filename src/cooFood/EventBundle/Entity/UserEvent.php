<?php
namespace cooFood\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="UserEvent")
 * @ORM\Entity(repositoryClass="cooFood\UserBundle\Entity\userEventRepository")
 */

class UserEvent
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="cooFood\EventBundle\Entity\OrderItem", mappedBy="idUserEvent")
     */
    protected $orderItems;

    /**
     * @ORM\ManyToOne(targetEntity="cooFood\UserBundle\Entity\User", inversedBy="userEvents")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     */
    protected $idUser;

    /**
     * @ORM\ManyToOne(targetEntity="cooFood\EventBundle\Entity\Event", inversedBy="userEvents")
     * @ORM\JoinColumn(name="id_event", referencedColumnName="id")
     */
    protected $idEvent;

    /**
     * @ORM\Column(type="decimal", scale=2, name="paid")
     */
    protected $paid;

    /**
     * @ORM\Column(type="boolean", name="accepted_user")
     */
    protected $acceptedUser;

    /**
     * @ORM\Column(type="boolean", name="accepted_host")
     */
    protected $acceptedHost;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderItems = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set paid
     *
     * @param string $paid
     *
     * @return UserEvent
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * Get paid
     *
     * @return string
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * Set acceptedUser
     *
     * @param boolean $acceptedUser
     *
     * @return UserEvent
     */
    public function setAcceptedUser($acceptedUser)
    {
        $this->acceptedUser = $acceptedUser;

        return $this;
    }

    /**
     * Get acceptedUser
     *
     * @return boolean
     */
    public function getAcceptedUser()
    {
        return $this->acceptedUser;
    }

    /**
     * Set acceptedHost
     *
     * @param boolean $acceptedHost
     *
     * @return UserEvent
     */
    public function setAcceptedHost($acceptedHost)
    {
        $this->acceptedHost = $acceptedHost;

        return $this;
    }

    /**
     * Get acceptedHost
     *
     * @return boolean
     */
    public function getAcceptedHost()
    {
        return $this->acceptedHost;
    }

    /**
     * Add orderItem
     *
     * @param \cooFood\EventBundle\Entity\OrderItem $orderItem
     *
     * @return UserEvent
     */
    public function addOrderItem(\cooFood\EventBundle\Entity\OrderItem $orderItem)
    {
        $this->orderItems[] = $orderItem;

        return $this;
    }

    /**
     * Remove orderItem
     *
     * @param \cooFood\EventBundle\Entity\OrderItem $orderItem
     */
    public function removeOrderItem(\cooFood\EventBundle\Entity\OrderItem $orderItem)
    {
        $this->orderItems->removeElement($orderItem);
    }

    /**
     * Get orderItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * Set idUser
     *
     * @param \cooFood\UserBundle\Entity\User $idUser
     *
     * @return UserEvent
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
     * Set idEvent
     *
     * @param \cooFood\EventBundle\Entity\Event $idEvent
     *
     * @return UserEvent
     */
    public function setIdEvent(\cooFood\EventBundle\Entity\Event $idEvent = null)
    {
        $this->idEvent = $idEvent;

        return $this;
    }

    /**
     * Get idEvent
     *
     * @return \cooFood\EventBundle\Entity\Event
     */
    public function getIdEvent()
    {
        return $this->idEvent;
    }
}
