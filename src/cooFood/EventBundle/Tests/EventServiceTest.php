<?php

namespace cooFood\EventBundle\Tests;

use cooFood\EventBundle\Service\EventService;
use cooFood\EventBundle\Entity\Event;
use Proxies\__CG__\cooFood\UserBundle\Entity\User;
use Symfony\Component\ExpressionLanguage\Token;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EventServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testCheckIfOrganizer()
    {

    }

    public function testCheckIfJoined()
    {
        $user = $this->getMock(User::class);
        $userEvent = $this->getMock(Event::class);

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->any())
            ->method('getToken')
            ->willReturn($user);

        $userEventsRepository = $this
            ->getMockBuilder(\cooFood\EventBundle\Entity\Repository\UserEventRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userEventsRepository->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($userEvent));

        $em = $this
            ->getMockBuilder(\Doctrine\ORM\EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($userEventsRepository);

        $tokenStorage = $this
            ->getMockBuilder(\Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn($token);

        $formFactory = $this
            ->getMockBuilder(\Symfony\Component\Form\FormFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();


        $eventService = new EventService($em, $tokenStorage, $formFactory);
        $this->assertEquals(true, $eventService->checkIfJoined(1));
    }


}
