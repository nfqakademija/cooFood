<?php

namespace cooFood\EventBundle\Service;


use cooFood\EventBundle\Entity\OrderItem;
use cooFood\EventBundle\Entity\SharedOrder;
use cooFood\EventBundle\Form\OrderItemType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderService
{
    private $em;
    private $container;
    private $user;

    private $orderItemsRepository;
    private $userEventsRepository;
    private $sharedOrdersRepository;

    private $activeOrderItem;
    private $activeOrders;

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
        $this->user = $this->container->get('security.token_storage')->getToken()->getUser();
        $this->setRepositories();
    }


    public function createOrderForm($idSupplier)
    {
        $this->activeOrderItem = $this->defaultOrderItem();
        $form = $this->container->get('form.factory')->create(
            new OrderItemType($idSupplier),
            $this->activeOrderItem
        );

        return $form;
    }

    public function createOrder($idEvent)
    {
        $userEvent = $this->userEventsRepository->findOneBy(array(
            'idUser' => $this->user->getId(),
            'idEvent' => $idEvent
        ));

        $orderItem = $this->activeOrderItem;
        $orderItem->setIdUserEvent($userEvent);

        //check if simple order with same fields(except quantity) exist
        $prevOrderItem = $this->orderItemsRepository->findOneBy(
            array(
                'idProduct' => $orderItem->getIdProduct(),
                'idUserEvent' => $userEvent,
                'shareLimit' => 1
            )
        );

        //update quantity of existing order or create new
        if ($prevOrderItem != null
            && ($prevOrderItem->getShareLimit() == 1)
            && ($orderItem->getShareLimit() == 1)
        ) {
            $prevOrderItem->setQuantity($prevOrderItem->getQuantity() + $orderItem->getQuantity());
            $this->em->persist($prevOrderItem);
        } else {
            $this->em->persist($orderItem);
        }
        $this->em->flush();

        //shared order part
        if ($orderItem->getShareLimit() > 1) {
            $this->createSharedOrder($orderItem->getId());
        }
    }

    public function createSharedOrder($idOrderItem)
    {
        $orderItem = $this->orderItemsRepository->findOneById($idOrderItem);

        $sharedOrder = new SharedOrder();
        $sharedOrder->setIdUser($this->user);
        $sharedOrder->setIdOrderItem($orderItem);

        $this->em->persist($sharedOrder);
        $this->em->flush();
    }

    public function deleteOrder($idOrderItem, $idEvent)
    {
        $orderItem = $this->orderItemsRepository->findOneById($idOrderItem);
        $sharedOrders = $this->sharedOrdersRepository->findByIdOrderItem($orderItem);

        $removed = false;

        //for shared orders
        foreach ($sharedOrders as $sharedOrder) {
            if ($sharedOrder->getIdUser() == $this->user) {
                $removed = true;
                $this->em->remove($sharedOrder);
                $this->em->flush();

                //if someone already joined order, change owner to someone else
                if ($orderItem->getidUserEvent()->getIdUser() == $this->user && count($sharedOrders) != 1) {
                    $shared = $this->sharedOrdersRepository->findOneByIdOrderItem($orderItem);
                    $userEvents = $shared->getIdUser()->getUserEvents();
                    foreach ($userEvents as $userEvent) {
                        if ($userEvent->getIdEvent()->getId() == $idEvent) {
                            $orderItem->setIdUserEvent($userEvent);
                            $this->em->persist($orderItem);
                            break;
                        }
                    }
                }

                //if user is alone in current order delete it also
                if (count($sharedOrders) == 1) {
                    $this->em->remove($orderItem);
                }
                break;
            }
        }

        //for basic orders
        if (!$removed) {
            $this->em->remove($orderItem);
        }

        $this->em->flush();
    }

    public function getMyOrders($idEvent)
    {
        $userEvent = $this->userEventsRepository->findOneBy(array(
            'idUser' => $this->user->getId(),
            'idEvent' => $idEvent
        ));

        $orders = $userEvent->getOrderItems();
        $myOrders = array();

        foreach ($orders as $order) {
            if (!($order->getShareLimit() > 1)) {
                array_push($myOrders, $order);

            }
        }

        return $myOrders;
    }

    public function getMySharedOrders($idEvent)
    {
        $mySharedOrdersRaw = $this->sharedOrdersRepository->findUserSharedOrders($this->user, $idEvent);

        $this->activeOrders = array();
        $mySharedOrders = array();
        $sharedOrderUsers = array();

        foreach ($mySharedOrdersRaw as $order) {
            array_push($this->activeOrders, $order->getIdOrderItem());
            array_push($mySharedOrders, $order->getIdOrderItem());

            $users = $this->sharedOrdersRepository->findByidOrderItem($order->getIdOrderItem());
            $info = array();

            //track which users already joined order
            foreach ($users as $usr) {
                if ($usr->getIdUser() == $this->user) {
                    $me = $usr->getIdUser();
                    $me->setName("Aš");
                    $me->setSurname("");
                    $me->setEmail("");
                    $info[] = $me;
                } else {
                    $info[] = $usr->getIdUser();
                }
            }
            $sharedOrderUsers[] = $info;
        }
        $mySharedOrders['users'] = $sharedOrderUsers;

        return $mySharedOrders;
    }

    public function getSharedOrders($idEvent)
    {
        $sharedOrderUsers = array();
        $sharedOrders = array();

        if (!empty($this->activeOrders)) {
            $sharedOrdersRaw = $this->sharedOrdersRepository
                ->findNotUserSharedOrders($this->user, $idEvent, $this->activeOrders);
        } else {
            $sharedOrdersRaw = $this->sharedOrdersRepository
                ->findAllUserSharedOrders($this->user, $idEvent);
        }

        foreach ($sharedOrdersRaw as $order) {
            $sharedCount = $this->sharedOrdersRepository->getOrderUserCount($order);

            if ($sharedCount != $order->getIdOrderItem()->getShareLimit()) {
                array_push($sharedOrders, $order->getIdOrderItem());

                $users = $this->sharedOrdersRepository->findBy(array('idOrderItem' => $order->getIdOrderItem()));

                $info = array();
                foreach ($users as $usr) {
                    $info[] = $usr->getIdUser();
                }
                $sharedOrderUsers[] = $info;
            }
        }
        $sharedOrders['users'] = $sharedOrderUsers;

        return $sharedOrders;
    }

    public function getAllEventOrders($idEvent)
    {

        //$allOrders = $this->orderItemsRepository->fin
         $query = $this->orderItemsRepository->createQueryBuilder('oi')
            ->select('oi')
             ->leftJoin('oi.idUserEvent', 'ue', 'WITH', 'ue = oi.idUserEvent')
             ->leftJoin('ue.idEvent', 'e', 'WITH', 'e.id = :eventId')
             ->where('e.id = :eventId')//, 'oi.shareLimit = 1')//quantity
             ->groupBy('oi.idProduct')
             ->setParameter('eventId', $idEvent)
             ->getQuery();
        $baseOrders =  $query->getResult();

        $query = $this->orderItemsRepository->createQueryBuilder('oi')
            ->select('oi')
            ->leftJoin('oi.idUserEvent', 'ue', 'WITH', 'ue = oi.idUserEvent')
            ->leftJoin('ue.idEvent', 'e', 'WITH', 'e.id = :eventId')
            ->where('e.id = :eventId', 'oi NOT IN (:base)')//,'oi.shareLimit > 1')//quantity
            ->setParameter('eventId', $idEvent)
            ->setParameter('base', $baseOrders)
            ->getQuery();
        $otherOrders =  $query->getResult();

        $eventOrders = array();
        $priceArr = array();
        foreach($baseOrders as $order) {
            $price = $order->getIdProduct()->getPrice() * $order->getQuantity();
            foreach ($otherOrders as $oOrder)
            {
                if ($order->getIdProduct() == $oOrder->getIdProduct()) {
                    $order->setQuantity($order->getQuantity() + $oOrder->getQuantity());
                    $price += $oOrder->getIdProduct()->getPrice() * $oOrder->getQuantity();
                }
            }
            array_push($priceArr, $price);
            array_push($eventOrders, $order);
        }
        $eventOrders['price'] = $priceArr;
        return $eventOrders;
    }

    private function defaultOrderItem()
    {
        $orderItem = new OrderItem();
        $orderItem->setQuantity(1);
        $orderItem->setShareLimit(1);
        return $orderItem;
    }

    private function setRepositories()
    {
        $this->userEventsRepository = $this->em->getRepository('cooFoodEventBundle:UserEvent');
        $this->orderItemsRepository = $this->em->getRepository('cooFoodEventBundle:OrderItem');
        $this->sharedOrdersRepository = $this->em->getRepository('cooFoodEventBundle:SharedOrder');
    }
}

