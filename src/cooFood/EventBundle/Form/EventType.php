<?php

namespace cooFood\eventBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('eventDate')
            ->add('joinDateStart')
            ->add('joinDateEnd')
            ->add('orderDeadlineDate')
            ->add('idCity')
            ->add('idAddress')
            ->add('place')
            ->add('description')
            ->add('idSupplier')
            ->add('visible')
            ->add('reqApprove')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'cooFood\eventBundle\Entity\Event'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'coofood_eventbundle_event';
    }
}
