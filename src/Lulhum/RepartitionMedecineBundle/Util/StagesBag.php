<?php
// src/Lulhum/RepartitionMedecineBundle/Util/StagesBag.php

namespace Lulhum\RepartitionMedecineBundle\Util;

use Doctrine\Common\Collections\ArrayCollection;
use Lulhum\UserBundle\Entity\User;

class StagesBag
{    
    protected $user = null;

    protected $proposals;

    public function __construct()
    {
        $this->proposals = new ArrayCollection();
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getProposals()
    {
        return $this->proposals;
    }

    public function setProposals(ArrayCollection $proposals)
    {
        $this->proposals = $proposals;
    }
}