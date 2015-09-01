<?php
// src/Lulhum/RepartitionMedecineBundle/Util/StageGroupAction.php

namespace Lulhum\RepartitionMedecineBundle\Util;

class StageGroupAction extends GroupAction
{
    protected $actions = array(
        'lock' => 'Accepter',
        'unlock' => 'Refuser',
        'delete' => 'Supprimer',
     );

    protected $entity = 'LulhumRepartitionMedecineBundle:Stage';

    public function getStages()
    {
        return $this->getEntities();
    }

    public function setStages($stages)
    {
        $this->setEntities($stages);
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