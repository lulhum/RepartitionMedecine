<?php
// src/Lulhum/UserBundle/Controller/RegistrationController.php

namespace Lulhum\UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends BaseController
{
    public function registerAction(Request $request)
    {
        if($this->getDoctrine()->getManager()->getRepository('LulhumRepartitionMedecineBundle:Parameter')->findOneByName('allowUserRegistrations')->getValue() !== 'true') {
            throw $this->createNotFoundException('Les inscriptions sont actuellement fermées');
        }
 
        return parent::registerAction($request);
    }
}