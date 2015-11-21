<?php

namespace cooFood\EventBundle\Controller;

use cooFood\EventBundle\Entity\OrderItem;
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

        $form = $this->createForm(new OrderItemType($idSupplier), $orderItem);//, array(
        $form->handleRequest($request);

        if ($form->isValid()) {
            $orderItem->setIdUserEvent($userEvent);
            $em->persist($orderItem);
            $em->flush();
            return $this->redirectToRoute('event_show', array('id' => $idEvent));
        }

        $orders = $userEvent->getOrderItems();

        return (array(
            'form' => $form->createView(),
            'orders' => $orders,
            'supplier' => $idSupplier,
            'event' => $idEvent,

        ));
    }
}