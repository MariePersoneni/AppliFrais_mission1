<?php
use SebastianBergmann\CodeCoverage\Driver\Xdebug;

/**
 * Classe d'accès aux données.
 *
 * PHP Version 7 
 * 
 *
 * @category  PPE
 * @package   GSB\Includes
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

class PdoGsb
{
    private static $serveur = 'mysql:host=localhost';
    private static $bdd = 'dbname=gsb_frais';
    private static $user = 'userGsb';
    private static $mdp = 'secret';
    private static $monPdo;
    private static $monPdoGsb = null;    
    
    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        PdoGsb::$monPdo = new PDO(
            PdoGsb::$serveur . ';' . PdoGsb::$bdd,
            PdoGsb::$user,
            PdoGsb::$mdp
            );
        PdoGsb::$monPdo->query('SET CHARACTER SET utf8');
    }
    
    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        PdoGsb::$monPdo = null;
    }
    
    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }
    
    
    /**
     * Retourne les informations d'un utilisateur
     *
     * @param String $login     Login de l'utilisateur
     * @param String $mdp       Mot de passe de l'utilisateur
     * @param String $table     Table qui contient l'enregistrement
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosUtilisateur($login, $mdp, $table)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT $table.id AS id, $table.nom AS nom, "
            . "$table.prenom AS prenom "
            . "FROM $table "
            . "WHERE $table.login = '$login' AND $table.mdp = '$mdp'"
            );
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }
    
    /**
     * Retourne les informations d'un utilisateur 
     *
     * @param String $idUtilisateur     id de l'utilisateur
     * @param String $table             Table qui contient l'enregistrement
     * 
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getNomUtilisateur($idUtilisateur, $table)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT $table.id AS id, $table.nom AS nom, "
            . "$table.prenom AS prenom "
            . "FROM $table "
            . "WHERE $table.id = '$idUtilisateur'"
            );
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }
    
    
    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return array : tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT * FROM lignefraishorsforfait "
            . "WHERE lignefraishorsforfait.idvisiteur = '$idVisiteur' "
            . "AND lignefraishorsforfait.mois = '$mois'"
            );
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }
    
    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return int : le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT fichefrais.nbjustificatifs as nb FROM fichefrais "
            . "WHERE fichefrais.idvisiteur = '$idVisiteur' "
            . "AND fichefrais.mois = '$mois'"
            );
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }
    
    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            "SELECT fraisforfait.id as idfrais, "
            . "fraisforfait.libelle as libelle, "
            . "lignefraisforfait.quantite as quantite "
            . "FROM lignefraisforfait "
            . "INNER JOIN fraisforfait "
            . "ON fraisforfait.id = lignefraisforfait.idfraisforfait "
            . "WHERE lignefraisforfait.idvisiteur = '$idVisiteur' "
            . "AND lignefraisforfait.mois = '$mois' "
            . "ORDER BY lignefraisforfait.idfraisforfait"
            );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }
    
    /**
     * Fonction qui reçoit en paramètre un tableau de frais forfait,
     * qui calcule le montant total de chaque frais et qui retourne
     * un tableau réarrangé pour l'impression.
     * 
     * @param array     $lesFrais 
     * @return array    $LesFraisForfaitCalcules 
     */
    public function getLesFraisForfaitCalcules($lesFrais, $tableFrais)
    {
        $LesFraisForfaitCalcules = array();
        foreach ($lesFrais as $unFrais){
            $idFrais = $unFrais['idfrais'];
            $libelle = $unFrais['libelle'];
            $quantite = $unFrais['quantite'];
            $montantFrais = $this->getMontantFraisForfait($idFrais, $tableFrais);
            $total = floatval($montantFrais) * floatval($quantite);
            $LesFraisForfaitCalcules[] = array(
                'libelle' => $libelle,
                'quantite' => $quantite,
                'montant' => $montantFrais,
                'total' => number_format($total, 2, ',', ' ')
            );
        }
        return $LesFraisForfaitCalcules;
    }
    
    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * kilométriques concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisKm($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            "SELECT fraiskilometrique.id as idfrais, "
            . "fraiskilometrique.libelle as libelle, "
            . "lignefraisforfait.quantite as quantite "
            . "FROM lignefraisforfait "
            . "INNER JOIN fraiskilometrique "
            . "ON fraiskilometrique.id = lignefraisforfait.idfraiskm "
            . "WHERE lignefraisforfait.idvisiteur = '$idVisiteur' "
            . "AND lignefraisforfait.mois = '$mois' "
            . "ORDER BY lignefraisforfait.idfraiskm"
            );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }
    
    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return array : un tableau associatif
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT fraisforfait.id as idfrais "
            . "FROM fraisforfait ORDER BY fraisforfait.id"
            );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }
    
    /**
     * Retourne tous les id de la table Fraiskilometrique
     * 
     * @return array : un tableau associatif
     */
    public function getLesIdFraisKm()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT fraiskilometrique.id as idfrais "
            . "FROM fraiskilometrique "
            . "ORDER BY fraiskilometrique.id"
            );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }
    
    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = PdoGSB::$monPdo->prepare(
                "UPDATE lignefraisforfait "
                . "SET lignefraisforfait.quantite = $qte "
                . "WHERE lignefraisforfait.idvisiteur = '$idVisiteur' "
                . "AND lignefraisforfait.mois = '$mois' "
                . "AND lignefraisforfait.idfraisforfait = '$unIdFrais'"
                );
            $requetePrepare->execute();
        }
    }
    
    /**
     * Met à jour la table ligneFraisForfait (frais kilometriques)
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur   ID du visiteur
     * @param String $mois         Mois sous la forme aaaamm
     * @param Array  $lesFraisKm   tableau associatif de clé idfrais
     *                             et de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisKm($idVisiteur, $mois, $lesFraisKm)
    {
        $lesCles = array_keys($lesFraisKm);
        foreach ($lesCles as $unIdFraisKm) {
            $qte = $lesFraisKm[$unIdFraisKm];
            $requetePrepare = PdoGSB::$monPdo->prepare(
                "UPDATE lignefraisforfait "
                . "SET lignefraisforfait.quantite = $qte "
                . "WHERE lignefraisforfait.idvisiteur = '$idVisiteur' "
                . "AND lignefraisforfait.mois = '$mois' "
                . "AND lignefraisforfait.idfraiskm = '$unIdFraisKm'"
                );
            $requetePrepare->execute();
        }
    }
    
    /**
     * Met a jour la BDD en ajoutant la mention
     * "REFUSE" devant le libellé du frais rejeté
     * et en passant le montant a 0
     * @param string $idFrais : ID de la ligne de frais
     */
    public function rejeterFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT lignefraishorsforfait.libelle "
            . "FROM lignefraishorsforfait "
            . "WHERE lignefraishorsforfait.id = '$idFrais'"
            );
        $requetePrepare->execute();
        $resultat = $requetePrepare->fetch();
        $libelle = 'REFUSE ' . $resultat['libelle'];
        // vérifie que le nouveau libellé ne dépasse pas la taille maximum
        // de 100 caract�res
        $longueurLibelle = strlen($libelle);
        if ($longueurLibelle > 100){
            $libelle = substr($libelle, 0, 100);
        }
        $libelle = filtrerChainePourBD($libelle);
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "UPDATE lignefraishorsforfait "
            . "SET lignefraishorsforfait.libelle = '$libelle' "
            . ", lignefraishorsforfait.montant = 0 "
            . "WHERE lignefraishorsforfait.id = '$idFrais'"
            );
        $requetePrepare->execute();
    }
    
    
    /**
     * Modifie la BDD pour reporter une ligne de frais hors
     * forfait sur la fiche du mois suivant
     * Création de la fiche du mois suivant si inexistante
     * 
     * @param string $idVisiteur : ID du visiteur
     * @param string $mois : $mois de la fiche en cours
     * @param integer $idFrais : ID de la ligne de frais hors
     * forfait
     */
    public function reporterFraisHorsForfait($idVisiteur, $mois, $idFrais)
    {        
        // récupère le mois suivant
        $moisSuivant = getMoisSuivant($mois);
        // vérifie qu'une fiche pour le mois suivant existe
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT fichefrais.idVisiteur "
            . "FROM fichefrais "
            . "WHERE fichefrais.idVisiteur = '$idVisiteur' "
            . "AND fichefrais.mois = '$moisSuivant'"
            );
        $requetePrepare->execute();
        $resultat = $requetePrepare->fetch();
        if (!is_array($resultat)){
            // créé la fiche Mois+1            
            $this->creeNouvellesLignesFrais($idVisiteur, $moisSuivant);
        }         
        // Reporte la ligne au Mois+1
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "UPDATE lignefraishorsforfait "
            . "SET lignefraishorsforfait.mois = '$moisSuivant' "
            . "WHERE lignefraishorsforfait.id = '$idFrais'"
            );       
        $requetePrepare->execute();        
    }
        
    
    /**
     * Met à jour le nombre de justificatifs de la table fichefrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "UPDATE fichefrais "
            . "SET nbjustificatifs = $nbJustificatifs "
            . "WHERE fichefrais.idvisiteur = '$idVisiteur' "
            . "AND fichefrais.mois = '$mois'"
            );
        $requetePrepare->execute();
    }
    
    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return boolean : vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT fichefrais.mois FROM fichefrais "
            . "WHERE fichefrais.mois = '$mois' "
            . "AND fichefrais.idvisiteur = '$idVisiteur'"
            );
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }
    
    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return String : le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT MAX(mois) as dernierMois "
            . "FROM fichefrais "
            . "WHERE fichefrais.idvisiteur = '$idVisiteur'"
            );
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }
    
    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        // cloture de la fiche de frais précédente
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        // création d'une nouvelle fiche pour le mois en cours
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,"
            . "montantvalide,datemodif,idetat) "
            . "VALUES ('$idVisiteur','$mois',0,0,now(),'CR')"
            );
        $requetePrepare->execute();
        // création des lignes de frais forfait
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $idFrais = $unIdFrais['idfrais'];
            $requetePrepare = PdoGsb::$monPdo->prepare(
                "INSERT INTO lignefraisforfait (idvisiteur,mois,"
                . "idfraisforfait,quantite) "
                . "VALUES('$idVisiteur', '$mois', '$idFrais', 0)"
                );
            $requetePrepare->execute();        
        }
        // création des lignes de frais kilométrique
        $lesIdFraisKm = $this->getLesIdFraisKm();
        foreach ($lesIdFraisKm as $unIdFraisKm){
            $idFraisKm = $unIdFraisKm['idfrais'];
            $requetePrepare = PdoGsb::$monPdo->prepare(
                "INSERT INTO lignefraisforfait (idvisiteur,mois,"
                . "idfraiskm,quantite) "
                . "VALUES('$idVisiteur', '$mois', '$idFraisKm', 0)"
                );
            $requetePrepare->execute();  
        }
    }
    
    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait(
        $idVisiteur,
        $mois,
        $libelle,
        $date,
        $montant
        ) {
            $dateFr = dateFrancaisVersAnglais($date);
            $requetePrepare = PdoGSB::$monPdo->prepare(
                "INSERT INTO lignefraishorsforfait "
                . "VALUES (null, '$idVisiteur','$mois', '$libelle', '$dateFr',"
                . "$montant) "
                );
            $requetePrepare->execute();
    }
    
    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            "DELETE FROM lignefraishorsforfait "
            . "WHERE lignefraishorsforfait.id = '$idFrais'"
            );
        $requetePrepare->execute();
    }
    
    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return array :  un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            "SELECT fichefrais.mois AS mois FROM fichefrais "
            . "WHERE fichefrais.idvisiteur = '$idVisiteur' "
            . "ORDER BY fichefrais.mois desc"
            );
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }
    
    
    /**
     * Récupère tous les visiteurs de la base de données
     * @return array : un tableau associatif qui contien l'id, le nom et prénom
     * de chaque visiteur
     */
    public function getLesVisiteurs()
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            "SELECT visiteur.id AS id, visiteur.nom AS nom, "
            . "visiteur.prenom AS prenom "
            . "FROM visiteur "
            . "ORDER BY visiteur.nom ASC"
            );
        $requetePrepare->execute();
        $lesVisiteurs = array();
        while($laLigne = $requetePrepare->fetch()) {
            $idVisiteur = $laLigne['id'];
            $nomVisiteur = $laLigne['nom'];
            $prenomVisiteur = $laLigne['prenom'];
            $lesVisiteurs[] = array(
                'id' => $idVisiteur,
                'nom' => $nomVisiteur,
                'prenom' => $prenomVisiteur
            );
        }
        return $lesVisiteurs;
    }
    
    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return array : un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            "SELECT fichefrais.idetat as idEtat, "
            . "fichefrais.datemodif as dateModif,"
            . "fichefrais.nbjustificatifs as nbJustificatifs, "
            . "fichefrais.montantvalide as montantValide, "
            . "etat.libelle as libEtat, "
            . "fichefrais.auteurvalidation as comptable, "
            . "fichefrais.datevalidation as datevalidation "
            . "FROM fichefrais "
            . "INNER JOIN etat ON fichefrais.idetat = etat.id "
            . "WHERE fichefrais.idvisiteur = '$idVisiteur' "
            . "AND fichefrais.mois = '$mois'"
            );
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }
    
    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat)
    {
        if($etat == 'VA'){
            $idComptable = $_SESSION['idUtilisateur'];
            $requetePrepare = PdoGSB::$monPdo->prepare(
                "UPDATE fichefrais "
                . "SET idetat = '$etat', datemodif = now(), "
                . "auteurvalidation = '$idComptable', datevalidation = now() "
                . "WHERE fichefrais.idvisiteur = '$idVisiteur' "
                . "AND fichefrais.mois = '$mois'"
                );
        } else {
            $requetePrepare = PdoGSB::$monPdo->prepare(
                "UPDATE fichefrais "
                . "SET idetat = '$etat', datemodif = now() "
                . "WHERE fichefrais.idvisiteur = '$idVisiteur' "
                . "AND fichefrais.mois = '$mois'"
                );
        }
        $requetePrepare->execute();
    }
    
    /**
     * Procédure qui met a jour dans la base de données le
     * montant validé d'une fiche de frais
     * @param  $idVisiteur  = id du visiteur
     * @param  $mois        = mois de la fiche de frais
     * @param  $montant     = montant total validé
     */
    public function majMontantFicheFrais($idVisiteur, $mois, $montant)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            "UPDATE fichefrais "
            . "SET montantvalide = $montant, datemodif = now() "
            . "WHERE fichefrais.idvisiteur = '$idVisiteur' "
            . "AND fichefrais.mois = '$mois'"
            );
        $requetePrepare->execute();
    }
    
    public function calculeMontantTotalFiche(
        $lesFraisForfait, $lesFraisKm, $lesFraisHorsForfait)
    {        
        $montant = 0;
        // calcul des frais forfait
        foreach ($lesFraisForfait as $unFraisForfait){
            $qteFrais = intval($unFraisForfait['quantite']);
            if ($qteFrais <> 0){
                $idFrais = $unFraisForfait['idfrais'];
                $montantFrais = $this->getMontantFraisForfait($idFrais, 'fraisforfait');
                $montant += intval($montantFrais) * $qteFrais;
            }
        }
        // calcul des frais kilométriques
        foreach ($lesFraisKm as $unFraisKm){
            $qteFrais = intval($unFraisKm['quantite']);
            if ($qteFrais <> 0){
                $idFraisKm = $unFraisKm['idfrais'];
                $montantFrais = $this->getMontantFraisForfait($idFraisKm, 'fraiskilometrique');
                $montant += floatval($montantFrais) * $qteFrais;
            }
        }
        // calcul des frais hors forfait
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait){
            $libelle = ($unFraisHorsForfait['libelle']);
            $debut_libelle = substr($libelle, 0,6);
            if ($debut_libelle <> 'REFUSE'){
                $montantFrais = intval($unFraisHorsForfait['montant']);
                $montant += $montantFrais;
            }
        }
        return $montant;
    }
    
    /**
     * Fonction qui retourne le montant correspondant
     * au frais forfait passé en paramètre
     * @param $idFrais = id du frais forfait
     * @return String chaine qui contient le montant correspondant
     */
    public function getMontantFraisForfait($idFrais, $table)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT montant as mnt FROM $table "
            . "WHERE id = '$idFrais'"
            );
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['mnt'];
    }
    
    /**
     * Fonction qui met  jour la base de donnée et qui
     * cloture toutes les fiches de frais non cloturées
     * du mois précédent
     */
    public function clotureFichesMoisPrecedent()
    {
        $moisActuel = date('Ym');
        $moisPrecedent = getMoisPrecedent($moisActuel);        
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "UPDATE fichefrais "
            . "SET idetat = 'CL', datemodif = now() "
            . "WHERE mois = '$moisPrecedent' "
            . "AND idetat = 'CR'"
            );
        $requetePrepare->execute();
    }
    
    
    /** 
     * Récupère et retourne toutes les fiches qui 
     * ont été validées et qui sont donc a l'état :
     * - validé - VA
     * - mise en paiement - MP
     * - remboursee - RB
     * 
     * @return array : tableau associatif des fiches de frais
     */
    public function getLesFichesValidees()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT fichefrais.idvisiteur as idVisiteur, "
            . "fichefrais.mois as mois, "
            . "fichefrais.nbjustificatifs as nbJustificatifs, "
            . "fichefrais.montantvalide as montantValide, "
            . "visiteur.nom as nomVisiteur,  "
            . "visiteur.prenom as prenomVisiteur "
            . "FROM fichefrais INNER JOIN visiteur "
            . "ON fichefrais.idvisiteur = visiteur.id "
            . "WHERE fichefrais.idetat <> 'CL' "
            . "AND fichefrais.idetat <> 'CR' "
            . "ORDER BY fichefrais.mois DESC"
            );
        $requetePrepare->execute();
        $lesFichesValidees = array();
        while($laLigne = $requetePrepare->fetch()) {
            $idVisiteur = $laLigne['idVisiteur'];
            $nomVisiteur = $laLigne['nomVisiteur'];
            $prenomVisiteur = $laLigne['prenomVisiteur'];
            $mois = $laLigne['mois'];
            $montantValide = $laLigne['montantValide'];
            $nbJustificatifs = $laLigne['nbJustificatifs'];
            $lesFichesValidees[] = array(
                'idVisiteur'        => $idVisiteur,
                'nomVisiteur'       => $nomVisiteur,
                'prenomVisiteur'    => $prenomVisiteur,
                'mois'              => $mois,
                'montantValide'     => $montantValide,
                'nbJustificatifs'   => $nbJustificatifs,
            );
        }
        return $lesFichesValidees;
    }
        
     
    /**
     * Fonction utilisée une seule fois suite au changement de 
     * structure de la base de données pour la gestion des 
     * frais kilométrique.
     * La fonction parcours les fiches de frais et insert
     * des lignes de frais kilométrique la ou il en manque.
     * Peut être réutilisée si ajout de frais kilométrique dans 
     * la base de données.
     */
    public function insertFraisKmPrecedentesFiches()
    {
        //récupération de toutes les fiches
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT idvisiteur as idVisiteur, "
            . "mois as mois FROM fichefrais"
            );
        $requetePrepare->execute();
        $lesFiches = array();
        while($laLigne = $requetePrepare->fetch()) {
            $idVisiteur = $laLigne['idVisiteur'];            
            $mois = $laLigne['mois'];            
            $lesFiches[] = array(
                'idVisiteur'        => $idVisiteur,                
                'mois'              => $mois,
            );
        }
        // récupération des id frais km
        $lesIdFraisKm = $this->getLesIdFraisKm(); 
        // parcours des fiches
            foreach ($lesFiches as $uneFiche){
                //récupère les lignes de frais km
                $levisiteur = $uneFiche['idVisiteur'];
                $lemois = $uneFiche['mois'];
                $lesFraisKm = $this->getLesFraisKm($levisiteur, $lemois);
                // parcours des ID de frais km
                foreach ($lesIdFraisKm as $unIdFraisKm){
                    $idFraisKm = $unIdFraisKm['idfrais'];
                    $trouve = false;
                    foreach ($lesFraisKm as $unFraisKm){
                        if ($idFraisKm == $unFraisKm['idfrais']){
                            $trouve = true;
                        }
                    }
                    if (!$trouve){
                        // insertion dans la table
                        $requetePrepare = PdoGsb::$monPdo->prepare(
                            "INSERT INTO lignefraisforfait VALUES "
                            . "('$levisiteur', '$lemois', NULL , '$idFraisKm', 0, '')"
                            );
                        $requetePrepare->execute();
                    }
                }
            }
    }    
}
