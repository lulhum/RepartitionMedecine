<?php
// src/Lulhum/RepartitionMedecineBundle/Form/RequirementParamsType.php

namespace Lulhum\RepartitionMedecineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lulhum\UserBundle\Entity\User;
use Lulhum\RepartitionMedecineBundle\Entity\Requirement;

class RequirementParamsType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $paramType = $this->options['paramType'];
        $proposal = $this->options['proposal'];
        if($paramType === 'promotion') {
            $builder
                ->add('promotion', 'choice', array(
                    'label' => Requirement::TYPES[$paramType],
                    'choices' => User::PROMOTIONS,
                ));
        }
        elseif($paramType === 'group') {
            $builder
                ->add('group', 'choice', array(
                    'label' => Requirement::TYPES[$paramType],
                    'choices' => User::GROUPS,
                ));
        }
        elseif($paramType === 'maxStagesInCategory') {
            $builder
                ->add('category', 'entity', array(
                    'class' =>  'Lulhum\RepartitionMedecineBundle\Entity\Category',
                    'label' => Requirement::TYPES[$paramType],
                    'query_builder' => function($repository) use (&$proposal){
                        return $repository->findByProposalIdQB($proposal);
                    }
                ))
                ->add('maxStages', 'integer', array(
                    'label' => false,
                    'data' => 1,
                ));
        }
        elseif($paramType === 'maxChoicesInCategory') {
            $builder
                ->add('category', 'entity', array(
                    'class' =>  'Lulhum\RepartitionMedecineBundle\Entity\Category',
                    'label' => Requirement::TYPES[$paramType],
                    'query_builder' => function($repository) use (&$proposal){
                        return $repository->findByProposalIdQB($proposal);
                    }
                ))
                ->add('maxChoices', 'integer', array(
                    'label' => false,
                    'data' => 1,
                ));
        }
        elseif($paramType === 'maxStagesInStageCategory') {
            $builder
                ->add('maxStages', 'integer', array(
                    'label' => false,
                    'data' => 1,
                ));
        }
    }
    
    public function getName()
    {
        return 'lulhum_repartitionmedecine_requirementparams';
    }
} 