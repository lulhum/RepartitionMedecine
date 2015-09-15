<?php

namespace Lulhum\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * New
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Lulhum\CMSBundle\Repository\SiteNewRepository")
 */
class SiteNew
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
     * @ORM\Column(name="content", type="string", length=255)
     */
    private $content;

    /**
     * @var array
     *
     * @ORM\Column(name="visibility", type="array")
     */
    private $visibility;

    /**
     * @var string
     *
     * @ORM\Column(name="level", type="string", length=255)
     */
    private $level;

    public function __construct()
    {
        $this->visibility = array();
        $this->level = 'default';
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
     * Set content
     *
     * @param string $content
     * @return SiteNew
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set level
     *
     * @param string $level
     * @return SiteNew
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string 
     */
    public function getLevel()
    {
        return $this->level;
    }
    
    /**
     * Set visibility
     *
     * @param array $visibility
     * @return SiteNew
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Get visibility
     *
     * @return array 
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

}