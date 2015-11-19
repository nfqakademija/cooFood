<?php

namespace cooFood\UserBundle\Controller;

use cooFood\UserBundle\Entity\OrderItem;
use cooFood\UserBundle\Form\OrderItemType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use cooFood\UserBundle\Entity\UserEvent;
use cooFood\UserBundle\Form\UserEventType;
/**
 * Order controller.
 *
 * @Route()
 */

class OrderController extends Controller
{
    /**
     * Finds and displays a UserEvent entity.
     * @Template("cooFoodUserBundle:Order:order.html.twig")
     */
    public function indexAction($idSupplier)
    {
        $em = $this->getDoctrine()->getManager();
        $productsRepository = $em->getRepository('cooFoodSupplierBundle:Product');
        $products = $productsRepository->findBySupplier($idSupplier);

        $orderItem = new OrderItem();
        $orderItem->setIdProduct(5);
        $orderItem->setShareLimit(5);

        $form = $this->createForm(new OrderItemType($idSupplier->getId()), $orderItem);

        return (array(
            'form' => $form->createView(),
            'products' => $products,

        ));
    }
}
