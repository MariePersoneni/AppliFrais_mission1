<?php
$titre = 'REMBOURSEMENT DE FRAIS ENGAGES';
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
//$pdf->Cadre($titre);
// Tableau de frais forfait
$colonnesFraisForfait = array(
    'Frais forfaitaires', 
    'Quantité',
    'Montant unitaire',
    'Total'
);
$pdf->BasicTable($colonnesFraisForfait, $lesFraisForfaitCalcules);
$pdf->Output();
?>