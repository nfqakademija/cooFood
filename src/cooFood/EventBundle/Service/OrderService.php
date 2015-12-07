<?php

namespace cooFood\EventBundle\Service;

use cooFood\EventBundle\Entity\OrderItem;
use cooFood\EventBundle\Entity\SharedOrder;
use cooFood\EventBundle\Entity\UserEvent;
use cooFood\EventBundle\Form\OrderItemType;
use cooFood\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OrderService
{
    private $em;
    private $formFactory;
    private $user;

    private $orderItemsRepository;
    private $userEventsRepository;
    private $sharedOrdersRepository;

    private $activeOrderItem;
    private $activeOrders;

    public function __construct(EntityManager $em, $tokenStorage, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->user = $tokenStorage->getToken(0)->getUser();

        $this->userEventsRepository = $this->em->getRepository('cooFoodEventBundle:UserEvent');
        $this->orderItemsRepository = $this->em->getRepository('cooFoodEventBundle:OrderItem');
        $this->sharedOrdersRepository = $this->em->getRepository('cooFoodEventBundle:SharedOrder');
    }


    public function createOrderForm($idSupplier)
    {
        $this->activeOrderItem = $this->defaultOrderItem();
        $form = $this->formFactory->create(
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

    public function getAllEventOrdersInfo($idEvent)
    {
        $baseOrders =  $this->orderItemsRepository->findBaseOrders($idEvent);
        $otherOrders =   $this->orderItemsRepository->findBaseOrdersDuplicates($idEvent, $baseOrders);

        $eventOrders = array();
        $priceArr = array();
        $quantityArr = array();

        $cost = 0;
        $supplier = null;
        foreach($baseOrders as $order) {
            if($supplier == null)
                $supplier = $order->getIdProduct()->getSupplier();

            $price = $order->getIdProduct()->getPrice() * $order->getQuantity();
            $quantity = $order->getQuantity();
            foreach ($otherOrders as $oOrder)
            {
                if ($order->getIdProduct() == $oOrder->getIdProduct()) {
                    $quantity += $oOrder->getQuantity();
                    $price += $oOrder->getIdProduct()->getPrice() * $oOrder->getQuantity();
                }
            }
            $cost += $price;
            array_push($priceArr, $price);
            array_push($quantityArr, $quantity);
            array_push($eventOrders, $order);
        }
        $eventOrders['price'] = $priceArr;
        $eventOrders['quantity'] = $quantityArr;
        $eventOrders['cost'] = $cost;
        $eventOrders['supplier'] = $supplier;

        return $eventOrders;
    }

    public function getUserOrdersInfo($idEvent)
    {
        $usersRepository = $this->em->getRepository('cooFoodUserBundle:User');
        $users =  $usersRepository->findEventUsers($idEvent);

        $simpleOrders = array();
        $ordersCost = array();
        $sharedOrders = array();
        $debt = array();

        foreach($users as $user)
        {
            $orders = $this->orderItemsRepository->findSimpleUserOrders($user, $idEvent);
            $price = 0;
            $userEvent = new UserEvent();

            foreach($orders as $order)
            {
                $price += $order->getIdProduct()->getPrice() * $order->getQuantity();
                if ($order === end($orders))
                    $userEvent = $order->getIdUserEvent();
            }

            $userSharedOrders = $this->sharedOrdersRepository->findUserSharedOrders($user, $idEvent);
            $tmpShared = array();

            foreach($userSharedOrders as $sharedOrder)
            {
                $sharedItem = $sharedOrder->getIdOrderItem();
                $itemInfo = $sharedItem->getIdProduct()->getName() . ' ' . $sharedItem->getQuantity() .
                    'vnt.' .  PHP_EOL . ' Dalinasi: ';
                $namesList = '';
                $count = 0;
                $orderUsers = $this->sharedOrdersRepository->findByidOrderItem($sharedOrder->getIdOrderItem());

                //show users who also joins this order
                foreach ($orderUsers as $usr) {
                    $u = $usr->getIdUser();
                    $namesList .= $u->getName() . ' ' . $u->getSurname() . ' ' . ";";
                    $count++;
                }

                $price += $sharedItem->getIdProduct()->getPrice() * $sharedItem->getQuantity() / $count;
                $itemInfo .= $namesList;
                array_push($tmpShared, $itemInfo);
            }
            $paid = $price - $userEvent->getPaid();

            array_push($ordersCost,  round($price, 2));
            array_push($sharedOrders, $tmpShared);
            array_push($simpleOrders, $orders);
            array_push($debt,  round($paid, 2));
        }

        $users['orders'] = $simpleOrders;
        $users['sharedOrders'] = $sharedOrders;
        $users['cost'] = $ordersCost;
        $users['debt'] = $debt;

        return $users;
    }

    private function defaultOrderItem()
    {
        $orderItem = new OrderItem();
        $orderItem->setQuantity(1);
        $orderItem->setShareLimit(1);
        return $orderItem;
    }

}

