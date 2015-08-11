<?php
// src/Lulhum/RepartitionMedecineBundle/Form/StageType.php

namespace Lulhum\RepartitionMedecineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Form\FormEvents;
use \Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StageType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', 'entity', array(
                'label' => 'Utilisateur',
                'class' => 'LulhumUserBundle:User',
            ))
            ->add('proposal', 'entity', array(
                'label' => 'Proposition de stage',
                'class' => 'LulhumRepartitionMedecineBundle:StageProposal',
            ))
            ->add('locked', 'choice', array(
                'label' => 'Accepté',
                'choices' => array(1 => 'Oui', 0 => 'Non'),
                'expanded' => true,
               ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();
                $data->setLocked( $data->getLocked() === false ? 0 : 1 );
                $event->setData($data);
            })
            ->add('valider', 'submit');            
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\RepartitionMedecineBundle\Entity\Stage',
        ));
    }
    
    public function getName()
    {
        return 'lulhum_repartitionmedecine_stage';
    }
} 