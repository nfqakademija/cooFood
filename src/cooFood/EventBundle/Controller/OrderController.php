<?php

namespace cooFood\EventBundle\Controller;

use cooFood\EventBundle\Entity\OrderItem;
use cooFood\EventBundle\Entity\SharedOrder;
use cooFood\EventBundle\Form\OrderItemType;
use cooFood\SupplierBundle\Entity\Supplier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use cooFood\EventBundle\Entity\UserEvent;
use cooFood\UserBundle\Form\UserEventType;

/**
 * Order controller.
 *
 * @Route("/order")
 */
class OrderController extends Controller
{
    /**
     * Finds and displays a UserEvent entity.
     *
     * @Route("/{idSupplier}/{idEvent}", name="order_create")
     * @Method("POST")
     * @Template("cooFoodEventBundle:Order:order.html.twig")
     */
    public function indexAction(Request $request, $idSupplier, $idEvent)
    {
        $em = $this->getDoctrine()->getManager();
        $userEventsRepository = $em->getRepository('cooFoodEventBundle:UserEvent');
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $userEvent = $userEventsRepository->findOneBy(array('idUser' => $user->getId(), 'idEvent' => $idEvent));


        $orderItem = new OrderItem();
        $orderItem->setQuantity(1);
        $orderItem->setShareLimit(1);

        $form = $this->createForm(new OrderItemType($idSupplier), $orderItem);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $orderItem->setIdUserEvent($userEvent);
            $orderItemsRepository = $em->getRepository('cooFoodEventBundle:OrderItem');
            $prevOrderItem = $orderItemsRepository->findOneBy(
                array(
                    'idProduct' => $orderItem->getIdProduct(),
                    'idUserEvent' => $userEvent,//wtf kaip praleidau pirmai
                    'shareLimit' => 1
                )
            );

            //--------------------------------------
            //tikrinam ar nepakeisim sharinamojo jei lipdysim prie esanciojo
            if ($prevOrderItem != null
                && ($prevOrderItem->getShareLimit() == 1)
                && ($orderItem->getShareLimit() == 1)
            ) {
                $prevOrderItem->setQuantity($prevOrderItem->getQuantity() + $orderItem->getQuantity());
                $em->persist($prevOrderItem);
            } else {
                $em->persist($orderItem);
            }
            $em->flush();

            //shared order part
            if ($orderItem->getShareLimit() > 1) {
                $sharedOrder = new SharedOrder();
                $sharedOrder->setIdOrderItem($orderItem);
                $sharedOrder->setIdUser($user);
                $em->persist($sharedOrder);
                $em->flush();
            }

            return $this->redirectToRoute('event_show', array('id' => $idEvent));
        }

        //atvaizdavimo dalis: atskiriam kur orderis private, kur mano shared, o kur tiesiog shared
        $orders = $userEvent->getOrderItems();
        $myOrders = array();
        $mySharedOrders = array();
        $sharedOrders = array();
        $products = array();

        $sharedOrdersRepository = $em->getRepository('cooFoodEventBundle:SharedOrder');

        $mySharedOrdersQuery = $sharedOrdersRepository->createQueryBuilder('q')
            ->where('q.idUser = :user')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->execute();

        $sharedOrdersQuery = $sharedOrdersRepository->createQueryBuilder('q')
            ->where('q.idUser != :user')
            ->groupBy('q.idOrderItem')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->execute();

        /**
         * select event.id event_id, event.name event_name, user_event.id user_event_id, user_event.id_user, order_item.id order_item_id, order_item.quantity from event left join UserEvent user_event on event.id = user_event.id_event left join order_item on user_event.id = order_item.id_user_event;
         */
        $orderUsers = array();// kurie useriai dalyvauja sharinamam orderi
        foreach ($mySharedOrdersQuery as $order) {
            if ($userEvent->getIdEvent()->getId() ==
                $order->getIdOrderItem()->getIdUserEvent()->getIdEvent()->getId()
            ) {
                array_push($products, $order->getIdOrderItem()->getIdProduct()->getId());
                array_push($mySharedOrders, $order->getIdOrderItem());

                $users = $sharedOrdersRepository->findBy(array('idOrderItem' => $order->getIdOrderItem()));//pakeisti
                $info = array();
                foreach ($users as $usr) {
                    if ($usr->getIdUser() == $user) {
                        $me = $usr->getIdUser();
                        $me->setName("Aš");
                        $me->setSurname("");
                        $me->setEmail("");
                        $info[] = $me;
                    } else {
                        $info[] = $usr->getIdUser();
                    }
                }

                $orderUsers[] = $info;
            }
        }
        $mySharedOrders['users'] = $orderUsers;
        $orderUsers = array();

        foreach ($sharedOrdersQuery as $order) {
            if ($userEvent->getIdEvent()->getId() ==
                $order->getIdOrderItem()->getIdUserEvent()->getIdEvent()->getId()
            ) {
                if (!in_array($order->getIdOrderItem()->getIdProduct()->getId(), $products)) {
                    $sharedCount = $sharedOrdersRepository->createQueryBuilder('so')
                        ->select('COUNT(so)')
                        ->where('so.idOrderItem = :orderItem')
                        ->setParameter('orderItem', $order->getIdOrderItem())
                        ->getQuery()
                        ->getSingleScalarResult();
                   // die(var_dump(serialize($sharedCount)));
                    if($sharedCount != $order->getIdOrderItem()->getShareLimit()) {
                        array_push($sharedOrders, $order->getIdOrderItem());

                        $users = $sharedOrdersRepository->findBy(array('idOrderItem' => $order->getIdOrderItem()));//pakeis
                        $info = array();
                        foreach ($users as $usr) {
                            $info[] = $usr->getIdUser();
                        }

                        $orderUsers[] = $info;
                    }
                }
            }
        }
        $sharedOrders['users'] = $orderUsers;

        foreach ($orders as $order) {
            if (!($order->getShareLimit() > 1)) {
                array_push($myOrders, $order);
            }
        }

        return (array(
            'form' => $form->createView(),
            'orders' => $myOrders,
            'mySharedOrders' => $mySharedOrders,
            'sharedOrders' => $sharedOrders,
            'supplier' => $idSupplier,
            'event' => $idEvent,

        ));
    }

    /**
     * Deletes a OrderItem/SharedOrder entity.
     *
     * @Route("/{idOrderItem}/{idEvent}", name="order_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction($idOrderItem, $idEvent)
    {
        $em = $this->getDoctrine()->getManager();
        $orderItemsRepository = $em->getRepository('cooFoodEventBundle:OrderItem');
        $sharedOrdersRepository = $em->getRepository('cooFoodEventBundle:SharedOrder');
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $orderItem = $orderItemsRepository->findOneById($idOrderItem);
        $sharedOrders = $sharedOrdersRepository->findByIdOrderItem($orderItem);
        $removed = false;

        foreach ($sharedOrders as $sharedOrder) {
            if ($sharedOrder->getIdUser() == $user) {
                $removed = true;
                $em->remove($sharedOrder);
                $em->flush();

                //jei orderi palieka jo kurejas, perduodam teises kitam :service1
                if ($orderItem->getidUserEvent()->getIdUser() == $user && count($sharedOrders) != 1) {
                    $shared = $sharedOrdersRepository->findOneByIdOrderItem($orderItem);
                    $userEvents = $shared->getIdUser()->getUserEvents();
                    foreach ($userEvents as $userEvent) {
                        if ($userEvent->getIdEvent()->getId() == $idEvent) {
                            $orderItem->setIdUserEvent($userEvent);
                            $em->persist($orderItem);
                            break;
                        }
                    }
                }


                if (count($sharedOrders) == 1) { // jei paskutinis shared orderio dalyvis tai trinam ir order item
                    $em->remove($orderItem);
                }
                break;
            }
        }

        if (!$removed) {            //paprasto orderio trinimui
            $em->remove($orderItem);
        }

        $em->flush();

        return $this->redirectToRoute('event_show', array('id' => $idEvent));
    }

    /**
     * Creates a SharedOrder entity.
     *
     * @Route("/order/{idOrderItem}/{idEvent}", name="sharedOrder_create")
     * @Method({"GET"})
     */
    public function createAction($idOrderItem, $idEvent)
    {
        $securityContext = $this->container->get('security.token_storage');
        $user = $securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $orderItemsRepository = $em->getRepository('cooFoodEventBundle:OrderItem');
        $orderItem = $orderItemsRepository->findOneById($idOrderItem);

        $entity = new SharedOrder();
        $entity->setIdUser($user);
        $entity->setIdOrderItem($orderItem);

        $em->persist($entity);
        $em->flush();

        return $this->redirectToRoute('event_show', array('id' => $idEvent));
    }
}
