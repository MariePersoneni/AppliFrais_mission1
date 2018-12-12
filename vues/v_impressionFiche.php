<?php
$titre = 'REMBOURSEMENT DE FRAIS ENGAGES';
$pdf = new PDF();
$pdf->AddPage();
$pdf->Cadre($titre);
$pdf->ln(10);
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
$pdf->ImprovedTable($colonnesFraisForfait, $lesFraisForfaitCalcules, true);
$pdf->Ln(10);
// Tableau de frais KM
$pdf->SetFont('Times', 'BI', 11);
$pdf->SetTextColor(31, 72, 118);
$w = $pdf->GetStringWidth('Frais kilométriques');
$pdf->SetX((210-$w)/2);
$pdf->Cell(40, 6, 'Frais kilométriques', 0, 1, 'C');
$colonnesFraisKm = array(
    'Type de véhicule',
    'Kilomètres parcourus',
    'Montant unitaire',
    'Total'
);
$pdf->ImprovedTable($colonnesFraisKm,$lesFraisKmCalcules, true);
$pdf->Ln(10);
// Tableau de frais hors forfait
$colonnesFraisHorsForfait = array(
    'Date',
    'Libellé',
    'Montant'
);
$pdf->SetFont('Times', 'BI', 11);
$pdf->SetTextColor(31, 72, 118);
$w = $pdf->GetStringWidth('Autres frais');
$pdf->SetX((210-$w)/2);
$pdf->Cell(40, 6, 'Autres frais', 0, 1, 'C');
$pdf->ImprovedTable($colonnesFraisHorsForfait, $lesFraisHorsForfait, false);
$pdf->Output();
?>