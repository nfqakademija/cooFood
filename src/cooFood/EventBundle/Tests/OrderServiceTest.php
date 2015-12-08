<?php

class OrderServiceTest extends PHPUnit_Framework_TestCase
{
    public function testGetMyOrders()
    {
        $orderItems= array();
        $item = new \cooFood\EventBundle\Entity\OrderItem();
        $item->setShareLimit(2);
        $orderItems[] = $item;
        $orderItems[] = $this->getMock('\cooFood\EventBundle\Entity\OrderItem');

        $user = $this->getMock('\cooFood\UserBundle\Entity\User');

        $userEvent = $this->getMock('\cooFood\EventBundle\Entity\UserEvent');
        $userEvent->expects($this->once())
            ->method('getOrderItems')
            ->willReturn($orderItems);

        $userEventsRepository = $this
            ->getMockBuilder('\cooFood\EventBundle\Entity\Repository\UserEventRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $userEventsRepository->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($userEvent));

        $em = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($userEventsRepository);

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->any())
            ->method('getUser')
            ->willReturn($user);
        $tokenStorage = $this
            ->getMockBuilder('\Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $tokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn($token);

        $formFactory = $this
            ->getMockBuilder('\Symfony\Component\Form\FormFactoryInterface')
            ->disableOriginalConstructor()
            ->getMock();


        $orderService = new \cooFood\EventBundle\Service\OrderService($em, $tokenStorage, $formFactory);
        $this->assertEquals(1, count($orderService->getMyOrders(1)));
    }
}
