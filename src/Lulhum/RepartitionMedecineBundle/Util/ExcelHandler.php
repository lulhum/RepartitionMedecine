<?php
// src/Lulhum/RepartitionMedecine/Util/ExcelHandler.php

namespace Lulhum\RepartitionMedecineBundle\Util;

use Doctrine\ORM\EntityManager;

class ExcelHandler
{
    const TYPES = array(
        'xls' => array(
            'writer' => 'Excel5',
            'type' => 'application/vnd.ms-excel; charset=utf-8',
            'supportSheets' => true,
            'writeAllSheets' => false,
        ),
        'xlsx' => array(
            'writer' => 'Excel2007',
            'type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8',
            'supportSheets' => true,
            'writeAllSheets' => false,
        ),
        'html' => array(
            'writer' => 'HTML',
            'type' => 'text/html; charset=utf-8',
            'supportSheets' => true,
            'writeAllSheets' => true,
        ),
        'csv' => array(
            'writer' => 'CSV',
            'type' => 'text/csv; charset=utf-8',
            'supportSheets' => false,
            'writeAllSheets' => false,
        ),
    );

    const TABLES = array(
        'stagesbyuser',
        'stagesbycategory',
        'users'
    );
    
    protected $phpExcelObject;

    protected $em;

    protected $htmlHelper;

    protected $sheet;

    protected $ext;

    protected $table;

    protected $page = null;

    public function __construct(EntityManager $em, \PhpExcel $phpExcelObject, $htmlHelper, $table, $ext)
    {
        if(!array_key_exists($ext, self::TYPES)) {
            echo $ext;
            throw new \Exception('Format d\'export non supporté');
        }
        if(!in_array($table, self::TABLES)) {
            throw new \Exception('Format d\'export non supporté');
        }
        $this->ext = $ext;
        $this->table = $table;
        $this->phpExcelObject = $phpExcelObject;
        $this->htmlHelper = $htmlHelper;
        $this->em = $em;
    }

    private function num2alpha($n)
    {
        for($r = ""; $n >= 0; $n = intval($n / 26) - 1) {
            $r = chr($n%26 + 0x41) . $r;
        }
        
        return $r;
    }

    private function removeAccents($str, $charset='utf-8')
    {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);    
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        $str = preg_replace('#&[^;]+;#', '', $str);
    
        return $str;
    }

    public function getWriter()
    {
        return self::TYPES[$this->ext]['writer'];
    }

    public function getFilename()
    {
        return $this->table.(is_null($this->page) ? '' : '-'.$this->page).'.'.$this->ext;
    }

    public function getContentType()
    {
        return self::TYPES[$this->ext]['type'];
    }

    public function supportSheets()
    {
        return self::TYPES[$this->ext]['supportSheets'];
    }

    public function writeAllSheets()
    {
        return self::TYPES[$this->ext]['writeAllSheets'];
    }
        
    public function getPhpExcelObject()
    {
        return $this->phpExcelObject;
    }

    public function setPage($page)
    {
        if(!is_null($page) && $page >= 0 && $page <= $this->phpExcelObject->getSheetCount()) {
            $this->page = $page;
            $sheetname = $this->phpExcelObject->getSheet($page)->getTitle();
            $this->phpExcelObject->setIndexByName($sheetname, 0);
            $this->phpExcelObject->setActiveSheetIndex(0);
        }
    }    

    public function create()
    {
        call_user_func(array($this, $this->table));
    }

    private function sheetHeader($title, $periods, $subject)
    {
        $this->phpExcelObject->getActiveSheet()
                             ->setTitle($this->removeAccents($title))
                             ->setCellValue('A1', $this->htmlHelper->toRichTextObject('<b>'.$this->removeAccents($subject).'</b>'))
                             ->getColumnDimension('A')->setAutoSize(true);
        $i = 1;
        foreach($periods as $period) {
            $this->phpExcelObject->getActiveSheet()->setCellValue($this->num2alpha($i).'1', $this->htmlHelper->toRichTextObject('<b>'.$this->removeAccents($period).'</b>'))
                                 ->getColumnDimension($this->num2alpha($i))->setAutoSize(true);
            $i++;
        }
    }

    private function stagesByUser()
    {
        $this->phpExcelObject->getProperties()->setCreator("liuggio")
                             ->setLastModifiedBy($this->em->getRepository('LulhumRepartitionMedecineBundle:Parameter')->findOneByName('plateformMail')->getValue())
                             ->setTitle($this->removeAccents('Répartition des Stages par étudiants - ').(new \DateTime())->format('d/m/Y'))
                             ->setSubject($this->removeAccents('Répartition des Stages par étudiants - ').(new \DateTime())->format('d/m/Y'))
                             ->setDescription($this->removeAccents('Répartition des Stages par étudiants - ').(new \DateTime())->format('d/m/Y'))
                             ->setKeywords($this->removeAccents("Répartition Stages Médecine"))
                             ->setCategory($this->removeAccents("Répartition Stages Médecine"));
        $periods = $this->em->getRepository('LulhumRepartitionMedecineBundle:Period')->findCurrents();
        $periodsColumn = array();
        $i = 1;
        foreach($periods as $period) {
            $periodsColumn[$period->getId()] = $this->num2alpha($i);
            $i++;
        }        
        $start = true;
        $promotion = null;
        $this->sheet = 0;        
        foreach($this->em->getRepository('LulhumRepartitionMedecineBundle:Stage')->periodsResume($periods, 'byuser') as $stage) {
            if($stage->getUser()->getPromotion() != $promotion || $start) {
                $start = false;
                $promotion = $stage->getUser()->getPromotion();
                if($this->sheet > 0) {
                    $this->phpExcelObject->createSheet();
                }
                $this->phpExcelObject->setActiveSheetIndex($this->sheet);
                $this->sheet++;
                $this->sheetHeader($promotion, $periods, 'Utilisateur');                
                $user = null;
                $i = 1;
            }
            if($user != $stage->getUser()->getId()) {
                $i++;
                $user = $stage->getUser()->getId();
                $this->phpExcelObject->getActiveSheet()->setCellValue('A'.$i, $this->removeAccents($stage->getUser()));
            }
            $this->phpExcelObject->getActiveSheet()->setCellValue($periodsColumn[$stage->getProposal()->getPeriod()->getId()].$i, $this->removeAccents($stage->getProposal()->getCategory()));            
        }
        $this->phpExcelObject->setActiveSheetIndex(0);
    }

    private function stagesByCategory()
    {
        $this->phpExcelObject->getProperties()->setCreator("liuggio")
                             ->setLastModifiedBy($this->em->getRepository('LulhumRepartitionMedecineBundle:Parameter')->findOneByName('plateformMail')->getValue())
                             ->setTitle($this->removeAccents('Répartition des Stages par Modèle - ').(new \DateTime())->format('d/m/Y'))
                             ->setSubject($this->removeAccents('Répartition des Stages par Modèle - ').(new \DateTime())->format('d/m/Y'))
                             ->setDescription($this->removeAccents('Répartition des Stages par Modèle - ').(new \DateTime())->format('d/m/Y'))
                             ->setKeywords($this->removeAccents("Répartition Stages Médecine"))
                             ->setCategory($this->removeAccents("Répartition Stages Médecine"));
        $periods = $this->em->getRepository('LulhumRepartitionMedecineBundle:Period')->findCurrents();
        $periodsColumn = array();
        $i = 1;        
        foreach($periods as $period) {
            $periodsColumn[$period->getId()] = $this->num2alpha($i);
            $i++;
        }
        $start = true;
        $promotion = null;
        $this->sheet = 0;        
        foreach($this->em->getRepository('LulhumRepartitionMedecineBundle:Stage')->periodsResume($periods, 'bycategory') as $stage) {
            if($stage->getUser()->getPromotion() != $promotion || $start) {
                $start = false;
                $promotion = $stage->getUser()->getPromotion();
                if($this->sheet > 0) {
                    $this->phpExcelObject->createSheet();
                }
                $this->phpExcelObject->setActiveSheetIndex($this->sheet);
                $this->sheet++;
                $this->sheetHeader($promotion, $periods, 'Modèle');                
                $category = null;
                $periodsColumnIndexes = array_fill_keys(array_keys($periodsColumn), 1);
                $i = 1;
            }
            if($stage->getProposal()->getCategory()->getId() != $category) {
                $max = max($periodsColumnIndexes) + 1;
                $periodsColumnIndexes = array_fill_keys(array_keys($periodsColumnIndexes), $max);
                $category = $stage->getProposal()->getCategory()->getId();
                $this->phpExcelObject->getActiveSheet()->setCellValue('A'.($max+1), $this->removeAccents($stage->getProposal()->getCategory()));
            }
            $periodId = $stage->getProposal()->getPeriod()->getId();
            $periodsColumnIndexes[$periodId]++;
            $this->phpExcelObject->getActiveSheet()
                                 ->setCellValue(
                                     $periodsColumn[$periodId].$periodsColumnIndexes[$periodId],
                                     $this->removeAccents($stage->getUser())
                                 );            
        }
        $this->phpExcelObject->setActiveSheetIndex(0);
    }

    private function users()
    {
        $this->phpExcelObject->getProperties()->setCreator("liuggio")
                             ->setLastModifiedBy($this->em->getRepository('LulhumRepartitionMedecineBundle:Parameter')->findOneByName('plateformMail')->getValue())
                             ->setTitle($this->removeAccents('Liste des utilisateurs - ').(new \DateTime())->format('d/m/Y'))
                             ->setSubject($this->removeAccents('Liste des utilisateurs - ').(new \DateTime())->format('d/m/Y'))
                             ->setDescription($this->removeAccents('Liste des utilisateurs - ').(new \DateTime())->format('d/m/Y'))
                             ->setKeywords($this->removeAccents("Utilisateurs Répartition Stages Médecine"))
                             ->setCategory($this->removeAccents("Utilisateurs Répartition Stages Médecine"));
        $start = true;
        $promotion = null;
        $this->sheet = 0;        
        foreach($this->em->getRepository('LulhumUserBundle:User')->findBy(array(), array('promotion' => 'ASC', 'lastname' => 'ASC', 'firstname' => 'ASC')) as $user) {
            if($user->getPromotion() != $promotion || $start) {
                $start = false;
                $promotion = $user->getPromotion();
                if($this->sheet > 0) {
                    $this->phpExcelObject->createSheet();
                }
                $this->phpExcelObject->setActiveSheetIndex($this->sheet);
                $this->sheet++;
                $this->phpExcelObject->getActiveSheet()
                                     ->setTitle($this->removeAccents($user->getTextPromotion()))
                                     ->setCellValue('A1', $this->htmlHelper->toRichTextObject('<b>'.$this->removeAccents('Nom').'</b>'))
                                     ->getColumnDimension('A')->setAutoSize(true);
                $this->phpExcelObject->getActiveSheet()
                                     ->setCellValue('B1', $this->htmlHelper->toRichTextObject('<b>'.$this->removeAccents('Prénom').'</b>'))
                                     ->getColumnDimension('B')->setAutoSize(true);
                $this->phpExcelObject->getActiveSheet()
                                     ->setCellValue('C1', $this->htmlHelper->toRichTextObject('<b>'.$this->removeAccents('Mail').'</b>'))
                                     ->getColumnDimension('C')->setAutoSize(true);
                $this->phpExcelObject->getActiveSheet()
                                     ->setCellValue('D1', $this->htmlHelper->toRichTextObject('<b>'.$this->removeAccents('Téléphone').'</b>'))
                                     ->getColumnDimension('D')->setAutoSize(true);
                $this->phpExcelObject->getActiveSheet()
                                     ->setCellValue('E1', $this->htmlHelper->toRichTextObject('<b>'.$this->removeAccents('N. Étudiant').'</b>'))
                                     ->getColumnDimension('E')->setAutoSize(true);
                $this->phpExcelObject->getActiveSheet()
                                     ->setCellValue('F1', $this->htmlHelper->toRichTextObject('<b>'.$this->removeAccents('Groupe').'</b>'))
                                     ->getColumnDimension('F')->setAutoSize(true);
                $this->phpExcelObject->getActiveSheet()
                                     ->setCellValue('G1', $this->htmlHelper->toRichTextObject('<b>'.$this->removeAccents('Présent').'</b>'))
                                     ->getColumnDimension('G')->setAutoSize(true);
                $this->phpExcelObject->getActiveSheet()
                                     ->setCellValue('H1', $this->htmlHelper->toRichTextObject('<b>'.$this->removeAccents('Internet').'</b>'))
                                     ->getColumnDimension('H')->setAutoSize(true);
                $this->phpExcelObject->getActiveSheet()
                                     ->setCellValue('I1', $this->htmlHelper->toRichTextObject('<b>'.$this->removeAccents('Procuration').'</b>'))
                                     ->getColumnDimension('I')->setAutoSize(true);
                $i = 1;
            }
            $i++;
            $this->phpExcelObject->getActiveSheet()
                                 ->setCellValue('A'.$i, $this->removeAccents($user->getLastname()))
                                 ->setCellValue('B'.$i, $this->removeAccents($user->getFirstname()))
                                 ->setCellValue('C'.$i, $this->removeAccents($user->getEmail()))
                                 ->setCellValue('D'.$i, $this->removeAccents($user->getPhone()))
                                 ->setCellValue('E'.$i, $this->removeAccents($user->getStudentId()))
                                 ->setCellValue('F'.$i, $this->removeAccents($user->getTextRepartitionGroup()))
                                 ->setCellValue('G'.$i, $this->removeAccents($user->getPresent()))
                                 ->setCellValue('H'.$i, $this->removeAccents($user->getInternetAccess()))
                                 ->setCellValue('I'.$i, $this->removeAccents($user->getTextProxy()));            
        }
        $this->phpExcelObject->setActiveSheetIndex(0);
    }
}
