<?php
require_once('fpdf/fpdf.php');
require_once('include/fct.inc.php');
require_once('include/class.pdogsb.inc.php');
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Image('images/logo.jpg');
$pdf->Cell(100,10,'REMBOURSEMENT DES FRAIS ');
$pdf->Ln(20);
$pdf->Cell(100,10,'Visiteur : '.$visiteur['prenom'].' '.$visiteur['nom']);
$pdf->Ln(10);
$pdf->Cell(100,10,'Mois : '.$leMois);
$pdf->Ln(10);

$pdf->SetFont('Arial','B',12);
//En-tête du tableau frais forfait 
$pdf->Cell(55,10,'Frais Forfaitaires',1);
$pdf->Cell(30,10,utf8_decode('Quantité'),1);
$pdf->Cell(50,10,'Montant unitaire',1);
$pdf->Cell(25,10,'Total',1);
$pdf->Ln(10);


//Données du tableau
foreach($lesFraisForfait as $unFrais){
    $pdf->Cell(55,10, utf8_decode($unFrais['libelle']),1);
    $pdf->Cell(30,10,$unFrais['quantite'],1);
    $pdf->Cell(50,10,$unFrais['montant'],1);
    $pdf->Cell(25,10,$unFrais['total'],1);
    $pdf->Ln(10);
}

$pdf->Ln(10);
$pdf->Cell(100,10,'Autres Frais :');
$pdf->Ln(10);

//En-tête du tableau frais hors forfait
$pdf->Cell(50,10,'Date',1);
$pdf->Cell(100,10,utf8_decode('Libellé'),1);
$pdf->Cell(30,10,' Montant',1);
$pdf->Ln(10);
//Données du tableau Hors Forfait
foreach($lesFraisHorsForfait as $unHorsForfait){
    $pdf->Cell(50,10,$unHorsForfait['mois'],1);
    $pdf->Cell(100,10,$unHorsForfait['libelle'],1);
    $pdf->Cell(30,10,$unHorsForfait['montant'],1);
    $pdf->Ln(10);
}
$pdf->Ln(10);
$pdf->Cell(100,10,'Total du '.$leMois.' : '.$totaux.chr(128));
$pdf->Ln(10);
$pdf->Cell(100,10, utf8_decode('Montant refusé : '.$totalAvecRefus).chr(128));
$pdf->Ln(10);
$pdf->Cell(100,10, utf8_decode('Montant validé : '.$montantValide).chr(128));
$pdf->Ln(10);
date_default_timezone_get('FR/Paris');
$currentdate = date("d-m-Y");
$pdf->Cell(100,10, utf8_decode('Fait à Paris, le : '.$currentdate));
$pdf->Ln(10);
$pdf->Cell(120,10,"Vu l'agent comptable");
$pdf->Image('images/signature.jpg');
ob_end_clean();
$pdf->Output();
?>