<?php
// src/Lulhum/CMSBundle/Form/PageType.php

namespace Lulhum\CMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lulhum\CMSBundle\Entity\Page;

class PageType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Titre',
            ))
            ->add('menu', 'text', array(
                'label' => 'Élément de menu',
                'required' => false
            ))
            ->add('visibility', 'choice', array(
                'label' => 'Visibilité',
                'choices' => PAGE::VISIBILITIES,
                'empty_value' => 'Publique',
                'required' => false,
            ))
            ->add('content', 'textarea', array(
                'label' => false,
            ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\CMSBundle\Entity\Page',
        ));
    }
    public function getName()
    {
        return 'lulhum_cms_page';
    }
} 