<?php

namespace cooFood\EventBundle\Form;

use cooFood\SupplierBundle\Entity\Product;
use cooFood\EventBundle\Entity\Repository\UserEventRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    private $supplier;

    public function __construct($supplier)
    {
        $this->supplier = $supplier;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', 'integer', array(
                'attr' => array('min' => 1, 'max' => 100)
            ))
            ->add('shareLimit', 'integer', array(
                'attr' => array('min' => 1, 'max' => 100)
            ))
            ->add('idProduct', 'entity', array(
                'class' => 'cooFood\SupplierBundle\Entity\Product',
                'empty_value' => 'Visi produktai',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.supplier = :supplierId')
                        ->setParameter('supplierId', $this->supplier);
                }));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'cooFood\EventBundle\Entity\OrderItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'coofood_Eventbundle_orderitem';
    }
}
