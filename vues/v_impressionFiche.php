<?php
$titre = 'REMBOURSEMENT DE FRAIS ENGAGES';
$pdf = new PDF();
$pdf->AddPage();
$pdf->Cadre($titre);
$pdf->ln(12);
// Infos visiteur
$pdf->SetFont('Times', '', 11);
$pdf->SetTextColor(0, 0, 0);
$colonneG = 30;
$colonneM = 95;
$colonneD = 140;
$pdf->SetX($colonneG);
$pdf->Cell(40, 6, 'Visiteur', 0, 0);
$pdf->SetX($colonneM);
$pdf->Cell(40, 6, $idFicheFrais, 0, 0);
$pdf->SetX($colonneD);
$pdf->Cell(40, 6, $nomVisiteur, 0, 1, 'R');
$pdf->SetX($colonneG);
$pdf->Cell(40, 6, 'Mois', 0, 0);
$pdf->SetX($colonneM);
$pdf->Cell(40, 6, $moisAffiche, 0, 1);
// Tableau de frais forfait
$colonnesFraisForfait = array(
    'Frais forfaitaires', 
    'Quantité',
    'Montant unitaire',
    'Total'
);
$pdf->ImprovedTable($colonnesFraisForfait, $lesFraisForfaitCalcules);
// Tableau de frais KM
$colonnesFraisKm = array(
    'Type de véhicule',
    'Kilomètres parcourus',
    'Montant unitaire',
    'Total'
);
$pdf->Output();
?>