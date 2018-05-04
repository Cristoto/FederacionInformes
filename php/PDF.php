<?php
namespace FederacionInformes\php;

require_once __DIR__ . '/../vendor/autoload.php';
use FPDF;

/**
 * Class to generate PDF
 * 
 * @author Cristo M. Estévez Hernández <cristom.estevez@gmail.com>
 */
class PDF extends FPDF
{

    private $title;

    /**
     * Title to header section of PDF file.
     *
     * @param string $title
     */
    function __construct(string $title){
        parent::__construct();
        $this->title = $title;
    }

    /**
     * Configure header structure of PDF Files
     *
     * @return void
     */
    function Header(){
        $this->Cell(10);
        $this->Image('Assets/images/RFESS.gif', null, null, 30, 30);
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Movernos a la derecha
        $this->Cell(70);
        // Título
        $this->Cell(30,-10,'Title',1,0,'C');
        // Salto de línea
        $this->Ln(20);
        $this->Line(20, 45, 210-20, 45);
        $this->Ln(20);
    }

    /**
     * Configure footer structure of PDF Files
     *
     * @return void
     */
    function Footer(){
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
    }


}
