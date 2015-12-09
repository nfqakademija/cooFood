<?php
namespace cooFood\SupplierBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SupplierRepositoryTest extends KernelTestCase
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
        $supplier = $this->em
            ->getRepository('cooFoodSupplierBundle:Supplier')
            ->findBy(array('name' => 'Belekoks neegzistuojantis vardas'));

        $this->assertCount(0, $supplier);
    }

    public function testFindAll()
    {
        $elements = $this->em
            ->getRepository('cooFoodSupplierBundle:Supplier')
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