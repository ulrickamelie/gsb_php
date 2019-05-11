<table class="listeLegere">
  	   <caption>Descriptif des éléments hors forfait -<?php //echo $nbJustificatifs ?> justificatifs reçus -
       </caption>
             <tr>
                <th class="idhf">ID Hors Forfait</th>
                <th class="id">ID</th>
                <th class="nom">Nom</th>
                <th class="date">Date</th>
                <th class="libelle">Libellé</th>
                <th class='montant'>Montant</th>
             </tr>
        <?php  
                //foreach ($lesEtat as $unEtat) 
        //{
                   //$idEtat = $unEtat['idEtat']; 
        //}
             if( $lesFraisHorsForfait !=null)   {
          foreach ( $lesFraisHorsForfait as $unFraisHorsForfait ) 
		  {
                        $idhf = $unFraisHorsForfait['idhf'];
                        $id = $unFraisHorsForfait['idVisiteur'];
                        $nom = $unFraisHorsForfait['nom'];
			$date = $unFraisHorsForfait['date'];
			$libelle = $unFraisHorsForfait['libelle'];
                        $montant = $unFraisHorsForfait['montant'];
		?>
             <tr>
                 <td><?php echo $idhf ?></td>
                 <td><?php echo $id ?></td>
                 <td><?php echo $nom ?></td>
                <td><?php echo $date ?></td>
                <td><?php echo $libelle ?></td>
                <td><?php echo $montant ?></td>
             </tr>
             
        <?php 
        
        
                if (!estRefuser($idhf))
                    
        {
            
            echo ("<td><center>
                <form method=post action=index.php?uc=suivreFrais&action=refuserHorsForfait>
                <input type=hidden name=idhf value=$idhf>
                <input type=hidden name=lstVisiteur value=$leVisiteur>
                <input type=hidden name=lstMois value=$leMois>
                <input type=submit value='Refuser'>
                </form>
                </center></td>
                </tr>");
        }
        else
        {
            echo ("<td><center><font color=red>Refusé</font></center></td>
                </tr>");
            echo("<td><center>
                <form method=post action=index.php?uc=suivreFrais&action=confirmerRefus&idhf=$idhf>
                <input type=hidden name=lstVisiteur value=$leVisiteur>
                <input type=hidden name=lstMois value=$leMois>
                <input type=submit value='Retirer'>
                </form>
                </center></td>
                </tr>");
        }
        
                  }
             
                        //if (!estValider($idhf) && $idEtat !='VA')
                            if (!estValider($idhf))
                    
        {
            
            echo ("<td><center>
                <form method=post action=index.php?uc=suivreFrais&action=validerHorsForfait>
                <input type=hidden name=idhf value=$idhf>
                <input type=hidden name=lstVisiteur value=$leVisiteur>
                <input type=hidden name=lstMois value=$leMois>
                <input type=submit value='Valider'>
                </form>
                </center></td>
                </tr>");
        }
        else
        {
            echo ("<td><center><font color=blue>VALIDER</font></center></td>
                </tr>");
            
        }
             }
        
		?>
    </table>