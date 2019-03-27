<?php
/**
 * Suivi des frais
 *
 * PHP Version 7.0.3
 *
 * @category  PPE
 * @package   GSB
 * @author    PERSONENI Marie <marie.c.personeni@gmail.com>
 * @link      http://mariepersoneni.yn.fr/2019/03/22/appli-frais/
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$lesFichesValidees = $pdo->getLesFichesValidees();
$fiche = filter_input(INPUT_POST, 'hdFiche', FILTER_SANITIZE_STRING);
$idVisiteur = substr($fiche, 6);
$leMois = substr($fiche, 0, 6);
$visiteurASelectionner = $idVisiteur;
$moisASelectionner = $leMois;
$ficheASelectionner = $fiche;

switch ($action) {
    case 'selectionnerFiche':
        $idVisiteur = $lesFichesValidees[0]['idVisiteur'];
        $leMois = $lesFichesValidees[0]['mois'];
        $fiche = $leMois.$idVisiteur;
        $ficheASelectionner = $fiche;
        include 'vues/v_listeFicheValidee.php';
        break;
    case 'voirFicheFrais':
        $fiche = filter_input(INPUT_POST, 'lstFichesValidees', FILTER_SANITIZE_STRING);
        $idVisiteur = substr($fiche, 6);
        $leMois = substr($fiche, 0, 6);
        $visiteurASelectionner = $idVisiteur;
        $moisASelectionner = $leMois;
        $ficheASelectionner = $fiche;
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $lesFraisKm = $pdo->getLesFraisKm($idVisiteur, $leMois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
        $numAnnee = substr($leMois, 0, 4);
        $numMois = substr($leMois, 4, 2);
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
    case 'MAJfiche':
        $bouton = filter_input(INPUT_GET, 'bouton', FILTER_SANITIZE_STRING);
        $pdo->majEtatFicheFrais($idVisiteur, $leMois, $bouton);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $lesFraisKm = $pdo->getLesFraisKm($idVisiteur, $leMois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
        $numAnnee = substr($leMois, 0, 4);
        $numMois = substr($leMois, 4, 2);
        $etatFiche = $lesInfosFicheFrais['idEtat'];
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        if($lesInfosFicheFrais){
            $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        }$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        include 'vues/v_modificationEffectuee.php';
        include 'vues/v_listeFicheValidee.php';
        include 'vues/v_etatFrais.php';        
        break;
}
