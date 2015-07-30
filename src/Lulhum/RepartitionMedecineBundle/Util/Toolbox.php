<?php
// src/Lulhum/RepartitionMedecine/Util/Toolbox.php

namespace Lulhum\RepartitionMedecineBundle\Util;

class Toolbox
{
    function getCurrentSchoolYear()
    {
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
        
        return $schoolYear;
    }
}