<?php
include("vues/v_sommaireComptable.php");
$idVisiteur = $_SESSION['idVisiteur'];

$action = $_REQUEST["action"];
switch ($action){
   case 'validerMois':{   
        $date = $pdo->getMoisPrecedent();    
        $lesMois=$pdo->getLesMoisDisponibles2();
        $lesVisiteurs=$pdo->getIdLesVisiteurs();
        // Afin de sélectionner par défaut le dernier mois dans la zone de liste
        // on demande toutes les clés, et on prend la première,
        // les mois étant triés décroissants
        $lesCles = array_keys( $lesMois );
        $moisASelectionner = $lesCles[0];        
        include("vues/v_listeMoisComptable.php");
        break;
   }
   case 'saisirMois':{
        $leMois = $_REQUEST['lstMois'];
        $leVisiteur = $_REQUEST['lstVisiteur'];
        $lesMois=$pdo->getLesMoisDisponibles($idVisiteur); //Liste déroulante
        $lesVisiteurs=$pdo->getIdLesVisiteurs();// Liste deroulante visiteur
        $moisASelectionner = $leMois;
        initRefus();
        initValider();
        include("vues/v_listeMoisComptable.php");
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfaitComptable($leVisiteur,$leMois);
        $lesFraisForfait= $pdo->getLesFraisForfaitVisiteurChoisi($leMois, $leVisiteur);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFraisComptable($leVisiteur,$leMois);
        if(empty($lesFraisHorsForfait || $lesFraisForfait))
            {   
                ajouterErreur ("Il n'y a pas de fiche de frais pour ce visiteur ");
                include("vues/v_erreurs.php");
            }
        else
        {
        $numAnnee =substr( $leMois,0,4);
        $numMois =substr( $leMois,4,2);
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $dateModif =  $lesInfosFicheFrais['dateModif'];
        $dateModif =  dateAnglaisVersFrancais($dateModif);
        include("vues/v_listeFraisForfait.comptable.php");
        include("vues/v_listeHorsForfait.comptable.php");
        include("vues/v_validerFicheFrais.php");
        }
                
        break;
   }
   case 'validerFraisForfait':{
        $lesFrais = $_REQUEST['lesFrais'];
        $leMois = $_REQUEST['lstMois'];
        $leVisiteur = $_REQUEST['lstVisiteur'];
        $lesMois=$pdo->getLesMoisDisponibles($idVisiteur);
        $lesVisiteurs=$pdo->getIdLesVisiteurs();
        $lesFraisForfait= $pdo->getLesFraisForfaitVisiteurChoisi($leMois, $leVisiteur);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfaitComptable($leVisiteur,$leMois);
        if(lesQteFraisValides($lesFrais))
        {
                $pdo->majFraisForfaitChoisi($leVisiteur, $leMois, $lesFrais);
                echo 'Frais forfait'.' de '. $leVisiteur. ' à jour';
        }
        else
        {
                ajouterErreur("Les valeurs des frais doivent être numériques");
                include("vues/v_erreurs.php");
        }
        include("vues/v_listeMoisComptable.php");
        include("vues/v_listeFraisForfait.comptable.php");
        include("vues/v_listeHorsForfait.comptable.php");
        include("vues/v_validerFicheFrais.php");
        break;
   }
   case 'refuserHorsForfait':{
       $leMois = $_REQUEST['lstMois'];
                $leVisiteur = $_REQUEST['lstVisiteur'];
                $idhf= $_REQUEST['idhf'];
                initRefus();
                ajouterAuRefus($idhf);
                $lesMois=$pdo->getLesMoisDisponibles($idVisiteur);
                $lesVisiteurs=$pdo->getIdLesVisiteurs();
                $lesFraisForfait= $pdo->getLesFraisForfaitVisiteurChoisi($leMois, $leVisiteur);
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfaitComptable($leVisiteur,$leMois);
                $pdo->majFraisHorsForfaitRefuser($idhf);
                include("vues/v_listeMoisComptable.php");
                include("vues/v_listeFraisForfait.comptable.php");
                include("vues/v_listeHorsForfait.comptable.php");
                include("vues/v_validerFicheFrais.php");
                break;
   }
   case 'confirmerRefus':{
                $leMois = $_REQUEST['lstMois'];
                $leVisiteur = $_REQUEST['lstVisiteur'];
                $idhf= $_REQUEST['idhf'];
                initRefus();
                retirerDuRefus($idhf);
                $pdo->majFraisHorsForfaitRetirerRefuser($idhf);
                $lesMois=$pdo->getLesMoisDisponibles($idVisiteur);
                $lesVisiteurs=$pdo->getIdLesVisiteurs();
                $lesFraisForfait= $pdo->getLesFraisForfaitVisiteurChoisi($leMois, $leVisiteur);
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfaitComptable($leVisiteur,$leMois);
                //$pdo->majFraisForfaitChoisi($leVisiteur, $leMois, $lesFrais);
                include("vues/v_listeMoisComptable.php");
                include("vues/v_listeFraisForfait.comptable.php");
                include("vues/v_listeHorsForfait.comptable.php");
                include("vues/v_validerFicheFrais.php");
                break;
               }
   case 'validerHorsForfait':{
       $leMois = $_REQUEST['lstMois'];
                $leVisiteur = $_REQUEST['lstVisiteur'];
                $idhf= $_REQUEST['idhf'];
                $test = initValider();
                initValider();
                ajouterAuValider($idhf);
                $lesMois=$pdo->getLesMoisDisponibles($idVisiteur);
                $lesVisiteurs=$pdo->getIdLesVisiteurs();
                $lesFraisForfait= $pdo->getLesFraisForfaitVisiteurChoisi($leMois, $leVisiteur);
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfaitComptable($leVisiteur,$leMois);
                $pdo->majFraisHorsForfaitValider($leVisiteur,$leMois);
                $lesEtat = $pdo->getLesEtat($leVisiteur);
                include("vues/v_listeMoisComptable.php");
                include("vues/v_listeFraisForfait.comptable.php");
                include("vues/v_listeHorsForfait.comptable.php");
                include("vues/v_validerFicheFrais.php");
                break;
   }
   case 'confirmerValider':{
       $leMois = $_REQUEST['lstMois'];
                        $leVisiteur = $_REQUEST['lstVisiteur'];
                        $idhf= $_REQUEST['idhf'];
                        initValider();
			retirerDuValide($idhf);
                        //$pdo->majFraisHorsForfaitRetirerRefuser($idhf);
                        $lesMois=$pdo->getLesMoisDisponibles($idVisiteur);
                        $lesVisiteurs=$pdo->getIdLesVisiteurs();
                        $lesFraisForfait= $pdo->getLesFraisForfaitVisiteurChoisi($leMois, $leVisiteur);
                        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfaitComptable($leVisiteur,$leMois);
                        //$pdo->majFraisForfaitChoisi($leVisiteur, $leMois, $lesFrais);
                        include("vues/v_listeMoisComptable.php");
                        include("vues/v_listeFraisForfait.comptable.php");
                        include("vues/v_listeHorsForfait.comptable.php");
                        include("vues/v_validerFicheFrais.php");
                        break;
   }
   case 'validerFicheFrais':{
       $leMois = $_REQUEST['lstMois'];
                $leVisiteur = $_REQUEST['lstVisiteur'];
                $idhf= $_REQUEST['idhf'];
                initRefus();
        initValider();
                $lesMois=$pdo->getLesMoisDisponibles($idVisiteur);
                $lesVisiteurs=$pdo->getIdLesVisiteurs();
                $lesFraisForfait= $pdo->getLesFraisForfaitVisiteurChoisi($leMois, $leVisiteur);
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfaitComptable($leVisiteur,$leMois);
                $VA = 'VA';
                $pdo->majEtatFicheFrais($idVisiteur,$leMois,$VA);
       include("vues/v_listeMoisComptable.php");
                include("vues/v_listeFraisForfait.comptable.php");
                include("vues/v_listeHorsForfait.comptable.php");
                include("vues/v_validerFicheFrais.php");
                echo "La fiche a été validée";
                break;
   }
}
//include('vues/v_suivreFrais.php');
//include ("vues/v_validerVisiteur.php");  
//include ("vues/v_detailsFiche.php");
?>