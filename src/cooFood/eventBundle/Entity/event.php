<?php

namespace cooFood\eventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use cooFood\eventBundle\Entity\eventOrder;

/**
 * event
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="cooFood\eventBundle\Entity\eventRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class event
{
    public function __construct() {
        $this->orders = new ArrayCollection();
    }

    /**
     * @ORM\OneToMany(targetEntity="cooFood\eventBundle\Entity\eventOrder", mappedBy="idEvent")
     */
    private $orders;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
    private $joinDateStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="join_date_end", type="datetime")
     */
    private $joinDateEnd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="order_deadline_date", type="datetime")
     */
    private $orderDeadlineDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_city", type="integer")
     */
    private $idCity;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_address", type="integer")
     */
    private $idAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="place", type="string", length=255)
     */
    private $place;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_supplier", type="integer")
     */
    private $idSupplier;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_user", type="integer")
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
     * @return event
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
     * @return event
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
     * Set joinDateStart
     *
     * @param \DateTime $joinDateStart
     *
     * @return event
     */
    public function setJoinDateStart($joinDateStart)
    {
        $this->joinDateStart = $joinDateStart;

        return $this;
    }

    /**
     * Get joinDateStart
     *
     * @return \DateTime
     */
    public function getJoinDateStart()
    {
        return $this->joinDateStart;
    }

    /**
     * Set joinDateEnd
     *
     * @param \DateTime $joinDateEnd
     *
     * @return event
     */
    public function setJoinDateEnd($joinDateEnd)
    {
        $this->joinDateEnd = $joinDateEnd;

        return $this;
    }

    /**
     * Get joinDateEnd
     *
     * @return \DateTime
     */
    public function getJoinDateEnd()
    {
        return $this->joinDateEnd;
    }

    /**
     * Set orderDeadlineDate
     *
     * @param \DateTime $orderDeadlineDate
     *
     * @return event
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
     * Set idCity
     *
     * @param integer $idCity
     *
     * @return event
     */
    public function setIdCity($idCity)
    {
        $this->idCity = $idCity;

        return $this;
    }

    /**
     * Get idCity
     *
     * @return integer
     */
    public function getIdCity()
    {
        return $this->idCity;
    }

    /**
     * Set idAddress
     *
     * @param integer $idAddress
     *
     * @return event
     */
    public function setIdAddress($idAddress)
    {
        $this->idAddress = $idAddress;

        return $this;
    }

    /**
     * Get idAddress
     *
     * @return integer
     */
    public function getIdAddress()
    {
        return $this->idAddress;
    }

    /**
     * Set place
     *
     * @param string $place
     *
     * @return event
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return event
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
     * Set idSupplier
     *
     * @param integer $idSupplier
     *
     * @return event
     */
    public function setIdSupplier($idSupplier)
    {
        $this->idSupplier = $idSupplier;

        return $this;
    }

    /**
     * Get idSupplier
     *
     * @return integer
     */
    public function getIdSupplier()
    {
        return $this->idSupplier;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return event
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
     * Set visible
     *
     * @param boolean $visible
     *
     * @return event
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
     * @return event
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
     * @ORM\PrePersist
     */
    public function prePersist() {
        // this will be executed before new object pushed to database
    }
}

