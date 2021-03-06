<?php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Period
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Lulhum\RepartitionMedecineBundle\Repository\PeriodRepository")
 */
class Period
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description=null;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetime")
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stop", type="datetime")
     */
    private $stop;

    /**
     * @ORM\OneToMany(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\StageProposal", mappedBy="period")
     */  
    private $proposals;

    public function __construct()
    {
        $this->start = new \DateTime();
        $this->stop = new \DateTime();
        $this->proposals = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Period
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Period
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        if(is_null($this->description)) {

            return $this->name;
        }
        
        return $this->description;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     * @return Period
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime 
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set stop
     *
     * @param \DateTime $stop
     * @return Period
     */
    public function setStop($stop)
    {
        $this->stop = $stop;

        return $this;
    }

    /**
     * Get stop
     *
     * @return \DateTime 
     */
    public function getStop()
    {
        return $this->stop;
    }

    public function __toString()
    {
        if(!is_null($this->getName())) {
            return $this->getName();
        }

        return "#";
    }

    public function getProposals()
    {
        return $this->proposals;
    }

    public function sameSchoolyear(Period $period)
    {
        return $this->getSchoolyear() === $period->getSchoolyear();
    }

    public function getSchoolyear()
    {
        $year = (int)$this->start->format('Y');
        if((int)$this->start->format('m') < 9) {
            $year--;
        }

        return $year;
    }

    public function getTextSchoolyear()
    {
        return $year.'-'.($year+1);
    }
            
}
