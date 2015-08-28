<?php
// src/Lulhum/RepartitionMedecineBundle/Entity/StageProposalFilter.php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class StageProposalFilter
{    
    protected $periods;

    protected $stageCategories;

    protected $categoriesOr;

    protected $categoriesAnd;

    protected $promotions;

    protected $locked=null;

    public function __construct()
    {
        $this->periods = new ArrayCollection();
        $this->stageCategories = new ArrayCollection();
        $this->categoriesOr = new ArrayCollection();
        $this->categoriesAnd = new ArrayCollection();
        $this->promotions = array();
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

    public function getStageCategories()
    {
        return $this->stageCategories;
    }

    public function setStageCategories(ArrayCollection $stageCategories)
    {
        $this->stageCategories = $stageCategories;        
    }

    public function addStageCategory(StageCategory $stageCategory)
    {
        $this->stageCategories[] = $stageCategory;
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

    public function getCollections()
    {
        return array(   
            'periods' => $this->periods,
            'stageCategories' => $this->stageCategories,
            'categoriesOr' => $this->categoriesOr,
            'categoriesAnd' => $this->categoriesAnd,
        );
    }
    
}