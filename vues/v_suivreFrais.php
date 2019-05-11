<div id="contenu">
    <div class="encadre">
        <h3>Choisir visiteur :</h3>
        <form action="index.php?uc=suivreFrais&action=validerMois" method="post">
            <select id="lstMois" name="lstMois">
            <?php
            foreach ($lesVisiteurs as $unVisiteur) {
                $id = $unVisiteur['id'];
                $prenom =  $unVisiteur['prenom'];
                $nom =  $unVisiteur['nom'];  
                ?>
                <option value="<?php echo $id ?>"><?php echo $prenom.' '.$nom ?></option>
                <?php
                }
            ?>
            </select>
            <input type="submit" value="Ok">
        </form>
    <form action="index.php" method="post" name="choix" id="choix">
    <input name="uc" type="hidden" id="uc" value="<?php echo $uc; ?>">
    <p> Mois :
        <select name="SelMois" id="SelMois">
        <?php
            foreach ($lesMois as $unMois) {
                $mois = $unMois['mois'];
                $numAnnee =  $unMois['numAnnee'];
                $numMois =  $unMois['numMois'];
                $sel="";
                if ($SelMois==$mois) {
                    $sel=" Selected";	
                }
        ?>
        <option  value="<?php echo $mois ?>"<?php  echo $sel ?> ><?php echo  $numMois."/".$numAnnee ?></option>
        <?php
            }
        ?>
        </select>
        <input type="submit" value="Ok">
    
    </p>
    </form>
    
    &nbsp;
    <table width="378" height="99" border="1">
        <tr>
            <th width="116" scope="col">Repas midi</th>
            <th width="69" scope="col">Nuitée Hôtel</th>
            <th width="69" scope="col">Etape</th>
            <th width="96" scope="col">Km</th>
        </tr>
        <tr>
            <td>
            <?php
                $recup = $pdo->getFrais($idVisiteur,$SelMois,"REP");
                echo $recup;
            ?>
            </td>
            <td>
            <?php
                $recup = $pdo->getFrais($idVisiteur,$SelMois,"NUI");
                echo $recup;
            ?>
            </td>
            <td>
            <?php
                $recup = $pdo->getFrais($idVisiteur,$SelMois,"ETP");
                echo $recup;
            ?>
            </td>
            <td>
            <?php
                $recup = $pdo->getFrais($idVisiteur,$SelMois,"KM");
                echo $recup;
            ?>
            </td>
        </tr>
    </table>
    
    <h3>Hors Forfait</h3>
    <table width="588" height="74" border="1">
        <tr>
            <th width="146" scope="col">Date</th>
            <th width="262" scope="col">Libellé</th>
            <th width="158" scope="col">Montant</th>
        </tr>
        <tr>
        <?php
            $result = $pdo->getHorsForfait($idVisiteur,$SelMois);
            $z = $result->fetch();
            while($z != null){
        ?>
            <td>
                <input name="date" size=11 value ="<?php echo $z[4]; ?>">
            </td>
            <td>
                <input name="intitulé" size=45 value ="<?php echo $z[3]; ?>">
            </td>
            <td>
                <input name="somme" size=7 value ="<?php echo $z[5]; ?>">
            </td>
        <?php
            $z = $result->fetch();
            }
        ?>
        </tr>
    </table>
    
    <h3>Hors classification</h3>
    <table width="554" height="99" border="1">
        <tr>
            <th width="136" scope="col">Situation</th>
            <th width="125" scope="col">Date opération </th>
            <th width="135" scope="col">Nb Justificatifs</th>
            <th width="126" scope="col">Montant</th>
        </tr>
        <tr>
            <td>
                <select name="situation" id="situation">
                    <option value="CR" 
                    <?php if ($z[5]=="CR") {echo "selected";} ?>>Enregistré</option>
                    <option value="RB" <?php if ($z[5]=="RB") {echo "selected";} ?>>Remboursé</option>
                    <option value="VA" <?php if ($z[5]=="VA") {echo "selected";} ?>>Validé</option>
                    <option value="CL" <?php if ($z[5]=="CL") {echo "selected";} ?>>Cloturé</option>
                </select>
            </td>
            <td>
            <?php echo "<br><font size=1> Dernière modification enregistrée le :".$z[4]."</font>"; ?>
            </td>
            <td>
            <?php
                $z = $pdo->getHorsClass($SelMois,$idVisiteur);
                 echo $z[2];
            ?>
            </td>
            <td>
            <?php echo $z[3]; ?>
            </td>
        </tr>
    </table><h2>Choisir visiteur :</h2>
    <form action="index.php?uc=suivreFrais&action=validerMois" method="post">
        
    </form>
</div>