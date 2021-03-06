<?php
/**
 * Vue Validation des fiches de frais
 *
 * PHP Version 7.0.3
 *
 * @category  PPE
 * @package   GSB\vues
 * @author    PERSONENI Marie <marie.c.personeni@gmail.com>
 * @link      http://mariepersoneni.yn.fr/2019/03/22/appli-frais/
 */

?>
<?php 
// Si une fiche de frais existe pour ce mois et ce visiteur, on affiche les
// infos sinon, on affiche "pas de fiche"
if ($lesInfosFicheFrais) {
?>   
<div class="row">
    <h2>Etat de la fiche : <?php echo $libEtat ?></h2>
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
    					<label for="idFrais"><?php echo $libelle ?></label> 
    					<input type="text" id="idFrais" 
                               name="lesFrais[<?php echo $idFrais ?>]"
                               size="10" 
                               value="<?php echo $quantite ?>" 
                               class="form-control">
                       </div>
						<?php 
                        } 
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
                                    foreach ($lesFraisKm as $unFraisKm) {
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
                <?php
                if ($etatFiche == 'CL') {
                ?>
                    <button class="btn btn-success" type="submit">Corriger</button>
    				<button class="btn btn-danger" type="reset">Réinitialiser</button>    				
				<?php 
                } 
                ?>
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
					<td><?php echo $date ?></td>
					<td><?php echo $libelle ?></td>
					<td><?php echo $montant ?></td>					
					<td><?php 
				       // vérifie si le frais est déja refusé
    					$debut_libelle = substr($libelle, 0,6);
    					if ($debut_libelle <> 'REFUSE' && $etatFiche == 'CL'){
					   ?>
        					<form 	method="post"
        							action="index.php?uc=validerFiche&action=reporterFraisHorsForfait">
            					<input type="hidden" id="idVisiteur" name="idVisiteur" 
                    			value="<?php echo $visiteurASelectionner ?>">
                    			<input type="hidden" id="mois" name="mois" 
                    			value="<?php echo $moisASelectionner ?>">
                    			<input type="hidden" id="idFrais" name="idFrais"
                				value="<?php echo $id ?>">
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
    					<?php 
    					} 
    					?>
					</td>					
				</tr>               
                <?php
                }
                ?>             	
            </tbody>
		</table>
	</div>
</div>
<div class="form-group">
	<form 	method="post"
			action="index.php?uc=validerFiche&action=majNbJustificatifs">
		<input 	type="hidden" id="idVisiteur" name="idVisiteur" 
				value="<?php echo $visiteurASelectionner ?>">
		<input 	type="hidden" id="mois" name="mois" 
				value="<?php echo $moisASelectionner ?>">
		<label for="nbJustificatifs">Nombre de justificatifs : </label> 
		<input
			type="text" id="nbJustificatifs" 
			size="10" maxlength="5" name ="nbJustificatifs"
			value="<?php echo $nbJustificatifs ?>" class="form-control">
		<?php 
        if ($etatFiche == 'CL'){
		?>
			<br>
			<button class="btn btn-success" type="submit">Corriger</button>
        	<button class="btn btn-danger" type="reset">Réinitialiser</button>    				
		<?php 
        }
        ?>			
	</form>		
</div>
<form 	method="post"
		action="index.php?uc=validerFiche&action=validerFicheFrais">
	<input type="hidden" id="idVisiteur" name="idVisiteur" 
	value="<?php echo $visiteurASelectionner ?>">
	<input type="hidden" id="mois" name="mois" 
	value="<?php echo $moisASelectionner ?>">
	<?php 
    if ($etatFiche == 'CL'){
	?>
		<br>
    	<button class="btn btn-success" type="submit">Valider</button>
    	<button class="btn btn-danger" type="reset">Réinitialiser</button>
	<?php 
    }
    ?>
</form>
<hr>
<?php 
} else {
    ?>
    <h3>Pas de fiche de frais pour ce visiteur pour ce mois.</h3>
    <?php 
}
?>