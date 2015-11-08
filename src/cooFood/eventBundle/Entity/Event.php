<?php
/**
 * Created by PhpStorm.
 * User: klaudijus
 * Date: 15.11.8
 * Time: 11.55
 */

namespace cooFood\eventBundle\Entity;


class Event
{
    protected $title;
    protected $eventDate;
    protected $joinDateStart;
    protected $joinDateEnd;
    protected $orderDeadline;
    protected $city;
    protected $address;
    protected $location;
    protected $summary;
    protected $supplier;
    protected $type;

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getEventDate()
    {
        return $this->eventDate;
    }

    /**
     * @param mixed $eventDate
     */
    public function setEventDate($eventDate)
    {
        $this->eventDate = $eventDate;
    }

    /**
     * @return mixed
     */
    public function getJoinDateStart()
    {
        return $this->joinDateStart;
    }

    /**
     * @param mixed $joinDateStart
     */
    public function setJoinDateStart($joinDateStart)
    {
        $this->joinDateStart = $joinDateStart;
    }

    /**
     * @return mixed
     */
    public function getJoinDateEnd()
    {
        return $this->joinDateEnd;
    }

    /**
     * @param mixed $joinDateEnd
     */
    public function setJoinDateEnd($joinDateEnd)
    {
        $this->joinDateEnd = $joinDateEnd;
    }

    /**
     * @return mixed
     */
    public function getOrderDeadline()
    {
        return $this->orderDeadline;
    }

    /**
     * @param mixed $orderDeadline
     */
    public function setOrderDeadline($orderDeadline)
    {
        $this->orderDeadline = $orderDeadline;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param mixed $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * @return mixed
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @param mixed $supplier
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $status
     */
    public function setType($type)
    {
        $this->type = $type;
    }

}