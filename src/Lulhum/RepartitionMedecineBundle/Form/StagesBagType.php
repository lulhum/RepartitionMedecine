<?php
// src/Lulhum/RepartitionMedecineBundle/Form/StagesBagType.php

namespace Lulhum\RepartitionMedecineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StagesBagType extends AbstractType
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
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('u')
                                      ->addOrderBy('u.lastname')
                                      ->addOrderBy('u.firstname');
                }
            ))
            ->add('proposals', 'entity', array(
                'label' => 'Proposition de stage',
                'class' => 'LulhumRepartitionMedecineBundle:StageProposal',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('s')
                                      ->join('s.category', 'c')
                                      ->leftjoin('s.requirements', 'r', 'WITH', 'r.type = \'promotion\'')
                                      ->addOrderBy('r.params')
                                      ->addOrderBy('s.name')
                                      ->addOrderBy('c.name');
                },
                'group_by' => 'promotion',
                'multiple' => 'true'
            ));
         
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\RepartitionMedecineBundle\Util\StagesBag',
        ));
    }
    
    public function getName()
    {
        return 'lulhum_repartitionmedecine_stages';
    }
}