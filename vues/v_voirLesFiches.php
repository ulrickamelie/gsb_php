<div id="contenu">
﻿<h2> Fiches de frais</h2>
<?php if(isset($lesFiches)){?>
<table class="listeLegere">
        <tr>
                <th class="idVisiteur">idVisiteur</th>
                <th class="mois">Mois</th>
                <th class="nbJustificatif">Nb jutificatifs</th>
                <th class="datemodif">Dernière modification</th>
                <th class="idEtat">Etat</th>
                <th class="PDF">PDF</th>
                <th class="Remboursement">Remboursement</th>
        </tr>
        <?php
        $i=0;
        foreach($lesFiches as $uneFiche){
            $idVisiteur = $uneFiche['idVisiteur'];
            $mois=$uneFiche['mois'];
            $nbJustificatifs=$uneFiche['nbJustificatifs'];
            $dateModif=$uneFiche['dateModif'];
            $idEtat=$uneFiche['idEtat'];
        
        ?>
            <tr>
                <td><?php echo $idVisiteur ?></td>
                <td><?php echo $mois ?></td>
                <td><?php echo $nbJustificatifs ?></td>
                <td><?php echo $dateModif ?></td>
                <td><?php echo $idEtat ?></td>
                <td><a href="index.php?uc=voirLesFiches&action=pdf&id=<?php echo $idVisiteur?>&mois=<?php echo $mois?>"> <img src="images/iconePdf.png" height="50" width="50"></a></td>
                <td> <a href="index.php?uc=voirLesFiches&action=rembourse&id=<?php echo $idVisiteur?>&mois=<?php echo $mois?> ">Remboursement</a></td>
            </tr>
        <?php
        
        } ?>
</table>
        <a href="index.php?uc=voirLesFiches&action=rbAll">Rembourser tous les visiteurs</a>
<?php }
      else{
          echo "aucune fiche validee disponible";
      }
 ?>
</div>