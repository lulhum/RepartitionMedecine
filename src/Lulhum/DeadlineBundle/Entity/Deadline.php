<?php

namespace Lulhum\DeadlineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deadline
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Deadline
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="delay", type="datetime", nullable=true)
     */
    private $delay;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active=false;

    /**
     * @var string
     *
     * @ORM\Column(name="callback", type="string", length=255, nullable=true)
     */
    private $callback=null;

    /**
     * @var array
     *
     * @ORM\Column(name="callback_params", type="array")
     */
    private $callbackParams=array();

    public function __construct($name = null, $date = null, $delay = null, $callback = null, $callbackParams = array())
    {
        $this->setName($name);
        if(is_null($date)) {
            $date = new \DateTime();
            $date->setTime(0, 0, 0);
        }
        $this->setDate($date);
        $this->setDelay($delay);
        $this->setCallback($callback);
        $this->setCallbackParams($callbackParams);
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Deadline
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set callback
     *
     * @param string $callback
     * @return Deadline
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Get callback
     *
     * @return string
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Set callbackParams
     *
     * @param array $callbackParams
     * @return Deadline
     */
    public function setCallbackParams($callbackParams)
    {
        $this->callbackParams = $callbackParams;

        return $this;
    }

    /**
     * Get callbackParams
     *
     * @return array
     */
    public function getCallbackParams()
    {
        return $this->callbackParams;
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
     * @return Deadline
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
     * Set date
     *
     * @param \DateTime $date
     * @return Deadline
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set delay
     *
     * @param \DateTime $delay
     * @return Deadline
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * Get delay
     *
     * @return \DateTime
     */
    public function getDelay()
    {
        return $this->delay;
    }

    public function getDateWithDelay()
    {
        $d = clone $this->date;
        return $d->add(new \DateInterval($this->delay->format('\P\TH\Hi\M')));
    }
       
}
