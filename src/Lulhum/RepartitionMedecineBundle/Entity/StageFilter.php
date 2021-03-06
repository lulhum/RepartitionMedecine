<?php
// src/Lulhum/RepartitionMedecineBundle/Entity/StageFilter.php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class StageFilter
{    
    protected $stageProposals;

    protected $periods;

    protected $categoriesOr;

    protected $categoriesAnd;

    protected $locked=null;

    protected $promotions;

    protected $group=null;

    protected $users;

    protected $stageCategories;

    public function __construct()
    {
        $this->periods = new ArrayCollection();
        $this->stageProposals = new ArrayCollection();
        $this->categoriesOr = new ArrayCollection();
        $this->categoriesAnd = new ArrayCollection();
        $this->promotions = array();
        $this->users = new ArrayCollection();
        $this->stageCategories = new ArrayCollection();
    }

    public function getPeriods()
    {
        return $this->periods;
    }

    public function setPeriods(ArrayCollection $periods)
    {
        $this->periods = $periods;
    }

    public function addPeriod(Period $period)
    {
        $this->periods[] = $period;
    }

    public function getStageProposals()
    {
        return $this->stageProposals;
    }

    public function setStageProposals(ArrayCollection $stageProposals)
    {
        $this->stageProposals = $stageProposals;        
    }

    public function addStageProposal(StageProposal $stageProposal)
    {
        $this->stageProposals[] = $stageProposal;
    }

    public function getCategoriesOr()
    {
        return $this->categoriesOr;
    }

    public function setCategoriesOr(ArrayCollection $categoriesOr)
    {
        $this->categoriesOr = $categoriesOr;
    }

    public function getCategoriesAnd()
    {
        return $this->categoriesAnd;
    }

    public function setCategoriesAnd(ArrayCollection $categoriesAnd)
    {
        $this->categoriesAnd = $categoriesAnd;
    }

    public function getPromotions()
    {
        return $this->promotions;
    }

    public function setPromotions($promotions)
    {
        $this->promotions = $promotions;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup($group)
    {
        $this->group = $group;
    }

    public function getLocked()
    {
        return $this->locked;
    }

    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers(ArrayCollection $users)
    {
        $this->users = $users;
    }

    public function getStageCategories()
    {
        return $this->stageCategories;
    }

    public function setStageCategories(ArrayCollection $stageCategories)
    {
        $this->stageCategories = $stageCategories;
    }

    public function getCollections()
    {
        return array(   
            'stageProposals' => $this->stageProposals,
            'periods' => $this->periods,
            'categoriesOr' => $this->categoriesOr,
            'categoriesAnd' => $this->categoriesAnd,
            'users' => $this->users,
            'stageCategories' => $this->stageCategories,
        );
    }
    
}
