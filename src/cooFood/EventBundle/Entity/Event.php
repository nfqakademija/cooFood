<?php

namespace cooFood\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Event
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Event
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
     * @ORM\OneToMany(targetEntity="cooFood\EventBundle\Entity\InvitedUser", mappedBy="idEvent")
     */
    private $invitedUsers;

    /**
     * @ORM\OneToMany(targetEntity="cooFood\EventBundle\Entity\UserEvent", mappedBy="idEvent")
     */
    private $userEvents;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="event_date", type="datetime")
     */
    private $eventDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="join_date_start", type="datetime")
     */

    private $orderDeadlineDate;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text")
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="cooFood\SupplierBundle\Entity\Supplier", inversedBy="events")
     * @ORM\JoinColumn(name="id_supplier", referencedColumnName="id")
     */
    private $idSupplier;

    /**
     * @ORM\ManyToOne(targetEntity="cooFood\UserBundle\Entity\User", inversedBy="events")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     */
    private $idUser;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible;

    /**
     * @var boolean
     *
     * @ORM\Column(name="req_approve", type="boolean")
     */
    private $reqApprove;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->invitedUsers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userEvents = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set eventDate
     *
     * @param \DateTime $eventDate
     *
     * @return Event
     */
    public function setEventDate($eventDate)
    {
        $this->eventDate = $eventDate;

        return $this;
    }

    /**
     * Get eventDate
     *
     * @return \DateTime
     */
    public function getEventDate()
    {
        return $this->eventDate;
    }

    /**
     * Set orderDeadlineDate
     *
     * @param \DateTime $orderDeadlineDate
     *
     * @return Event
     */
    public function setOrderDeadlineDate($orderDeadlineDate)
    {
        $this->orderDeadlineDate = $orderDeadlineDate;

        return $this;
    }

    /**
     * Get orderDeadlineDate
     *
     * @return \DateTime
     */
    public function getOrderDeadlineDate()
    {
        return $this->orderDeadlineDate;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Event
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Event
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set reqApprove
     *
     * @param boolean $reqApprove
     *
     * @return Event
     */
    public function setReqApprove($reqApprove)
    {
        $this->reqApprove = $reqApprove;

        return $this;
    }

    /**
     * Get reqApprove
     *
     * @return boolean
     */
    public function getReqApprove()
    {
        return $this->reqApprove;
    }

    /**
     * Add invitedUser
     *
     * @param \cooFood\EventBundle\Entity\InvitedUser $invitedUser
     *
     * @return Event
     */
    public function addInvitedUser(\cooFood\EventBundle\Entity\InvitedUser $invitedUser)
    {
        $this->invitedUsers[] = $invitedUser;

        return $this;
    }

    /**
     * Remove invitedUser
     *
     * @param \cooFood\EventBundle\Entity\InvitedUser $invitedUser
     */
    public function removeInvitedUser(\cooFood\EventBundle\Entity\InvitedUser $invitedUser)
    {
        $this->invitedUsers->removeElement($invitedUser);
    }

    /**
     * Get invitedUsers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvitedUsers()
    {
        return $this->invitedUsers;
    }

    /**
     * Add userEvent
     *
     * @param \cooFood\EventBundle\Entity\UserEvent $userEvent
     *
     * @return Event
     */
    public function addUserEvent(\cooFood\EventBundle\Entity\UserEvent $userEvent)
    {
        $this->userEvents[] = $userEvent;

        return $this;
    }

    /**
     * Remove userEvent
     *
     * @param \cooFood\EventBundle\Entity\UserEvent $userEvent
     */
    public function removeUserEvent(\cooFood\EventBundle\Entity\UserEvent $userEvent)
    {
        $this->userEvents->removeElement($userEvent);
    }

    /**
     * Get userEvents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserEvents()
    {
        return $this->userEvents;
    }

    /**
     * Set idSupplier
     *
     * @param \cooFood\SupplierBundle\Entity\Supplier $idSupplier
     *
     * @return Event
     */
    public function setIdSupplier(\cooFood\SupplierBundle\Entity\Supplier $idSupplier = null)
    {
        $this->idSupplier = $idSupplier;

        return $this;
    }

    /**
     * Get idSupplier
     *
     * @return \cooFood\SupplierBundle\Entity\Supplier
     */
    public function getIdSupplier()
    {
        return $this->idSupplier;
    }

    /**
     * Set idUser
     *
     * @param \cooFood\UserBundle\Entity\User $idUser
     *
     * @return Event
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
     * @Assert\IsTrue(message = "Užsakymų pabaigos data turi būti prieš renginio pradžią.")
     */
    public function isDateCorrect()
    {
        return($this->getEventDate() < $this->getOrderDeadlineDate());
    }
}
