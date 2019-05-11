<?php
if(!isset($_REQUEST['action'])){
	$_REQUEST['action'] = 'demandeConnexion';
}
$action = $_REQUEST['action'];
switch($action){
	case 'demandeConnexion':{
		include("vues/v_connexion.php");
		break;
	}
	case 'valideConnexion':{
		$login = $_REQUEST['login'];
		$mdp =$_REQUEST['mdp'];
                //$mdp = $pdo->updateMdpVisiteur($pdo);
                //$mdp5=md5($mdp);
                //$mdp =$pdo->crypterMdp();
                //$type = $_REQUEST['type']; 
		$visiteur = $pdo->getInfosVisiteur($login,$mdp);
		if(!is_array( $visiteur)){
			ajouterErreur("Login ou mot de passe incorrect");
			include("vues/v_erreurs.php");
			include("vues/v_connexion.php");
                        break;
		}
		else{
			$id = $visiteur['id'];
			$nom =  $visiteur['nom'];
			$prenom = $visiteur['prenom'];
                        $type = $visiteur['type'];
			connecter($id,$nom,$prenom,$type);
                        if($type=='Visiteur' || $type=='visiteur'){
                            include("vues/v_sommaire.php");
                            break;
                        }		
                        else if($type=='Comptable' || $type=='comptable'){
                            include("vues/v_sommaireComptable.php");
                            break;
                        }
                }
        }
	default :{
		include("vues/v_connexion.php");
		break;
	}
}
?>