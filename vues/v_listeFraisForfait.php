<?php
/**
 * Vue Liste des frais au forfait
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
<div class="row">    
    <h2>Renseigner ma fiche de frais du mois 
        <?php echo $numMois . '-' . $numAnnee ?>
    </h2>
    <h3>Eléments forfaitisés</h3>
    <div class="col-md-4">
        <form method="post" 
              action="index.php?uc=gererFrais&action=validerMajFraisForfait" 
              role="form">
            <fieldset>       
                <?php
                foreach ($lesFraisForfait as $unFrais) {
                    $idFrais = $unFrais['idfrais'];
                    $libelle = htmlspecialchars($unFrais['libelle']);
                    $quantite = $unFrais['quantite']; ?>
                    <div class="form-group">
                        <label for="idFrais"><?php echo $libelle ?></label>
                        <?php 
                        if ($idFrais == 'KM') {
                        ?>
                        <br>
                        <label for="lstTypeehicule" class="sous-label">Puissance véhicule</label>
                        <select type="text" id="lstTypeVehicule" name="lstTypeVehicule"
                               class="form-control" onchange="calculMontantKM()"> 
                               <option selected value = "0.52">4CV Diesel</option>
                               <option value = "0.58">5/6CV Diesel</option>
                               <option value = "0.62">4CV Essence</option>
                               <option value = "0.67">5/6CV Essence</option>
                        </select>
                        <label for="qteKM" class="sous-label">Kilomètres parcourus</label>
                        <input type="text" id="qteKM" name="qteKM"
                               size="10" maxlength="5" 
                               value="<?php echo $quantite ?>" 
                               class="form-control" onchange="calculMontantKM()">
                       <label for="montantKM" class="sous-label">Montant pris en charge</label>
                       <input type="text" id="montantKM" name="montantKM"
                               size="10" maxlength="5"  
                               class="form-control">
                       
						<?php 
                        } else {
                       ?>
                       <input type="text" id="idFrais" 
                               name="lesFrais[<?php echo $idFrais ?>]"
                               size="10" 
                               value="<?php echo $quantite ?>" 
                               class="form-control">
						<?php 
                        } 
                        ?>
                    </div>
                    <?php
                }
                ?>
                <button class="btn btn-success" type="submit">Ajouter</button>
                <button class="btn btn-danger" type="reset">Effacer</button>
            </fieldset>
        </form>
    </div>
</div>
<?php 
print("<SCRIPT language=javascript>");
print(
    "function calculMontantKM() {
        var coeffVoiture = document.getElementById(\"lstTypeVehicule\").value;
        var nbKM = document.getElementById(\"qteKM\").value;
        var montantKM = Math.round(coeffVoiture * nbKM);
        document.getElementById(\"montantKM\").value = montantKM + \" euros\";
    }"
    );
print("</script>");
?>

