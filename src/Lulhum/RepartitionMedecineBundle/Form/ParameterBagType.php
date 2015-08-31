<?php
// src/Lulhum/RepartitionMedecineBundle/Form/ParameterBagType.php

namespace Lulhum\RepartitionMedecineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ParameterBagType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
            ->add('parameters', 'collection', array(
                'type' => new ParameterType(),
                'label' => false,
                'options' => array('label' => false),
            ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\RepartitionMedecineBundle\Entity\ParameterBag',
        ));
    }
    public function getName()
    {
        return 'lulhum_repartitionmedecine_parameterbag';
    }
} 