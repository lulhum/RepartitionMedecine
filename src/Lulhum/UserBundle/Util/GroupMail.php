<?php
// src/Lulhum/RepartitionMedecineBundle/Util/GroupMail.php

namespace Lulhum\UserBundle\Util;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Templating\EngineInterface;

class GroupMail extends Mail
{    
    protected $promotions;

    protected $group = null;

    protected $em;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating, EntityManager $em)
    {
        parent::__construct($mailer, $templating);
        $this->promotions = array();
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