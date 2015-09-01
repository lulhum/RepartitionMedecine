<?php
// src/Lulhum/RepartitionMedecine/Util/StageValidator.php

namespace Lulhum\RepartitionMedecineBundle\Util;

use Doctrine\ORM\EntityManager;
use Lulhum\UserBundle\Entity\User;
use Lulhum\RepartitionMedecineBundle\Entity\Stage;
use Lulhum\RepartitionMedecineBundle\Entity\Requirement;

class StageValidator
{
    const ERROR_LEVEL = array(
        'success',
        'warning',
        'danger',
    );
    
    private $em;

    private $categories = null;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    private function check(Requirement $requirement, $stage) {
        if($requirement->getType() === 'maxPlaces' && $stage->getProposal()->getStages()->count() > (int)$requirement->getParams()) {

            return $requirement->getStrict() ? 2 : 1;
        }
        if($requirement->getType() === 'promotion' && $stage->getUser()->getPromotion() !== $requirement->getParams()) {

            return $requirement->getStrict() ? 2 : 1;
        }
        if($requirement->getType() === 'group' && $stage->getUser()->getRepartitionGroup() !== $requirement->getParams()) {

            return $requirement->getStrict() ? 2 : 1;
        }
        if(substr($requirement->getType(), 0, 10) === 'maxChoices') {
            if(substr($requirement->getType(), 10) === 'InPeriod') {
                $parameters = array($stage->getProposal()->getPeriod());
            }
            elseif(substr($requirement->getType(), 10) === 'InStageCategory') {
                $parameters = array($stage->getProposal()->getCategory()->getId());
            }
            else {
                $parameters = array((int)$requirement->getParamsArray(0));
            }
            if(substr($requirement->getType(), -16) === 'WithinSchoolyear') {
                $parameters = array_merge(array($stage->getProposal()->getPeriod()), $parameters);
            }
            $choices = call_user_func_array(array($stage->getUser(), 'countStages'.substr($requirement->getType(), 10)), array_merge($parameters, array(null)));
            $max = (int)$requirement->getParamsArray(1);
            if($choices > $max) {

                return $requirement->getStrict() ? 2 : 1;
            }
        }
        if(substr($requirement->getType(), 0, 9) === 'maxStages') {
            if(substr($requirement->getType(), 9) === 'InPeriod') {
                $parameters = array($stage->getProposal()->getPeriod(), true);
            }
            elseif(substr($requirement->getType(), 9) === 'InStageCategory') {
                $parameters = array($stage->getProposal()->getCategory()->getId());
            }
            else {
                $parameters = array((int)$requirement->getParamsArray(0), true);
            }
            if(substr($requirement->getType(), -16) === 'WithinSchoolyear') {
                $parameters = array_merge(array($stage->getProposal()->getPeriod()), $parameters);
            }
            $choices = call_user_func_array(array($stage->getUser(), 'countStages'.substr($requirement->getType(), 9)), array_merge($parameters, array(false)));
            $stages = call_user_func_array(array($stage->getUser(), 'countStages'.substr($requirement->getType(), 9)), array_merge($parameters, array(true)));
            $max = (int)$requirement->getParamsArray(1);
            if((!$stage->getLocked() && $stages === $max && $choices != 0) || $stages > $max) {

                return $requirement->getStrict() ? 2 : 1;
            }
        }        
        if(!array_key_exists($requirement->getType(), Requirement::TYPES)) {

            return 1;
        }

        return 0;
    }

    private function getMessage($requirement, $stage) {
        if($requirement->getType() === 'maxPlaces') {

            return 'Ce stage a '.$stage->getProposal()->getStages()->count().' utilisateurs inscrits pour '.$requirement->getParams().' places';
        }
        if($requirement->getType() === 'promotion') {

            return 'L\'utilisateur n\'appartient pas à la promotion requise pour ce stage ('.User::PROMOTIONS[$requirement->getParams()].')';
        }
        if($requirement->getType() === 'group') {

            return 'L\'utilisateur n\'appartient pas au groupe requis pour ce stage ('.$requirement->getParams().')';
        }
        if($requirement->getType() === 'maxChoicesInCategory') {
            $choices = $stage->getUser()->countStagesInCategory((int)$requirement->getParamsArray(0), false);
            $max = (int)$requirement->getParamsArray(0) - $stage->getUser()->countStagesInCategory((int)$requirement->getParamsArray(0), true);
            $category = $this->getCategories()[(int)$requirement->getParamsArray(0)];

            return 'L\'utilisateur a déjà effectué '.$choices.'/'.$max.' choix dans la catégorie "'.$category.'"';
        }
        if($requirement->getType() === 'maxStagesInCategory') {
            $stages = $stage->getUser()->countStagesInCategory((int)$requirement->getParamsArray(0), true);
            $max = $requirement->getParamsArray(1);
            $category = $this->getCategories()[(int)$requirement->getParamsArray(0)];

            return 'L\'utilisateur a déjà effectué '.$stages.'/'.$max.' stages dans la catégorie "'.$category.'"';
        }
        if($requirement->getType() === 'maxChoicesInCategoryWithinSchoolyear') {
            $choices = $stage->getUser()->countStagesInCategoryWithinSchoolyear($stage->getProposal()->getPeriod(), (int)$requirement->getParamsArray(0), false);
            $max = (int)$requirement->getParamsArray(1);
            $max -= $stage->getUser()->countStagesInCategoryWithinSchoolyear($stage->getProposal()->getPeriod(), (int)$requirement->getParamsArray(0), true);
            $category = $this->getCategories()[(int)$requirement->getParamsArray(0)];
            $schoolyear = $stage->getProposal()->getPeriod()->getTextSchoolyear();

            return 'L\'utilisateur a déjà effectué '.$choices.'/'.$max.' choix dans la catégorie "'.$category.'" pour l\'année '.$schoolyear;
        }
        if($requirement->getType() === 'maxStagesInCategoryWithinSchoolyear') {
            $stages = $stage->getUser()->countStagesInCategoryWithinSchoolyear($stage->getProposal()->getPeriod(), (int)$requirement->getParamsArray(0), true);
            $max = $requirement->getParamsArray(1);
            $category = $this->getCategories()[(int)$requirement->getParamsArray(0)];
            $schoolyear = $stage->getProposal()->getPeriod()->getTextSchoolyear();

            return 'L\'utilisateur a déjà effectué '.$stages.'/'.$max.' stages dans la catégorie "'.$category.'" pour l\'année '.$schoolyear;
        }
        if($requirement->getType() === 'maxChoicesInPeriod') {
            $choices = $stage->getUser()->countStagesInPeriod($stage->getProposal()->getPeriod(), false);
            $max = (int)$requirement->getParams() - $stage->getUser()->countStagesInPeriod($stage->getProposal()->getPeriod(), true);
            $period = $stage->getProposal()->getPeriod();

            return 'L\'utilisateur a déjà effectué '.$choices.'/'.$max.' choix dans la période "'.$period.'"';
        }
        if($requirement->getType() === 'maxStagesInPeriod') {
            $stages = $stage->getUser()->countStagesInPeriod($stage->getProposal()->getPeriod(), true);
            $max = $requirement->getParams();
            $period = $stage->getProposal()->getPeriod();

            return 'L\'utilisateur a déjà effectué '.$stages.'/'.$max.' stages dans la période "'.$period.'"';
        }
        if($requirement->getType() === 'maxChoicesInStageCategory') {
            $choices = $stage->getUser()->countStagesInStageCategory($stage->getProposal()->getCategory()->getId(), false);
            $max = (int)$requirement->getParams() - $stage->getUser()->countStagesInStageCategory($stage->getProposal()->getCategory()->getId(), true);
            $category = $stage->getProposal()->getCategory();

            return 'L\'utilisateur a déjà effectué '.$choices.'/'.$max.' choix avec le modèle "'.$category.'"';
        }
        if($requirement->getType() === 'maxStagesInStageCategory') {
            $stages = $stage->getUser()->countStagesInStageCategory($stage->getProposal()->getCategory()->getId(), true);
            $max = $requirement->getParams();
            $category = $stage->getProposal()->getCategory();

            return 'L\'utilisateur a déjà effectué '.$stages.'/'.$max.' stages avec le modèle "'.$category.'"';
        }
        if($requirement->getType() === 'maxChoicesInStageCategoryWithinSchoolyear') {
            $choices = $stage->getUser()->countStagesInStageCategoryWithinSchoolyear($stage->getProposal()->getPeriod(), $stage->getProposal()->getCategory()->getId(), false);
            $max = (int)$requirement->getParamsArray(1);
            $max -= $stage->getUser()->countStagesInStageCategoryWithinSchoolyear($stage->getProposal()->getPeriod(), $stage->getProposal()->getCategory()->getId(), true);
            $category = $stage->getProposal()->getCategory();
            $schoolyear = $stage->getProposal()->getPeriod()->getTextSchoolyear();

            return 'L\'utilisateur a déjà effectué '.$choices.'/'.$max.' choix avec le modèle "'.$category.'" pour l\'année '.$schoolyear;
        }
        if($requirement->getType() === 'maxStagesInCategoryWithinSchoolyear') {
            $stages = $stage->getUser()->countStagesInStageCategoryWithinSchoolyear($stage->getProposal()->getPeriod(), $stage->getProposal()->getCategory()->getId(), true);
            $max = $requirement->getParams();
            $category = $stage->getProposal()->getCategory();
            $schoolyear = $stage->getProposal()->getPeriod()->getTextSchoolyear();

            return 'L\'utilisateur a déjà effectué '.$stages.'/'.$max.' stages avec le modèle "'.$category.'" pour l\'année '.$schoolyear;
        }        

        return 'Le type de contrainte "'.$requirement->getType().'" n\'est pas pris en charge';
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

    public function isValid(Stage $stage, $notValidOnWarning = false)
    {
        if($stage->getProposal()->getStages()->filter(function($s) use (&$stage) {
            return $s->getUser() === $stage->getUser();
        })->count() > 1) {
            return false;
        }

        foreach($stage->getProposal()->getRequirements() as $requirement) {
            if($notValidOnWarning || $requirement->getStrict()) {
                if($this->check($requirement, $stage) > 0) {
                    return false;
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
            $check = $this->check($requirement, $stage);
            if($check > 0) {
                $conflicts[] = array(
                    'level' => self::ERROR_LEVEL[$check],
                    'message' => $this->getMessage($requirement, $stage),
                );
            }            
        }

        usort($conflicts, function($conflict1, $conflict2) {
            if($conflict1['level'] === $conflict2['level']) return 0;
            if($conflict1['level'] === 'danger') return -1;
            return 1;
        });
        
        return $conflicts;
    }

    

    public function getValidity(Stage $stage)
    {
        $validity = 0;

        if($stage->getProposal()->getStages()->filter(function($s) use (&$stage) {
            return $s->getUser() === $stage->getUser();
        })->count() > 1) {

            return 'danger';
        }

        foreach($stage->getProposal()->getRequirements() as $requirement) {
            $check = $this->check($requirement, $stage);
            if($check === 2) {

                return 'danger';
            }
            else{
                $validity = max($validity, $check);
            }
        }
        
        return self::ERROR_LEVEL[$validity];
    }
}