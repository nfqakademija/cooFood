<?php
/**
 * Created by PhpStorm.
 * User: rokas
 * Date: 15.11.15
 * Time: 13.38
 */

namespace cooFood\SupplierBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use cooFood\SupplierBundle\Entity\Supplier;

class LoadSupplierData extends AbstractFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $supplier1 = new Supplier();
        $supplier1->setName('CanCan');
        $supplier1->setLocation('Mindaugo g. 11, PC "Maxima", Vilnius');
        $supplier1->setEmail('delano@delano.lt');
        $supplier1->setPhone('+37065932584');
        $supplier1->setImage('');
        $supplier1->setDescription('Maistas į namus | CanCan.lt');

        $supplier2 = new Supplier();
        $supplier2->setName('Jammi');
        $supplier2->setLocation('Tauro g. 3, Vilnius');
        $supplier2->setEmail('');
        $supplier2->setPhone('+37052505668');
        $supplier2->setImage('');
        $supplier2->setDescription('Užsukę pas mus galite skaniai ir greitai paragauti ką tik iškeptų sultingų kebabų, meksikietiškų buritų, itališkų panini sumuštinių, traškių gruzdintų bulvyčių ir kitų greito maisto patiekalų, taip pat kavos išsinešimui. Iš anksto galite užsisakyti telefonu, kad atvažiavus jums nereikėtų laukti kol paruošime užsakymą. Ant Tauro g. ir Klavarijų g. kebabinėse, Jūsų patogumui veikia "Jammi kebab drive". Jūs visada laukiami. Bet kuriuo paros metu!');

        $supplier3 = new Supplier();
        $supplier3->setName('YapYap Sushi');
        $supplier3->setLocation('Kalvarijų gatvė 101a, Vilnius');
        $supplier3->setEmail('');
        $supplier3->setPhone('+37061307864');
        $supplier3->setImage('');
        $supplier3->setDescription('Yapyap sushi – suši pristatymas į namus Vilniuje. Iliustruotas valgiaraštis su nuotraukomis, aprašymais ir kainomis  Suši,užkandžiai, gėrimai.  Užsakymas internetu, apmokėjimas banko kortele. Pristatymo sąlygos. Kontaktai.');

        $supplier4 = new Supplier();
        $supplier4->setName('Wok to Walk');
        $supplier4->setLocation('Vilniaus g. 19, Vilnius');
        $supplier4->setEmail('');
        $supplier4->setPhone('+37065591919');
        $supplier4->setImage('');
        $supplier4->setDescription('Mes naudojame šviežius produktus iš vietinių tiekėjų. Jūsų wok’ą ruošiame Jums prieš akis. Ir darome tai pagal Jūsų norus, nes šefas esate JŪS.');

        $supplier5 = new Supplier();
        $supplier5->setName('"Forto dvaras"');
        $supplier5->setLocation('Pilies g. 16, Vilnius');
        $supplier5->setEmail('pilies@fortodvaras.lt');
        $supplier5->setPhone('+37065613688');
        $supplier5->setImage('');
        $supplier5->setDescription('"FORTO DVARAS" - pirmasis restoranas Lietuvoje, pasiūlęs Kulinarijos paveldo fondo sertifikuotų tradicinių patiekalų. Tai patiekalai, ruošiami tradiciniu gamybos būdu, naudojant vietinius produktus. Jaukioje senovinio dvarelio aplinkoje, išpuoštoje senosios grafikos elementais ir piešiniais iš autentiškų knygų, skamba lietuviška muzika. Vaikai žaidžia mediniais žaislais, mėgaujasi mielais siurprizais.');

        $this->addReference('Supplier1', $supplier1);
        $this->addReference('Supplier2', $supplier2);
        $this->addReference('Supplier3', $supplier3);
        $this->addReference('Supplier4', $supplier4);
        $this->addReference('Supplier5', $supplier5);

        $manager->persist($supplier1);
        $manager->persist($supplier2);
        $manager->persist($supplier3);
        $manager->persist($supplier4);
        $manager->persist($supplier5);

        $manager->flush();
    }
}