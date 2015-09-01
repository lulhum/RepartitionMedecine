<?php
// src/Lulhum/RepartitionMedecine/Util/Paginator.php

namespace Lulhum\RepartitionMedecineBundle\Util;

class Paginator
{
    const NEIGHBORHOOD = 5;

    protected $max;
    
    protected $page;

    protected $offset;
    
    protected $count;
    
    protected $pages;

    protected $url;

    public function __construct($max, $count, $page = 1, $url)
    {
        $this->url = $url;
        $this->max = $max;
        $this->count = $count;
        $this->pages = max(1, ceil($count / $max));
        $this->page = $page;
        if($this->page > $this->pages) {
            $this->page = $this->pages;
        }
        elseif($page < 1) {
            $this->page = 1;
        }        
        $this->offset = ($this->page - 1) * $max;        
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getMax()
    {
        return $this->max;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getPages()
    {
        return $this->pages;
    }

    public function getPrevious()
    {
        if($this->page > 1) {
            
            return $this->page - 1;
        }

        return false;
    }

    public function getNext()
    {
        if($this->page < $this->pages) {
            
            return $this->page + 1;
        }

        return false;
    }

    public function getNeighbors()
    {
        $res = array();
        for($i = max(1, $this->page - self::NEIGHBORHOOD); $i < min($this->pages + 1, $this->page + self::NEIGHBORHOOD + 1); $i++) {
            $res[] = $i;
        }

        return $res;
    }

    public function getFirst()
    {
        return 1;
    }

    public function getLast()
    {
        return $this->pages;
    }
}