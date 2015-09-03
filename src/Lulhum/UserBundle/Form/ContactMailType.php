<?php
// src/Lulhum/UserBundle/Form/ContactMailType.php

namespace Lulhum\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactMailType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
            ->add('from', 'email', array(
                'label' => 'Expéditeur',
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
            'data_class' => 'Lulhum\UserBundle\Util\Mail',
        ));
    }
    public function getName()
    {
        return 'lulhum_user_contactmail';
    }
}
