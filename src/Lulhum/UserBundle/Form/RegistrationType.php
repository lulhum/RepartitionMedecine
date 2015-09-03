<?php
// src/Lulhum/UserBundle/Form/RegistrationType.php

namespace Lulhum\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\Container;

class RegistrationType extends AbstractType
{
    private $class;
    private $container;

    public function __construct($class, Container $container)
    {
        $this->class = $class;
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $schoolYear = $this->container->get("lulhum_toolbox")->getCurrentSchoolYear();
        $builder
            ->add('lastname', null, array('label' => 'Nom de famille'))
            ->add('firstname', null, array('label' => 'Prénom'))
            ->add('studentId', null, array('label' => 'Numéro Étudiant'))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('phone', null, array('label' => 'Numéro de portable'))
            ->add('promotion', 'choice', array(
                'label' => 'Promotion pour l\'année '.$schoolYear,
                'choices' => \Lulhum\UserBundle\Entity\User::getPromotionChoicesValues()
            ))
            ->add('present', 'choice', array(
                'label' => 'Présent pendant la période de répartition',
                'choices' => array('Oui' => 'Oui', 'Non' => 'Non', 'Variable' => 'Variable')
            ))
            ->add('internetAccess', 'choice', array(
                'label' => 'Accès Internet pendant la période de répartition',
                'choices' => array('Oui' => 'Oui', 'Non' => 'Non', 'Variable' => 'Variable')
            ))
            ->add('proxy', 'entity', array(
                'label' => 'Personne de confiance ayant procuration en cas d\'absence',
                'empty_value' => 'Pas de procuration',
                'required' => false,
                'class' => 'LulhumUserBundle:User'             
            ))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'registration',
        ));
    }

    public function getName()
    {
        return 'app_user_registration';
    }
}
