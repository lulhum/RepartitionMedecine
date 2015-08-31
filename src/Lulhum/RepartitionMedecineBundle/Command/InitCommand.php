<?php
// src/Acme/DemoBundle/Command/initCommand.php

namespace Lulhum\RepartitionMedecineBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Lulhum\RepartitionMedecineBundle\Entity\Parameter;
use Lulhum\DeadlineBundle\Entity\Deadline;

class InitCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('repartitionmedecine:init')
            ->setDescription('Initialisation de la plateforme')
            ->addArgument('confirm', InputArgument::REQUIRED, 'Confirmation')
            ->addArgument('allDefaults', InputArgument::OPTIONAL, 'Initialiser tous les paramètres à leurs valeurs par défaut') 
            ;
        foreach(Parameter::PARAMETERS as $key => $param) {
            if(!is_null($param['values'])) {
                $this->addArgument($key, InputArgument::OPTIONAL, $param['description']);
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if($input->getArgument('confirm') === 'Oui') {
            
            $em = $this->getContainer()->get('doctrine.orm.entity_manager');
         
            $output->writeln('Initialisation des parametres');
            $parameterRepository = $em->getRepository('LulhumRepartitionMedecineBundle:Parameter');
            foreach(Parameter::PARAMETERS as $key => $param) {                
                if(!$parameterRepository->findOneByName($key)) {
                    if($input->getArgument('allDefaults') === 'Oui' && is_null($param['values'])) {
                        $em->persist(new Parameter($key));
                    }
                    else {
                        $em->persist(new Parameter($key, $input->getArgument($key)));
                    }
                }
            }
            
            $output->writeln('Initialisation des deadlines');
            $deadlineRepository = $em->getRepository('LulhumDeadlineBundle:Deadline');
            if(!$deadlineRepository->findOneByName('repartitionGroupDFASM1')) {
                $em->persist(new Deadline('repartitionGroupDFASM1', null, null, 'lulhum_repartitionmedecine_repartition', array(
                    'method' => 'finalizeGroupRepartition',
                    'args' => array('DFASM1')
                )));
            }
            if(!$deadlineRepository->findOneByName('repartitionGroupDFASM2')) {
                $em->persist(new Deadline('repartitionGroupDFASM2', null, null, 'lulhum_repartitionmedecine_repartition', array(
                    'method' => 'finalizeGroupRepartition',
                    'args' => array('DFASM2')
                )));
            }

            $em->flush();
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $answers = array('Oui', 'Non');
        $choice = $this->getHelper('dialog')->select(
            $output,
            'Initialise la plateforme. Continuer ? (Non)',
            $answers,
            1
        );
        $input->setArgument('confirm', $answers[$choice]);

        if($choice == 0) {
            $answers = array('Oui', 'Non');
            $choice = $this->getHelper('dialog')->select(
                $output,
                'Initialiser tous les paramètres à leurs valeurs par défaut (Non)',
                $answers,
                1
            );
            $input->setArgument('allDefaults', $answers[$choice]);

            if($choice == 1) {
                foreach(Parameter::PARAMETERS as $key => $param) {
                    if(!is_null($param['values'])) {
                        $answers = $param['values'];
                        $choice = $this->getHelper('dialog')->select(
                            $output,
                            $param['description'].' ('.$param['default'].'):',
                            $answers,
                            $param['default']
                        );
                        $input->setArgument($key, $choice);
                    }
                }
            }
        }
    }

}
