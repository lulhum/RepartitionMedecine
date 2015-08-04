<?php
// src/Lulhum/RepartitionMedecineBundle/Controller/AdminRepartitionGroupController.php

namespace Lulhum\RepartitionMedecineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Lulhum\DeadlineBundle\Form\DeadlineType;
use Lulhum\UserBundle\Entity\User;

class AdminRepartitionGroupController extends Controller
{

    public function indexAction()
    {
        $userRepository = $this->getDoctrine()->getManager()->getRepository('LulhumUserBundle:User');
        $userListGroups['DFASM1'] = $userRepository->getRepartitionGroupsByPromotion('DFASM1');
        $userListGroups['DFASM2'] = $userRepository->getRepartitionGroupsByPromotion('DFASM2');

        $deadlineRepository = $this->getDoctrine()->getManager()->getRepository('LulhumDeadlineBundle:Deadline');
        $deadline['DFASM1'] = $deadlineRepository->findOneByName('repartitionGroupDFASM1');
        $deadline['DFASM2'] = $deadlineRepository->findOneByName('repartitionGroupDFASM2');
        
        return $this->render('LulhumRepartitionMedecineBundle:Admin:repartitiongroupindex.html.twig', array(
            'userListGroupsAll' => $userListGroups,
            'deadline' => $deadline
        ));
    }

    public function stopAction($promotion)
    {
        $em = $this->getDoctrine()->getManager();
        
        $deadlineRepository = $em->getRepository('LulhumDeadlineBundle:Deadline');
        $em->persist($deadlineRepository->findOneByName('repartitionGroup'.$promotion)->setActive(false));
        
        $groupRepartitionMode = $em->getRepository('LulhumRepartitionMedecineBundle:Parameter')->findOneByName('groupRepartitionMode')->getValue();
        $this->get('lulhum_repartitionmedecine_repartition')->finalizeGroupRepartition($promotion, $groupRepartitionMode);        

        $em->flush();
        
        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_repartitiongroup_home'));
    }

    public function switchUserAction(User $user, $id) {
        $em = $this->getDoctrine()->getManager();
        $user->switchRepartitionGroup();
        $em->persist($user);
        $em->flush();

        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_repartitiongroup_home'));           
    }

    public function startAction(Request $request, $promotion)
    {
        $em = $this->getDoctrine()->getManager();
        $deadline = $em->getRepository('LulhumDeadlineBundle:Deadline')->findOneByName('repartitionGroup'.$promotion);
	if($deadline->getDate() < new \DateTime()) {
            $deadline->setDate(new \DateTime());
        }

        $form = $this->createForm(new DeadlineType(array(
            array('field' => 'reset', 'type' => 'checkbox', 'options' => array(
                'mapped' => false,
                'label' => 'Réinitialiser les groupes',
                'required' => false
            )))),
            $deadline
        );

        $form->handleRequest($request);

        if($form->isValid()) {
            
            if($form->get('reset')->getData()) {
                $this->get('lulhum_repartitionmedecine_repartition')->resetGroupRepartition($promotion);
            }

            $deadline->setActive(true);
            $em->persist($deadline);
        
            $em->flush();            
        
            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_repartitiongroup_home'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:opengrouprepartition.html.twig', array(
            'form' => $form->createView(),
            'promotion' => $promotion
        ));
    }

}
