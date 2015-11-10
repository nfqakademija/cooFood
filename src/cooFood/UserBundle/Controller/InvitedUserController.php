<?php

namespace cooFood\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use cooFood\UserBundle\Entity\InvitedUser;
use cooFood\UserBundle\Form\InvitedUserType;

/**
 * InvitedUser controller.
 *
 * @Route("/inviteduser")
 */
class InvitedUserController extends Controller
{

    /**
     * Lists all InvitedUser entities.
     *
     * @Route("/", name="inviteduser")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('cooFoodUserBundle:InvitedUser')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new InvitedUser entity.
     *
     * @Route("/", name="inviteduser_create")
     * @Method("POST")
     * @Template("cooFoodUserBundle:InvitedUser:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new InvitedUser();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('inviteduser_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a InvitedUser entity.
     *
     * @param InvitedUser $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(InvitedUser $entity)
    {
        $form = $this->createForm(new InvitedUserType(), $entity, array(
            'action' => $this->generateUrl('inviteduser_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new InvitedUser entity.
     *
     * @Route("/new", name="inviteduser_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new InvitedUser();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a InvitedUser entity.
     *
     * @Route("/{id}", name="inviteduser_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('cooFoodUserBundle:InvitedUser')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvitedUser entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing InvitedUser entity.
     *
     * @Route("/{id}/edit", name="inviteduser_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('cooFoodUserBundle:InvitedUser')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvitedUser entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a InvitedUser entity.
    *
    * @param InvitedUser $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(InvitedUser $entity)
    {
        $form = $this->createForm(new InvitedUserType(), $entity, array(
            'action' => $this->generateUrl('inviteduser_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing InvitedUser entity.
     *
     * @Route("/{id}", name="inviteduser_update")
     * @Method("PUT")
     * @Template("cooFoodUserBundle:InvitedUser:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('cooFoodUserBundle:InvitedUser')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvitedUser entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('inviteduser_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a InvitedUser entity.
     *
     * @Route("/{id}", name="inviteduser_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('cooFoodUserBundle:InvitedUser')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InvitedUser entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('inviteduser'));
    }

    /**
     * Creates a form to delete a InvitedUser entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inviteduser_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
