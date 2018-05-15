<?php 

namespace FederacionInformes\php;

use FileUpload\FileUpload;
use FileUpload\Validator\Simple as SimpleValidator;
use FileUpload\FileSystem\Simple as SimpleFile;
use FileUpload\PathResolver\Simple as SimplePath;
use FederacionInformes\php\Consultas;
use \PhpOffice\PhpSpreadsheet\Reader\Csv;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Clase encargada del manejo de los ficheros excel.
 * Subida de los ficheros en el servidor, cargar sus datos en la BD, etc.
 * 
 * @author Cristo M. Estévez Hernández <cristom.estevez@gmail.com>
 */
class Excel
{
    private $files;
    private $directory;

    function __construct(array $files, string $pathDirectoryFiles){
        $this->files = $files;
        $this->directory = $pathDirectoryFiles; 
    }

    /**
     *Upload file into server specifying the support types
    *
    * @param array $mimes Extensions of file permit.
    * @return void
    */
    public function uploadFile(array $mimes, array $serverData) : void{
        
        $this->createDirectory($this->directory);

        // Simple validation (max file size 2MB and only two allowed mime types)
        $validator = new SimpleValidator('2M', $mimes);

        // Simple path resolver, where uploads will be put
        $pathresolver = new SimplePath($this->directory);
        // The machine's filesystem
        $filesystem = new SimpleFile();

        // FileUploader itself
        $fileupload = new FileUpload($this->files , $serverData);

        // Adding it all together. Note that you can use multiple validators or none at all
        $fileupload->setPathResolver($pathresolver);
        $fileupload->setFileSystem($filesystem);
        //TODO: Search MIME Type to files csv 
        //$fileupload->addValidator($validator);

        // Doing the deed
        list($files, $headers) = $fileupload->processAll();
        
    }

    /**
     * Create a directory if not exist
     *
     * @param string $pathDirectory
     * @return void
     */
    private function createDirectory(string $pathDirectory) : void{
        if(!file_exists($pathDirectory) && !is_dir($pathDirectory)){
            mkdir($pathDirectory, 0777);
        }
    }

    /**
     * Delete all files into directory specify on the constructor
     *
     * @return void
     */
    public function deleteFiles() : void{
        $files = glob($this->directory . '/*');
            foreach($files as $file){
                if(is_file($file))
                    unlink($file);
            }
    }

    /**
     * Load data file into db
     *
     * @return void
     */
    public function loadDataIntoBD() : void{
        $consulta = new Consultas();
        try{
            $reader = new Csv();
        
            $reader->setInputEncoding('CP1252');
            $reader->setDelimiter(';');
            $reader->setEnclosure('');
            $reader->setSheetIndex(0);
            
            $spreadsheet = $reader->load('./files/' . $this->files['name']);
            
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
                    $arrayFila[] = $this->utf8Decode(trim($cell->getValue()));				
                }
                
                if($arrayFila[0] != '') {
                    $consulta->insertarCompetidor($arrayFila);
                }
                
            }
                    
        }catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e){
            die('Error loading file: ' . $e->getMessage());
        }
    }

    /**
    * Elimina los caracteres extraños y los caracteres binarios de una cadena de texto.
    * @param string $string Cadena a "limpiar".
    * @return string Devuelve la cadena que recibió (como parámetro) "limpia", es decir, sin caracteres raros.
    */
    private function utf8Decode(string $string) : string
    {
        $string = str_replace("\n","[NEWLINE]",$string);
        $string = htmlentities($string);
        $string = preg_replace('/[^(\x20-\x7F)]*/','',$string);
        $string = html_entity_decode($string);     
        $string = str_replace("[NEWLINE]","\n",$string);
        return $string;
    }
}

