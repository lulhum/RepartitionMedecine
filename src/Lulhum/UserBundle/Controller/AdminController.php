<?php
// src/Lulhum/UserBundle/Controller/AdminController.php

namespace Lulhum\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Lulhum\UserBundle\Form\UserType;
use Lulhum\UserBundle\Entity\User;

class AdminController extends Controller
{

    public function menuUserPromotionAction()
    {
        return $this->render('LulhumUserBundle:Admin:usermenupromotion.html.twig', array(
            'promotions' => \Lulhum\UserBundle\Entity\User::getPromotionChoicesValues()
        ));
    }

    public function listUsersAction(Request $request, $promotion=null,$repartitionGroup=null)
    {
        $filter=array();
        if(!empty($promotion)) {
            if(array_key_exists($promotion, \Lulhum\UserBundle\Entity\User::getPromotionChoicesValues())) {
                $filter['promotion'] = $promotion;
                if(!empty($repartitionGroup) && ($repartitionGroup === 'A' || $repartitionGroup === 'B')) {
                    $filter['repartitionGroup'] = $repartitionGroup;
                }
            }
            else {
                throw $this->createNotFoundException('Cette promotion n\'existe pas.');
            }
        }
        $request->getSession()->set('listUserPromotion', $promotion);
        $repository = $this->getDoctrine()->getManager()->getRepository('LulhumUserBundle:User');
        $userList = $repository->findBy(
            $filter,
            array('lastname' => 'asc', 'firstname' => 'asc')            
        );

        return $this->render('LulhumUserBundle:Admin:listusers.html.twig', array(
            'userList' => $userList,
            'promotion' => $promotion
        ));
    }

    public function showUserAction(Request $request, User $user, $id) {       
        return $this->render('LulhumUserBundle:Admin:user.html.twig', array(
            'user' => $user,
            'listUserPromotion' => $request->getSession()->get('listUserPromotion')
        ));
    }

    public function editUserAction(Request $request, User $user, $id) {
        $userManager = $this->get('fos_user.user_manager');
        
        $schoolYear = $this->container->get("lulhum_toolbox")->getCurrentSchoolYear();
        $form = $this->createForm(new UserType(array('passwordRequired' => false, 'schoolyear' => $schoolYear)), $user);

        $form->handleRequest($request);

        if($form->isValid()) {
            $userManager->updateUser($user);
            $request->getSession()->getFlashBag()->add('success', 'Utilisateur modifié.');

            if(empty($request->getSession()->get('listUserPromotion'))) {
                
                return $this->redirect($this->generateUrl('lulhum_user_admin_userlist'));
            }
            else {

                return $this->redirect($this->generateUrl('lulhum_user_admin_userlist_promotion', array('promotion' => $request->getSession()->get('listUserPromotion'))));
            }
        }

        return $this->render('LulhumUserBundle:Admin:useredit.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    public function addUserAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $schoolYear = $this->container->get("lulhum_toolbox")->getCurrentSchoolYear();
        $form = $this->createForm(new UserType(array('passwordRequired' => true, 'schoolyear' => $schoolYear)), $user);

        $form->handleRequest($request);

        if($form->isValid()) {
            $userManager->updateUser($user);
            $request->getSession()->getFlashBag()->add('success', 'Utilisateur ajouté.');
            
            if(empty($request->getSession()->get('listUserPromotion'))) {
                
                return $this->redirect($this->generateUrl('lulhum_user_admin_userlist'));
            }
            else {

                return $this->redirect($this->generateUrl('lulhum_user_admin_userlist_promotion', array('promotion' => $request->getSession()->get('listUserPromotion'))));
            }
        }

        return $this->render('LulhumUserBundle:Admin:useradd.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

}