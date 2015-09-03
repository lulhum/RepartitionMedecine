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
                    'attr' => array('class' => 'form-control bottom-buffer'),
                ));
        }
        elseif($paramType === 'group') {
            $builder
                ->add('group', 'choice', array(
                    'label' => Requirement::TYPES[$paramType],
                    'choices' => User::GROUPS,
                    'attr' => array('class' => 'form-control bottom-buffer'),
                ));
        }
        elseif(preg_match('/InCategory/', $paramType)) {
            $builder
                ->add('category', 'entity', array(
                    'class' =>  'Lulhum\RepartitionMedecineBundle\Entity\Category',
                    'label' => Requirement::TYPES[$paramType],
                    'query_builder' => function($repository) use (&$proposal){
                        return $repository->findByProposalIdQB($proposal);
                    },
                    'attr' => array('class' => 'form-control bottom-buffer'),
                ))
                ->add('maxStages', 'integer', array(
                    'label' => false,
                    'data' => 1,
                    'attr' => array('class' => 'form-control bottom-buffer'),
                ));
        }
        else {
            $builder
                ->add('maxStages', 'integer', array(
                    'label' => false,
                    'data' => 1,
                    'attr' => array('class' => 'form-control bottom-buffer'),
                ));
        }
    }
    
    public function getName()
    {
        return 'lulhum_repartitionmedecine_requirementparams';
    }
}
