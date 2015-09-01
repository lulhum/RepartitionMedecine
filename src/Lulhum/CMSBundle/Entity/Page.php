<?php

namespace Lulhum\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Page
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Lulhum\CMSBundle\Repository\PageRepository")
 */
class Page
{
    const VISIBILITIES = array(
        'ROLE_USER' => 'Utilisateur connecté',
        'ROLE_ADMIN' => 'Administrateur',
    );
    
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="visibility", type="string", length=255, nullable=true)
     */
    private $visibility;

    /**
     * @var string
     *
     * @ORM\Column(name="menu", type="string", length=255, nullable=true)
     */
    private $menu;


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
     * Set title
     *
     * @param string $title
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Page
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
     * Set visibility
     *
     * @param array $visibility
     * @return Page
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

    /**
     * Set menu
     *
     * @param string $menu
     * @return Page
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return string 
     */
    public function getMenu()
    {
        return $this->menu;
    }

    public function __toString()
    {
        return $this->title;
    }

    public function isVisible(SecurityContext $context)
    {
        return is_null($this->visibility) || $context->isGranted($this->visibility);
    }

    public function textVisibility()
    {
        return is_null($this->visibility) ? 'Publique' : self::VISIBILITIES[$this->visibility];
    }

}
