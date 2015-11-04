<?php


namespace FOS\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->buildUserForm($builder, $options);

        $builder->add('current_password', 'password', array(
            'label' => 'form.current_password',
            'translation_domain' => 'FOSUserBundle',
            'mapped' => false,
            'constraints' => new UserPassword(),
        ));
    }

    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('username')//, null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('name', 'text', array('label' => 'Vardas'))
            ->add('surname', 'text', array('label' => 'Pavardė'))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
        ;
    }

    public function getParent()
    {
        return 'fos_user_profile';
    }

    public function getName()
    {
        return 'fos_user_profile';
    }

}
