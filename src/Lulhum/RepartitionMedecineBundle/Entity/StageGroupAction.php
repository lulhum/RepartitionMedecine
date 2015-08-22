<?php
// src/Lulhum/RepartitionMedecineBundle/Entity/StageGroupAction.php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class StageGroupAction
{
    const ACTIONS = array(
        'unlock' => 'Refuser',
        'lock' => 'Accepter',
        'delete' => 'Supprimer',
     );

    protected $stages;

    protected $action=null;

    public function __construct()
    {
        $this->stages = new ArrayCollection();
    }

    public function getStages()
    {
        return $this->stages;
    }

    public function setStages(ArrayCollection $stages)
    {
        $this->stages = $stages;
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
        foreach($this->getStages() as $stage) {
            $stage->setLocked(true);
        }
    }

    public function executeUnlockAction()
    {
        foreach($this->getStages() as $stage) {
            $stage->setLocked(false);
        }
    }
    
}