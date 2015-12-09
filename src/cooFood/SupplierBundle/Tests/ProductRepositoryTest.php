<?php
namespace cooFood\SupplierBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

    }

    public function testFindBy()
    {
        $products = $this->em
            ->getRepository('cooFoodSupplierBundle:Product')
            ->findBy(array('name' => 'Belekoks neegzistuojantis vardas'));

        $this->assertCount(0, $products);
    }

    public function testFindAll()
    {
        $elements = $this->em
            ->getRepository('cooFoodSupplierBundle:Product')
            ->findAll();

        $this->assertTrue(count($elements) == 0);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
    }
}