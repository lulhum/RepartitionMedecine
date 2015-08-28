<?php
// src/Lulhum/RepartitionMedecine/Util/Calendar.php

namespace Lulhum\RepartitionMedecineBundle\Util;

class Calendar
{

    private $categories;

    private $proposals;

    private $keys;

    public function __construct($proposals)
    {
        $this->proposals = $proposals;

        $this->keys = array();
        $date = new \DateTime();
        if($date->format('m')<=7) {            
            $date->sub(new \DateInterval('P1Y'));
        }
        $date->setDate((int)$date->Format('Y'), 10, 15);
        $date->setTime(0, 0, 0);
        for($i = 0; $i<12; $i++) {
            $this->keys[] = $date->Format('Y-m-d');
            $date->add(new \DateInterval('P1M'));
        }
                
        $this->categories = array();
        foreach($this->proposals as $pkey => $proposal) {
            $id = $proposal->getCategory()->getId();
            if(!isset($this->categories[$id])) {
                $this->categories[$id] = array_fill_keys($this->keys, null);
            }
            foreach($this->categories[$id] as $key => $value) {
                $date = new \DateTime($key);
                if($proposal->getPeriod()->getStart() <= $date && $date <= $proposal->getPeriod()->getStop()) {
                    $this->categories[$id][$key] = $pkey;
                }
            }
        }
    }

    public function getCategoriesId()
    {
        return array_keys($this->categories);
    }

    public function getMonth($categoryId, $month)
    {
        if($month != 0 && $this->categories[$categoryId][$this->keys[$month-1]] === $this->categories[$categoryId][$this->keys[$month]]) {
            
            return null;
        }
        $colspan = 0;
        while($this->categories[$categoryId][$this->keys[$month + $colspan]] === $this->categories[$categoryId][$this->keys[$month]]) {            
            $colspan++;
            if(($month + $colspan) == 12) break;
        }

        return array('colspan' => $colspan, 'val' => $this->categories[$categoryId][$this->keys[$month]]);
    }

    public function getProposal($id)
    {
        return $this->proposals[$id];
    }

}