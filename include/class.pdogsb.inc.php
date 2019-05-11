<?php
/** 
 * Classe d'accËs aux donnÈes. 
 
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO 
 * $monPdoGsb qui contiendra l'unique instance de la classe
 
 * @package default
 * @author Cheri Bibi≤
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php 
 */

class PdoGsb{   		
      	private static $serveur='mysql:host=localhost';
      	private static $bdd='dbname=gsb_frais';   		
      	private static $user='root' ;    		
      	private static $mdp='' ;	
		private static $monPdo;
		private static $monPdoGsb=null;
/**
 * Constructeur privé, crée l'instance de PDO qui sera sollicitée
 * pour toutes les méthodes de la classe
 */				
	private function __construct(){
    	PdoGsb::$monPdo = new PDO(PdoGsb::$serveur.';'.PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp); 
		PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
	}
	public function _destruct(){
		PdoGsb::$monPdo = null;
	}
/**
 * Fonction statique qui crée l'unique instance de la classe
 
 * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
 
 * @return l'unique objet de la classe PdoGsb
 */
	public  static function getPdoGsb(){
		if(PdoGsb::$monPdoGsb==null){
			PdoGsb::$monPdoGsb= new PdoGsb();
		}
		return PdoGsb::$monPdoGsb;  
	}
/**
 * Retourne les informations d'un visiteur
 
 * @param $login 
 * @param $mdp
 * @return l'id, le nom, le prénom et le type sous la forme d'un tableau associatif 
*/
	public function getInfosVisiteur($login, $mdp){
		$req = "select visiteur.id as id, visiteur.nom as nom, visiteur.prenom as prenom, visiteur.type as type from visiteur 
		where visiteur.login='$login' and visiteur.mdp=md5('$mdp')";
               // $mdpmd5 = md5($mdp);
		$rs = PdoGsb::$monPdo->query($req);             
		$ligne = $rs->fetch();
		return $ligne;
	}

/**
 * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
 * concernÈes par les deux arguments
 
 * La boucle foreach ne peut Ítre utilisÈe ici car on procËde
 * ‡ une modification de la structure itÈrÈe - transformation du champ date-
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif 
*/
	public function getLesFraisHorsForfait($idVisiteur,$mois){
	    $req = "select * from lignefraishorsforfait where lignefraishorsforfait.idVisiteur ='$idVisiteur' 
		and lignefraishorsforfait.mois = '$mois' ";	
		$res = PdoGsb::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		$nbLignes = count($lesLignes);
		for ($i=0; $i<$nbLignes; $i++){
			$date = $lesLignes[$i]['date'];
			$lesLignes[$i]['date'] =  dateAnglaisVersFrancais($date);
		}
		return $lesLignes; 
	}
/**
 * Retourne le nombre de justificatif d'un visiteur pour un mois donnÈ
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return le nombre entier de justificatifs 
*/
	public function getNbjustificatifs($idVisiteur, $mois){
		$req = "select fichefrais.nbjustificatifs as nb from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne['nb'];
	}
/**
 * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
 * concernÈes par les deux arguments
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return l'id, le libelle et la quantitÈ sous la forme d'un tableau associatif 
*/
	public function getLesFraisForfait($idVisiteur, $mois){
		$req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle, fraisforfait.montant as montant, (quantite*montant) as total, 
		lignefraisforfait.quantite as quantite,lignefraisforfait.idfraisforfait as fraisforfait from lignefraisforfait inner join fraisforfait 
		on fraisforfait.id = lignefraisforfait.idfraisforfait
		where lignefraisforfait.idVisiteur ='$idVisiteur' and lignefraisforfait.mois='$mois' 
		order by lignefraisforfait.idfraisforfait";	
		$res = PdoGsb::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes; 
	}
/**
 * Retourne tous les id de la table FraisForfait
 
 * @return un tableau associatif 
*/
	public function getLesIdFrais(){
		$req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
		$res = PdoGsb::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
	}
/**
 * Met ‡ jour la table ligneFraisForfait
 
 * Met ‡ jour la table ligneFraisForfait pour un visiteur et
 * un mois donnÈ en enregistrant les nouveaux montants
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @param $lesFrais tableau associatif de clÈ idFrais et de valeur la quantitÈ pour ce frais
 * @return un tableau associatif 
*/
	public function majFraisForfait($idVisiteur, $mois, $lesFrais){
		$lesCles = array_keys($lesFrais);
		foreach($lesCles as $unIdFrais){
			$qte = $lesFrais[$unIdFrais];
			$req = "update lignefraisforfait set lignefraisforfait.quantite = $qte
			where lignefraisforfait.idvisiteur = '$idVisiteur' and lignefraisforfait.mois = '$mois'
			and lignefraisforfait.idfraisforfait = '$unIdFrais'";
			PdoGsb::$monPdo->exec($req);
		}
		
	}
/**
 * met ‡ jour le nombre de justificatifs de la table ficheFrais
 * pour le mois et le visiteur concernÈ
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
*/
	public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs){
		$req = "update fichefrais set nbjustificatifs = $nbJustificatifs 
		where fichefrais.idvisiteur = '$idVisiteur' and fichefrais.mois = '$mois'";
		PdoGsb::$monPdo->exec($req);	
	}
/**
 * Teste si un visiteur possËde une fiche de frais pour le mois passÈ en argument
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return vrai ou faux 
*/	
	public function estPremierFraisMois($idVisiteur,$mois)
	{
		$ok = false;
		$req = "select count(*) as nblignesfrais from fichefrais 
		where fichefrais.mois = '$mois' and fichefrais.idvisiteur = '$idVisiteur'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		if($laLigne['nblignesfrais'] == 0){
			$ok = true;
		}
		return $ok;
	}
/**
 * Retourne le dernier mois en cours d'un visiteur
 
 * @param $idVisiteur 
 * @return le mois sous la forme aaaamm
*/	
	public function dernierMoisSaisi($idVisiteur){
		$req = "select max(mois) as dernierMois from fichefrais where fichefrais.idvisiteur = '$idVisiteur'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		$dernierMois = $laLigne['dernierMois'];
		return $dernierMois;
	}
	
/**
 * CrÈe une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnÈs
 
 * rÈcupËre le dernier mois en cours de traitement, met ‡ 'CL' son champs idEtat, crÈe une nouvelle fiche de frais
 * avec un idEtat ‡ 'CR' et crÈe les lignes de frais forfait de quantitÈs nulles 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
*/
	public function creeNouvellesLignesFrais($idVisiteur,$mois){
		$dernierMois = $this->dernierMoisSaisi($idVisiteur);
		$laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur,$dernierMois);
		if($laDerniereFiche['idetat']=='CR'){
				$this->majEtatFicheFrais($idVisiteur, $dernierMois,'CL');
				
		}
		$req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat) 
		values('$idVisiteur','$mois',0,0,now(),'CR')";
		PdoGsb::$monPdo->exec($req);
		$lesIdFrais = $this->getLesIdFrais();
		foreach($lesIdFrais as $uneLigneIdFrais){
			$unIdFrais = $uneLigneIdFrais['idfrais'];
			$req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite) 
			values('$idVisiteur','$mois','$unIdFrais',0)";
			PdoGsb::$monPdo->exec($req);
		 }
	}
/**
 * CrÈe un nouveau frais hors forfait pour un visiteur un mois donnÈ
 * ‡ partir des informations fournies en paramËtre
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @param $libelle : le libelle du frais
 * @param $date : la date du frais au format franÁais jj//mm/aaaa
 * @param $montant : le montant
*/
	public function creeNouveauFraisHorsForfait($idVisiteur,$mois,$libelle,$date,$montant){
		$dateFr = dateFrancaisVersAnglais($date);
		$req = "insert into lignefraishorsforfait 
		values('','$idVisiteur','$mois','$libelle','$dateFr','$montant',0)";
		PdoGsb::$monPdo->exec($req);
	}
/**
 * Supprime le frais hors forfait dont l'id est passÈ en argument
 
 * @param $idFrais 
*/
	public function supprimerFraisHorsForfait($idFrais){
		$req = "delete from lignefraishorsforfait where lignefraishorsforfait.id =$idFrais ";
		PdoGsb::$monPdo->exec($req);
	}
/**
 * Retourne les mois pour lesquel un visiteur a une fiche de frais
 
 * @param $idVisiteur 
 * @return un tableau associatif de clÈ un mois -aaaamm- et de valeurs l'annÈe et le mois correspondant 
*/
	public function getLesMoisDisponibles($idVisiteur){
		$req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' 
		order by fichefrais.mois desc ";
		$res = PdoGsb::$monPdo->query($req);
		$lesMois =array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$mois = $laLigne['mois'];
			$numAnnee =substr( $mois,0,4);
			$numMois =substr( $mois,4,2);
			$lesMois["$mois"]=array(
		     "mois"=>"$mois",
		    "numAnnee"  => "$numAnnee",
			"numMois"  => "$numMois"
             );
			$laLigne = $res->fetch(); 		
		}
		return $lesMois;
	}
        
        /**
         * retourne les mois disponibles selon les comptables qui on des fiches de  ?alider.
         * @return type
         */
        /**
         * retourne les mois ou les visiteurs ont des fiches de frais ‡ l'etat cr
         * @return type
         */
        public function getLesMoisDisponibles2(){ 
		$req = "select fichefrais.mois as mois from  fichefrais where idetat='CL'
		order by fichefrais.mois desc ";
		$res = PdoGsb::$monPdo->query($req);
		$lesMois =array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$mois = $laLigne['mois'];
			$numAnnee =substr( $mois,0,4);
			$numMois =substr( $mois,4,2);
			$lesMois["$mois"]=array(
		     "mois"=>"$mois",
		    "numAnnee"  => "$numAnnee",
			"numMois"  => "$numMois"
             );
			$laLigne = $res->fetch(); 		
		}
		return $lesMois;
	}
        /**
         * retourne la liste des visiteurs qui ont des fiches de frais pour le mois selectionÈ
         * @param type $mois
         * @return type
         */
        public function getLesVisiteurs($mois){
		$req = "select visiteur.id,visiteur.nom,visiteur.prenom from visiteur where visiteur.id in "
                        . "(select fichefrais.idvisiteur from fichefrais where fichefrais.mois='$mois' and fichefrais.idetat='CL') 
		order by nom desc ";
		$res = PdoGsb::$monPdo->query($req);
		$lesVisiteurs =array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$selection = $laLigne['id'];
                        $nom=$laLigne['nom'];
                        $prenom=$laLigne['prenom'];
			$lesVisiteurs["$selection"]=array(
                        "id"=>"$selection",
                        "nom"=>"$nom",
                         "prenom"=>"$prenom"
             );
			$laLigne = $res->fetch(); 		
		}
		return $lesVisiteurs;
	}
/**
 * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donnÈ
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'Ètat 
*/	
	public function getLesInfosFicheFrais($idVisiteur,$mois){
		$req = "select fichefrais.idetat as idetat, fichefrais.datemodif as datemodif, fichefrais.nbjustificatifs as nbjustificatifs, 
fichefrais.montantValide as montantvalide, etat.libelle as libetat from  fichefrais inner join etat on fichefrais.idetat = etat.id 
where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois ='$mois'";
		$res = PdoGsb::$monPdo->query($req);
                $laLigne = $res->fetch();
		return $laLigne;
	}
/**
 * Modifie l'Ètat et la date de modification d'une fiche de frais
 
 * Modifie le champ idEtat et met la date de modif ‡ aujourd'hui
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 */
 
	public function majEtatFicheFrais($idVisiteur,$mois,$etat){
		$req =("update fichefrais set idetat ='$etat', datemodif = now() 
where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'");
		PdoGsb::$monPdo->exec($req);
	}
      /**
       * permet d'avoir la liste de tous les visiteurs 
       * @return type
       */
        public function getAllVisiteurs(){
		$req ="select * from visiteur where comptable=0";
		
		$res = PdoGsb::$monPdo->query($req);
		$lesVisiteurs =array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$selection = $laLigne['id'];
                        $nom=$laLigne['nom'];
                        $prenom=$laLigne['prenom'];
			$lesVisiteurs["$selection"]=array(
                            "id"=>"$selection",
                            "nom"=>"$nom",
                            "prenom"=>"$prenom"
             );
			$laLigne = $res->fetch(); 		
		}
		return $lesVisiteurs;
	}
        /**
         * permet de reporter les frais hors forfait 
         * @param type $idFrais
         */
      //public function getAllVisiteurs(){
		//$req ="select * from visiteur where comptable=0";
		
		//$res = PdoGsb::$monPdo->query($req);
		//$lesVisiteurs =array();
		//$laLigne = $res->fetch();
		//while($laLigne != null)	{
			//$selection = $laLigne['id'];
                        //$nom=$laLigne['nom'];
                        //$prenom=$laLigne['prenom'];
			//$lesVisiteurs["$selection"]=array(
                           // "id"=>"$selection",
                           // "nom"=>"$nom",
                         //  "prenom"=>"$prenom"
             //);
		//	$laLigne = $res->fetch(); 		
		//}
		//return $lesVisiteurs;
	//}
        
        /**
         * 
         * @param type $idFrais
         * reportent les frais hors forfaits
         */
        public function reporterFraisHorsForfait($idFrais){
		$req = "select mois,idvisiteur,montant from lignefraishorsforfait where id='$idFrais'";
		$res=PdoGsb::$monPdo->query($req);
                $laLigne=$res->fetch();
                $mois=$laLigne["mois"];
                $visiteur=$laLigne["idvisiteur"];
                $montant=$laLigne["montant"];
                
                $annee= (int)(substr($mois,0,4));
                $leMois=  substr($mois,5,2);
                
                if($leMois<12){
                    $leMois+=1;
                    if($leMois<10){
                        $leMois2="0"."$leMois";
                    }
                }
                else{
                    $annee+=1;
                    $leMois2="01";
                }
                    
                $mois2=$annee."/".$leMois2;
                //return $mois2;
                $req2="select count(*) as nbligne from fichefrais where mois='$mois2' and idvisiteur='$visiteur'";
                PdoGsb::$monPdo->query($req2);
                $laLigne=$res->fetch();
                $nb=(int)($laLigne["nbligne"]);
                if($nb==0){
                $req0=("insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat) 
		values('$visiteur','$mois2',0,0,now(),'CR')");
                PdoGsb::$monPdo->exec($req0);
                }
                $req2=("update  lignefraishorsforfait set mois='$mois2' where id='$idFrais'");
                PdoGsb::$monPdo->exec($req2);
                
	}
        /**
         * 
         * @param type $idFrais
         * refusent les frais hors forfaits
         */
        public function refuFraisHorsForfait($idFrais){
            $req=("select refus from lignefraishorsforfait where id='$idFrais'");
            $res=PdoGsb::$monPdo->query($req);
            $laLigne=$res->fetch();
            $refu=(int)($laLigne["refus"]);
            var_dump($refu);
            if($refu==0){
                $req2=("update lignefraishorsforfait set refus=1  where id='$idFrais'");
                PdoGsb::$monPdo->exec($req2);
            }
            
            
        }
        
        /**
         * 
         * @param type $idHF
         * @return boolean
         * verifie si les frais hors  forfaits sont refusÈs ou pas
         */
        public function estRefuse($idHF){
            $req=("select refus from lignefraishorsforfait where id='$idHF'");
            $res=PdoGsb::$monPdo->query($req);
            $laLigne=$res->fetch();
            $refu=(int)($laLigne["refus"]);
                if($refu==1){
                    return true;
                }
            return false;
        }
        /**
         * 
         * @param type $unType
         * @return type
         * 
         */
       public function valeurTF($unType){
           $req=("select montant from fraisforfait where id='$unType'");
           $res=PdoGsb::$monPdo->query($req);
           $montant=$res->fetch();
           return $montant;
       }
       /**
        * 
        * @return type
        * retournent les fiches validÈes
        */
        public function getLesFiches(){
          $req=("select * from fichefrais where idetat='VA' OR idetat='RB'");
          $res=PdoGsb::$monPdo->query($req);
          $laLigne=$res->fetch();
          $lesFiches=null;
          while($laLigne != null)	{
			$idVisiteur=$laLigne['idVisiteur'];
                        $mois=$laLigne['mois'];
                        $nbJustificatifs=$laLigne['nbJustificatifs'];
                        $dateModif=$laLigne['dateModif'];
                        $idEtat=$laLigne['idEtat'];
                        
			$lesFiches["$idVisiteur"]=array(
                            "idVisiteur"=>"$idVisiteur",
                            "mois"=>"$mois",
                            "nbJustificatifs"=>"$nbJustificatifs",
                            "dateModif"=>"$dateModif",
                            "idEtat"=>"$idEtat"
             );
			$laLigne = $res->fetch(); 		
		}
		return $lesFiches;

        }
        
        /**
         * 
         * @param type $idVisiteur
         * @param type $mois
         * @return type
         * total des couts forfaitaires
         */
        function totalHFSansRefus($idVisiteur,$mois){
            $req=("select sum(montant) as totalHF from lignefraishorsforfait where idVisiteur='$idVisiteur' and mois='$mois'");
            $res=PdoGsb::$monPdo->query($req);
            $laLigne=$res->fetch();
            $total=$laLigne['totalHF'];
            return $total;
        }
        /**
         * met a l'Ètat b toutes les fiches de frais
         */
        function rbAll(){
            $req=("update fichefrais set idetat='RB' where idetat='VA'");
            PdoGsb::$monPdo->query($req);
        }
        /**
         * 
         * @param type $idVisiteur
         * @param type $mois
         * @return type
         * recup?rent les infos pour le pdf
         */
        function getInfosvisiteurPdf($idVisiteur, $mois){ //rajout
		$req = "select visiteur.id,visiteur.nom,visiteur.prenom from visiteur where visiteur.id='$idVisiteur'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}
        function totalF($idVisiteur, $mois){
            $req=("select sum(montant*quantite) as total from lignefraisforfait inner join fraisforfait 
		on fraisforfait.id = lignefraisforfait.idfraisforfait where lignefraisforfait.idVisiteur='$idVisiteur' and lignefraisforfait.mois='$mois'");
            $res=PdoGsb::$monPdo->query($req);
            $laLigne=$res->fetch();
            $total=$laLigne['total'];
            return $total;
        }

        function getLesInfos($idVisiteur,$mois){
		$req = "select fichefrais.mois, visiteur.id from  fichefrais inner join etat on fichefrais.idetat = etat.id 
where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois ='$mois'";
		$res = PdoGsb::$monPdo->query($req);
                $laLigne = $res->fetch();
		return $laLigne;
	}
        public function getIdLesVisiteurs()
    {
        $res = PdoGsb::$monPdo->query("select distinct visiteur.nom, visiteur.prenom from visiteur, fichefrais where fichefrais.idEtat = 'CL' order by visiteur.nom");   //la requête
        $lesLignes = array();           // déclare un tableau vide
        $lesLignes =  $res->fetchAll();  //rempli le tableau à l'aide de la methode fecthAll
        return $lesLignes;    
    } 
        public function getFrais($idVisiteur,$mois,$type){
            $req = "select quantite from lignefraisforfait where idVisiteur = '$idVisiteur' and mois = '$mois' and idFraisForfait = '$type';";
            $res = PdoGsb::$monPdo->query($req);
            $ligne = $res->fetch();
            return $ligne[0];
        }
        
        public function getHorsForfait($idVisiteur,$mois){
		$req = "select * from lignefraishorsforfait where idVisiteur = '$idVisiteur' and mois = '$mois'";
		$result = PdoGsb::$monPdo->query($req);
		
		return $result;
        }
        
        public function getLesFraisHorsForfaitComptable($nom,$mois){
	    $req = "select lignefraishorsforfait.id as idhf, lignefraishorsforfait.idVisiteur as idVisiteur, lignefraishorsforfait.mois as mois, lignefraishorsforfait.libelle as libelle, lignefraishorsforfait.date as date, lignefraishorsforfait.montant as montant , visiteur.nom 
                   from lignefraishorsforfait, visiteur, fichefrais 
                   where lignefraishorsforfait.idVisiteur = visiteur.id 
                   and fichefrais.idVisiteur = visiteur.id
                   and visiteur.nom ='$nom' 
                   and lignefraishorsforfait.mois = '$mois'
                   group by idhf";
                //echo $req;
		$res = PdoGsb::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		$nbLignes = count($lesLignes);
		for ($i=0; $i<$nbLignes; $i++){
			$date = $lesLignes[$i]['date'];
			$lesLignes[$i]['date'] =  dateAnglaisVersFrancais($date);
		}
		return $lesLignes; 
	}
        
        public function getLesFraisForfaitVisiteurChoisi($mois, $nom){
		$req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle, lignefraisforfait.quantite as quantite, visiteur.nom as nom
                        from lignefraisforfait, fraisforfait, visiteur
                        where fraisforfait.id = lignefraisforfait.idfraisforfait
                        and lignefraisforfait.idVisiteur = visiteur.id
                        and lignefraisforfait.mois='$mois' 
                        and visiteur.nom ='$nom' 
                        group by idfraisforfait order by lignefraisforfait.idfraisforfait";
                //echo $req;
		$res = PdoGsb::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes; 
	}
        
        public function getLesInfosFicheFraisComptable($idVisiteur,$mois){
		$req = "select fichefrais.idEtat as idEtat, fichefrais.dateModif as dateModif, fichefrais.nbJustificatifs as nbJustificatifs,
			fichefrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join etat on fichefrais.idEtat = etat.id
			where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne;
	}
        
        public function majFraisForfaitChoisi($nom, $mois, $lesFrais){
		$lesCles = array_keys($lesFrais);
		foreach($lesCles as $unIdFrais){
			$qte = $lesFrais[$unIdFrais];
			$req = "update lignefraisforfait
                                JOIN visiteur ON visiteur.id = lignefraisforfait.idVisiteur
                                set lignefraisforfait.quantite = '$qte'
                                where visiteur.nom = '$nom'
                                and lignefraisforfait.mois = '$mois'
                                and lignefraisforfait.idfraisforfait = '$unIdFrais'";
			PdoGsb::$monPdo->exec($req);
		}
		
	}
        
        public function majFraisHorsForfaitRefuser($idhf){
			$req = "update lignefraishorsforfait 
                        set libelle = concat ('REFUSER',' ',libelle)
			where lignefraishorsforfait.id ='$idhf'";
			PdoGsb::$monPdo->exec($req);
		}
       
        public function majFraisHorsForfaitRetirerRefuser($idhf){
			$req = "update lignefraishorsforfait 
                        set libelle = SUBSTRING(libelle, 9)
			where lignefraishorsforfait.id ='$idhf'";
			PdoGsb::$monPdo->exec($req);
		}
                
        public function majFraisHorsForfaitValider($idVisiteur,$mois){
			$req = "update fichefrais
                        inner join visiteur on visiteur.id = fichefrais.idVisiteur
                        set idEtat = 'VA'
			where visiteur.nom ='$idVisiteur'
                        and fichefrais.mois = '$mois'";
			PdoGsb::$monPdo->exec($req);
		}
        
        public function getLesEtat($nom)
    {
        $res = PdoGsb::$monPdo->query("select fichefrais.idVisiteur, fichefrais.mois, fichefrais.nbJustificatifs, fichefrais.montantValide,
            fichefrais.dateModif, fichefrais.idEtat, visiteur.nom as nomEtat
                FROM fichefrais, visiteur
                where nom = '$nom'");
        $lesLignes = array();           // déclare un tableau vide
        $lesLignes =  $res->fetchAll();  //rempli le tableau à l'aide de la methode fecthAll
        return $lesLignes;    
    }
    
    function totalHFAvecRefus($idVisiteur,$mois){
            $req=("select sum(montant) as totalHF from lignefraishorsforfait where idVisiteur='$idVisiteur' and mois='$mois' and libelle LIKE 'REFUSER%'");
            $res=PdoGsb::$monPdo->query($req);
            $laLigne=$res->fetch();
            $total=$laLigne['totalHF'];
            return $total;
        }
        
        
    
    public function getMoisPrecedent(){
        $moisPrecedent = new DateTime();
        $moisPrecedent->modify('-1 month');
        $date = $moisPrecedent->format('Ym');
        $req = "update fichefrais set idEtat = 'CL' where mois < '$date' and idEtat ='CR' ";
        PdoGsb::$monPdo->exec($req);
        //echo $date->format('Ym');
    }
        
    public function cloturerFiches($idVisiteur, $mois){
        $req = "update fichefrais set idEtat ='VA', datemodif = now() where idisiteur ='$idVisiteur' and mois= '$mois' ";
        PdoGsb::$monPdo->exec($req);
    }
    }
      



?>
