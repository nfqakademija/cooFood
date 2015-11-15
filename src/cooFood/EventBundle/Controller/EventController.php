<?php

namespace cooFood\EventBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use cooFood\EventBundle\Entity\Event;
use cooFood\EventBundle\Form\EventType;

/**
 * Event controller.
 *
 * @Route("/event")
 */
class EventController extends Controller
{

    /**
     * Lists all Event entities.
     *
     * @Route("/", name="event")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('cooFoodEventBundle:Event')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Event entity.
     *
     * @Route("/", name="event_create")
     * @Method("POST")
     * @Template("cooFoodEventBundle:Event:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $entity = new Event();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdUser($user->getId());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $userEventService = $this->get("user_event");
            $userEventEntity = $userEventService->createUserEvent($user->getId(), $entity->getId());

            $em->persist($userEventEntity);
            $em->flush();

            return $this->redirect($this->generateUrl('event_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Event entity.
     *
     * @param Event $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Event $entity)
    {
        $form = $this->createForm(new EventType(), $entity, array(
            'action' => $this->generateUrl('event_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Event entity.
     *
     * @Route("/new", name="event_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Event();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Event entity.
     *
     * @Route("/{id}", name="event_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $userEvent = $em->getRepository('cooFoodUserBundle:UserEvent')->findByidEvent($id);
        $participantsRepository = $em->getRepository('cooFoodUserBundle:User');
        $eventRepository = $em->getRepository('cooFoodEventBundle:Event');

        $participants = array();

        $securityAuthorizationChecker = $this->container->get('security.authorization_checker');
        $securityTokenStorage = $this->get('security.token_storage');

        if ($securityAuthorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $securityTokenStorage->getToken()->getUser();
            $userId = $user->getId();
        } else {
            $userId = null;
        }

        $events = $eventRepository->findOneById($id);
        if ($events->getIdUser() == $userId) {
            $organizer = true;
        } else {
            $organizer = false;
        }

        $joined = false;

        foreach ($userEvent as $event) {
            $user = $participantsRepository->findOneByid($event->getIdUser());
            if ($user->getId() == $userId) {
                $joined = true;
            }
            array_push($participants, $user->getEmail());
        }


        $entity = $em->getRepository('cooFoodEventBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'participants' => $participants,
            'joined' => $joined,
            'organizer' => $organizer
        );
    }

    /**
     * Displays a form to edit an existing Event entity.
     *
     * @Route("/{id}/edit", name="event_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('cooFoodEventBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteFgorm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Event entity.
     *
     * @param Event $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Event $entity)
    {
        $form = $this->createForm(new EventType(), $entity, array(
            'action' => $this->generateUrl('event_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Event entity.
     *
     * @Route("/{id}", name="event_update")
     * @Method("PUT")
     * @Template("cooFoodEventBundle:Event:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('cooFoodEventBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('event_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Event entity.
     *
     * @Route("/{id}", name="event_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('cooFoodEventBundle:Event')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Event entity.');
            }

            $em->remove($entity);
            //  $em->flush();

            $userEventRepository = $em->getRepository('cooFoodUserBundle:UserEvent');
            $userEvent = $userEventRepository->findByidEvent($id);
            foreach ($userEvent as $event) {
                $em->remove($event);
            }
            $em->flush();
        }
        return $this->redirectToRoute('homepage');
    }

    /**
     * Creates a form to delete a Event entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('event_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
