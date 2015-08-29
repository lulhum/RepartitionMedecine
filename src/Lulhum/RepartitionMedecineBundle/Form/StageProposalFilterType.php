<?php
// src/Lulhum/RepartitionMedecineBundle/Form/StageProposalFilterType.php

namespace Lulhum\RepartitionMedecineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lulhum\UserBundle\Entity\User;

class StageProposalFilterType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
            ->add('periods', 'entity', array(
                'label' => 'Périodes',
                'class' => 'LulhumRepartitionMedecineBundle:Period',
                'multiple' => true,
                'required' => false,
            ))
            ->add('stageCategories', 'entity', array(
                'label' => 'Modèles',
                'class' => 'LulhumRepartitionMedecineBundle:StageCategory',
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
                'choices' => USER::PROMOTIONS,
                'label' => 'Promotions',
                'multiple' => true,
                'required' => false,
            ))         
            ->add('filtrer', 'submit');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\RepartitionMedecineBundle\Entity\StageProposalFilter',
        ));
    }
    public function getName()
    {
        return 'lulhum_repartitionmedecine_stageproposalfilter';
    }
} 