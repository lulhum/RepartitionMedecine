<?php
// src/Lulhum/UserBundle/Form/UserFilterType.php

namespace Lulhum\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserFilterType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
            ->add('promotions', 'choice', array(
                'label' => 'Promotions',
                'choices' => \Lulhum\UserBundle\Entity\User::PROMOTIONS,
                'required' => false,
                'multiple' => true,
            ))
            ->add('group', 'choice', array(
                'choices' => array('A' => 'A', 'B' => 'B'),
                'label' => 'Groupe',
                'empty_value' => 'Indifférent',
                'required' => false,
                'multiple' => false,
            ))
            ->add('periods', 'entity', array(
                'label' => 'Périodes de stages',
                'class' => 'LulhumRepartitionMedecineBundle:Period',
                'multiple' => true,
                'required' => false,
            ))
            ->add('categories', 'entity', array(
                'label' => 'Catégories de stages',
                'class' => 'LulhumRepartitionMedecineBundle:Category',
                'multiple' => true,
                'required' => false,
            ))
            ->add('filtrer', 'submit');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\UserBundle\Entity\UserFilter',
        ));
    }
    public function getName()
    {
        return 'lulhum_repartitionmedecine_userfilter';
    }
} 