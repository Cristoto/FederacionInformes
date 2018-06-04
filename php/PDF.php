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
        $this->title = utf8_decode($title);
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
        $this->Cell(30,-10, $this->title ,0,0,'C');
        // Salto de línea
        $this->Ln(20);
        $this->Line(20,55,280,55);
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

    /**
     * Write content into pdf file
     *
     * @param array $header
     * @param array $data
     * @return void
     */
    public function loadTable(array $data){
        $widthCells = 50;
    
        //Competition
        foreach ($data as $competiciones) {
            // Colors, line width and bold font
            $this->SetFillColor(255,0,0);
            $this->SetTextColor(255);
            $this->SetDrawColor(128,0,0);
            $this->SetLineWidth(.3);
            $this->SetFont('','B');
            foreach ($competiciones as $key => $content) {
                if($key !== 'Competidores'){
                    $this->Cell($widthCells,7,$content,0,0,'C',true);
                }
            }
            $this->Ln(10);
            //Header
            foreach ($data[0]['Competidores'] as $competidor) {
                foreach ($competidor as $key => $value) {
                    $this->Cell($widthCells,7,$key,1,0,'C',true);
                }
            }
            $this->Ln();

            // Color and font restoration
            $this->SetFillColor(224,235,255);
            $this->SetTextColor(0);
            $this->SetFont('Arial');

            //DATA
            $fill = false;
            foreach ($competiciones as $key => $content) {
                if($key == 'Competidores'){
                    foreach ($content as $competidor) {
                        foreach ($competidor as $dato) {
                            $this->Cell($widthCells, 6, utf8_decode($dato), 'LR', 0, 'L', $fill);
                        }
                        $this->Ln();
                        $fill = !$fill;
                    }
                }
            }
            $this->AddPage('L');
        }
        // Closing line
        //$this->Cell($widthCells*count($header),0,'','T');
    }

    /**
     * Create a PDF with specify content
     *
     * @param string $title Title to the PDF
     * @param array $header Content to the header of the table
     * @param array $content array with the content of the table
     * @return void
     */
    public static function createPDF(string $title,array $data, bool $global){
        ob_start();
            $pdf = new PDF($title);
            $pdf->AliasNbPages();
            $pdf->AddPage('L');
            $pdf->SetFont('Times','',12);
            if(!$global){
                $pdf->loadTable($data);
            }else{
                foreach ($data as $value) {
                    $pdf->loadTable($value);
                }
            }
            $pdf->Output('D', $title . '.pdf', true);
        ob_end_flush(); 
        ob_end_clean(); 
    }
}
