<?php

namespace cooFood\EventBundle\Form;

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
            ->add('orderDeadlineDate')
            ->add('address')
            ->add('description')
            ->add('idSupplier', 'entity', array(
                'class' => 'cooFood\SupplierBundle\Entity\Supplier',
                'choice_label' => 'name',
                'required' => true,
            ))
            ->add('visible', 'choice', array(
                'choices' => array(
                    true => 'Taip',
                    false => 'Ne'
                ),
                'multiple' => false,
                'expanded' => true,
                'required' => true,
             ))
            ->add('reqApprove', 'choice', array(
                'choices' => array(
                    true => 'Taip',
                    false => 'Ne'
                ),
                'multiple' => false,
                'expanded' => true,
                'required' => true,
            ))
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
