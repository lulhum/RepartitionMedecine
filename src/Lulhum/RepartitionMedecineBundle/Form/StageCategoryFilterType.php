<?php
// src/Lulhum/RepartitionMedecineBundle/Form/StageCategoryFilterType.php

namespace Lulhum\RepartitionMedecineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StageCategoryFilterType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
            ->add('locations', 'entity', array(
                'label' => 'Lieux',
                'class' => 'LulhumRepartitionMedecineBundle:Location',
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
            ->add('filtrer', 'submit');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\RepartitionMedecineBundle\Entity\StageCategoryFilter',
        ));
    }
    public function getName()
    {
        return 'lulhum_repartitionmedecine_stagecategoryfilter';
    }
} 