<?php
// src/Lulhum/UserBundle/Controller/AdminController.php

namespace Lulhum\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Lulhum\RepartitionMedecineBundle\Form\GroupActionType;
use Lulhum\RepartitionMedecineBundle\Util\Paginator;
use Lulhum\UserBundle\Form\GroupMailType;
use Lulhum\UserBundle\Form\UserFilterType;
use Lulhum\UserBundle\Form\UserType;
use Lulhum\UserBundle\Entity\User;
use Lulhum\UserBundle\Entity\UserFilter;
use Lulhum\UserBundle\Util\UserGroupAction;

class AdminController extends Controller
{

    public function listUsersAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $session = new Session();
        if($session->has('adminUsersFilter') && $session->get('adminUsersFilter') instanceof UserFilter) {
            $userFilter = $session->get('adminUsersFilter');
            // Merge all the filter entities into the entity manager
            foreach($userFilter->getCollections() as $key => $collection) {
                call_user_func(array($userFilter, 'set'.ucfirst($key)), call_user_func(array($userFilter, 'get'.ucfirst($key)))->map(function($item) use (&$em) {
                    return $em->merge($item);
                }));
            }
        }
        else {
            $userFilter = new UserFilter();
        }

        $filterFormType = new UserFilterType();
        $filterForm = $this->createForm($filterFormType, $userFilter);
        if($request->getMethod() === 'POST' && $request->request->has($filterFormType->getName())) {
                $filterForm->handleRequest($request);
                $session->set('adminUsersFilter', $userFilter);
        }     

        $repository = $em->getRepository('LulhumUserBundle:User');
        $pagination = new Paginator(
            $em->getRepository('LulhumRepartitionMedecineBundle:Parameter')->findOneByName('pagination')->getValue(),
            count($repository->findBy($userFilter->getForFindBy(), array('lastname' => 'asc', 'firstname' => 'asc'))),
            $page,
            'lulhum_user_admin_userlist_page'
        );

        $groupAction = new UserGroupAction();
        $groupActionFormType = new GroupActionType(array(
            'filter' => $userFilter,
            'max' => $pagination->getMax(),
            'offset' => $pagination->getOffset(),
        ));
        $groupActionForm = $this->createForm($groupActionFormType, $groupAction);        

        if($request->getMethod() === 'POST' && $request->request->has($groupActionFormType->getName())) {                
            $groupActionForm->handleRequest($request);    
            if($groupActionForm->isValid()) {
                if($groupAction->getAction() === 'changePromotion') {
                    $session->set('adminUserGroupAction', $groupAction);
                    
                    return $this->redirect($this->generateUrl('lulhum_user_admin_group_changepromotion'));
                }
            }
        }
        
        $userList = $repository->findBy(
            $userFilter->getForFindBy(),
            array('lastname' => 'asc', 'firstname' => 'asc'),
            $pagination->getMax(),
            $pagination->getOffset()
        );        

        return $this->render('LulhumUserBundle:Admin:listusers.html.twig', array(
            'userList' => $userList,
            'filterForm' => $filterForm->createView(),
            'filter' => $userFilter,
            'pagination' => $pagination,
            'groupActionForm' => $groupActionForm->createView(),
            
        ));
    }

    public function groupActionChangePromotionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();        
        $session = new Session();
        $groupAction = UserGroupAction::merge($em, $session, 'adminUserGroupAction');
        if(is_null($groupAction)) {

            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        $form = $this->get('form.factory')
                     ->createBuilder('form')
                     ->add('promotion', 'choice', array(
                         'label' => 'Promotion',
                         'choices' => User::PROMOTIONS,
                         'required' => false,
                         'empty_value' => 'Indéfini',
                     ))
                     ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            foreach($groupAction->getEntities() as $user) {
                $user->setPromotion($form->get('promotion')->getData());
                $em->persist($user);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_user_admin_userlist'));
        }
       
        return $this->render('LulhumUserBundle:Admin:groupactionchangepromotion.html.twig', array(
            'groupAction' => $groupAction,
            'form' => $form->createView(),
        ));
    }

    public function resetUserFilterAction()
    {
        $session = new Session();
        $session->remove('adminUsersFilter');

        return $this->redirect($this->generateUrl('lulhum_user_admin_userlist'));
    }

    public function showUserAction(Request $request, User $user, $id) {       
        return $this->render('LulhumUserBundle:Admin:user.html.twig', array(
            'user' => $user,
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

            return $this->redirect($this->generateUrl('lulhum_user_admin_userlist'));
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

            return $this->redirect($this->generateUrl('lulhum_user_admin_userlist'));
        }

        return $this->render('LulhumUserBundle:Admin:useradd.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    public function deleteUserAction(Request $request, User $user, $id)
    {
        $form = $this->get('form.factory')
                     ->createBuilder('form')
                     ->add('Confirmer', 'submit', array('attr' => array('class' => 'btn btn-danger')))
                     ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_user_admin_userlist'));
        }

        return $this->render('LulhumUserBundle:Admin:confirm.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
            'message' => 'Attention, vous êtes sur le point de supprimer l\'utilisateur suivant de manière définitive !',
        ));
    }

    public function switchAdminAction(Request $request, User $user, $id)
    {
        $form = $this->get('form.factory')
                     ->createBuilder('form')
                     ->add('Confirmer', 'submit', array('attr' => array('class' => 'btn btn-danger')))
                     ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            if($user->hasRole('ROLE_ADMIN')) {
                $user->removeRole('ROLE_ADMIN');
            }
            else {
                $user->addRole('ROLE_ADMIN');
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_user_admin_userlist'));
        }

        if($user->hasRole('ROLE_ADMIN')) {
            $message = 'Attention, vous êtes sur le point de supprimer le rôle d\'administrateur de l\'utilisateur suivant !';
        }
        else {
            $message = 'Attention, vous êtes sur le point de nommer administrateur l\'utilisateur suivant !';
        }

        return $this->render('LulhumUserBundle:Admin:confirm.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
            'message' => $message,
        ));
    }
    
    public function groupMailAction(Request $request)
    {
        $groupmail = $this->get('lulhum_user_groupmailer');
        $form = $this->createForm(new GroupMailType(), $groupmail);

        $form->handleRequest($request);

        if($form->isValid()) {
            $groupmail->send();
            $request->getSession()->getFlashBag()->add('success', 'Mail envoyé.');
        }

        return $this->render('LulhumUserBundle:Admin:groupmail.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
