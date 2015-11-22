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

        //atvaizdavimo dalis: atskiriam kur orderis private, o kur shared
        $orders = $userEvent->getOrderItems();
        $myOrders = array();
        $mySharedOrders = array();

        $sharedOrdersRepository = $em->getRepository('cooFoodEventBundle:SharedOrder');
        $allSharedOrders = $sharedOrdersRepository->createQueryBuilder('q')
            ->groupBy('q.idOrderItem')
            ->getQuery()
            ->execute();

        foreach ($allSharedOrders as $order) {
                array_push($mySharedOrders, $order->getIdOrderItem());
        }

        foreach ($orders as $order) {
            if (!($order->getShareLimit() > 1))
                array_push($myOrders, $order);
        }

        return (array(
            'form' => $form->createView(),
            'orders' => $myOrders,
            'mySharedOrders' => $mySharedOrders,
            'supplier' => $idSupplier,
            'event' => $idEvent,

        ));
    }

    /**
     * Deletes a OrderItem entity.
     *
     * @Route("/{idOrderItem}/{idEvent}", name="order_delete")
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction($idOrderItem, $idEvent) //neturetu vps viek su sharinamais
    {
        $em = $this->getDoctrine()->getManager();
        $orderItemsRepository = $em->getRepository('cooFoodEventBundle:OrderItem');
        $sharedOrdersRepository = $em->getRepository('cooFoodEventBundle:SharedOrder');

        $orderItem = $orderItemsRepository->findOneById($idOrderItem);
        $sharedOrders = $sharedOrdersRepository->findByIdOrderItem($orderItem);
        //kol kas trinam ir sharinamus, po to teises kazkam perduosim

        foreach ($sharedOrders as $sharedOrder) {
            if ($sharedOrder->getIdOrderItem() == $orderItem) {
                $em->remove($sharedOrder);
            }
        }
        $em->remove($orderItem);
        $em->flush();

        return $this->redirectToRoute('event_show', array('id' => $idEvent));
    }
}
