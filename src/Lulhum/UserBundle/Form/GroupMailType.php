<?php
// src/Lulhum/UserBundle/Form/GroupMailType.php

namespace Lulhum\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lulhum\UserBundle\Entity\User;

class GroupMailType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
            ->add('promotions', 'choice', array(
                'label' => 'Promotions',
                'multiple' => true,
                'choices' => User::PROMOTIONS,                
            ))
            ->add('group', 'choice', array(
                'label' => 'Groupe',
                'choices' => User::GROUPS,
                'empty_value' => 'Indifférent',
                'required' => false,
            ))
            ->add('title', 'text', array(
                'label' => 'Sujet',
            ))
            ->add('content', 'textarea', array(
                'label' => false,
            ));
            
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\UserBundle\Util\GroupMail',
        ));
    }
    public function getName()
    {
        return 'lulhum_user_groupmail';
    }
} 