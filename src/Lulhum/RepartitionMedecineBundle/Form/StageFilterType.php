<?php
// src/Lulhum/RepartitionMedecineBundle/Form/StageFilterType.php

namespace Lulhum\RepartitionMedecineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lulhum\UserBundle\Entity\User;

class StageFilterType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
            ->add('stageCategories', 'entity', array(
                'label' => 'Modèles',
                'class' => 'LulhumRepartitionMedecineBundle:StageCategory',
                'multiple' => true,
                'required' => false,
            ))
            ->add('periods', 'entity', array(
                'label' => 'Périodes',
                'class' => 'LulhumRepartitionMedecineBundle:Period',
                'multiple' => true,
                'required' => false,
            ))
            ->add('categoriesOr', 'entity', array(
                'label' => 'Catégories (Ou)',
                'class' => 'LulhumRepartitionMedecineBundle:Category',
                'multiple' => true,
                'required' => false,
            ))
            ->add('categoriesAnd', 'entity', array(
                'label' => 'Catégories (Et)',
                'class' => 'LulhumRepartitionMedecineBundle:Category',
                'multiple' => true,
                'required' => false,
            ))
            ->add('promotions', 'choice', array(
                'choices' => User::PROMOTIONS,
                'label' => 'Promotions',
                'multiple' => true,
                'required' => false,
            ))
            ->add('group', 'choice', array(
                'choices' => User::GROUPS,
                'label' => 'Groupe',
                'required' => false,
                'empty_value' => 'Indifférent',
            ))
            ->add('locked', 'choice', array(
                'choices' => array(0 => 'Non', 1 => 'Oui'),
                'label' => 'Accepté',
                'required' => false,
                'empty_value' => 'Indifférent',
            ))
            ->add('users', 'entity', array(
                'label' => 'Utilisateurs',
                'class' => 'LulhumUserBundle:User',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('u')
                                      ->addOrderBy('u.lastname')
                                      ->addOrderBy('u.firstname');
                },
                'multiple' => true,
                'required' => false,
            ))
            ->add('filtrer', 'submit');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\RepartitionMedecineBundle\Entity\StageFilter',
        ));
    }
    public function getName()
    {
        return 'lulhum_repartitionmedecine_stagefilter';
    }
}
