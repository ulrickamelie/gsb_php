    <!-- Division pour le sommaire -->
    <div id="menuGauche">
     <div id="infosUtil">
    
        <h2>
    
</h2>
    
      </div>  
        <ul id="menuList">
			<li >
				  Comptable :<br>
				<?php echo $_SESSION['prenom']."  ".$_SESSION['nom']  ?>
			</li>
          <li class="smenu">
              <a href="index.php?uc=voirLesFiches&action=voirFiches" title="voir  les fiches ">Voir les fiches de frais</a>
           </li>
           <li class="smenu">
              <a href="index.php?uc=suivreFrais&action=validerMois" title="Suivre paiement des fiches de frais">Suivre paiement des fiches de frais</a>
           </li>
 	   <li class="smenu">
              <a href="index.php?uc=connexion&action=deconnexion" title="Se déconnecter">Deconnexion</a>
           </li> 
      </ul>
        
    </div>