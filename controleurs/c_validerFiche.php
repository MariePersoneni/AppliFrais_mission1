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
$lesVisiteurs = $pdo->getLesVisiteurs();
$lesMois = getLesMois();

switch ($action) {
    case 'selectionnerVisiteur':
        $idVisiteur = $lesVisiteurs[0]['id'];
        $mois = $lesMois[0]['mois'];
        $visiteurASelectionner = $idvisiteur;
        $moisASelectionner = $mois;
        include 'vues/v_listeVisiteurs.php';
        break;        
    case 'voirFicheFrais': 
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $visiteurASelectionner = $idvisiteur;
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
        include 'vues/v_listeVisiteurs.php';
        include 'vues/v_validerFicheFrais.php';
        break;
    case 'validerMajFraisForfait':
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $visiteurASelectionner = $idvisiteur;
        $moisASelectionner = $mois;
        $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if (lesQteFraisValides($lesFrais)) {
            $pdo->majFraisForfait($idVisiteur, $mois, $lesFrais);
            include 'vues/v_listeVisiteurs.php';
            include 'vues/v_validerFicheFrais.php';
        } else {
            ajouterErreur('Les valeurs des frais doivent être numériques');
            include 'vues/v_erreurs.php';
        }
        break;
}
