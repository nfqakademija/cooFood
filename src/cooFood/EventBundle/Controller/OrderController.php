<?php

namespace cooFood\EventBundle\Controller;

use cooFood\EventBundle\Entity\OrderItem;
use cooFood\EventBundle\Entity\SharedOrder;
use cooFood\EventBundle\Form\OrderItemType;
use cooFood\SupplierBundle\Entity\Supplier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $orderService = $this->get("order");

        $form = $orderService->createOrderForm($idSupplier);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $orderService->createOrder($idEvent);
            return new JsonResponse(array(
                'id' => $idEvent,
                'html' => $this->forward('cooFoodEventBundle:Order:display', array(
                    'idSupplier' => $idSupplier,
                    'idEvent' => $idEvent
                ))->getContent()
            ), 200);
        }

        $myOrders = $orderService->getMyOrders($idEvent);
        $mySharedOrders = $orderService->getMySharedOrders($idEvent);
        $sharedOrders = $orderService->getSharedOrders($idEvent);

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
     * Finds and displays a UserEvent entity.
     *
     * @Route("/test/{idSupplier}/{idEvent}", name="order_display")
     * @Method("GET")
     * @Template("cooFoodEventBundle:Order:order.html.twig")
     */
    public function displayAction($idSupplier, $idEvent)
    {
        $orderService = $this->get("order");

        $form = $orderService->createOrderForm($idSupplier);
        $myOrders = $orderService->getMyOrders($idEvent);
        $mySharedOrders = $orderService->getMySharedOrders($idEvent);
        $sharedOrders = $orderService->getSharedOrders($idEvent);

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
     * @Route("/{idOrderItem}/{idEvent}/{idSupplier}", name="order_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction($idOrderItem, $idEvent, $idSupplier)
    {
        $orderService = $this->get("order");
        $orderService->deleteOrder($idOrderItem, $idEvent);

        return new JsonResponse(array(
            'id' => $idEvent,
            'html' => $this->forward('cooFoodEventBundle:Order:display', array(
                'idSupplier' => $idSupplier,
                'idEvent' => $idEvent
            ))->getContent()
        ), 200);
    }

    /**
     * Creates a SharedOrder entity.
     *
     * @Route("/order/{idOrderItem}/{idEvent}/{idSupplier}", name="sharedOrder_create")
     * @Method({"GET"})
     */
    public function createAction($idOrderItem, $idEvent, $idSupplier)
    {
        $orderService = $this->get("order");
        $orderService->createSharedOrder($idOrderItem);

        return new JsonResponse(array(
            'id' => $idEvent,
            'html' => $this->forward('cooFoodEventBundle:Order:display', array(
                'idSupplier' => $idSupplier,
                'idEvent' => $idEvent
            ))->getContent()
        ), 200);
    }
}
