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
<?php 
if ($lesInfosFicheFrais) {
?>
<div class="row">
	<h3>Eléments forfaitisés</h3>
	<div class="col-md-4">
		<form method="post"
			action="index.php?uc=validerFiche&action=validerMajFraisForfait"
			role="form">
			<fieldset>    
    			<input type="hidden" id="idVisiteur" name="idVisiteur" 
    			value="<?php echo $visiteurASelectionner ?>">
    			<input type="hidden" id="mois" name="mois" 
    			value="<?php echo $moisASelectionner ?>">
                <?php
                foreach ($lesFraisForfait as $unFrais) {
                    $idFrais = $unFrais['idfrais'];
                    $libelle = htmlspecialchars($unFrais['libelle']);
                    $quantite = $unFrais['quantite'];
                    ?>
                    <div class="form-group">
					<label for="idFrais"><?php echo $libelle ?></label> <input
						type="text" id="idFrais" name="lesFrais[<?php echo $idFrais ?>]"
						size="10" maxlength="5" value="<?php echo $quantite ?>"
						class="form-control">
					</div>
                    <?php
                }
                ?>
                <button class="btn btn-success" type="submit">Corriger</button>
				<button class="btn btn-danger" type="reset">Réinitialiser</button>
			</fieldset>
		</form>
	</div>
</div>

<hr>
<div class="row">
	<div class="panel panel-info">
		<div class="panel-heading">Descriptif des éléments hors forfait</div>
		<table class="table table-bordered table-responsive">
			<thead>
				<tr>
					<th class="date">Date</th>
					<th class="libelle">Libellé</th>
					<th class="montant">Montant</th>
					<th class="action">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
            <?php
            foreach ($lesFraisHorsForfait as $unFraisHorsForfait) { 
                $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                $date = $unFraisHorsForfait['date'];
                $montant = $unFraisHorsForfait['montant'];
                $id = $unFraisHorsForfait['id'];
                ?>                
                <tr>
					<td> <?php echo $date ?></td>
					<td> <?php echo $libelle ?></td>
					<td><?php echo $montant ?></td>					
					<td>
    					<?php 
        					// v�rifie si le frais est d�ja refus�
        					$debut_libelle = substr($libelle, 0,6);
        					if ($debut_libelle <> 'REFUSE'){  
    					?>
        					<form 	method="post"
        							action="index.php?uc=validerFiche&action=reporterFraisHorsForfait">
            					<input type="hidden" id="idVisiteur" name="idVisiteur" 
                    			value="<?php echo $visiteurASelectionner ?>">
                    			<input type="hidden" id="mois" name="mois" 
                    			value="<?php echo $moisASelectionner ?>">
            					<button class="btn btn-success" type="submit">Reporter</button>
        					</form>
        					<form 	method="post"
        							action="index.php?uc=validerFiche&action=rejeterFraisHorsForfait">
                    			<input type="hidden" id="idFrais" name="idFrais"
                    			value="<?php echo $id ?>">
                    			<input type="hidden" id="idVisiteur" name="idVisiteur" 
                    			value="<?php echo $visiteurASelectionner ?>">
                    			<input type="hidden" id="mois" name="mois" 
                    			value="<?php echo $moisASelectionner ?>">
                    			<button class="btn btn-danger" type="submit">Rejeter</button>
        					</form>
    					<?php } ?>
					</td>					
				</tr>               
                <?php
            }
            ?>             	
            </tbody>
		</table>
	</div>
	<div class="form-group">
		<label for="nbJustificatifs">Nombre de justificatifs : </label> <input
			type="text" id="nbJustificatifs" size="10" maxlength="5"
			value="<?php echo $nbJustificatifs ?>" class="form-control">
	</div>
	<button class="btn btn-success" type="submit">Valider</button>
	<button class="btn btn-danger" type="reset">Réinitialiser</button>
</div>
<?php 
} else {
?>
<h3>Pas de fiche de frais pour ce visiteur pour ce mois.</h3>
<?php } ?>