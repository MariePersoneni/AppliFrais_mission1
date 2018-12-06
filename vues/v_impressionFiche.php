<?php
require_once 'includes/FPDF/fpdf.php';
require_once 'includes/classExtends.fpdf.inc.php';
$titre = 'REMBOURSEMENT DE FRAIS ENGAGES';
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cadre($titre);
$pdf->Output();
?>