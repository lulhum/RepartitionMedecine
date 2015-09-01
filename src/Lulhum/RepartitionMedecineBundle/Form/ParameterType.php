<?php
// src/Lulhum/RepartitionMedecineBundle/Form/ParameterType.php

namespace Lulhum\RepartitionMedecineBundle\Form;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lulhum\RepartitionMedecineBundle\Entity\Parameter;

class ParameterType extends AbstractType
{

    private $em;

    public function __construct(Doctrine $doctrine, $options = null) {
        $this->options = $options;
        $this->em = $doctrine->getManager();
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            formEvents::PRE_SET_DATA,
            function(FormEvent $event) {
                $form = $event->getForm();
                $name = $event->getData()->getName();
                if(is_null(Parameter::PARAMETERS[$name]['values'])) {
                    if(Parameter::PARAMETERS[$name]['entity']) {
                        $form->add('value', 'choice', array(
                            'label' => Parameter::PARAMETERS[$name]['description'],
                            'choices' => $this->em->getRepository(Parameter::PARAMETERS[$name]['entity'])->findChoices(),
                            'empty_value' => 'Défaut',
                            'required' => false,
                        ));                        
                    }
                    else {
                        $form->add('value', 'text', array(
                            'label' => Parameter::PARAMETERS[$name]['description'],
                        ));
                    }
                }
                else {
                    $form->add('value', 'choice', array(
                        'label' => Parameter::PARAMETERS[$name]['description'],
                        'choices' => Parameter::PARAMETERS[$name]['values'],
                        'expanded' => true,
                        'multiple' => false,
                    ));
                }
            }
        );
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\RepartitionMedecineBundle\Entity\Parameter',
            'parameterName' => null,
        ));
    }
    public function getName()
    {
        return 'lulhum_parameter_type';
    }
} 