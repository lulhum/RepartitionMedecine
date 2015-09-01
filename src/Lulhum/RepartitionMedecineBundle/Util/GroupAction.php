<?php
// src/Lulhum/RepartitionMedecineBundle/Util/GroupAction.php

namespace Lulhum\RepartitionMedecineBundle\Util;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class GroupAction
{
    protected $actions = array();

    protected $entities;

    protected $action=null;

    protected $entity=null;

    public function __construct()
    {
        $this->entities = new ArrayCollection();
    }

    public function getEntity()
    {
        if(is_null($this->entity)) {
            throw new \Exception('Entity name must be set');
        }

        return $this->entity;
    }

    public function getActions()
    {
        if(count($this->actions) == 0) {
            throw new \Exception('Actions must be set');
        }

        return $this->actions;
    }

    public function getEntities()
    {
        return $this->entities;
    }

    public function setEntities(ArrayCollection $entities)
    {
        $this->entities = $entities;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getTextAction()
    {
        return $this->actions[$this->action];
    }

    public function setAction($action)
    {
        if(array_key_exists($action, $this->actions)) {
            $this->action = $action;
        }
    }

    public function executeAction()
    {
        call_user_func(array($this, 'execute'.ucfirst($this->getAction()).'Action'));
    }

    public static function merge(EntityManager $em, Session $session, $key)
    {
        if($session->has($key) && $session->get($key) instanceof self) {
            $groupAction = $session->get($key);
            if($groupAction->getEntities()->count() == 0) {

                return null;
            }
            $groupAction->setEntities($groupAction->getEntities()->map(function($item) use (&$em) {
                $managedItem = $em->merge($item);
                $em->refresh($managedItem);
                return $managedItem;
            }));

            return $groupAction;
        }

        return null;
    }
    
}