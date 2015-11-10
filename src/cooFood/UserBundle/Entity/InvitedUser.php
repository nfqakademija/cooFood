<?php

namespace cooFood\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InvitedUser
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class InvitedUser
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
     * @var string
     *
     * @ORM\Column(name="id_event", type="integer")
     */
    private $idEvent;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="secret_code", type="string", length=255)
     */
    private $secretCode;


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
     * Set idEvent
     *
     * @param string $idEvent
     *
     * @return InvitedUser
     */
    public function setIdEvent($idEvent)
    {
        $this->idEvent = $idEvent;

        return $this;
    }

    /**
     * Get idEvent
     *
     * @return string
     */
    public function getIdEvent()
    {
        return $this->idEvent;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return InvitedUser
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set secretCode
     *
     * @param string $secretCode
     *
     * @return InvitedUser
     */
    public function setSecretCode($secretCode)
    {
        $this->secretCode = $secretCode;

        return $this;
    }

    /**
     * Get secretCode
     *
     * @return string
     */
    public function getSecretCode()
    {
        return $this->secretCode;
    }
}

