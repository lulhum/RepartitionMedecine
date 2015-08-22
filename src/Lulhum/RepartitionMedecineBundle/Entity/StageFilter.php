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

    public function __construct()
    {
        $this->periods = new ArrayCollection();
        $this->stageProposals = new ArrayCollection();
        $this->categoriesOr = new ArrayCollection();
        $this->categoriesAnd = new ArrayCollection();
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

    public function getCollections()
    {
        return array(   
            'stageProposals' => $this->stageProposals,
            'periods' => $this->periods,
            'categoriesOr' => $this->categoriesOr,
            'categoriesAnd' => $this->categoriesAnd,
        );
    }
    
}