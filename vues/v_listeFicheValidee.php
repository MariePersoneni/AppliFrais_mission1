<?php
/**
 * Vue liste des fiches de frais validées uniquement
 *
 * PHP Version 7.0.3
 *
 * @category  PPE
 * @package   GSB\vues
 * @author    PERSONENI Marie <marie.c.personeni@gmail.com>
 * @link      http://mariepersoneni.yn.fr/2019/03/22/appli-frais/
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
                        $idVisiteurFiche = $uneFiche['idVisiteur'];
                        $nomVisiteurFiche = $uneFiche['nomVisiteur'];
                        $prenomVisiteurFiche = $uneFiche['prenomVisiteur'];
                        $mois = $uneFiche['mois'];
                        $numAnneeFiche = substr($mois, 0, 4);
                        $numMoisFiche = substr($mois, -2);
                        $formatMois = $numMoisFiche . '/' . $numAnneeFiche;
                        $fiche = $mois.$idVisiteurFiche;
                        if ($fiche == $ficheASelectionner) {
                            ?>                            
                            <option selected value="<?php echo $fiche ?>">
                                <?php echo $formatMois . ' - ' . $nomVisiteurFiche . ' ' .  $prenomVisiteurFiche ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $fiche ?>">
                                <?php echo $formatMois . ' - ' . $nomVisiteurFiche . ' ' .  $prenomVisiteurFiche ?> </option>
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