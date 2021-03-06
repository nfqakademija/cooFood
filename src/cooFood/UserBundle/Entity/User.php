<?php
namespace cooFood\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="cooFood\UserBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="cooFood\EventBundle\Entity\UserEvent", mappedBy="idUser")
     */
    private $userEvents;

    /**
     * @ORM\OneToMany(targetEntity="cooFood\EventBundle\Entity\SharedOrder", mappedBy="idUser")
     */
    private $sharedOrders;

    /**
     * @ORM\OneToMany(targetEntity="cooFood\EventBundle\Entity\Event", mappedBy="idUser")
     */
    private $events;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $surname;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
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
     * Set email
     *
     * @return string
     */
    public function setEmail($email)
    {
        $email = is_null($email) ? '' : $email;
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }

    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Add userEvent
     *
     * @param \cooFood\EventBundle\Entity\UserEvent $userEvent
     *
     * @return User
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
     * Add sharedOrder
     *
     * @param \cooFood\EventBundle\Entity\SharedOrder $sharedOrder
     *
     * @return User
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
     * Add event
     *
     * @param \cooFood\EventBundle\Entity\Event $event
     *
     * @return User
     */
    public function addEvent(\cooFood\EventBundle\Entity\Event $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Remove event
     *
     * @param \cooFood\EventBundle\Entity\Event $event
     */
    public function removeEvent(\cooFood\EventBundle\Entity\Event $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }
}
