<?php
// src/Lulhum/UserBundle/Controller/RegistrationController.php

namespace Lulhum\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends BaseController
{
    protected function renderLogin(array $data)
    {
        $data['allowUserRegistration'] = ($this->getDoctrine()->getManager()->getRepository('LulhumRepartitionMedecineBundle:Parameter')->findOneByName('allowUserRegistrations')->getValue() === 'true');
 
        return parent::renderLogin($data);
    }

}
