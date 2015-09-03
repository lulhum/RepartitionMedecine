<?php
// src/Lulhum/RepartitionMedecineBundle/Entity/ParameterBag.php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class ParameterBag
{    
    protected $parameters;

    public function __construct()
    {
        $this->parameters = new ArrayCollection();
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters(ArrayCollection $parameters)
    {
        $this->parameters = $parameters;
    }
}
