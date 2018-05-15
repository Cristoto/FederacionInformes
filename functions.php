<?php
use FederacionInformes\php\PDF;
use FederacionInformes\php\Consultas;
require_once 'vendor/autoload.php';

/**
 * Create a PDF with specify content
 *
 * @param string $title Title to the PDF
 * @param array $header Content to the header of the table
 * @param array $content array with the content of the table
 * @return void
 */
function createPDF(string $title, array $header, array $content){
	ob_start();
		$pdf = new PDF($title);
		$pdf->AliasNbPages();
		$pdf->AddPage('L');
		$pdf->SetFont('Times','',12);
		$pdf->loadTable($header, $content);
		$pdf->Output();
	ob_end_flush(); 
}

function puntos($bloqueo, $cantParticipantes, $puntInicial, $difPuntos, $temporada) {
	$puntos = [];
	array_push($puntos, $puntInicial);
	for ($i = 1; $i < $cantParticipantes ; $i++) { 
		array_push($puntos, $puntos[$i-1] + $difPuntos);
	}
}