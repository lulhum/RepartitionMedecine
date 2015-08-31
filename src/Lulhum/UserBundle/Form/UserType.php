<?php

// src/Lulhum/UserBundle/Form/UserType.php

namespace Lulhum\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', null, array('label' => 'Nom de famille:'))
            ->add('firstname', null, array('label' => 'Prénom:'))
            ->add('studentId', null, array('label' => 'Numéro Étudiant:'))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('phone', null, array('label' => 'Numéro de portable:'))
            ->add('promotion', 'choice', array(
                'label' => 'Promotion pour l\'année '.$this->options['schoolyear'].':',
                'choices' => \Lulhum\UserBundle\Entity\User::getPromotionChoicesValues(),
                'empty_value' => 'Non défini',
                'required' => false
            ))
            ->add('repartitionGroup', 'choice', array(
                'choices' => array('A' => 'A', 'B' => 'B'),
                'label' => 'Groupe de répartition:',
                'empty_value' => 'Non défini',
                'required' => false
            ))
            ->add('repartitionGroupForce', 'checkbox', array(
                'label' => 'Forcer le groupe de répartition:',
                'required' => false
            ))
            ->add('present', 'choice', array(
                'label' => 'Présent pendant la période de répartition:',
                'choices' => array('Oui' => 'Oui', 'Non' => 'Non', 'Variable' => 'Variable')
            ))
            ->add('internetAccess', 'choice', array(
                'label' => 'Accès Internet pendant la période de répartition:',
                'choices' => array('Oui' => 'Oui', 'Non' => 'Non', 'Variable' => 'Variable')
            ))
            ->add('proxy', 'entity', array(
                'label' => 'Personne de confiance ayant procuration en cas d\'absence:',
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
                'required' => $this->options['passwordRequired']
            ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\UserBundle\Entity\User'
        ));
    }
    public function getName()
    {
        return 'lulhum_userbundle_user';
    }
} 