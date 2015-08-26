<?php
// src/Lulhum/RepartitionMedecineBundle/Entity/StageProposalGroupAction.php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class StageProposalGroupAction
{
    const ACTIONS = array(
        'unlock' => 'Activer',
        'lock' => 'Désactiver',
        'addConstraint' => 'Ajouter une Contrainte',
        'start' => 'Démarrer la répartition',
        'clone' => 'Cloner pour les périodes',
    );

    protected $proposals;

    protected $action=null;

    public function __construct()
    {
        $this->proposals = new ArrayCollection();
    }

    public function getProposals()
    {
        return $this->proposals;
    }

    public function setProposals(ArrayCollection $proposals)
    {
        $this->proposals = $proposals;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setAction($action)
    {
        if(array_key_exists($action, self::ACTIONS)) {
            $this->action = $action;
        }
    }

    public function executeAction()
    {
        call_user_func(array($this, 'execute'.ucfirst($this->getAction()).'Action'));
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