<div id="contenu">
      <h2>fiches de frais a valider</h2>
      <h3>Mois a selectionner : </h3>
      <form action="index.php?uc=suivreFrais&action=validerMois" method="post">
      <div class="corpsForm">
         
      <p>
	 
        <label for="lstMois" accesskey="n">Mois : </label>
        <select id="lstMois" name="lstMois">
            <?php
			foreach ($lesMois as $unMois)
			{
			    $mois = $unMois['mois'];
				$numAnnee =  $unMois['numAnnee'];
				$numMois =  $unMois['numMois'];
				if($mois == $moisASelectionner){
				?>
				<option selected value="<?php echo $mois ?>"><?php echo  $numMois."/".$numAnnee ?> </option>
				<?php 
				}
				else{ ?>
				<option value="<?php echo $mois ?>"><?php echo  $numMois."/".$numAnnee ?> </option>
				<?php 
				}
			
			}
           
		   ?>    
            
        </select>
      </p>
      </div>
      <div class="piedForm">
          <?php if(empty($_POST['lstMois'] )|| empty($_POST['lstVisiteur'] )){?>
      <p>
        <input id="ok" type="submit" value="Valider" size="20" />
        <input id="annuler" type="reset" value="Effacer" size="20" />
      </p> 
          <?php } ?>
      </div>
        
      </form>