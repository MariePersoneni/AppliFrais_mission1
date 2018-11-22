<?php
/**
 * Vue Liste des mois
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
?>
<h2>Suivre le paiement de la fiche de frais</h2>
<div class="row">
    <div class="col-md-4">
        <form action="index.php?uc=suivrePaiement&action=voirFicheFrais" 
              method="post" role="form">
            <div class="form-group"> 
            	<label for="lstFichesValidees" accesskey="n">Choisir une fiche : </label>         
                <select id="lstFichesValidees" name="lstFichesValidees" class="form-control">
                    <?php
                    foreach ($lesFichesValidees as $uneFiche) {
                        $idVisiteur = $uneFiche['idVisiteur'];
                        $nomVisiteur = $uneFiche['nomVisiteur'];
                        $prenomVisiteur = $uneFiche['prenomVisiteur'];
                        $mois = $uneFiche['mois'];
                        $numAnnee = substr($mois, 0, 4);
                        $numMois = substr($mois, -2);
                        $formatMois = $numMois . '/' . $numAnnee;
                        $fiche = $mois.$idVisiteur;
                        if ($fiche == $ficheASelectionner) {
                            ?>
                            <option selected value="<?php echo $fiche ?>">
                                <?php echo $formatMois . ' - ' . $nomVisiteur . ' ' .  $prenomVisiteur ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $fiche ?>">
                                <?php echo $formatMois . ' - ' . $nomVisiteur . ' ' .  $prenomVisiteur ?> </option>
                            <?php
                        }
                    }
                    ?>
                    </select>
            </div>
            <input id="ok" type="submit" value="Valider" class="btn btn-success" 
                   role="button">
            <input id="annuler" type="reset" value="Effacer" class="btn btn-danger" 
                   role="button">
        </form>
    </div>
</div>