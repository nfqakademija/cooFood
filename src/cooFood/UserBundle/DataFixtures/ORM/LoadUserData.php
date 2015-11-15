<?php
/**
 * Created by PhpStorm.
 * User: rokas
 * Date: 15.11.15
 * Time: 13.38
 */

namespace cooFood\SupplierBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use cooFood\UserBundle\Entity\User;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
     * Loads fixtures for Passenger in defined order
     *
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $users = array(
            array('test1@mail.com',true,'test','ROLE_USER','Jonas','Jonaitis','user-1'),
            array('test2@mail.com',true,'test','ROLE_USER','Petras','Petraitis','user-2'),
            array('test3@mail.com',true,'test','ROLE_USER','Lina','Pavardaitė','user-3'),
            array('test4@mail.com',true,'test','ROLE_USER','Gabrielė','Matulytė','user-4'),
            array('test5@mail.com',true,'test','ROLE_USER','Janina','Liūdienė','user-5'),
            array('test6@mail.com',true,'test','ROLE_USER','Emilė','Tuomaitė','user-6'),
            array('test7@mail.com',true,'test','ROLE_USER','Gabrielė','Matulytė','user-7'),
            array('test8@mail.com',true,'test','ROLE_USER','Tomas','Matulytė','user-8'),
            array('test9@mail.com',true,'test','ROLE_USER','Marius','Grigaitis','user-9'),
            array('test10@mail.com',true,'test','ROLE_USER','Rokas','Rokas','user-10'),
            array('test11@mail.com',true,'test','ROLE_USER','Mindaugas','Rūkas','user-11'),
            array('test12@mail.com',true,'test','ROLE_USER','Sergėj','Gron','user-12'),
            array('test13@mail.com',true,'test','ROLE_USER','Justas','Alksninis','user-13'),
            array('test14@mail.com',true,'test','ROLE_USER','Mantas','Maciulevičius','user-14'),
            array('test15@mail.com',true,'test','ROLE_USER','Mantvydas','Račkauskas','user-15'),
            array('test16@mail.com',true,'test','ROLE_USER','Ona','Vaičaitė','user-16'),
            array('test17@mail.com',true,'test','ROLE_USER','Eglė','Eidukevičiutė','user-17'),
            array('test18@mail.com',true,'test','ROLE_USER','Kamilė','Babickaitė','user-18'),
            array('test19@mail.com',true,'test','ROLE_USER','Laura','Čaplikienė','user-19'),
            array('test20@mail.com',true,'test','ROLE_USER','Monika','Fedaravičienė','user-20'),
        );
        $userManager = $this->container->get('fos_user.user_manager');
        foreach ($users as $userData) {
            $userUser = $userManager->createUser();

            $userUser->setUsername($userData[0]);
            $userUser->setUsernameCanonical($userData[0]);
            $userUser->setEmail($userData[0]);
            $userUser->setEmailCanonical($userData[0]);
            $userUser->setEnabled($userData[1]);
            $userUser->setPlainPassword($userData[2]);
            $userUser->setRoles(array($userData[3]));
            $userUser->setName($userData[4]);
            $userUser->setSurname($userData[5]);
            $manager->persist($userUser);
            $this->addReference($userData[6], $userUser);
        }
        $manager->flush();
    }
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}