<?php
// src/Lulhum/CMSBundle/Repository/SiteNewRepository.php

namespace Lulhum\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Lulhum\UserBundle\Entity\User;

class SiteNewRepository extends EntityRepository
{

    public function findAllValid($user)
    {
        return array_filter($this->findAll(), function($new) use (&$user) {
            return count($new->getVisibility()) === 0 || (!is_null($user) && in_array($user->getPromotion(), $new->getVisibility()));
        });
    }

}