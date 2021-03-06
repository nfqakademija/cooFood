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
     * Creates a new event entity.
     *
     * @Route("/", name="event_create")
     * @Method("POST")
     * @Template("cooFoodEventBundle:Event:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Event();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);


        if ($form->isValid()) {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $entity->setIdUser($user);
            $em = $this->getDoctrine()->getManager();

            $eventService = $this->get("event_manager");

            if($eventService->checkIfEventExist($entity->getName()))
            {
                $request->getSession()
                    ->getFlashBag()
                    ->add('error', 'Toks renginys jau egzistuoja!');

                return $this->redirect($this->generateUrl('event_new'));
            }

            $em->persist($entity);
            $em->flush();
            $userEventService = $this->get("user_event_manager");
            $userEventService->createUserEvent($entity);

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Renginys sėkmingai sukurtas');

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

       // $form->add('submit', 'submit', array('label' => 'Sukurti', 'attr' => array('class' => 'btn-success')));

        return $form;
    }

    /**
     * Displays a form to create a new event entity.
     *
     * @Route("/new", name="event_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $securityAuthorizationChecker = $this->container->get('security.authorization_checker');
        $securityTokenStorage = $this->get('security.token_storage');

        if ($securityAuthorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $entity = new Event();
            $form = $this->createCreateForm($entity);

            return array(
                'entity' => $entity,
                'form' => $form->createView()
            );
        } else {
            $request->getSession()
                ->getFlashBag()
                ->add('error', 'Prisijunkite prie sistemos');
            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    /**
     * Finds and displays a event entity.
     *
     * @Route("/{id}", name="event_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id, Request $request)
    {
        $eventService = $this->get("event_manager");

        $organizer = $eventService->checkIfOrganizer($id);
        $joined = $eventService->checkIfJoined($id);
        $userApprove = $eventService->checkIfUserApprove($id);
        $participants = $eventService->getEventParticipants($id);

        $event = $eventService->getEvent($id);
        if (!$event) {
            $request->getSession()
                ->getFlashBag()
                ->add('error', 'Renginys nerastas');
            return $this->redirectToRoute('homepage');
        }
        $deleteForm = $this->createDeleteForm($id);

        if ($joined) {
            $payForOrderService = $this->get("payfororder");
            $payForOrderService->setEventId($id);
            $totalAmount = round($payForOrderService->getTotalAmount(), 2);
        } else {
            $totalAmount = false;
        }

        $deadlineDate = $event->getOrderDeadlineDate()->format('Y-m-d H:i:s');
        $deadlineStatus = false;
        if ($deadlineDate < date("Y-m-d H:i:s")) {
            $deadlineStatus = true;
        }

        return array(
            'entity' => $event,
            'delete_form' => $deleteForm->createView(),
            'participants' => $participants,
            'joined' => $joined,
            'organizer' => $organizer,
            'userApprove' => $userApprove,
            'payAmount' => $totalAmount,
            'deadlineStatus' => $deadlineStatus
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
        $eventService = $this->get("event_manager");

        if ($eventService->checkIfOrganizer($id)) {
            $event = $eventService->getEvent($id);
            $editForm = $this->createEditForm($event);
            $deleteForm = $this->createDeleteForm($id);

            return array(
                'entity' => $event,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView()
            );
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

        $form->add('submit', 'submit', array('label' => 'Atnaujinti', 'attr' => array('class' => 'btn-success')));

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
        $eventService = $this->get("event_manager");
        $em = $this->getDoctrine()->getManager();

        $event = $eventService->getEvent($id);

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($event);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Renginio informacija atnaujinta');

            return $this->redirect($this->generateUrl('event_administrate', array('id' => $id)));
        }

        return array(
            'entity' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
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
        $eventService = $this->get("event_manager");
        $userEventService = $this->get("user_event_manager");

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $userEventService->deleteUserEvent($id);
            $eventService->deleteEvent($id);

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Renginys atšauktas');
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
            ->add('submit', 'submit', array('label' => 'Atšaukti renginį', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }

    /**
     * Displays a page for event administration
     *
     * @Route("/{id}/administrate", name="event_administrate")
     * @Method("GET")
     * @Template()
     */
    public function administrateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $userEvent = $em->getRepository('cooFoodEventBundle:UserEvent')->findByidEvent($id);

        $participantsRepository = $em->getRepository('cooFoodUserBundle:User');
        $eventRepository = $em->getRepository('cooFoodEventBundle:Event');

        $entity = $eventRepository->find($id);

        if (!$entity) {
            $request->getSession()
                ->getFlashBag()
                ->add('error', 'Renginys nerastas!');
            return $this->redirectToRoute('homepage');
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
            $request->getSession()
                ->getFlashBag()
                ->add('error', 'Jūs nesate renginio organizatorius!');
            return $this->redirectToRoute('event_show', ['id'=>$id]);
        }

        $participants = array();
        $participantsId = array();

        foreach ($userEvent as $key => $event) {
            $user = $participantsRepository->findOneByid($event->getIdUser());

            $participants[$key]["user"] = $user->getName() . " " . $user->getSurname() . " (" . $user->getEmail() . ")";

            if (!$event->getAcceptedUser() && $events->getReqApprove()) {
                $participants[$key]["addLink"] = $this->createApproveUserEventFormAction($id, $event->getId());
            } else {
                $participants[$key]["addLink"] = false;
            }

            if ($organizer != $user->getId()) {
                $participants[$key]["delLink"] = $this->createDeleteUserEventFormAction($id, $event->getId());
            } else {
                $participants[$key]["delLink"] = false;
                $participants[$key]["addLink"] = false;
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
     * Generating user event approve form
     *
     * @param $id
     * @param $userEventId
     * @return \Symfony\Component\Form\FormView
     */
    private function createApproveUserEventFormAction($id, $userEventId)
    {
        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->setAction($this->generateUrl('Approve_user_event', array('id' => $id)))
            ->add('userEventId','hidden', array(
                'data' => $userEventId
            ))
            ->add('Pridėti', 'submit', array(
                'attr' => array('class' => 'btn-default btn-xs'),
            ))
            ->getForm();

        return $form->createView();
    }

    /**
     * Generating user event delete form
     *
     * @param $id
     * @param $userEventId
     * @return \Symfony\Component\Form\FormView
     */
    private function createDeleteUserEventFormAction($id, $userEventId)
    {
        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->setAction($this->generateUrl('Delete_user_event', array('id' => $id)))
            ->add('userEventId','hidden', array(
                'data' => $userEventId
            ))
            ->add('Pašalinti', 'submit', array(
                'attr' => array('class' => 'btn-default btn-xs'),
            ))
            ->getForm();

        return $form->createView();
    }

    /**
     * Generating invite guest form
     *
     * @param $id
     * @param array $allUsers
     * @return \Symfony\Component\Form\FormView
     */
    private function createInviteGuestFormAction($id, array $allUsers)
    {
        $userList = array();
        foreach ($allUsers as $user) {
            $userList[$user["id"]] = $user["name"] . ' ' . $user["surname"];
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

            /* Remove comment if send notification email to user
            $emailText = $this->renderView(
                '@cooFoodEvent/Emails/addedUser.html.twig',
                array('eventName' => $event->getName(),
                    'eventId' => $id)
            );

            $this->sendEmail($user->getEmail(), $emailText);*/

            $request->getSession()
                ->getFlashBag()
                ->add('success', $user->getname() . ' ' . $user->getsurname() . ' įtraukta(s) į renginį');
        }
        $em->flush();

        return $this->redirectToRoute('event_administrate', ['id'=>$id]);
    }

    /**
     * Delete user in event action
     *
     * @Route("/{id}/administrate/delete/", name="Delete_user_event")
     * @Method("POST")
     */
    public function deleteUserEventAction($id, Request $request)
    {
        $data = $request->request->get('form');
        $em = $this->getDoctrine()->getManager();
        $userEventRepository = $em->getRepository('cooFoodEventBundle:UserEvent')->findOneById($data['userEventId']);

        if (!$userEventRepository) {
            $request->getSession()
                ->getFlashBag()
                ->add('error', 'Tokio vartotojo renginyje nėra');
            return $this->redirectToRoute('event_administrate', ['id' => $id]);
        }

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
        $request->getSession()
            ->getFlashBag()
            ->add('success', 'Svečias pašalintas iš renginio');
        return $this->redirectToRoute('event_administrate', ['id'=>$id]);

    }

    /**
     * Approve user in event
     *
     * @Route("/{id}/administrate/approve/", name="Approve_user_event")
     * @Method("POST")
     */
    public function approveUserEventAction($id, Request $request)
    {
        $data = $request->request->get('form');
        $em = $this->getDoctrine()->getManager();
        $userEventRepository = $em->getRepository('cooFoodEventBundle:UserEvent')->findOneById($data['userEventId']);
        if (!$userEventRepository) {
            $request->getSession()
                ->getFlashBag()
                ->add('error', 'Tokio vartotojo renginyje nėra');
            return $this->redirectToRoute('event_administrate', ['id' => $id]);
        }
        $userEventRepository->setacceptedUser(true);
        $em->flush();
        $request->getSession()
            ->getFlashBag()
            ->add('success', 'Svečio dalyvavimas renginyje patvirtintas');
        return $this->redirectToRoute('event_administrate', ['id'=>$id]);
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

        if (!$iuEmail) {
            $secretCode = uniqid();

            $entity = new InvitedUser();
            $entity->setIdEvent($event);
            $entity->setEmail($data['email']);
            $entity->setSecretCode($secretCode);
            $em->persist($entity);

            $em->flush();

            $emailText = $this->renderView(
                                '@cooFoodEvent/Emails/secretInvitation.html.twig',
                                array('eventName' => $event->getName(),
                                    'eventId' => $id,
                                    'secretCode' => $secretCode)
                            );

            $this->sendEmail($data['email'], $emailText);

            $request->getSession()
                ->getFlashBag()
                ->add('success', $data['email'] . ' išsiųstas kvietimas į renginį');

        } else {
            $request->getSession()
                ->getFlashBag()
                ->add('error', $data['email'] . ' jau buvo siųstas kvietimas');
        }

        return $this->redirectToRoute('event_administrate', ['id' => $id]);

    }


    /**
     * Join event with secret code
     *
     * @Route("/{id}/join/{secretCode}", name="secret_join_event")
     * @Method("GET")
     */
    public function secretJoinAction($id, $secretCode, Request $request)
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
                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Sveikiname prisijungus prie renginio!');
                return $this->redirectToRoute('event_show', ['id' => $id]);

            } else {
                $request->getSession()
                    ->getFlashBag()
                    ->add('error', 'Svetimas kvietimas į renginį negalioja!');
                return $this->redirect('/');
            }
        } else {
            $request->getSession()
                ->getFlashBag()
                ->add('error', 'Prisijunkite/užsiregistruokite prie sistemos, o tada panaudokite kvietimo nuorodą');
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

        $orderService = $this->get("order_manager");
        $userOrders = $orderService->getUserOrdersInfo($id);
        $allOrders = $orderService->getAllEventOrdersInfo($id);

        return array(
            'allOrders' => $allOrders,
            'userOrders' => $userOrders,
            'idEvent' => $id
        );
    }

    /**
     * Pay for your orders
     *
     * @Route("/{id}/pay", name="event_payment")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function ordersPayAction(Request $request, $id)
    {
        $eventService = $this->get("event_manager");

        if (!$eventService->checkIfJoined($id)) {
            $request->getSession()
                ->getFlashBag()
                ->add('error', 'Pirma prisijunkite prie renginio');
            return $this->redirectToRoute('event_show', ['id' => $id]);
        }

        $securityAuthorizationChecker = $this->container->get('security.authorization_checker');
        $securityTokenStorage = $this->get('security.token_storage');

        if ($securityAuthorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $securityTokenStorage->getToken()->getUser();

            $payForOrderService = $this->get("payfororder");
            $payForOrderService->setEventId($id);

            $em = $this->getDoctrine()->getManager();
            $userEventRepository = $em->getRepository('cooFoodEventBundle:UserEvent');
            $userEvent = $userEventRepository->findOneBy(array('idEvent' => $id, 'idUser' => $user->getId()));

            $totalAmount = $payForOrderService->getTotalAmount();

            if ($totalAmount > 0) {

                $form = $this->createFormBuilder()
                    ->add('amount', 'text', array(
                        'required' => true,
                        'read_only' => true,
                        'label' => false,
                        'data' => $totalAmount
                    ))
                    ->add('Apmokėti', 'submit')
                    ->getForm();


                $form->handleRequest($request);

                if ($form->isValid()) {
                    $result = $form->getData();
                    $payAmount = $userEvent->getPaid() + $result['amount'];
                    $userEvent->setPaid($payAmount);
                    $em->flush();
                    $request->getSession()
                        ->getFlashBag()
                        ->add('success', 'Jūsų užsakymas sėkmingai apmokėtas');
                    return $this->redirectToRoute('event_show', ['id' => $id]);
                }

                return array(
                    'paymentForm' => $form->createView(),
                    'eventId' => $id
                );

            }
            return $this->redirectToRoute('event_show', ['id' => $id]);
        }
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
