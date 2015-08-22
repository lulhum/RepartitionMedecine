<?php

// src/Lulhum/RepartitionMedecineBundle/Form/DeadlineType.php

namespace Lulhum\DeadlineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DeadlineType extends AbstractType
{

    public function __construct($options = null) {
        $this->options = $options;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'datetime', array(
                'label' => 'Fin de la répartition',
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'dd/MM/yyyy HH:mm'
            ))
            ->add('delay', 'time', array(
                'label' => 'Délais de fin'
            ));
        if(!is_null($this->options)) {
            foreach($this->options as $option) {            
                $builder->add($option['field'], $option['type'], $option['options']);            
            }
        }
        $builder->add('ouvrir', 'submit');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lulhum\DeadlineBundle\Entity\Deadline',
        ));
    }
    public function getName()
    {
        return 'lulhum_deadline_deadline';
    }
} 
