<?php

namespace cooFood\EventBundle\Service;

use cooFood\EventBundle\Entity\OrderItem;
use cooFood\EventBundle\Entity\SharedOrder;
use cooFood\EventBundle\Entity\UserEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PayForOrderService
{
    private $em;
    private $container;
    private $user;
    private $userId;
    private $eventId;
    private $totalAmount = 0;
    private $userEventRepository;
    private $userEvent;
    private $orderItemRepository;
    private $sharedOrderRepository;


    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
        $this->user = $this->container->get('security.token_storage')->getToken()->getUser();
        $this->userId = $this->user->getId();

        $this->userEventRepository = $this->em->getRepository('cooFoodEventBundle:UserEvent');
        $this->orderItemRepository = $this->em->getRepository('cooFoodEventBundle:OrderItem');
        $this->sharedOrderRepository = $this->em->getRepository('cooFoodEventBundle:SharedOrder');
    }

    public function setEventId($id) {
        $this->eventId = $id;
    }

    public function getOrderItems() {
        $this->userEvent = $this->userEventRepository->findOneBy(array('idEvent' => $this->eventId, 'idUser' => $this->userId));
        return $this->orderItemRepository->findBy(array('idUserEvent' => $this->userEvent->getId()));
    }

    public function getTotalAmount() {
        foreach ($this->getOrderItems() as $order) {
            // if whole order for myself
            if ($order->getShareLimit() == 1) {
                $price = $order->getIdProduct()->getPrice();
                $amount = $order->getQuantity();
                $this->totalAmount += $price * $amount;
            }
        }
        // if joined shared order
        $this->addSharedOrders();

        // minus already paid sum
        $this->totalAmount -= $this->userEvent->getPaid();
        return $this->totalAmount;
    }

    private function addSharedOrders() {
        $sharedOrders = $this->sharedOrderRepository->findBy(array('idUser' => $this->userId));

        foreach ($sharedOrders as $sharedOrder) {
            if ($sharedOrder->getIdOrderItem()->getIdUserEvent()->getIdEvent()->getId() == $this->eventId) {
                $amount = $sharedOrder->getIdOrderItem()->getQuantity();
                $shareCount = count($this->sharedOrderRepository->findBy(array('idOrderItem' => $sharedOrder->getIdOrderItem()->getId())));
                $price = $sharedOrder->getIdOrderItem()->getIdProduct()->getPrice();
                $total = $price * $amount / $shareCount;
                $this->totalAmount += round(ceil($total*1000)/1000,2);
            }
        }
    }

}