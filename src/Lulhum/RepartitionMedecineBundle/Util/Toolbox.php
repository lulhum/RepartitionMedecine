<?php
// src/Lulhum/RepartitionMedecine/Util/Toolbox.php

namespace Lulhum\RepartitionMedecineBundle\Util;

class Toolbox
{
    private $currentSchoolYear = null;
    
    public function getCurrentSchoolYear()
    {
        if(is_null($this->currentSchoolYear)) {
            $currentTime = new \DateTime();
            $schoolYear = $currentTime->format('Y');
            if($currentTime->format('m')<=6) {            
                $currentTime->sub(new \DateInterval('P1Y'));
                $schoolYear = $currentTime->format('Y').'/'.$schoolYear;
            }
            else {
                $currentTime->add(new \DateInterval('P1Y'));
                $schoolYear .= '/'.$currentTime->format('Y');
            }
            $this->currentSchoolYear = $schoolYear;
        }
        
        return $this->currentSchoolYear;
    }
}
