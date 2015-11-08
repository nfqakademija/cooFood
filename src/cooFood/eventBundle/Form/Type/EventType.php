<?php
/**
 * Created by PhpStorm.
 * User: klaudijus
 * Date: 15.11.8
 * Time: 13.34
 */

namespace cooFood\eventBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class EventType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('eventDate', 'datetime')
            ->add('joinDateStart', 'datetime')
            ->add('joinDateEnd', 'datetime')
            ->add('orderDeadline', 'datetime')
            ->add('city', 'text')
            ->add('address', 'text')
            ->add('location', 'text')
            ->add('summary', 'textarea')
            ->add('supplier', 'choice', array(
                'choices'  => array('CanCan' => 'CanCan', 'Katpedėlė' => 'Katpedėlė', 'Alaus namai' => 'Alaus namai'),
                'required' => true,))
            ->add('type', 'choice', array(
                'choices'  => array('private' => 'Privatus renginys', 'public' => 'Viešas renginys'),
                'required' => true, 'expanded' => true, 'multiple' => false));
    }

    public function getName()
    {
        return 'event';
    }
}