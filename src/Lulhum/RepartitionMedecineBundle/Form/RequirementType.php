<?php
// src/Lulhum/RepartitionMedecineBundle/Form/RequirementType.php

namespace Lulhum\RepartitionMedecineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lulhum\RepartitionMedecineBundle\Entity\Requirement;

class RequirementType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                'label' => 'Type',
                'choices' => Requirement::TYPES,
                'attr' => array('class' => 'form-control'),
                'label_attr' => array('class' => 'label-control'),
            ))
            ->add('params', 'text', array(
                'label' => 'Paramètre',
                'attr' => array('class' => 'form-control'),
                'label_attr' => array('class' => 'label-control'),
            ))
            ->add('strict', 'checkbox', array(
                'label' => 'Stricte',
                'required' => false,
                'attr' => array('class' => 'form-control'),
                'label_attr' => array('class' => 'label-control'),
            ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\RepartitionMedecineBundle\Entity\Requirement',
        ));
    }
    public function getName()
    {
        return 'lulhum_repartitionmedecine_requirement';
    }
} 