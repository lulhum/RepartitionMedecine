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

    private function sheetHeader($title, $periods)
    {
        $this->phpExcelObject->getActiveSheet()
                             ->setTitle($this->removeAccents($title))
                             ->setCellValue('A1', $this->htmlHelper->toRichTextObject('<b>Utilisateur</b>'))
                             ->getColumnDimension('A')->setAutoSize(true);
        $i = 1;
        foreach($periods as $period) {
            $this->phpExcelObject->getActiveSheet()->setCellValue($this->num2alpha($i).'1', $this->htmlHelper->toRichTextObject('<b>'.$this->removeAccents($period).'</b>'))
                                 ->getColumnDimension($this->num2alpha($i))->setAutoSize(true);
            $i++;
        }
    }

    public function create()
    {
        call_user_func(array($this, $this->table));
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
        $periodsColumns = array();
        $i = 1;
        foreach($periods as $period) {
            $periodsColumn[$period->getId()] = $this->num2alpha($i);
            $i++;
        }        
        $start = true;
        $promotion = null;
        $this->sheet = 0;        
        foreach($this->em->getRepository('LulhumRepartitionMedecineBundle:Stage')->periodsResume($periods) as $stage) {
            if($stage->getUser()->getPromotion() != $promotion || $start) {
                $start = false;
                $promotion = $stage->getUser()->getPromotion();
                if($this->sheet > 0) {
                    $this->phpExcelObject->createSheet();
                }
                $this->phpExcelObject->setActiveSheetIndex($this->sheet);
                $this->sheet++;
                $this->sheetHeader($stage->getUser()->getPromotion(), $periods);                
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
}