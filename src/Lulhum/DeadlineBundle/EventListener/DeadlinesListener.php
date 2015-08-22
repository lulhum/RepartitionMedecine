<?php
// src/Lulhum/DeadlineBundle/EventListener/DeadlinesListener.php

namespace Lulhum\DeadlineBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\ContainerAware;

class DeadlinesListener extends ContainerAware
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function processDeadlines(Event $event)
    {
        if ($event->getRequestType() != HttpKernelInterface::MASTER_REQUEST) {
            return;
        }
        $deadlines = $this->em->getRepository('LulhumDeadlineBundle:Deadline')->findByActive(true);
        foreach($deadlines as $deadline) {
            if($deadline->getDateWithDelay() < new \DateTime()) {
                if(!is_null($deadline->getCallback())) {
                    $args = array();
                    foreach($deadline->getCallbackParams()['args'] as $arg) {
                        if($arg === 'this') $args[] = $deadline;
                        else $args[] = $arg;
                    }
                    call_user_func_array(array($this->container->get($deadline->getCallback()), $deadline->getCallbackParams()['method']), $args);
                }
                if(is_null($deadline->getName())) {
                    $this->em->remove($deadline);
                }
                else {
                    $deadline->setActive(false);
                    $this->em->persist($deadline);
                }
            }
        }
        $this->em->flush();
    }

}