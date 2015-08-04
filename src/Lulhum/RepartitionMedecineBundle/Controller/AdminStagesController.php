<?php
// src/Lulhum/RepartitionMedecineBundle/Controller/AdminStagesController.php

namespace Lulhum\RepartitionMedecineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminStagesController extends Controller
{

    public function stageCategoriesAction() {

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stagecategories.html.twig');
    }
}
