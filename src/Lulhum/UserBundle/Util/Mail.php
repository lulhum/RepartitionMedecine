<?php
// src/Lulhum/RepartitionMedecineBundle/Util/Mail.php

namespace Lulhum\UserBundle\Util;

use Symfony\Component\Templating\EngineInterface;

class Mail
{
    protected $from;

    protected $to;
    
    protected $title;

    protected $content;

    protected $htmlContent = null;

    protected $mailer;

    protected $templating;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
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

    public function getTo()
    {
        return $this->to;
    }

    public function setTo($to)
    {
        $this->to = $to;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function setFrom($from)
    {
        $this->from = $from;
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
        $mail = \Swift_Message::newInstance();
        $mail->setFrom($this->from)
             ->setTo($this->to)
             ->setSubject($this->title)
             ->setBody($this->getHtmlContent())
             ->setContentType('text/html');
        $this->mailer->send($mail);
    }
        
}