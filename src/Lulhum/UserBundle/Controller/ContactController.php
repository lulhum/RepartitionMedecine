<?php
// src/Lulhum/UserBundle/Controller/ContactController.php

namespace Lulhum\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Lulhum\UserBundle\Form\ContactMailType;

class ContactController extends Controller
{

    public function contactAction(Request $request)
    {
        $mail = $this->get('lulhum_user_contactmailer');
        $mail->setTo($this->get('lulhum_parameters_repository')->findOneByName('plateformMail')->getValue());
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        if($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $mail->setFrom($user->getEmail());
        }
        
        $form = $this->createForm(new ContactMailType(), $mail);

        $form->handleRequest($request);

        if($form->isValid()) {
            $mail->send();
            $request->getSession()->getFlashBag()->add('success', 'Mail envoyé.');

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_home'));
        }

        return $this->render('LulhumUserBundle:Mail:contact.html.twig', array(
            'form' => $form->createView(),
        ));        
    }

}