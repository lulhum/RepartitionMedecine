<?php
// src/Lulhum/RepartitionMedecineBundle/Form/StageProposalType.php

namespace Lulhum\RepartitionMedecineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lulhum\RepartitionMedecineBundle\Form\PeriodType;

class StageProposalType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Nom',
                'required' => false,
            ))
            ->add('description', 'text', array(
                'label' => 'Description',
                'required' => false,
            ))
            ->add('category', 'entity', array(
                'class' => 'LulhumRepartitionMedecineBundle:StageCategory',
                'label' => 'Modèle',
            ))
            ->add('period', 'entity', array(
                'label' => 'Période',
                'class' => 'LulhumRepartitionMedecineBundle:Period',
            ))
            ->add('new_period', new PeriodType(), array(
                'label' => false,
                'required' => false,
            ))
            ->add('requirements', 'collection', array(
                'label' => false,
                'type' => new RequirementType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
            ->add('valider', 'submit');            
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\RepartitionMedecineBundle\Entity\StageProposal',
        ));
    }
    
    public function getName()
    {
        return 'lulhum_repartitionmedecine_stageproposal';
    }
} 