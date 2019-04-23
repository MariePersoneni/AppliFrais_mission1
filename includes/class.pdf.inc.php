<?php
/**
 * Classe de génération de PDF
 *
 * PHP Version 7.0.3
 *
 * @category  PPE
 * @package   GSB\Includes
 * @author    PERSONENI Marie <marie.c.personeni@gmail.com>
 * @link      http://mariepersoneni.yn.fr/2019/03/22/appli-frais/
 */

class PDF extends FPDF
{
    // définition de constantes pour les marges du pdf
    const POS_G = 30;    
    const POS_M = 95;
    const POS_D = 140;
    const MARGE_G = 20;
    const MARGE_D = 190;    
    const LARGEUR_TITRE = 170;
    const LARGEUR_PAGE = 210;
    const PIED_PAGE = 260;
    
    // En-tête
    function Header()
    {
        // Logo
        $this->Image('images/logo.jpg',80);
        // Saut de ligne
        $this->Ln(20);        
    }
     
    function Titre($titre)
    {
        $this->SetFont('Times','B',13.5);
        $this->SetX(self::MARGE_G);
        // Couleur du texte et du fond
        $this->SetTextColor(31, 72, 118);
        // Titre
        $this->Cell(self::LARGEUR_TITRE, 10, $titre, 1, 1, 'C');
    }
    
    // Cadre principal
    function Cadre()
    {        
        $margeH = 70;
        $margeB = $this->GetY();        
        $this->Line(self::MARGE_G, $margeH, self::MARGE_G, $margeB);
        $this->Line(self::MARGE_D, $margeH, self::MARGE_D, $margeB);
        $this->Line(self::MARGE_G, $margeB, self::MARGE_D, $margeB);
        
    }  
       
    function TableauFrais($header, $data, $fraisForfait)
    {        
        // Largeurs des colonnes
        if ($fraisForfait) {
            $w = array(50, 37, 37, 26);
            $case1 = 'libelle';
            $case2 = 'quantite';
            $case4 = 'total';
        } else {
            $w = array(25, 99, 26);
            $case1 = 'date';
            $case2 = 'libelle';
        }        
        $case3 = 'montant';
        $this->SetX(self::POS_G);
        $posY = $this->GetY();
        // En-téte
        for ($i=0;$i<count($header);$i++) {
            $this->SetFont('Times','BI',11);
            $this->SetTextColor(31, 72, 118);
            $this->Cell($w[$i],7,$header[$i],'TB',0,'C');
        }
        $this->Line(self::POS_G, $posY, self::POS_G, $posY+7);
        $this->Line(self::MARGE_D-10, $posY, self::MARGE_D-10, $posY+7);
        $this->Ln();
        // Données
        foreach ($data as $row)
        {
            // initialisation de l'alignement pour la 2nde colonne
            if ($fraisForfait) {
                $align = 'R';
            } else {
                $align = 'L';
            }
            if (!$fraisForfait | $row[$case2] <> 0) {
                $this->SetX(self::POS_G);
                $this->SetFont('Times','',11);
                $this->SetTextColor(0, 0, 0);
                $this->Cell($w[0],8,utf8_decode($row[$case1]),1);
                $this->Cell($w[1],8,utf8_decode($row[$case2]),1,0,$align);
                $this->Cell($w[2],8,number_format($row[$case3],2, ',', ' '),1,0,'R');
                if ($fraisForfait) {
                    $this->Cell($w[3],8,$row['total'],1,0,'R');
                }                
                $this->Ln();
            }
        }        
    }
    
    function TableauTotal($mois, $montant)
    {
        $numAnnee = substr($mois, 0, 4);
        $numMois = substr($mois, 4, 2);
        $leMois = $numMois . '/' . $numAnnee;
        $this->SetX(self::POS_G+87);
        $this->Cell(37,8,'TOTAL '. $leMois,1);
        $this->Cell(26,8,number_format($montant,2, ',', ' '),1,1,'R');
        $this->ln(5);
        
    }
    
}