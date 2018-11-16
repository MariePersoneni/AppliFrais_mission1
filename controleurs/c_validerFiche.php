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
switch ($action) {
    case 'selectionnerVisiteur':
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $lesClesVisiteurs = array_keys($lesVisiteurs);
        $visiteurASelectionner = $lesClesVisiteurs[0];
        $idVisiteur = $lesVisiteurs[0]['id'] ;
        xdebug_break();
        $lesMois = getLesMois();
        $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
        // Afin de sélectionner par défaut le dernier mois dans la zone de liste
        // on demande toutes les clés, et on prend la première,
        // les mois étant triés décroissants
        $lesClesMois = array_keys($lesMois);
        $moisASelectionner = $lesClesMois[0];
        include 'vues/v_listeVisiteurs.php';   
        break;
        
    case 'voirFicheFrais': 
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $visiteurASelectionner = $idVisiteur;
        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
        $moisASelectionner = $leMois;       
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
        $numAnnee = substr($leMois, 0, 4);
        $numMois = substr($leMois, 4, 2);
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        if ($lesInfosFicheFrais){
            $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        }    
        include 'vues/v_listeVisiteurs.php';
        include 'vues/v_validerFicheFrais.php';
}
