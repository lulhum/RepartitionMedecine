<?php
// src/Lulhum/RepartitionMedecineBundle/Form/StageProposalGroupActionType.php

namespace Lulhum\RepartitionMedecineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lulhum\RepartitionMedecineBundle\Entity\StageProposalGroupAction;

class StageProposalGroupActionType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $filter = $this->options['filter'];
        $max = $this->options['max'];
        $offset = $this->options['offset'];
        $builder
            ->add('proposals', 'entity', array(
                'class' => 'LulhumRepartitionMedecineBundle:StageProposal',
                'query_builder' => function($repository) use (&$filter, &$max, &$offset) {
                    return $repository->filteredFindQB($filter, $max, $offset);
                },
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('action', 'choice', array(
                'label' => 'Action groupée',
                'choices' => StageProposalGroupAction::ACTIONS,
            ))
            ->add('appliquer', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\RepartitionMedecineBundle\Entity\StageProposalGroupAction'
        ));
    }
    
    public function getName()
    {
        return 'lulhum_repartitionmedecine_stageproposalgroupaction';
    }
} 