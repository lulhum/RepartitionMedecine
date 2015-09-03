<?php 
// src/Lulhum/RepartitionMedecineBundle/Form/GroupActionType.php

namespace Lulhum\RepartitionMedecineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lulhum\RepartitionMedecineBundle\Entity\StageGroupAction;

class GroupActionType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder->addEventListener(
            formEvents::PRE_SET_DATA,
            function(FormEvent $event) {
                $filter = $this->options['filter'];
                $max = $this->options['max'];
                $offset = $this->options['offset'];
                $form = $event->getForm();
                $class = $event->getData()->getEntity();
                $form
                    ->add('entities', 'entity', array(
                        'class' => $class,
                        'query_builder' => function($repository) use (&$filter, &$max, &$offset) {
                            return $repository->filteredFindQB($filter, $max, $offset);
                        },
                        'expanded' => true,
                        'multiple' => true,
                        'label' => false,
                    ))
                    ->add('action', 'choice', array(
                        'label' => 'Action groupée',
                        'choices' => $event->getData()->getActions(),
                    ));
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\RepartitionMedecineBundle\Util\GroupAction'
        ));
    }
    
    public function getName()
    {
        return 'lulhum_repartitionmedecine_groupaction';
    }
}
