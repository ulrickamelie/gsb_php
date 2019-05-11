 <div id="contenu">
      <h2>Valider fiche de frais</h2>
      <h3>Mois et Visiteur à sélectionner : </h3>
      <form action="index.php?uc=suivreFrais&action=saisirMois" method="post">
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
      
            <p>
	 
        <label for="lstVisiteur" accesskey="v">Visiteur : </label>
        <select id="lstVisiteur" name="lstVisiteur">
            <?php
			foreach ($lesVisiteurs as $unVisiteur)
			{
                                $nom = $unVisiteur['nom'];
				$prenom =  $unVisiteur['prenom'];
				?>
				<option selected value="<?php echo $nom ?>"><?php echo  $nom." ".$prenom ?> </option>
				<?php 
			}
			
			
           
		   ?>    
            
        </select>
      </p>
      
      
      
      
      </div>
      <div class="piedForm">
      <p>
        <input id="ok" type="submit" value="Valider" size="20" />
        <input id="annuler" type="reset" value="Effacer" size="20" />
      </p> 
      </div>
        
      </form>