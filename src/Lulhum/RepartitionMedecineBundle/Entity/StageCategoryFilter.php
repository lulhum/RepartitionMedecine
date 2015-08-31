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

    public function addLocation(Location $location)
    {
        $this->locations[] = $location;
    }

    public function getCategoriesOr()
    {
        return $this->categoriesOr;
    }

    public function setCategoriesOr(ArrayCollection $categoriesOr)
    {
        $this->categoriesOr = $categoriesOr;
    }

    public function addCategoryOr(Category $category)
    {
        $this->categoriesOr[] = $category;
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
            'locations' => $this->locations,
            'categoriesOr' => $this->categoriesOr,
            'categoriesAnd' => $this->categoriesAnd,
        );
    }
}