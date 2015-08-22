<?php 
// src/Lulhum/RepartitionMedecineBundle/Form/StageGroupActionType.php

namespace Lulhum\RepartitionMedecineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lulhum\RepartitionMedecineBundle\Entity\StageGroupAction;

class StageGroupActionType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $filter = $this->options['filter'];
        $builder
            ->add('stages', 'entity', array(
                'class' => 'LulhumRepartitionMedecineBundle:Stage',
                'query_builder' => function($repository) use (&$filter) {
                    return $repository->filteredFindQB($filter);
                },
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('action', 'choice', array(
                'label' => 'Action groupée',
                'choices' => StageGroupAction::ACTIONS,
            ))
            ->add('appliquer', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\RepartitionMedecineBundle\Entity\StageGroupAction'
        ));
    }
    
    public function getName()
    {
        return 'lulhum_repartitionmedecine_stagegroupaction';
    }
} 