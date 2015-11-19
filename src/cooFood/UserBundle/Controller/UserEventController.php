<?php

namespace cooFood\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use cooFood\EventBundle\Entity\UserEvent;
use cooFood\UserBundle\Form\UserEventType;

/**
 * UserEvent controller.
 *
 * @Route("/userevent")
 */
class UserEventController extends Controller
{


    //Komentuotu daliu dar gali prireikti !!! <-----------------------------------------------



//    /**
//     * Lists all UserEvent entities.
//     *
//     * @Route("/", name="userevent")
//     * @Method("GET")
//     * @Template()
//     */
//    public function indexAction()
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entities = $em->getRepository('cooFoodUserBundle:UserEvent')->findAll();
//
//        return array(
//            'entities' => $entities,
//        );
//    }
    /**
     * Creates a new UserEvent entity.
     *
     * @Route("/{eventId}", name="userevent_create")
     * @Method("POST")
     * @Template("cooFoodUserBundle:UserEvent:new.html.twig")
     */
    public function createAction($eventId)//Request $request)
    {
        $entity = new UserEvent();
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $id = $user->getId();

        $entity->setIdUser($id);
        $entity->setIdEvent($eventId);
        $entity->setPaid(1);
        $entity->setAcceptedUser(0);
        $entity->setAcceptedHost(0);

        $em = $this->getDoctrine()->getManager();

        $em->persist($entity);
        $em->flush();

        return $this->redirectToRoute('homepage');
       }

    /**
     * Deletes a UserEvent entity.
     *
     * @Route("/{eventId}", name="userevent_delete")
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction($eventId)
    {
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $id = $user->getId();

        $em = $this->getDoctrine()->getManager();
        $userEvent = $em->getRepository('cooFoodEventBundle:UserEvent')->findByidUser($id);

        foreach($userEvent as $event)
        {
            if($event->getidEvent() == $eventId) {
                $em->remove($event);
                $em->flush();
            }
        }

        return $this->redirectToRoute('homepage');
    }
//
//    /**
//     * Creates a form to create a UserEvent entity.
//     *
//     * @param UserEvent $entity The entity
//     *
//     * @return \Symfony\Component\Form\Form The form
//     */
//    private function createCreateForm(UserEvent $entity)
//    {
//        $form = $this->createForm(new UserEventType(), $entity, array(
//            'action' => $this->generateUrl('userevent_create'),
//            'method' => 'POST',
//        ));
//
//        $form->add('submit', 'submit', array('label' => 'Create'));
//
//        return $form;
//    }
//
//    /**
//     * Displays a form to create a new UserEvent entity.
//     *
//     * @Route("/new", name="userevent_new")
//     * @Method("GET")
//     * @Template()
//     */
//    public function newAction()
//    {
//        $entity = new UserEvent();
//        $form   = $this->createCreateForm($entity);
//
//        return array(
//            'entity' => $entity,
//            'form'   => $form->createView(),
//        );
//    }
//
//    /**
//     * Finds and displays a UserEvent entity.
//     *
//     * @Route("/{id}", name="userevent_show")
//     * @Method("GET")
//     * @Template()
//     */
//    public function showAction($id)
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('cooFoodUserBundle:UserEvent')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find UserEvent entity.');
//        }
//
//        $deleteForm = $this->createDeleteForm($id);
//
//        return array(
//            'entity'      => $entity,
//            'delete_form' => $deleteForm->createView(),
//        );
//    }
//
//    /**
//     * Displays a form to edit an existing UserEvent entity.
//     *
//     * @Route("/{id}/edit", name="userevent_edit")
//     * @Method("GET")
//     * @Template()
//     */
//    public function editAction($id)
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('cooFoodUserBundle:UserEvent')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find UserEvent entity.');
//        }
//
//        $editForm = $this->createEditForm($entity);
//        $deleteForm = $this->createDeleteForm($id);
//
//        return array(
//            'entity'      => $entity,
//            'edit_form'   => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        );
//    }
//
//    /**
//    * Creates a form to edit a UserEvent entity.
//    *
//    * @param UserEvent $entity The entity
//    *
//    * @return \Symfony\Component\Form\Form The form
//    */
//    private function createEditForm(UserEvent $entity)
//    {
//        $form = $this->createForm(new UserEventType(), $entity, array(
//            'action' => $this->generateUrl('userevent_update', array('id' => $entity->getId())),
//            'method' => 'PUT',
//        ));
//
//        $form->add('submit', 'submit', array('label' => 'Update'));
//
//        return $form;
//    }




//    /**
//     * Edits an existing UserEvent entity.
//     *
//     * @Route("/{id}", name="userevent_update")
//     * @Method("PUT")
//     * @Template("cooFoodUserBundle:UserEvent:edit.html.twig")
//     */
//    public function updateAction(Request $request, $id)
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('cooFoodUserBundle:UserEvent')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find UserEvent entity.');
//        }
//
//        $deleteForm = $this->createDeleteForm($id);
//        $editForm = $this->createEditForm($entity);
//        $editForm->handleRequest($request);
//
//        if ($editForm->isValid()) {
//            $em->flush();
//
//            return $this->redirect($this->generateUrl('userevent_edit', array('id' => $id)));
//        }
//
//        return array(
//            'entity'      => $entity,
//            'edit_form'   => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        );
//    }
//    /**
//     * Deletes a UserEvent entity.
//     *
//     * @Route("/{id}", name="userevent_delete")
//     * @Method("DELETE")
//     */
//    public function deleteAction(Request $request, $id)
//    {
//        $form = $this->createDeleteForm($id);
//        $form->handleRequest($request);
//
//        if ($form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $entity = $em->getRepository('cooFoodUserBundle:UserEvent')->find($id);
//
//            if (!$entity) {
//                throw $this->createNotFoundException('Unable to find UserEvent entity.');
//            }
//
//            $em->remove($entity);
//            $em->flush();
//        }
//
//        return $this->redirect($this->generateUrl('userevent'));
//    }

//    /**
//     * Creates a form to delete a UserEvent entity by id.
//     *
//     * @param mixed $id The entity id
//     *
//     * @return \Symfony\Component\Form\Form The form
//     */
//    private function createDeleteForm($id)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('userevent_delete', array('id' => $id)))
//            ->setMethod('DELETE')
//            ->add('submit', 'submit', array('label' => 'Delete'))
//            ->getForm()
//        ;
//    }
}
