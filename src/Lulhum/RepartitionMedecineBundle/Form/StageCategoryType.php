<?php
// src/Lulhum/RepartitionMedecineBundle/Form/StageCategoryType.php

namespace Lulhum\RepartitionMedecineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lulhum\RepartitionMedecineBundle\Form\CategoryType;
use Lulhum\RepartitionMedecineBundle\Form\LocationType;

class StageCategoryType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'Nom du modèle'))
            ->add('description', 'textarea', array(
                'label' => 'Description',
                'required' => false,
            ))
            ->add('location', 'entity', array(
                'label' => 'Lieu',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('l')
                                      ->orderBy('l.name');
                },
                'class' => 'LulhumRepartitionMedecineBundle:Location',
            ))
            ->add('new_location', new LocationType(), array(
                'label' => false,
                'required' => false,
            ))
            ->add('categories', 'entity', array(
                'class' => 'LulhumRepartitionMedecineBundle:Category',
                'label' => 'Catégories',
                'multiple' => true,
                'required' => false,
            ))
            ->add('new_categories', 'collection', array(
                'label' => false,
                'type' => new CategoryType(),
                'allow_add' => true,
                'allow_delete' => true
            ))
            ->add('valider', 'submit');            
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\RepartitionMedecineBundle\Entity\StageCategory',
        ));
    }
    public function getName()
    {
        return 'lulhum_repartitionmedecine_stagecategory';
    }
} 