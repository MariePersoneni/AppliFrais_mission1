<?php
/**
 * Fonctions pour l'application GSB
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Teste si un quelconque utilisateur est connecté
 *
 * @return boolean vrai ou faux
 */
function estConnecte()
{
    return isset($_SESSION['idUtilisateur']);
}

/**
 * Enregistre dans une variable session les infos d'un utilisateur
 *
 * @param String $idUtilisateur ID du visiteur
 * @param String $nom        Nom du visiteur
 * @param String $prenom     Prénom du visiteur
 *
 * @return null
 */
function connecter($idUtilisateur, $nom, $prenom, $profil)
{
    $_SESSION['idUtilisateur'] = $idUtilisateur;
    $_SESSION['nom']        = $nom;
    $_SESSION['prenom']     = $prenom;
    $_SESSION['profil']     = $profil;
}

/**
 * Détruit la session active
 *
 * @return null
 */
function deconnecter()
{
    session_destroy();
}

/**
 * Transforme une date au format français jj/mm/aaaa vers le format anglais
 * aaaa-mm-jj
 *
 * @param String $maDate au format  jj/mm/aaaa
 *
 * @return String Date au format anglais aaaa-mm-jj
 */
function dateFrancaisVersAnglais($maDate)
{
    @list($jour, $mois, $annee) = explode('/', $maDate);
    return date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
}

/**
 * Transforme une date au format format anglais aaaa-mm-jj vers le format
 * français jj/mm/aaaa
 *
 * @param String $maDate au format  aaaa-mm-jj
 *
 * @return String Date au format format français jj/mm/aaaa
 */
function dateAnglaisVersFrancais($maDate)
{
    @list($annee, $mois, $jour) = explode('-', $maDate);
    $date = $jour . '/' . $mois . '/' . $annee;
    return $date;
}


/**
 * Retourne le mois au format aaaamm selon le jour dans le mois
 *
 * @param String $date au format  jj/mm/aaaa
 *
 * @return String Mois au format aaaamm
 */
function getMois($date)
{
    @list($jour, $mois, $annee) = explode('/', $date);
    unset($jour);
    if (strlen($mois) == 1) {
        $mois = '0' . $mois;
    }
    return $annee . $mois;
}


/**
 * Fonction qui retourne le mois suivant un mois passé en paramètre
 *
 * @param String $mois Contient le mois à utiliser
 *
 * @return String le mois d'après
 */
function getMoisSuivant($mois)
{
    $numAnnee = substr($mois, 0, 4);
    $numMois = substr($mois, 4, 2);
    if ($numMois == '12') {
        $numMois = '01';
        $numAnnee++;
    } else {
        $numMois++;
    }
    if (strlen($numMois) == 1) {
        $numMois = '0' . $numMois;
    }
    return $numAnnee . $numMois;
}

/**
 * Fonction qui retourne le mois précédent un mois passé en paramètre
 *
 * @param String $mois Contient le mois à utiliser
 *
 * @return String le mois d'avant
 */
function getMoisPrecedent($mois)
{
    $numAnnee = substr($mois, 0, 4);
    $numMois = substr($mois, 4, 2);
    if ($numMois == '01') {
        $numMois = '12';
        $numAnnee--;
    } else {
        $numMois--;
    }
    if (strlen($numMois) == 1) {
        $numMois = '0' . $numMois;
    }
    return $numAnnee . $numMois;
}


/**
 * Retourne un tableau associatif qui contient les mois
 * d'un an en arrière depuis la date du jour
 * @return string[]|number[]
 */
function getLesMois()
{
    $mois   = date('m');
    $annee  = date('Y');
    for ($i = 0 ; $i <12 ; $i++) {
        $lesMois[] = array(
            'mois'      => $annee . $mois,
            'numAnnee'  => $annee,
            'numMois'   => $mois
        );
        $mois--; // Décrémente le mois
        if (strlen($mois) == 1) {
            $mois = '0' . $mois;
        }
        if ($mois   == 0) {
            $mois   = 12;
            $annee --;
        }
    }
    return $lesMois;
}

/**
 * Indique si une valeur est un entier positif ou nul
 *
 * @param Integer $valeur Valeur
 *
 * @return Boolean vrai ou faux
 */
function estEntierPositif($valeur)
{
    return preg_match('/[^0-9]/', $valeur) == 0;
}

/**
 * Indique si un tableau de valeurs est constitué d'entiers positifs ou nuls
 *
 * @param Array $tabEntiers Un tableau d'entier
 *
 * @return Boolean vrai ou faux
 */
function estTableauEntiers($tabEntiers)
{
    $boolReturn = true;
    foreach ($tabEntiers as $unEntier) {
        if (!estEntierPositif($unEntier)) {
            $boolReturn = false;
        }
    }
    return $boolReturn;
}

/**
 * Vérifie si une date est inférieure d'un an à la date actuelle
 *
 * @param String $dateTestee Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateDepassee($dateTestee)
{
    $dateActuelle = date('d/m/Y');
    @list($jour, $mois, $annee) = explode('/', $dateActuelle);
    $annee--;
    $anPasse = $annee . $mois . $jour;
    @list($jourTeste, $moisTeste, $anneeTeste) = explode('/', $dateTestee);
    return ($anneeTeste . $moisTeste . $jourTeste < $anPasse);
}

/**
 * Vérifie la validité du format d'une date française jj/mm/aaaa
 *
 * @param String $date Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateValide($date)
{
    $tabDate = explode('/', $date);
    $dateOK = true;
    if (count($tabDate) != 3) {
        $dateOK = false;
    } else {
        if (!estTableauEntiers($tabDate)) {
            $dateOK = false;
        } else {
            if (!checkdate($tabDate[1], $tabDate[0], $tabDate[2])) {
                $dateOK = false;
            }
        }
    }
    return $dateOK;
}

/**
 * Vérifie que le tableau de frais ne contient que des valeurs numériques
 *
 * @param Array $lesFrais Tableau d'entier
 *
 * @return Boolean vrai ou faux
 */
function lesQteFraisValides($lesFrais)
{
    return estTableauEntiers($lesFrais);
}


/**
 * Fonction qui retourne vrai si le tableau de frais contient des
 * quantités.
 *
 * @param array $lesFrais
 * @return boolean
 */
function existeFraisForfait($lesFrais)
{
    $existe = false;
    foreach ($lesFrais as $unFrais) {
        if ($unFrais['quantite'] <> 0) {
            $existe = true;
        }
    }
    return $existe;
}

/**
 * Vérifie la validité des trois arguments : la date, le libellé du frais
 * et le montant
 *
 * Des message d'erreurs sont ajoutés au tableau des erreurs
 *
 * @param String $dateFrais Date des frais
 * @param String $libelle   Libellé des frais
 * @param Float  $montant   Montant des frais
 *
 * @return null
 */
function valideInfosFrais($dateFrais, $libelle, $montant)
{
    if ($dateFrais == '') {
        ajouterErreur('Le champ date ne doit pas être vide');
    } else {
        if (!estDatevalide($dateFrais)) {
            ajouterErreur('Date invalide');
        } else {
            if (estDateDepassee($dateFrais)) {
                ajouterErreur(
                    "date d'enregistrement du frais dépassé, plus de 1 an"
                    );
            }
        }
    }
    if ($libelle == '') {
        ajouterErreur('Le champ description ne peut pas être vide');
    }
    if ($montant == '') {
        ajouterErreur('Le champ montant ne peut pas être vide et les nombres décimaux doivent utiliser le point et non la virgule (ex : 15.35)');
    } elseif (!is_numeric($montant)) {
        ajouterErreur('Le champ montant doit être numérique');
    }
}

/**
 * Ajoute le libellé d'une erreur au tableau des erreurs
 *
 * @param String $msg Libellé de l'erreur
 *
 * @return null
 */
function ajouterErreur($msg)
{
    if (!isset($_REQUEST['erreurs'])) {
        $_REQUEST['erreurs'] = array();
    }
    $_REQUEST['erreurs'][] = $msg;
}

/**
 * Retoune le nombre de lignes du tableau des erreurs
 *
 * @return Integer le nombre d'erreurs
 */
function nbErreurs()
{
    if (!isset($_REQUEST['erreurs'])) {
        return 0;
    } else {
        return count($_REQUEST['erreurs']);
    }
}

/**
 * - Vérifie que le php.ini gere l'échappement des
 * chaines récupérées par input
 * - Utilise la fonction d'échappement si ce n'est
 * pas le cas
 * - Retourne la chaine échappée
 * @param String $chaine : la chaine a échapper
 * @return string $chaine : chaine échappée
 */
function filtrerChainePourBD($chaine)
{
    if (!get_magic_quotes_gpc()){
        $chaine = addslashes($chaine);
    }
    return $chaine;
}

/**
 * Fonction qui transforme une date au format JJ/MM/AAAA
 * au format JJ nomDuMois AAAA
 * 
 * @param  $date
 * @return string
 */
function getDateFormatTexte($date)
{
    // récupération du format AAAAMM de la date
    $mois = getMois($date);
    // transformation du mois en texte
    $moisTexte = getMoisFormatTexte($mois);
    // construction de la date entière en format texte
    $jour =  substr($date, 0, 2);
    $dateTexte = $jour . ' ' . $moisTexte;
    return $dateTexte;
}

/**
 * Fonction qui transforme un mois du format AAAAMM au 
 * format nomDuMois AAAA
 * 
 * @param  $mois
 * @return string
 */
function getMoisFormatTexte($mois)
{
    $numAnnee = substr($mois, 0, 4);
    $numMois = substr($mois, 4, 2);
    switch ($numMois) {
        case '01' :
            $leMois = 'Janvier';
            break;
        case '02' :
            $leMois = 'Février';
            break;
        case '03' :
            $leMois = 'Mars';
            break;
        case '04' :
            $leMois = 'Avril';
            break;
        case '05' :
            $leMois = 'Mai';
            break;
        case '06' :
            $leMois = 'Juin';
            break;
        case '07' :
            $leMois = 'Juillet';
            break;
        case '08' :
            $leMois = 'Août';
            break;
        case '09' :
            $leMois = 'Septembre';
            break;
        case '10' :
            $leMois = 'Octobre';
            break;
        case '11' :
            $leMois = 'Novembre';
            break;
        case '12' :
            $leMois = 'Décembre';
            break;
    }    
    return $leMois.' '.$numAnnee;
}



