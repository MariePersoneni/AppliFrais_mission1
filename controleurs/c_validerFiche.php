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
$idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
$mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
$lesVisiteurs = $pdo->getLesVisiteurs();
$lesMois = getLesMois();
$visiteurASelectionner = $idVisiteur;
$moisASelectionner = $mois;
include 'vues/v_listeVisiteurs.php';
switch ($action) {
    case 'selectionnerVisiteur':
        $idVisiteur = $lesVisiteurs[0]['id'];
        $moisA = $lesMois[0]['mois'];
        //include 'vue//v_listeVisiteurs.php';   
        break;        
    case 'voirFicheFrais': 
        $moisASelectionner = $mois;       
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
        $numAnnee = substr($mois, 0, 4);
        $numMois = substr($mois, 4, 2);
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        if($lesInfosFicheFrais){
            $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        }    
        //include 'vues/v_listeVisiteurs.php';
        include 'vues/v_validerFicheFrais.php';
        break;
    case 'validerMajFraisForfait':
        $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if (lesQteFraisValides($lesFrais)) {
            $pdo->majFraisForfait($idVisiteur, $mois, $lesFrais);
        } else {
            ajouterErreur('Les valeurs des frais doivent être numériques');
            include 'vues/v_erreurs.php';
        }
        break;
}
