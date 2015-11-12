<?php
namespace cooFood\UserBundle\Entity;

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
     * @ORM\Column(type="integer", name="id_user")
     */
    protected $idUser;

    /**
     * @ORM\Column(type="integer", name="id_event")
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
     * @return UserEvent
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
     * Set idEvent
     *
     * @param integer $idEvent
     *
     * @return UserEvent
     */
    public function setIdEvent($idEvent)
    {
        $this->idEvent = $idEvent;

        return $this;
    }

    /**
     * Get idEvent
     *
     * @return integer
     */
    public function getIdEvent()
    {
        return $this->idEvent;
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
}