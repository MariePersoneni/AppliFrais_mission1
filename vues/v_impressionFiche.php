<?php
/**
 * DÃ©finition du modÃ¨le d'impression d'une fiche PDF
 *
 * PHP Version 7.0.3
 *
 * @category  PPE
 * @package   GSB
 * @author    PERSONENI Marie <marie.c.personeni@gmail.com>
 * @link      http://mariepersoneni.yn.fr/2019/03/22/appli-frais/
 */

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
$pdf->ln(5);
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
    $pdf->SetX(0);
    $pdf->Cell(PDF::LARGEUR_PAGE, 6, 'Frais kilométriques', 0, 1, 'C');
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
    $pdf->SetX(0);
    $pdf->Cell(PDF::LARGEUR_PAGE, 6, 'Autres frais', 0, 1, 'C');
    $pdf->TableauFrais($colonnesFraisHorsForfait, $lesFraisHorsForfait, false);
    $pdf->Ln(10);
}
// Tableau montant total
$pdf->TableauTotal($leMois, $montantTotal);
if ($pdf->PageNo() == 1) {
    $pdf->Cadre();
}
// Affichage infos comptable
if ($idComptable <> '') {
    // récupération de la position verticale du curseur
    $posY = $pdf->GetY();
    if ($posY > PDF::PIED_PAGE) {
        $pdf->AddPage();
    }
    // positionnement à 1,5 cm du bas
    $pdf->SetXY(PDF::POS_D, PDF::PIED_PAGE);
    $pdf->SetFont('Times', '', 11);
    $pdf->Cell(0,8, 'Fait à Paris, le ' . $dateValidation,0,2);
    $pdf->Cell( 0,8, 'Vu l\'agent comptable ' . strtoupper($nomComptable) 
                . ' ' . $prenomComptable);
}
$pdf->Output();
?>