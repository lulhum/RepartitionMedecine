<?php
// src/Lulhum/RepartitionMedecineBundle/Entity/StageCategoryFilter.php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class StageCategoryFilter
{    
    protected $locations;

    protected $categoriesOr;

    protected $categoriesAnd;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
        $this->categoriesOr = new ArrayCollection();
        $this->categoriesAnd = new ArrayCollection();
    }

    public function getLocations()
    {
        return $this->locations;
    }

    public function setLocations(ArrayCollection $locations)
    {
        $this->locations = $locations;
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
}