<?php

namespace Lulhum\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Lulhum\CMSBundle\Entity\Page;
use Lulhum\CMSBundle\Entity\SiteNew;
use Lulhum\CMSBundle\Form\PageType;
use Lulhum\CMSBundle\Form\SiteNewType;

class AdminController extends Controller
{
    
    public function pagesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pages = $em->getRepository('LulhumCMSBundle:Page')->findBy(array(), array('title' => 'ASC'));
        
        return $this->render('LulhumCMSBundle:Admin:pages.html.twig', array(
            'pages' => $pages,
        ));
    }

    public function newPageAction(Request $request)
    {
        $page = new Page();
        $form = $this->createForm(new PageType(), $page);
        
        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_cms_admin_pages'));
        }
        
        return $this->render('LulhumCMSBundle:Admin:page.html.twig', array(
            'form' => $form->createView(),
            'new' => true,
            'page' => $page,
        ));
    }

    public function editPageAction(Request $request, Page $page, $id)
    {
        $form = $this->createForm(new PageType(), $page);
        
        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_cms_admin_pages'));
        }
        
        return $this->render('LulhumCMSBundle:Admin:page.html.twig', array(
            'form' => $form->createView(),
            'new' => false,
            'page' => $page,
        ));
    }

    public function deletePageAction(Request $request, Page $page, $id)
    {
        if($page->getId() === $this->get('lulhum_parameters_repository')->findOneByName('siteHomepage')->getIntValue()) {

            return $this->redirect($this->generateUrl('lulhum_cms_admin_pages'));
        }
            
        $form = $this->get('form.factory')
                     ->createBuilder('form')
                     ->add('Confirmer', 'submit', array('attr' => array('class' => 'btn btn-danger')))
                     ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($page);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_cms_admin_pages'));
        }

        $message = 'Vous êtes sur le point de supprimer la page "'.$page.'"';

        return $this->render('LulhumRepartitionMedecineBundle:Admin:confirm.html.twig', array(
            'form' => $form->createView(),
            'message' => $message,
        ));
    }

    public function newsAction(Request $request)
    {        
        $em = $this->getDoctrine()->getManager();
        $news = $em->getRepository('LulhumCMSBundle:SiteNew')->findAll();
        $news[] = new SiteNew();

        $i=0;
        $forms = array_map(function($new) use (&$i) {
            return $this->createForm(new SiteNewType($i++), $new);
        }, $news);    

        array_map(function($form) use (&$request) {
            $form->handleRequest($request);
        }, $forms);

        $i=0;
        foreach($forms as $form) {
            if($form->isValid()) {
                $em->persist($news[$i]);
                $em->flush();

                return $this->redirect($this->generateUrl('lulhum_cms_admin_news'));
            }
            $i++;
        }       

        return $this->render('LulhumCMSBundle:Admin:news.html.twig', array(
            'news' => $news,
            'forms' => array_map(function($form) {
                return $form->createView();
            }, $forms),
        ));
    }
        
}
