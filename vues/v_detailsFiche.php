
<h3>Fiche de frais du mois <?php echo $numMois."-".$numAnnee?> : 
    </h3>
<form method="POST" action="index.php?uc=suivreFrais&action=validerFiche">
        <input type="hidden" name="moisVA" value="<?php echo $leMois?>">
        <input type="hidden" name ="idVisiteurVA" value="<?php echo $idVisiteur?>">
        <input type="submit" value="valider">
</form>    
    <div class="encadre">
    <p>
        Etat : <?php echo $libEtat?> depuis le <?php echo $dateModif?> <br> Montant totale : <?php echo $total?>
    
                     
    </p>
  	<table class="listeLegere">
  	   <caption>Eléments forfaitisés </caption>
        <tr>
         <?php
         foreach ( $lesFraisForfait as $unFraisForfait ) 
		 {
			$libelle = $unFraisForfait['libelle'];
		?>	
			<th> <?php echo $libelle?></th>
		 <?php
        }
		?>
		</tr>
        <tr>
        <?php
          foreach (  $lesFraisForfait as $unFraisForfait  ) 
		  {
				$quantite = $unFraisForfait['quantite'];
                                
		?>
                <td class="qteForfait"><?php echo $quantite?> </td>
		 <?php
          }
		?>
		</tr>
    </table>
  	<table class="listeLegere">
  	   <caption>Descriptif des éléments hors forfait -<?php echo $nbJustificatifs ?> justificatifs reçus -
       </caption>
             <tr>
                <th class="date">Date</th>
                <th class="libelle">Libellé</th>
                <th class='montant'>Montant</th>                
             </tr>
        <?php      
          foreach ( $lesFraisHorsForfait as $unFraisHorsForfait ) 
		  {     $idHF=$unFraisHorsForfait['id'];
			$date = $unFraisHorsForfait['date'];
			$libelle = $unFraisHorsForfait['libelle'];
                        if($pdo->estRefuse($idHF)){
                            $libelle=$libelle.":refuse";
                        }
			$montant = $unFraisHorsForfait['montant'];
                        
		
                        ?>
             <tr>
                <td><?php echo $date ?></td>
                <td><?php echo $libelle ?></td>
                <td><?php echo $montant ?></td>
                <td><a href="index.php?uc=suivreFrais&action=validerMois&idRP=<?php echo $idHF ?>">reporter</a></td>
                <td><a href="index.php?uc=suivreFrais&action=validerMois&idRF=<?php echo $idHF ?>">refuser</a></td>
             </tr>
        <?php 
          }
		?>
    </table>
  </div>
  </div>
 













