<?php
// src/Lulhum/RepartitionMedecineBundle/Util/StageProposalGroupAction.php

namespace Lulhum\RepartitionMedecineBundle\Util;

class StageProposalGroupAction extends GroupAction
{
    protected $actions = array(
        'start' => 'Démarrer la répartition',
        'unlock' => 'Activer',
        'lock' => 'Désactiver',
        'addRequirement' => 'Ajouter une contrainte',
        'removeRequirement' => 'Supprimer des contraintes',
        'clone' => 'Cloner pour les périodes',        
    );

    protected $entity = 'LulhumRepartitionMedecineBundle:StageProposal';

    public function getProposals()
    {
        return $this->getEntities();
    }

    public function setProposals($proposals)
    {
        $this->setEntities($proposals);
    }
    
    public function executeLockAction()
    {
        foreach($this->getProposals() as $proposal) {
            $proposal->setLocked(true);
        }
    }

    public function executeUnlockAction()
    {
        foreach($this->getProposals() as $proposal) {
            $proposal->setLocked(false);
        }
    }
    
}