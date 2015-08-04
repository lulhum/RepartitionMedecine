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
        $dt = new \DateTime();
        $years = array($dt->format('Y'));
        $dt->add(new \DateInterval('P1Y'));
        $years[] = $dt->format('Y');
        $builder
            ->add('date', 'datetime', array(
                'years' => $years,
                'label' => 'Fin de la répartition:'
            ))
            ->add('delay', 'time', array(
                'label' => 'Délais de fin:'
            ));
        foreach($this->options as $option) {            
            $builder->add($option['field'], $option['type'], $option['options']);            
        }
        $builder->add('Ouvrir', 'submit');
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
