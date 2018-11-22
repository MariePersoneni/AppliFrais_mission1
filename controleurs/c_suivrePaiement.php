<?php
/**
 * Gestion des frais
 *
 * PHP Version 7.0.3
 *
 * @category  PPE
 * @package   GSB
 * @author    PERSONENI Marie <mpersoneni@nomentreprise.com>
 * @copyright NomEntreprise
 * @license   nomEtreprise
 * @version   GIT: <0>
 * @link      http://www.siteEntreprise
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$lesFichesValidees = $pdo->getLesFichesValidees();
$fiche = filter_input(INPUT_POST, 'hdFiche', FILTER_SANITIZE_STRING);
$idVisiteur = substr($fiche, 6);
$mois = substr($fiche, 0, 6);
$visiteurASelectionner = $idVisiteur;
$moisASelectionner = $mois;
$ficheASelectionner = $fiche;
$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
$lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
$numAnnee = substr($mois, 0, 4);
$numMois = substr($mois, 4, 2);
$etatFiche = $lesInfosFicheFrais['idEtat'];
$libEtat = $lesInfosFicheFrais['libEtat'];
$montantValide = $lesInfosFicheFrais['montantValide'];
if($lesInfosFicheFrais){
    $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
}$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];

switch ($action) {
    case 'selectionnerFiche':
        $idVisiteur = $lesFichesValidees[0]['idVisiteur'];
        $mois = $lesFichesValidees[0]['mois'];
        $fiche = $mois.$idVisiteur;
        $ficheASelectionner = $fiche;
        include 'vues/v_listeFicheValidee.php';
        break;
    case 'voirFicheFrais':
        $fiche = filter_input(INPUT_POST, 'lstFichesValidees', FILTER_SANITIZE_STRING);
        $idVisiteur = substr($fiche, 6);
        $mois = substr($fiche, 0, 6);
        $visiteurASelectionner = $idVisiteur;
        $moisASelectionner = $mois;
        $ficheASelectionner = $fiche;
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
        $numAnnee = substr($mois, 0, 4);
        $numMois = substr($mois, 4, 2);
        $etatFiche = $lesInfosFicheFrais['idEtat'];
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        if($lesInfosFicheFrais){
            $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        }
        include 'vues/v_listeFicheValidee.php';
        include 'vues/v_etatFrais.php';
        break;
    case 'mettreEnPaiement':
        $pdo->majEtatFicheFrais($idVisiteur, $mois, 'MP');
        include 'vues/v_modificationEffectuee.php';
        include 'vues/v_listeFicheValidee.php';
        include 'vues/v_etatFrais.php';        
        break;
    case 'fichePayee':
        $pdo->majEtatFicheFrais($idVisiteur, $mois, 'RB');
        include 'vues/v_modificationEffectuee.php';
        include 'vues/v_listeFicheValidee.php';
        include 'vues/v_etatFrais.php';
        break;
}
