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
use cooFood\SupplierBundle\Entity\Product;

class LoadProductData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $jsonData = file_get_contents(realpath(__DIR__ . '/../Data/can_can.json'));
        $jsonProductsData = json_decode($jsonData);
        foreach ($jsonProductsData as $productsData) {
            $product = new Product();
            $product->setName($productsData->{'cboxelement_image/_title'});
            $product->setSupplier($this->getReference('Supplier1'));
            $product->setPrice($productsData->{'lined_price_1'});
            $product->setDescription($productsData->{'description'});
            $product->setImage($productsData->{'cboxelement_link'});
            $manager->persist($product);
        }

        $jsonData = file_get_contents(realpath(__DIR__ . '/../Data/jammi.json'));
        $jsonProductsData = json_decode($jsonData);
        foreach ($jsonProductsData as $productsData) {
            $product = new Product();
            $product->setName($productsData->{'dishlarger_image/_alt'});
            $product->setSupplier($this->getReference('Supplier2'));
            $product->setPrice($productsData->{'single_price'});
            $product->setDescription($productsData->{'desc_value'});
            $product->setImage($productsData->{'dishlarger_image'});
            $manager->persist($product);
        }

        $jsonData = file_get_contents(realpath(__DIR__ . '/../Data/yap_yap_sushi.json'));
        $jsonProductsData = json_decode($jsonData);
        foreach ($jsonProductsData as $productsData) {
            $product = new Product();
            $productName = explode('.', $productsData->{'captionh4_value'}, 2);
            $product->setName($productName[1]);
            $product->setSupplier($this->getReference('Supplier3'));
            $product->setPrice($productsData->{'sale_price'});
            $product->setDescription('');
            $product->setImage($productsData->{'sale_image'});
            $manager->persist($product);
        }

        $jsonData = file_get_contents(realpath(__DIR__ . '/../Data/wok_to_walk.json'));
        $jsonProductsData = json_decode($jsonData);
        foreach ($jsonProductsData as $productsData) {
            $product = new Product();
            $product->setName($productsData->{'menuitem_value_1'});
            $product->setSupplier($this->getReference('Supplier4'));
            $product->setPrice($productsData->{'base_price'});
            $product->setDescription($productsData->{'menuitem_value_2'});
            $product->setImage($productsData->{'lazy_image'});
            $manager->persist($product);
        }

        $jsonData = file_get_contents(realpath(__DIR__ . '/../Data/forto_dvaras.json'));
        $jsonProductsData = json_decode($jsonData);
        foreach ($jsonProductsData as $productsData) {
            $product = new Product();
            $product->setName($productsData->{'dishlarger_image/_alt'});
            $product->setSupplier($this->getReference('Supplier5'));
            $product->setPrice($productsData->{'single_price'});
            $product->setDescription($productsData->{'desc_description'});
            $product->setImage($productsData->{'dishlarger_image'});
            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

}