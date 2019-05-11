<div id="contenu">
<h2>visiteurs sélectionner : </h2>
    <form action="index.php?uc=suivreFrais&action=validerMois" method="post">
    <div class="corpsForm">         
    <p>	 
        <label for="lstVisiteurs" accesskey="n">Visiteurs : </label>
        <select id="lstVisiteur" name="lstVisiteur">
      
            <?php
                foreach ($lesVisiteurs as $unVisiteur)
                {
                    $id = $unVisiteur['id'];
                    $nom =  $unVisiteur['nom'];
                    $prenom =  $unVisiteur['prenom'];
                    if($id == $idASelectionner){
                    ?>
                    <option selected value="<?php echo $id?>"><?php echo  $nom." ".$prenom ?> </option>
                    <?php 
                    }
                    else{ ?>
                    <option value="<?php echo $id ?>"><?php echo  $nom." ".$prenom ?> </option>
                    <?php 
                    }
			
                }
           
            ?>    
            
        </select>
    </p>
    </div>
    <div class="piedForm">
        <?php if(empty($_POST['lstVisiteur'] )){?>
    <p>          
        <input type="hidden" name="mois" value="<?php echo $mois ?>" />
        <input id="ok" type="submit" value="Valider" size="20" />
        <input id="annuler" type="reset" value="Effacer" size="20" />
    </p> 
    <?php } ?>
    </div>
        
    </form>
