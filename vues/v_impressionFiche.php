<?php
$titre = 'REMBOURSEMENT DE FRAIS ENGAGES';
$pdf = new PDF();
$pdf->AddPage();
$pdf->Titre($titre);
$pdf->ln(10);
// Infos visiteur
$pdf->SetFont('Times', '', 11);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetX(PDF::POS_G);
$pdf->Cell(40, 6, 'Visiteur', 0, 0);
$pdf->SetX(PDF::POS_M);
$pdf->Cell(40, 6, $idFicheFrais, 0, 0);
$pdf->SetX(PDF::POS_D);
$pdf->Cell(40, 6, $nomVisiteur, 0, 1, 'R');
$pdf->SetX(PDF::POS_G);
$pdf->Cell(40, 6, 'Mois', 0, 0);
$pdf->SetX(PDF::POS_M);
$pdf->Cell(40, 6, $moisAffiche, 0, 1);
// Tableau de frais forfait
if($existeFraisForfait) {
    $colonnesFraisForfait = array(
        'Frais forfaitaires', 
        'Quantité',
        'Montant unitaire',
        'Total'
    );
    $pdf->TableauFrais($colonnesFraisForfait, $lesFraisForfaitCalcules, true);
    $pdf->Ln(10);
}
// Tableau de frais KM
if($existeFraisKm) {
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
    $pdf->TableauFrais($colonnesFraisKm,$lesFraisKmCalcules, true);
    $pdf->Ln(10);
}
// Tableau de frais hors forfait
if (!empty($lesFraisHorsForfait)) {
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
    $pdf->TableauFrais($colonnesFraisHorsForfait, $lesFraisHorsForfait, false);
    $pdf->Ln(10);
}
// Tableau montant total
$pdf->TableauTotal($leMois, $montantTotal);
if ($pdf->PageNo() == 1) {
    $pdf->Cadre();
}
$pdf->Output();
?>