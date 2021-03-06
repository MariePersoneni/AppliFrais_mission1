<?php
/**
 * Validation des fiches de frais
 *
 * PHP Version 7.0.3
 *
 * @category  PPE
 * @package   GSB\Controleurs
 * @author    PERSONENI Marie <marie.c.personeni@gmail.com>
 * @link      http://mariepersoneni.yn.fr/2019/03/22/appli-frais/
 */

//Script qui cloture toutes les fiche du mois dernier
$pdo->clotureFichesMoisPrecedent();

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$lesVisiteurs = $pdo->getLesVisiteurs();
$lesMois = getLesMois();
$idVisiteur = filter_input(INPUT_POST, 'idVisiteur', FILTER_SANITIZE_STRING);
$mois = filter_input(INPUT_POST, 'mois', FILTER_SANITIZE_STRING);
$visiteurASelectionner = $idVisiteur;
$moisASelectionner = $mois;
$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
$lesFraisKm = $pdo->getLesFraisKm($idVisiteur, $mois);
$lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
$etatFiche = $lesInfosFicheFrais['idEtat'];
$libEtat = $lesInfosFicheFrais['libEtat'];
$idFrais = filter_input(INPUT_POST, 'idFrais', FILTER_SANITIZE_STRING);

switch ($action) {
    case 'selectionnerVisiteur':
        $idVisiteur = $lesVisiteurs[0]['id'];
        $mois = $lesMois[0]['mois'];
        $visiteurASelectionner = $idVisiteur;
        $moisASelectionner = $mois;
        include 'vues/v_listeVisiteurs.php';
        break;
    case 'voirFicheFrais':
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs',
            FILTER_SANITIZE_STRING);
        $mois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $visiteurASelectionner = $idVisiteur;
        $moisASelectionner = $mois;
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
        $lesFraisKm = $pdo->getLesFraisKm($idVisiteur, $mois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
        $numAnnee = substr($mois, 0, 4);
        $numMois = substr($mois, 4, 2);
        $etatFiche = $lesInfosFicheFrais['idEtat']; 
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        if($lesInfosFicheFrais){
            $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        }
        include 'vues/v_listeVisiteurs.php';
        include 'vues/v_validerFicheFrais.php';
        break;
    case 'validerMajFraisForfait':        
        $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT,
            FILTER_FORCE_ARRAY);
        $lesFraisKm = filter_input(INPUT_POST, 'lesFraisKm', FILTER_DEFAULT,
            FILTER_FORCE_ARRAY);
        if (lesQteFraisValides($lesFrais) && lesQteFraisValides($lesFraisKm)) {
            $pdo->majFraisForfait($idVisiteur, $mois, $lesFrais);
            $pdo->majFraisKm($idVisiteur, $mois, $lesFraisKm);
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
            $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
            $lesFraisKm = $pdo->getLesFraisKm($idVisiteur, $mois);
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            include 'vues/v_modificationEffectuee.php';
            include 'vues/v_listeVisiteurs.php';
            include 'vues/v_validerFicheFrais.php';
        } else {
            ajouterErreur('Les valeurs des frais doivent être numériques');
            include 'vues/v_erreurs.php';
            include 'vues/v_listeVisiteurs.php';
            include 'vues/v_validerFicheFrais.php';
        }
        break;
    case 'majNbJustificatifs': 
        $nbJustificatifs = filter_input(INPUT_POST, 'nbJustificatifs',
            FILTER_SANITIZE_NUMBER_INT);
        if (estEntierPositif($nbJustificatifs)) {
            $pdo->majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs);
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
            $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
            $lesFraisKm = $pdo->getLesFraisKm($idVisiteur, $mois);
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
            include 'vues/v_modificationEffectuee.php';
            include 'vues/v_listeVisiteurs.php';
            include 'vues/v_validerFicheFrais.php';
        } else {
            ajouterErreur('Les valeurs des frais doivent être numériques');
            include 'vues/v_erreurs.php';
            include 'vues/v_listeVisiteurs.php';
            include 'vues/v_validerFicheFrais.php';
        }
        break;
    case 'reporterFraisHorsForfait':
        $pdo->reporterFraisHorsForfait($idVisiteur,$mois,$idFrais);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
        $lesFraisKm = $pdo->getLesFraisKm($idVisiteur, $mois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        include 'vues/v_modificationEffectuee.php';
        include 'vues/v_listeVisiteurs.php';
        include 'vues/v_validerFicheFrais.php';
        break;
    case 'rejeterFraisHorsForfait':       
        $pdo->rejeterFraisHorsForfait($idFrais);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
        $lesFraisKm = $pdo->getLesFraisKm($idVisiteur, $mois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        include 'vues/v_modificationEffectuee.php';
        include 'vues/v_listeVisiteurs.php';
        include 'vues/v_validerFicheFrais.php';
        break;
    case 'validerFicheFrais': 
        $pdo->majEtatFicheFrais($idVisiteur, $mois, 'VA');
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
        $lesFraisKm = $pdo->getLesFraisKm($idVisiteur, $mois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $etatFiche = $lesInfosFicheFrais['idEtat'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $montantValide = $pdo->calculeMontantTotalFiche(
            $lesFraisForfait, $lesFraisKm, $lesFraisHorsForfait
            );
        $pdo->majMontantFicheFrais($idVisiteur, $mois, $montantValide);
        include 'vues/v_modificationEffectuee.php';
        include 'vues/v_listeVisiteurs.php';
        include 'vues/v_validerFicheFrais.php';
        break;
}
