<?php
// src/Lulhum/CMSBundle/Form/SiteNewType.php

namespace Lulhum\CMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lulhum\UserBundle\Entity\User;

class SiteNewType extends AbstractType
{

    public function __construct($number) {
        $this->number = $number;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'text', array(
                'label' => false,
            ))
            ->add('visibility', 'choice', array(
                'multiple' => true,
                'required' => false,
                'choices' => USER::PROMOTIONS,
                'label' => 'Visibilité',
            ))
            ->add('level', 'choice', array(
                'label' => 'Importance',
                'choices' => array(
                    'default' => 'Normale',
                    'warning' => 'Importante',
                    'danger' => 'Très importante',
                )));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\CMSBundle\Entity\SiteNew',
        ));
    }
    public function getName()
    {
        return 'lulhum_cms_new'.$this->number;
    }
}