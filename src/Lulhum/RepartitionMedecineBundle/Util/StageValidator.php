<?php
// src/Lulhum/RepartitionMedecine/Util/StageValidator.php

namespace Lulhum\RepartitionMedecineBundle\Util;

use Doctrine\ORM\EntityManager;
use Lulhum\UserBundle\Entity\User;
use Lulhum\RepartitionMedecineBundle\Entity\Stage;
use Lulhum\RepartitionMedecineBundle\Entity\Requirement;

class StageValidator
{

    private $em;

    private $categories = null;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function isValid(Stage $stage)
    {
        if($stage->getProposal()->getStages()->filter(function($s) use (&$stage) {
            return $s->getUser() === $stage->getUser();
        })->count() > 1) {
            return false;
        }

        foreach($stage->getProposal()->getRequirements() as $requirement) {
            if($requirement->getStrict()) {
                if($requirement->getType() === 'maxPlaces' && $stage->getProposal()->getStages()->count() > (int)$requirement->getParams()) {

                    return false;
                }
                if($requirement->getType() === 'promotion' && $stage->getUser()->getPromotion() !== $requirement->getParams()) {

                    return false;
                }
                if($requirement->getType() === 'group' && $stage->getUser()->getRepartitionGroup() !== $requirement->getParams()) {

                    return false;
                }
                if($requirement->getType() === 'maxChoicesInCategory' && $stage->getUser()->countStagesInCategory((int)$requirement->getParamsArray()[0], false) > (int)$requirement->getParamsArray()[1]) {                    

                    return false;
                }
                if($requirement->getType() === 'maxStagesInCategory') {
                    if($stage->getUser()->countStagesInCategory((int)$requirement->getParamsArray()[0], true) > (int)$requirement->getParamsArray()[1]) {

                        return false;
                    }
                    if($stage->getUser()->countStagesInCategory((int)$requirement->getParamsArray()[0], true) == (int)$requirement->getParamsArray()[1] && $stage->getUser()->countStagesInCategory((int)$requirement->getParamsArray()[0], false) != 0) {

                        return false;
                    }
                }
                if($requirement->getType() === 'maxChoicesInPeriod' && $stage->getUser()->countStagesInPeriod($stage->getProposal()->getPeriod(), false) > (int)$requirement->getParams()) {                    

                    return false;
                }
                if($requirement->getType() === 'maxStagesInPeriod') {
                    if($stage->getUser()->countStagesInPeriod($stage->getProposal()->getPeriod(), true) > (int)$requirement->getParams()) {

                        return false;
                    }
                    if($stage->getUser()->countStagesInPeriod($stage->getProposal()->getPeriod(), true) == (int)$requirement->getParams() && $stage->getUser()->countStagesInPeriod($stage->getProposal()->getPeriod(), false) != 0) {

                        return false;
                    }
                }
                if($requirement->getType() === 'maxStagesInStageCategory') {
                    if($stage->getUser()->countStagesInStageCategory($stage->getProposal()->getCategory()->getId(), true) > (int)$requirement->getParams()) {

                        return false;
                    }
                    if($stage->getUser()->countStagesInStageCategory($stage->getProposal()->getCategory()->getId(), true) == (int)$requirement->getParams() && $stage->getUser()->countStagesInStageCategory($stage->getProposal()->getCategory()->getId(), false) != 0) {

                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function checkRequirements(Stage $stage)
    {
        $conflicts = array();
        $this->categories = null;
        if($stage->getProposal()->getStages()->filter(function($s) use (&$stage) {
            return $s->getUser() === $stage->getUser();
        })->count() > 1) {
            $conflicts[] = array('level' => 'danger', 'message' => 'L\'utilisateur est inscrit plusieures fois au même stage');
        }

        foreach($stage->getProposal()->getRequirements() as $requirement) {
            if($requirement->getType() === 'maxPlaces' && $stage->getProposal()->getStages()->count() > (int)$requirement->getParams()) {
                $conflicts[] = array(
                    'level' => $requirement->getStrict() ? 'danger' : 'warning',
                    'message' => 'Ce stage a '.$stage->getProposal()->getStages()->count().' utilisateurs inscrits pour '.$requirement->getParams().' places',
                );
            }
            elseif($requirement->getType() === 'promotion' && $stage->getUser()->getPromotion() !== $requirement->getParams()) {
                $conflicts[] = array(
                    'level' => $requirement->getStrict() ? 'danger' : 'warning',
                    'message' => 'L\'utilisateur n\'appartient pas à la promotion requise pour ce stage ('.User::PROMOTIONS[$requirement->getParams()].')',
                );
            }
            elseif($requirement->getType() === 'group' && $stage->getUser()->getRepartitionGroup() !== $requirement->getParams()) {
                $conflicts[] = array(
                    'level' => $requirement->getStrict() ? 'danger' : 'warning',
                    'message' => 'L\'utilisateur n\'appartient pas au groupe requis pour ce stage ('.$requirement->getParams().')',
                );
            }
            elseif($requirement->getType() === 'maxChoicesInCategory' && $stage->getUser()->countStagesInCategory((int)$requirement->getParamsArray()[0], false) > (int)$requirement->getParamsArray()[1]) {
                $conflicts[] = array(
                    'level' => $requirement->getStrict() ? 'danger' : 'warning',
                    'message' => 'L\'utilisateur a déjà effectué '.$stage->getUser()->countStagesInCategory((int)$requirement->getParamsArray()[0], false).'/'.$requirement->getParamsArray()[1].' choix dans la catégorie "'.$this->getCategories()[(int)$requirement->getParamsArray()[0]].'"',
                );
            }
            elseif($requirement->getType() === 'maxStagesInCategory') {
                $cond = !$stage->getLocked();
                $cond = $cond && $stage->getUser()->countStagesInCategory((int)$requirement->getParamsArray()[0], true) == (int)$requirement->getParamsArray()[1];
                $cond = $cond && $stage->getUser()->countStagesInCategory((int)$requirement->getParamsArray()[0], false) != 0;
                $cond = $cond || $stage->getUser()->countStagesInCategory((int)$requirement->getParamsArray()[0], true) > (int)$requirement->getParamsArray()[1];                
                if($cond) {            
                    $conflicts[] = array(
                        'level' => $requirement->getStrict() ? 'danger' : 'warning',
                        'message' => 'L\'utilisateur a déjà effectué '.$stage->getUser()->countStagesInCategory((int)$requirement->getParamsArray()[0], true).'/'.$requirement->getParamsArray()[1].' stages dans la catégorie "'.$this->getCategories()[(int)$requirement->getParamsArray()[0]].'"',
                    );
                }
            }
            elseif($requirement->getType() === 'maxChoicesInPeriod' && $stage->getUser()->countStagesInPeriod($stage->getProposal()->getPeriod(), false) > (int)$requirement->getParams()) {
                $conflicts[] = array(
                    'level' => $requirement->getStrict() ? 'danger' : 'warning',
                    'message' => 'L\'utilisateur a déjà effectué '.$stage->getUser()->countStagesInPeriod($stage->getProposal()->getPeriod(), false).'/'.$requirement->getParams().' choix dans la période "'.$stage->getProposal()->getPeriod().'"',
                );
            }
            elseif($requirement->getType() === 'maxStagesInPeriod') {
                $cond = !$stage->getLocked();
                $cond = $cond && $stage->getUser()->countStagesInPeriod($stage->getProposal()->getPeriod(), true) == (int)$requirement->getParams();
                $cond = $cond && $stage->getUser()->countStagesInPeriod($stage->getProposal()->getPeriod(), false) != 0;
                $cond = $cond || $stage->getUser()->countStagesInPeriod($stage->getProposal()->getPeriod(), true) > (int)$requirement->getParams();                
                if($cond) {            
                    $conflicts[] = array(
                        'level' => $requirement->getStrict() ? 'danger' : 'warning',
                        'message' => 'L\'utilisateur a déjà effectué '.$stage->getUser()->countStagesInPeriod($stage->getProposal()->getPeriod(), true).'/'.$requirement->getParams().' stages dans la période "'.$stage->getProposal()->getPeriod().'"',
                    );
                }
            }
            elseif($requirement->getType() === 'maxStagesInStageCategory') {
                $cond = !$stage->getLocked();
                $cond = $cond && $stage->getUser()->countStagesInStageCategory($stage->getProposal()->getCategory()->getId(), true) == (int)$requirement->getParams();
                $cond = $cond && $stage->getUser()->countStagesInStageCategory($stage->getProposal()->getCategory()->getId(), false) != 0;
                $cond = $cond || $stage->getUser()->countStagesInStageCategory($stage->getProposal()->getCategory()->getId(), true) > (int)$requirement->getParams();                
                if($cond) {            
                    $conflicts[] = array(
                        'level' => $requirement->getStrict() ? 'danger' : 'warning',
                        'message' => 'L\'utilisateur a déjà effectué '.$stage->getUser()->countStagesInStageCategory($stage->getProposal()->getCategory()->getId(), true).'/'.$requirement->getParams().' stages avec ce modèle.',
                    );
                }
            }
            elseif(!array_key_exists($requirement->getType(), Requirement::TYPES)) {
                $conflicts[] = array('level' => 'warning', 'message' => 'Le type de contrainte "'.$requirement->getType().'" n\'est pas pris en charge');
            }
        }

        usort($conflicts, function($conflict1, $conflict2) {
            if($conflict1['level'] === $conflict2['level']) return 0;
            if($conflict1['level'] === 'danger') return -1;
            return 1;
        });
        
        return $conflicts;
    }

    private function getCategories()
    {
        if(is_null($this->categories)) {
            $this->categories = array();
            $categories = $this->em->getRepository('LulhumRepartitionMedecineBundle:Category')->findAll();
            foreach($categories as $category) {
                $this->categories[$category->getId()] = $category;
            }
        }
        
        return $this->categories;
    }
}