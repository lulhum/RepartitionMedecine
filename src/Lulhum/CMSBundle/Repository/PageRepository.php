<?php
// src/Lulhum/CMSBundle/Repository/PageRepository.php

namespace Lulhum\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\SecurityContext;

class PageRepository extends EntityRepository
{

    public function findChoices()
    {
        $choices = array();
        foreach($this->findBy(array(), array('title' => 'ASC')) as $choice) {
            $choices[$choice->getId()] = $choice->getTitle();
        }

        return $choices;
    }

    public function findMenu(SecurityContext $context)
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->where($queryBuilder->expr()->isNotNull('p.menu'))
                     ->addOrderBy('p.menu')
                     ->addOrderBy('p.title');
        $menu = array();
        foreach($queryBuilder->getQuery()->getResult() as $item) {
            if($item->isVisible($context)) {
                if(isset($menu[$item->getMenu()])) {
                    $menu[$item->getMenu()][] = $item;
                }
                else {
                    $menu[$item->getMenu()] = array($item);
                }
            }
        }
        
        return $menu;
    }
    
}
