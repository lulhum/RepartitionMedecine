<?php
// src/Lulhum/RepartitionMedecineBundle/Util/GroupMail.php

namespace Lulhum\UserBundle\Util;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Templating\EngineInterface;

class GroupMail
{    
    protected $promotions;

    protected $group = null;

    protected $title;

    protected $content;

    protected $htmlContent = null;

    protected $mailer;

    protected $templating;

    protected $em;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating, EntityManager $em)
    {
        $this->promotions = array();
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->em = $em;
    }

    public function getPromotions()
    {
        return $this->promotions;
    }

    public function setPromotions($promotions)
    {
        $this->promotions = $promotions;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup($group)
    {
        $this->group = $group;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
        $this->htmlContent = null;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getHtmlContent()
    {
        if(is_null($this->htmlContent)) {
            $this->htmlContent = $this->templating->render('LulhumUserBundle:Mail:mail.html.twig', array(
                'content' => $this->content
            ));
        }

        return $this->htmlContent;
    }

    public function send()
    {
        $masterMail = $this->em->getRepository('LulhumRepartitionMedecineBundle:Parameter')->findOneByName('plateformMail')->getValue();
        if(is_null($this->group)) {
            $users = $this->em->getRepository('LulhumUserBundle:User')->findBy(array('promotion' => $this->promotions));
        }
        else {
            $users = $this->em->getRepository('LulhumUserBundle:User')->findBy(array(
                'promotion' => $this->promotions,
                'repartitionGroup' => $this->group,
            ));
        }
        foreach($users as $user) {
            $mail = \Swift_Message::newInstance();
            $mail->setFrom($masterMail)
                 ->setTo($user->getEmail())
                 ->setSubject($this->title)
                 ->setBody($this->getHtmlContent())
                 ->setContentType('text/html');
            $this->mailer->send($mail);
        }
    }
        
}