<?php

namespace cooFood\EventBundle\Controller;

use cooFood\EventBundle\Entity\InvitedUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use cooFood\EventBundle\Entity\Event;
use cooFood\EventBundle\Form\EventType;
use cooFood\EventBundle\Entity\UserEvent;

/**
 * Event controller.
 *
 * @Route("/event")
 */
class EventController extends Controller
{

    /**
     * Lists all event entities.
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
     * Creates a new event entity.
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
            $entity->setIdUser($user);//->getId());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $userEventService = $this->get("user_event");
            $userEventEntity = $userEventService->createUserEvent($user, $entity);

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
     * Creates a form to create a event entity.
     *
     * @param event $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(event $entity)
    {
        $form = $this->createForm(new eventType(), $entity, array(
            'action' => $this->generateUrl('event_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new event entity.
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
     * Finds and displays a event entity.
     *
     * @Route("/{id}", name="event_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $userEvent = $em->getRepository('cooFoodEventBundle:UserEvent')->findByidEvent($id);

        if (!$userEvent) {
            return array(
                'error' => 'Not found!'
            );
        }

        $participantsRepository = $em->getRepository('cooFoodUserBundle:User');
        $eventRepository = $em->getRepository('cooFoodEventBundle:Event');



        $securityAuthorizationChecker = $this->container->get('security.authorization_checker');
        $securityTokenStorage = $this->get('security.token_storage');

        if ($securityAuthorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $securityTokenStorage->getToken()->getUser();
            $userId = $user->getId();
        } else {
            $userId = null;
        }

        $events = $eventRepository->findOneById($id);
        if ($events->getIdUser()->getId() == $userId) {
            $organizer = true;
        } else {
            $organizer = false;
        }

        $joined = false;
        $userApprove = false;
        $participants = array();

        foreach ($userEvent as $event) {
            $user = $participantsRepository->findOneByid($event->getIdUser());
            if ($user->getId() == $userId) {
                $joined = true;
                $userApprove = $event->getAcceptedUser();
            }
            $participants[] = $user->getName() . " " . $user->getSurname() . " (" . $user->getEmail() . ")";
        }

        $entity = $em->getRepository('cooFoodEventBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find event entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'participants' => $participants,
            'joined' => $joined,
            'organizer' => $organizer,
            'userApprove' => $userApprove
        );

    }

    /**
     * Displays a form to edit an existing event entity.
     *
     * @Route("/{id}/edit", name="event_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $securityContext = $this->container->get('security.context');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $user = $securityContext->getToken()->getUser();
            $userId = $user->getId();

            $em = $this->getDoctrine()->getManager();

            $entity = $em->getRepository('cooFoodEventBundle:Event')->find($id);
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find event entity.');
            }

            if ($entity->getIdUser()->getId() == $userId) {


                $editForm = $this->createEditForm($entity);
                $deleteForm = $this->createDeleteForm($id);

                return array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                );
            }


        }
        return $this->redirectToRoute('homepage');
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
        $form = $this->createForm(new eventType(), $entity, array(
            'action' => $this->generateUrl('event_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing event entity.
     *
     * @Route("/{id}", name="event_update")
     * @Method("PUT")
     * @Template("cooFoodEventBundle:event:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('cooFoodEventBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find event entity.');
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
     * Deletes a event entity.
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
                throw $this->createNotFoundException('Unable to find event entity.');
            }

            $em->remove($entity);
            //  $em->flush();

            $userEventRepository = $em->getRepository('cooFoodEventBundle:UserEvent');
            $userEvent = $userEventRepository->findByidEvent($id);
            foreach ($userEvent as $event) {
                $em->remove($event);
            }
            $em->flush();
        }
        return $this->redirectToRoute('homepage');
    }

    /**
     * Creates a form to delete a event entity by id.
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

    /**
     * Displays a page for event administration
     *
     * @Route("/{id}/administrate", name="event_administrate")
     * @Method("GET")
     * @Template()
     */
    public function administrateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $userEvent = $em->getRepository('cooFoodEventBundle:UserEvent')->findByidEvent($id);

        $participantsRepository = $em->getRepository('cooFoodUserBundle:User');
        $eventRepository = $em->getRepository('cooFoodEventBundle:Event');

        $entity = $eventRepository->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find event entity.');
        }

        $securityAuthorizationChecker = $this->container->get('security.authorization_checker');
        $securityTokenStorage = $this->get('security.token_storage');

        if ($securityAuthorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $securityTokenStorage->getToken()->getUser();
            $userId = $user->getId();
        } else {
            $userId = null;
        }

        $events = $eventRepository->findOneById($id);
        if ($events->getIdUser()->getId() == $userId) {
            $organizer = $userId;
        } else {
            throw $this->createNotFoundException('Only for event organizer.');
        }

        $participants = array();
        $participantsId = array();

        foreach ($userEvent as $key => $event) {
            $user = $participantsRepository->findOneByid($event->getIdUser());
            $participants[$key]["user"] = $user->getName() . " " . $user->getSurname() . " (" . $user->getEmail() . ")";
            if (!$event->getAcceptedUser() && $events->getReqApprove()) {
                $participants[$key]["addLink"] = '<a href="' . $this->generateUrl('User_event_administrate', array('id' => $id, 'action' => 'approve', 'userEventId' => $event->getId())) . '">Add</a>';
            } else {
                $participants[$key]["addLink"] = '-';
            }
            if ($organizer != $user->getId()) {
                $participants[$key]["delLink"] = '<a href="' . $this->generateUrl('User_event_administrate',
                        array('id' => $id, 'action' => 'delete', 'userEventId' => $event->getId())) . '">Delete</a>';
            } else {
                $participants[$key]["delLink"] = '-';
                $participants[$key]["addLink"] = '-';
            }
            $participantsId[] = $user->getId();
        }

        $participantsIdStr = implode(", ", $participantsId);
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT id, email, name, surname FROM fos_user WHERE fos_user.id NOT IN (".$participantsIdStr.")");
        $statement->execute();
        $allUsers = $statement->fetchAll();

        return array(
            'entity' => $entity,
            'participants' => $participants,
            'inviteUsersForm' => $this->createInviteGuestFormAction($id, $allUsers),
            'inviteEmailForm' => $this->createInviteEmailFormAction($id)
        );
    }

    /**
     * Generate add user to event form
     */
    private function createInviteGuestFormAction($id, array $allUsers)
    {
        $userList = array();
        foreach ($allUsers as $user) {
            $userList[$user["id"]] = $user["name"] . ' ' . $user["surname"] . ' (' . $user["email"] . ')';
        }
        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->setAction($this->generateUrl('add_user_to_event', array('id' => $id)))
            ->add('usersList', 'choice', array(
                'multiple' => true,
                'label' => false,
                'choices' => $userList
            ))
            ->add('Kviesti', 'submit')

            ->getForm();

        return $form->createView();
    }

    /**
     * Generate send invitation email form
     */
    private function createInviteEmailFormAction($id)
    {
        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->setAction($this->generateUrl('send_email_invitation', array('id' => $id)))
            ->add('email', 'email', array(
                'label' => false
            ))
            ->add('Siųsti kvietimą', 'submit')
            ->getForm();

        return $form->createView();
    }

    /**
     * Adds users from selection form to event
     *
     * @Route("/{id}/administrate/add/", name="add_user_to_event")
     * @Method("POST")
     */
    public function addUserToEventAction (Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $participantsRepository = $em->getRepository('cooFoodUserBundle:User');
        $eventRepository = $em->getRepository('cooFoodEventBundle:Event');
        $event = $eventRepository->findOneByid($id);
        $eventApproveFlag = $event->getReqApprove();

        $data = $request->request->get('form');

        foreach ($data['usersList'] as $userId) {
            $user = $participantsRepository->findOneByid($userId);
            $entity = new UserEvent();
            $entity->setIdUser($user);
            $entity->setIdEvent($event);
            $entity->setPaid(0);
            $entity->setAcceptedUser($eventApproveFlag);
            $entity->setAcceptedHost($eventApproveFlag);
            $em->persist($entity);

            $emailText = 'Jūs buvote pridėtas prie "' . $event->getName() . '" renginio.
            Prisijunkite prie cooFood sistemos ir spauskite šią nuorodą: /event/' . $id;
            $this->sendEmail($user->getEmail(), $emailText);
        }
        $em->flush();



        return $this->redirectToRoute('event_administrate', ['id'=>$id]);
    }

    /**
     * Approve / delete user in event action
     *
     * @Route("/{id}/administrate/{action}/{userEventId}", name="User_event_administrate")
     * @Method("GET")
     */
    public function doUserEventAction($id, $action, $userEventId)
    {
        $em = $this->getDoctrine()->getManager();
        $userEventRepository = $em->getRepository('cooFoodEventBundle:UserEvent')->findOneById($userEventId);

        if (!$userEventRepository) {
            throw $this->createNotFoundException('Not found for user event id '.$userEventId);
        }

        switch ($action) {
            case 'delete':
                $orderItemRepository = $em->getRepository('cooFoodEventBundle:OrderItem')->findByidUserEvent($userEventRepository->getId());
                if ($orderItemRepository) {
                    foreach ($orderItemRepository as $orderItem) {
                        $sharedOrderRepository = $em->getRepository('cooFoodEventBundle:SharedOrder')->findByidOrderItem($orderItem->getId());
                        if ($sharedOrderRepository) {
                            foreach ($sharedOrderRepository as $sharedOrder) {
                                $em->remove($sharedOrder);
                            }
                        }
                        $em->remove($orderItem);
                    }
                }
                $em->remove($userEventRepository);
                $em->flush();
                return $this->redirectToRoute('event_administrate', ['id'=>$id]);
                break;

            case 'approve':
                $userEventRepository->setacceptedUser(true);
                $em->flush();
                return $this->redirectToRoute('event_administrate', ['id'=>$id]);
                break;
        }
    }

    /**
     * Sends email event invitation to user
     *
     * @Route("/{id}/administrate/sendemail/", name="send_email_invitation")
     * @Method("POST")
     */
    public function sendEmailAction (Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $invitedUserRepository = $em->getRepository('cooFoodEventBundle:InvitedUser');
        $eventRepository = $em->getRepository('cooFoodEventBundle:Event');
        $event = $eventRepository->findOneByid($id);

        $data = $request->request->get('form');
        $iuEmail = $invitedUserRepository->findBy(array('idEvent' => $id, 'email' => $data['email']));

        // if field dont exist - send email and create new row
        if (!$iuEmail) {
            $secretCode = uniqid();

            $entity = new InvitedUser();
            $entity->setIdEvent($event);
            $entity->setEmail($data['email']);
            $entity->setSecretCode($secretCode);
            $em->persist($entity);

            $em->flush();

            $emailText = 'Jūs buvote pakviestas prisijungti prie "' . $event->getName() . '" renginio.
            Norėdami prisijungti, užsiregistruokite cooFood sistemoje ir spauskite šią nuorodą: /event/' . $id . '/join/' . $secretCode;

            $this->sendEmail($data['email'], $emailText);

            var_dump($emailText);

        } else {
            throw $this->createNotFoundException('User with email: ' . $data['email'] . ' already invited');
        }

        return $this->redirectToRoute('event_administrate', ['id' => $id]);

    }


    /**
     * Join event with secret code
     *
     * @Route("/{id}/join/{secretCode}", name="secret_join_event")
     * @Method("GET")
     */
    public function secretJoinAction($id, $secretCode)
    {
        $securityAuthorizationChecker = $this->container->get('security.authorization_checker');
        $securityTokenStorage = $this->get('security.token_storage');

        if ($securityAuthorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $securityTokenStorage->getToken()->getUser();

            $em = $this->getDoctrine()->getManager();

            $eventRepository = $em->getRepository('cooFoodEventBundle:Event');
            $event = $eventRepository->findOneByid($id);
            $eventApproveFlag = $event->getReqApprove();

            $userEventRepository = $em->getRepository('cooFoodEventBundle:UserEvent');

            $invitedUserRepository = $em->getRepository('cooFoodEventBundle:InvitedUser');

            $invitedUser = $invitedUserRepository->findOneBysecretCode($secretCode);

            if ($invitedUser && ($invitedUser->getEmail() === $user->getEmail())) {
                $userEvent = $userEventRepository->findBy(array('idEvent' => $id, 'idUser' => $user->getId()));
                if (!$userEvent) {
                    $entity = new UserEvent();
                    $entity->setIdUser($user);
                    $entity->setIdEvent($event);
                    $entity->setPaid(0);
                    $entity->setAcceptedUser($eventApproveFlag);
                    $entity->setAcceptedHost($eventApproveFlag);
                    $em->persist($entity);
                    $em->remove($invitedUser);
                    $em->flush();
                }
                return $this->redirectToRoute('event_show', ['id' => $id]);

            } else {
                return $this->redirect('/');
            }
//
        } else {
            return $this->redirect('/login');
        }
    }

    /**
    * Finds and displays a event entity.
    *
    * @Route("/{id}/summary", name="event_summary")
    * @Method("GET")
    * @Template()
    */
    public function summaryAction($id)
    {
        $securityAuthorizationChecker = $this->container->get('security.authorization_checker');
        $securityTokenStorage = $this->get('security.token_storage');

        if ($securityAuthorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $user = $securityTokenStorage->getToken()->getUser();
        } else {
            throw $this->createNotFoundException('Only for event organizer.');
        }

        $orderService = $this->get("order");
        $userOrders = $orderService->getUserOrdersInfo($id);//1
        $allOrders = $orderService->getAllEventOrdersInfo($id);//2

        return array(
            'allOrders' => $allOrders,
            'userOrders' => $userOrders,
            'idEvent' => $id
        );
    }

    /**
     * Send email private function
     *
     * @param $email
     * @param $text
     */
    private function sendEmail($email, $text)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('cooFood LT')
            ->setFrom('noreply@coofood.lt')
            ->setTo($email)
            ->setBody($text);
        $this->get('mailer')->send($message);
    }
}
