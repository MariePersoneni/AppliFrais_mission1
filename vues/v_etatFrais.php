<?php
/**
 * Vue État de Frais
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
<hr>
<div class="panel panel-primary">
    <div class="panel-heading">
    	<form 	method="post" target="_blank"
            	action="index.php?uc=etatFrais&action=imprimerFiche">
    		<div>Fiche de frais du mois 
       		 <?php echo $numMois . '-' . $numAnnee ?> :              	
        		<button id="icon-print" type="submit">
        			<span class="glyphicon glyphicon-print"></span>
        		</button>
        		<input 	type="hidden" id="hdMois" name="hdMois" 
        				value="<?php echo $leMois ?>">
    		</form>
        </div>
    </div>
    <div class="panel-body">
        <strong><u>Etat :</u></strong> <?php echo $libEtat ?>
        depuis le <?php echo $dateModif ?> <br> 
        <strong><u>Montant validé :</u></strong> <?php echo $montantValide ?>
    </div>    
</div>
<div class="panel panel-info">
    <div class="panel-heading">Eléments forfaitisés</div>
    <table class="table table-bordered table-responsive">
        <tr>
            <?php
            foreach ($lesFraisForfait as $unFraisForfait) {
                $libelle = $unFraisForfait['libelle']; ?>
                <th> <?php echo htmlspecialchars($libelle) ?></th>
                <?php
            }
            ?>
        </tr>
        <tr>
            <?php
            foreach ($lesFraisForfait as $unFraisForfait) {
                $quantite = $unFraisForfait['quantite']; ?>
                <td class="qteForfait"><?php echo $quantite ?> </td>
                <?php
            }
            ?>
        </tr>
    </table>
</div>
<div class="panel panel-info">
    <div class="panel-heading">Frais kilométriques</div>
    <table class="table table-bordered table-responsive">
        <tr>
            <th class="libelle">Type de véhicule</th>
            <th class='montant'>Nombre de kilomètres</th>                
        </tr>
        <?php
        foreach ($lesFraisKm as $unFraisKm) {
            $libelle = htmlspecialchars($unFraisKm['libelle']);
            $quantite = $unFraisKm['quantite']; ?>
            <tr>
                <td><?php echo $libelle ?></td>
                <td><?php echo $quantite ?></td>
            </tr>
        <?php
        }
        ?>
    </table>
</div>
<div class="panel panel-info">
    <div class="panel-heading">Descriptif des éléments hors forfait - 
        <?php echo $nbJustificatifs ?> justificatifs reçus</div>
    <table class="table table-bordered table-responsive">
        <tr>
            <th class="date">Date</th>
            <th class="libelle">Libellé</th>
            <th class='montant'>Montant</th>                
        </tr>
        <?php
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
            $date = $unFraisHorsForfait['date'];
            $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
            $montant = $unFraisHorsForfait['montant']; ?>
            <tr>
                <td><?php echo $date ?></td>
                <td><?php echo $libelle ?></td>
                <td><?php echo $montant ?></td>
            </tr>
        <?php
        }
        ?>
    </table>
</div>
<?php 
if ($_SESSION['profil'] == 'comptable') {
    $fiche = $moisASelectionner.$visiteurASelectionner;
    ?>
    <div class="row">
    	<div class="col-md-4">
            <?php 
            if ($etatFiche == 'VA'){
            ?>       
            	<form 	method="post"
            			action="index.php?uc=suivrePaiement&action=MAJfiche&bouton=MP">
            		<input type="hidden" id="hdFiche" name="hdFiche" 
            		value="<?php echo $fiche ?>">	
            		<button class="btn btn-success" type="submit">
            			Mettre en paiement
        			</button>
            	</form>
            <?php 
            } elseif ($etatFiche == 'MP') {
            ?>
            	<form 	method="post"
            			action="index.php?uc=suivrePaiement&action=MAJfiche&bouton=RB">
            		<input type="hidden" id="hdFiche" name="hdFiche" 
            		value="<?php echo $fiche ?>">		
            		<button class="btn btn-success" type="submit">Payée</button>
            	</form>
        	<?php 
            }
            ?>
        </div>
    </div>
    <?php 
}
?>
<hr>
