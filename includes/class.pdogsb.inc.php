<?php
/**
 * Classe d'accès aux données.
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
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
     * Enregistre le profil dans la variable $profil
     *
     * @param String $login     Login de l'utilisateur
     * @param String $mdp       Mot de passe de l'utilisateur
     * @param String $profil    profil de l'utilisateur (comptable ou visiteur)
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosUtilisateur($login, $mdp, $table)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT {$table}.id AS id, {$table}.nom AS nom, "
            . "{$table}.prenom AS prenom "
            . "FROM {$table} "
            . "WHERE {$table}.login = :unLogin AND {$table}.mdp = :unMdp"
            );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
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
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unMois'
            );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
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
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
            );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
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
            'SELECT fraisforfait.id as idfrais, '
            . 'fraisforfait.libelle as libelle, '
            . 'lignefraisforfait.quantite as quantite '
            . 'FROM lignefraisforfait '
            . 'INNER JOIN fraisforfait '
            . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unMois '
            . 'ORDER BY lignefraisforfait.idfraisforfait'
            );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }
    
    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fraisforfait.id as idfrais '
            . 'FROM fraisforfait ORDER BY fraisforfait.id'
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
                'UPDATE lignefraisforfait '
                . 'SET lignefraisforfait.quantite = :uneQte '
                . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'AND lignefraisforfait.idfraisforfait = :idFrais'
                );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
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
            'SELECT lignefraishorsforfait.libelle '
            . 'FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :idFrais'
            );
        $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_INT);
        $requetePrepare->execute();
        $resultat = $requetePrepare->fetch();
        $libelle = 'REFUSE ' . $resultat['libelle'];
        // vérifie que le nouveau libellé ne dépasse pas la taille max de 100c
        $longueurLibelle = strlen($libelle);
        if ($longueurLibelle > 100){
            $libelle = substr($libelle, 0, 100);
        }
        $libelle = filtrerChainePourBD($libelle);
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET lignefraishorsforfait.libelle = :libelle '
            . ', lignefraishorsforfait.montant = 0 '
            . 'WHERE lignefraishorsforfait.id = :idFrais'
            );
        $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_INT);
        $requetePrepare->bindParam(':libelle', $libelle, PDO::PARAM_STR);
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
            'SELECT fichefrais.idVisiteur '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.idVisiteur = :idVisiteur '
            . 'AND fichefrais.mois = :moisSuivant'
            );
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':moisSuivant', $moisSuivant, PDO::PARAM_STR);
        $requetePrepare->execute();
        $resultat = $requetePrepare->fetch();
        if (!is_array($resultat)){
            // créé la fiche Mois+1            
            $dateJ = date('Y-m-d');
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO fichefrais VALUES ( '
                . ':idVisiteur, :moisSuivant, 0, 0, :dateJ, \'CR\' )'
                );
            $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':moisSuivant', $moisSuivant, PDO::PARAM_STR);
            $requetePrepare->bindParam(':dateJ', $dateJ, PDO::PARAM_STR);
            $requetePrepare->execute();
        }         
        // Reporte la ligne au Mois+1
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET lignefraishorsforfait.mois = :moisSuivant '
            . 'WHERE lignefraishorsforfait.id = :idFrais'
            );
        $requetePrepare->bindParam(':moisSuivant', $moisSuivant, PDO::PARAM_STR);
        $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_INT);        
        $requetePrepare->execute();
        
    }
        
    
    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
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
        $requetePrepare = PdoGB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET nbjustificatifs = :unNbJustificatifs '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
            );
        $requetePrepare->bindParam(
            ':unNbJustificatifs',
            $nbJustificatifs,
            PDO::PARAM_INT
            );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.mois FROM fichefrais '
            . 'WHERE fichefrais.mois = :unMois '
            . 'AND fichefrais.idvisiteur = :unIdVisiteur'
            );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
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
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
            );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
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
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,'
            . 'montantvalide,datemodif,idetat) '
            . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
            );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                . 'idfraisforfait,quantite) '
                . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
                );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':idFrais',
                $unIdFrais['idfrais'],
                PDO::PARAM_STR
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
                'INSERT INTO lignefraishorsforfait '
                . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
                . ':unMontant) '
                );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
            $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
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
            'DELETE FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdFrais'
            );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'ORDER BY fichefrais.mois desc'
            );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
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
     * @return un tableau associatif qui contien l'id, le nom et prénom
     * de chaque visiteur
     */
    public function getLesVisiteurs()
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT visiteur.id AS id, visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom '
            . 'FROM visiteur '
            . 'ORDER BY visiteur.nom ASC'
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
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
            . 'fichefrais.datemodif as dateModif,'
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'etat.libelle as libEtat '
            . 'FROM fichefrais '
            . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
            );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
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
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE ficheFrais '
            . 'SET idetat = :unEtat, datemodif = now() '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
            );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
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
            'UPDATE ficheFrais '
            . 'SET montantvalide = :montant, datemodif = now() '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
            );
        $requetePrepare->bindParam(':montant', $montant);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Fonction qui retourne le montant correspondant
     * au frais forfait passé en paramètre
     * @param $idFrais = id du frais forfait
     * @return chaine qui contient le montant correspondant
     */
    public function getMontantFraisForfait($idFrais)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT montant as mnt FROM fraisforfait '
            . 'WHERE id = :idFrais'
            );
        $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_STR);
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
            'UPDATE fichefrais '
            . 'SET idetat = \'CL\', datemodif = now() '
            . 'WHERE mois = :moisPrecedent '
            . 'AND idetat = \'CR\''
            );
        $requetePrepare->bindParam(':moisPrecedent', $moisPrecedent, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    
    /** 
     * Récupère et retourne toutes les fiches qui 
     * ont été validées et qui sont donc a l'état :
     * - validé - VA
     * - mise en paiement - MP
     * - remboursee - RB
     * 
     * @return tableau associatif des fiches de frais
     */
    public function getLesFichesValidees()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.idvisiteur as idVisiteur, '
            . 'fichefrais.mois as mois, '
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'visiteur.nom as nomVisiteur,  '
            . 'visiteur.prenom as prenomVisiteur '
            . 'FROM fichefrais INNER JOIN visiteur '
            . 'ON fichefrais.idvisiteur = visiteur.id '
            . 'WHERE fichefrais.idetat <> \'CL\' '
            . 'AND fichefrais.idetat <> \'CR\' '
            . 'ORDER BY fichefrais.mois DESC'
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
    
}
