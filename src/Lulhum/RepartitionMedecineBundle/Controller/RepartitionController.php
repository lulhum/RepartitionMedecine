<?php
// src/Lulhum/RepartitionMedecineBundle/Controller/AdvertController.php

namespace Lulhum\RepartitionMedecineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class RepartitionController extends Controller
{
    public function indexAction()
    {
        return $this->render('LulhumRepartitionMedecineBundle:Repartition:index.html.twig'); 
    }

    public function menuAction()
    {
        $listItems = array(
            array('title' => 'Ressource Test')
        );

        return $this->render('LulhumRepartitionMedecineBundle:Repartition:menu.html.twig', array(
            'listItems' => $listItems
        ));
    }
} 