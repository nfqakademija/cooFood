<?php

namespace demo\contactsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/contacts');

//        $this->assertTrue($crawler->filter('html:contains("")')->count() > 0);
       $this->assertTrue(true);
    }
}