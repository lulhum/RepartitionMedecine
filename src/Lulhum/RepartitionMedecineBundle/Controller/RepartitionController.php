<?php
// src/Lulhum/RepartitionMedecineBundle/Controller/RepartitionController.php

namespace Lulhum\RepartitionMedecineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Lulhum\RepartitionMedecineBundle\Entity\StageProposal;
use Lulhum\RepartitionMedecineBundle\Entity\Stage;

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
        if($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            if($user->getPromotion() === 'DFASM1' || $user->getPromotion() === 'DFASM2') {
                $listItems[] = array(
                    'label' => 'Groupe',
                    'path' => 'lulhum_repartitionmedecine_groupes',
                    'options' => array('promotion' => $user->getPromotion())
                );
            }
            $listItems[] = array(
                'label' => 'Stages',
                'path' => 'lulhum_repartitionmedecine_stages_proposals',
                'options' => array()
            );
        }

        return $this->render('LulhumRepartitionMedecineBundle:Repartition:menu.html.twig', array(
            'listItems' => $listItems
        ));
    }

    public function stagesProposalsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        $proposals = $em->getRepository('LulhumRepartitionMedecineBundle:StageProposal')->findAllValidForUser($user, $this->get('lulhum_repartitionmedecine_stagevalidator'));

        return $this->render('LulhumRepartitionMedecineBundle:Repartition:stages.html.twig', array(
            'proposals' => $proposals,
            'type' => 'proposals',
        ));
    }

    public function stagesPendingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        $proposals = array_map(function($stage) {
            return $stage->getProposal();
        }, $em->getRepository('LulhumRepartitionMedecineBundle:Stage')->findBy(array(
            'user' => $user,
            'locked' => false,            
        )));

        return $this->render('LulhumRepartitionMedecineBundle:Repartition:stages.html.twig', array(
            'proposals' => $proposals,
            'type' => 'pending',
        ));
    }

    public function stagesAcceptedAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        $proposals = array_map(function($stage) {
            return $stage->getProposal();
        }, $em->getRepository('LulhumRepartitionMedecineBundle:Stage')->findBy(array(
            'user' => $user,
            'locked' => true,            
        )));

        return $this->render('LulhumRepartitionMedecineBundle:Repartition:stages.html.twig', array(
            'proposals' => $proposals,
            'type' => 'accepted',
        ));
    }

    public function stagesSuscribeAction(Request $request, StageProposal $proposal, $id)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();        
        $user = $this->container->get('security.context')->getToken()->getUser();        
        $stage = new Stage($user, $proposal);
        if($this->get('lulhum_repartitionmedecine_stagevalidator')->isValid($stage)) {
            $em->persist($stage);
            $em->flush();
            $session->getFlashBag()->add('success', 'Inscription au stage "'.$proposal->getName().'" effectuée.');
        }
        else {
            $session->getFlashBag()->add('danger', 'Vous n\'avez pas pu être inscrit au stage "'.$proposal->getName().'".');
        }

        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_stages_proposals'));
    }

    public function stagesUnsuscribeAction(Request $request, StageProposal $proposal, $id)
    {
        $session = $request->getSession();
        if(!$proposal->getLocked()) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            $stages = $proposal->getStages()->filter(function($stage) use (&$user) {
                return $user == $stage->getUser();
            });
            if($stages->count() == 0) {
                $session->getFlashBag()->add('danger', 'Vous ne pouvez pas vous désinscrire d\'un stage auquel vous n\'êtes pas inscrit.');
            }
            elseif($stages->first()->getLocked()) {
                $session->getFlashBag()->add('danger', 'Votre inscription au stage "'.$proposal->getName().'" n\'a pas pu être annulée.');
            }
            else {
                $em = $this->getDoctrine()->getManager();            
                $em->remove($stages->first());
                $em->flush();
                $session->getFlashBag()->add('success', 'Votre inscription au stage "'.$proposal->getName().'" a bien été annulée.');
            }
        }
        else {
            $session->getFlashBag()->add('danger', 'Votre inscription au stage "'.$proposal->getName().'" n\'a pas pu être annulée.');
        }

        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_stages_pending'));
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
