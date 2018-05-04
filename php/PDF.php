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
    public function Header(){
        $this->Cell(10);
        $this->Image('Assets/images/RFESS.gif', null, null, 30, 30);
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Movernos a la derecha
        $this->Cell(70);
        // Título
        $this->Cell(30,-10,'Title',0,0,'C');
        // Salto de línea
        $this->Ln(20);
        $this->Line(20, 45, 210-20, 45);
    }

    /**
     * Configure footer structure of PDF Files
     *
     * @return void
     */
    public function Footer(){
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
    }

    public function loadTable(array $header, array $data){
        // Colors, line width and bold font
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Header
        //$w = array(40, 35, 40, 45);
        for($i=0;$i<count($header);$i++)
            $this->Cell(40,7,$header[$i],1,0,'C',true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('Arial');
        // Data
        $fill = false;
        foreach($data as $row)
        {
            $this->Cell(40, 6, $row, 'LR', 0, 'L', $fill);
           
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        //$this->Cell(array_sum($w),0,'','T');
    }
}
