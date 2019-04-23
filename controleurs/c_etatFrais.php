<?php
/**
 * Gestion de l'affichage des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB\Controleurs
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$idVisiteur = $_SESSION['idUtilisateur'];
switch ($action) {
case 'selectionnerMois':
    $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
    // Afin de sélectionner par défaut le dernier mois dans la zone de liste
    // on demande toutes les clés, et on prend la première,
    // les mois étant triés décroissants
    $lesCles = array_keys($lesMois);
    $moisASelectionner = $lesCles[0];
    include 'vues/v_listeMois.php';
    break;
case 'voirEtatFrais':
    $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
    $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
    $moisASelectionner = $leMois;
    include 'vues/v_listeMois.php';
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesFraisKm = $pdo->getLesFraisKm($idVisiteur, $leMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
    include 'vues/v_etatFrais.php';
    break;
case 'imprimerFiche':
    $leMois = filter_input(INPUT_POST, 'hdMois', FILTER_SANITIZE_STRING);
    $idVisiteur = filter_input(INPUT_POST,'hdVisiteur', FILTER_SANITIZE_STRING);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesFraisKm = $pdo->getLesFraisKm($idVisiteur, $leMois);
    $lesFraisForfaitCalcules = $pdo->getLesFraisForfaitCalcules($lesFraisForfait, 'fraisforfait');
    $lesFraisKmCalcules = $pdo->getLesFraisForfaitCalcules($lesFraisKm, 'fraiskilometrique');
    $idFicheFrais = $idVisiteur .'/'.$leMois;
    $lesInfosVisiteur = $pdo->getNomUtilisateur($idVisiteur, 'visiteur');
    $nomVisiteur = $lesInfosVisiteur['nom'];
    $prenomVisiteur = $lesInfosVisiteur['prenom'];
    $moisAffiche = getMoisFormatTexte($leMois);
    $existeFraisForfait = existeFraisForfait($lesFraisForfaitCalcules);
    $existeFraisKm = existeFraisForfait($lesFraisKmCalcules);
    $montantTotal = $pdo->calculeMontantTotalFiche(
        $lesFraisForfait, $lesFraisKm, $lesFraisHorsForfait
    );
    $idComptable = $lesInfosFicheFrais['comptable'];
    if ($idComptable <> '') {
        $dateValidation = $lesInfosFicheFrais['datevalidation'];
        $dateValidation = dateAnglaisVersFrancais($dateValidation);
        $dateValidation = getDateFormatTexte($dateValidation);
        $lesInfosComptable = $pdo->getNomUtilisateur($idComptable, 'comptable');
        $prenomComptable = $lesInfosComptable['prenom'];
        $nomComptable = $lesInfosComptable['nom'];
    }
    include 'vues/v_impressionFiche.php';
    break;    
}
