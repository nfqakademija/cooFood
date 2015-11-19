<?php

namespace cooFood\UserBundle\Form;

use cooFood\SupplierBundle\Entity\Product;
use cooFood\UserBundle\Entity\userEventRepository;
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
        //$supplier = $this->supplier;
        //var_dump(serialize($supplier));
        //die();
        $builder
            ->add('quantity')
            ->add('shareLimit')
            ->add('idProduct', 'entity', array(
                'class' => 'cooFood\SupplierBundle\Entity\Product',
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
            'data_class' => 'cooFood\UserBundle\Entity\OrderItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'coofood_userbundle_orderitem';
    }
}
