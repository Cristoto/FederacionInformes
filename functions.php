<?php
use FederacionInformes\php\PDF;
use FederacionInformes\php\Consultas;
require_once 'vendor/autoload.php';

/**
 * Load data file into db
 *
 * @param string $inputFileName Route with the name of the file
 * @return void
 */
function loadFileIntoBD(string $inputFileName) : void{
	$consulta = new Consultas();
    try{
       $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
	   
		//$reader->setInputEncoding('CP1252');
		$reader->setDelimiter(';');
		$reader->setEnclosure('');
		$reader->setSheetIndex(0);
		
		$spreadsheet = $reader->load($inputFileName);
		
		$worksheet = $spreadsheet->getActiveSheet();
		
	    	//Se debe hacer lectura anticipada, o hacerle un next al iterador
	    	//Manera no encontrada
	    	//Se salta el primero para que no lea las cabeceras de las columnas
	    
		$cutradaParaSaltarPrimero = 0;
		foreach ($worksheet->getRowIterator() as $row) {
			if($cutradaParaSaltarPrimero == 0){
				$cutradaParaSaltarPrimero++;
				continue;
			}

			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(FALSE);
			
			$arrayFila = array();
			foreach ($cellIterator as $cell) {
				$arrayFila[] = trim($cell->getValue());				
			}
			
			if($arrayFila[0] != '') {
				$consulta->insertarCompetidor($arrayFila);
			}
			
		}
				
    }catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e){
        die('Error loading file: '.$e->getMessage());
    }
}

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
