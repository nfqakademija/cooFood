<?php

namespace cooFood\EventBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserEventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idUser')
            ->add('idEvent')
            ->add('paid')
            ->add('acceptedUser')
            ->add('acceptedHost')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'cooFood\UserBundle\Entity\UserEvent'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'coofood_userbundle_userevent';
    }
}
