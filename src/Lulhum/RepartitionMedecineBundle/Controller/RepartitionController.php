<?php
// src/Lulhum/RepartitionMedecineBundle/Controller/RepartitionController.php

namespace Lulhum\RepartitionMedecineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RepartitionController extends Controller
{
    public function indexAction()
    {
        return $this->render('LulhumRepartitionMedecineBundle:Repartition:index.html.twig'); 
    }

    public function menuAction()
    {
        $listItems = array();

        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        if($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') && ($user->getPromotion() === 'DFASM1' || $user->getPromotion() === 'DFASM2')) {
            $listItems[] = array(
                'label' => 'Choix de Groupe',
                'path' => 'lulhum_repartitionmedecine_groupes',
                'options' => array('promotion' => $user->getPromotion())
            );
        }

        return $this->render('LulhumRepartitionMedecineBundle:Repartition:menu.html.twig', array(
            'listItems' => $listItems
        ));
    }

    public function groupesAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if($user->getPromotion() != 'DFASM1' && $user->getPromotion() != 'DFASM2') {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $userListGroups = $em->getRepository('LulhumUserBundle:User')->getRepartitionGroupsByPromotion($user->getPromotion());
        $deadline = $em->getRepository('LulhumDeadlineBundle:Deadline')->findOneByName('repartitionGroup'.$user->getPromotion());
        
        if(!$deadline->getActive()) {

            return $this->render('LulhumRepartitionMedecineBundle:Repartition:groupes.html.twig', array(
                'userListGroups' => $userListGroups,
                'promotion' => $user->getPromotion(),
                'deadline' => $deadline,
            ));
        }

        $formBuilder = $this->get('form.factory')->createBuilder('form', $user);
        $formBuilder
            ->add('repartitionGroup', 'choice', array(
                'label' => 'Groupe désiré',
                'choices' => array('A' => 'A', 'B' => 'B')
            ))
            ->add('Valider', 'submit');
        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            $request->getSession()->getFlashBag()->add('success', 'Choix de groupe enregistré');
            if(count($userListGroups[$user->getRepartitionGroup()]) > $userListGroups['limit']) {
                $request->getSession()->getFlashBag()->add('warning', 'Attention, le groupe a atteint sa limite. Vous risquez d\'être automatiquement changé de groupe.');
            }
        }
        
        return $this->render('LulhumRepartitionMedecineBundle:Repartition:groupes.html.twig', array(
            'userListGroups' => $userListGroups,
            'form' => $form->createView(),
            'promotion' => $user->getPromotion(),
            'deadline' => $deadline,
        ));
    }
} 
