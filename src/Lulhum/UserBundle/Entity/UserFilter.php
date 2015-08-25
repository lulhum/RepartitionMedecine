<?php
// src/Lulhum/UserBundle/Entity/UserFilter.php

namespace Lulhum\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class UserFilter
{    
    protected $promotions;

    protected $group = null;

    protected $periods;

    protected $categories;
    
    public function __construct()
    {
        $this->promotions = array();
        $this->periods = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getPromotions()
    {
        return $this->promotions;
    }

    public function setPromotions($promotions)
    {
        $this->promotions = $promotions;
    }

    public function addPromotion($promotion)
    {
        $this->promotions[] = $promotion;
    }

    public function getPeriods()
    {
        return $this->periods;
    }

    public function setPeriods(ArrayCollection $periods)
    {
        $this->periods = $periods;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function setCategories(ArrayCollection $categories)
    {
        $this->categories = $categories;
    }
    
    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup($group)
    {
        $this->group = $group;
    }

    public function getForFindBy()
    {
        $response = array();
        if(count($this->promotions) > 0) {
            $response['promotion'] = $this->promotions;
        }
        if(!is_null($this->group)) {
            $response['repartitionGroup'] = $this->group;
        }

        return $response;
    }

    public function getCollections()
    {
        return array(
            'periods' => $this->periods,
            'categories' => $this->categories,
        );
    }
    
}