<?php
// src/Lulhum/RepartitionMedecineBundle/Form/LocationType.php

namespace Lulhum\RepartitionMedecineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LocationType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'Nom'))
            ->add('description', 'text', array(
                'label' => 'Description',
                'required' => false
            ))
            ->add('distance', 'integer', array('label' => 'distance'));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\RepartitionMedecineBundle\Entity\Location',
        ));
    }
    public function getName()
    {
        return 'lulhum_repartitionmedecine_location';
    }
} 