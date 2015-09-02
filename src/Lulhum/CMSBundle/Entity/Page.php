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

    const SPECIAL_BLOCKS = array(
        'facebook' => array(
            'description' => 'Intégration du plugin Facebook',
            'markup' => '[[fb:facebook]]',
            'pattern' => '/\[\[fb:([[:graph:]]*)\]\]/',
            'html' =>'<div id="fb-root"></div><script>(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4";fjs.parentNode.insertBefore(js, fjs);}(document, \'script\', \'facebook-jssdk\'));</script><div class="fb-page" data-href="https://www.facebook.com/$1" data-tabs="timeline" data-width="500px" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/$1"><a href="https://www.facebook.com/$1">Facebook</a></blockquote></div></div>',
        )
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

    public function getContentSpecialBlocks()
    {
        $result = $this->content;
        foreach(self::SPECIAL_BLOCKS as $block) {
            $result = preg_replace($block['pattern'], $block['html'], $result);
        }

        return $result;
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

    public function getSpecialBlocks()
    {
        return self::SPECIAL_BLOCKS;
    }

}
