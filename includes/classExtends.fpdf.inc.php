<?php
class PDF extends FPDF
{
    // En-tête
    function Header()
    {
        global $titre;
        // Logo
        $this->Image('images/logo.jpg',80);
        // Saut de ligne
        $this->Ln(20);        
    }
    
    // Cadre principal
    function Cadre($titre)
    {
        $this->SetFont('Times','B',13.5);
        // Calcul de la largeur du titre et positionnement
        $largeur = 170;
        $margeG = (210-$largeur)/2;
        $margeD = ($margeG + $largeur);
        $margeH = 70;
        $margeB = 205;
        $this->SetX($margeG);
        // Couleur du texte et du fond
        $this->SetTextColor(31, 72, 118);
        // Titre
        $this->Cell($largeur, 10, $titre, 1, 1, 'C');        
        // Dessine les lignes du document
        $this->Line($margeG, $margeH, $margeG, $margeB);
        $this->Line($margeD, $margeH, $margeD, $margeB);
        $this->Line($margeG, $margeB, $margeD, $margeB);
        
    }   
    
    // Tableau simple
    function BasicTable($header, $data)
    {
        // En-téte
        foreach($header as $col){
            $this->Cell(40,7,$col,1);            
        }
        $this->Ln();
        // Données
        foreach($data as $row){            
            $precedent = "";
            foreach($row as $col){
                if ($precedent <> $col){
                    $this->Cell(40,6,$col,1);
                }
                $precedent = $col;  
            }            
            $this->Ln();
        }
        
    }
    
    // Tableau amélioré
    function ImprovedTable($header, $data)
    {        
        // Largeurs des colonnes
        $margeG = 30;
        $w = array(50, 37, 37, 26);
        $this->SetX($margeG);
        // En-téte
        for($i=0;$i<count($header);$i++) {
            $this->SetFont('Times','BI',11);
            $this->SetTextColor(31, 72, 118);
            $this->Cell($w[$i],7,$header[$i],1,0,'C');
        }
        $this->Ln();
        // Données
        foreach($data as $row)
        {
            $this->SetX($margeG);
            $this->SetFont('Times','',11);
            $this->SetTextColor(0, 0, 0);
            $this->Cell($w[0],8,$row['libelle'],1);
            $this->Cell($w[1],8,$row['quantite'],1,0,'R');
            $this->Cell($w[2],8,number_format($row['montant'],2, ',', ' '),1,0,'R');
            $this->Cell($w[3],8,$row['total'],1,0,'R');
            $this->Ln();
        }        
    }
}