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
    
    
}