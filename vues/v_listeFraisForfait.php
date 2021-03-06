<?php
/**
 * Vue Liste des frais au forfait
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB\vues
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
                        <input type="text" id="idFrais" 
                               name="lesFrais[<?php echo $idFrais ?>]"
                               size="10" 
                               value="<?php echo $quantite ?>" 
                               class="form-control">						
                    </div>
                    <?php
                }
                // Gestion des frais kilométrique
                ?>
                <div class="row">
                    <div class="panel panel-info">
                        <div class="panel-heading">Frais kilométriques</div>
                        <table class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th class="date">Type de véhicule</th>
                                    <th class="libelle">nombre de kilomètres</th>
                                </tr>
                            </thead>  
                            <tbody>
                            <?php
                            foreach ($lesFraisKilometriques as $unFraisKm) {
                                $libelle = htmlspecialchars($unFraisKm['libelle']);
                                $quantite = $unFraisKm['quantite'];
                                $idFraisKm = $unFraisKm['idfrais']; ?>           
                                <tr>
                                    <td> <?php echo $libelle ?></td>
                                    <td> <input type="text" id="idFraisKm" 
                                                name="lesFraisKm[<?php echo $idFraisKm ?>]"
                                                size="10" 
                                                value="<?php echo $quantite ?>" 
                                                class="form-control"> </td>                                    
                                </tr>                                
                                <?php
                            }
                            ?>
                            </tbody>  
                        </table>
                    </div>
                </div>
                <button class="btn btn-success" type="submit">Mettre à jour</button>
                <button class="btn btn-danger" type="reset">Effacer</button>
            </fieldset>
        </form>
    </div>
</div>

